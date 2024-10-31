<?php 

// ajax import callback
function postsaint_generate_images_ajax_callback() {

 	global $wpdb;

	extract($_POST);

	$response = $image_returned_error = $image_returned_error_message = $log_id = null;

	// save attachemnts ids if saving to Media Library
	$attachment_ids_array = array();

	$images_container = null;

	$images_key_legend = '<div class="postsaint-key-legend">';

	// images already added to library
	if( $generated_images_method == 'add_library' ){

		if($generator_standalone == 0){
			$images_key_legend.= '<button class="postsaint-generated-image-button"><span class="dashicons dashicons-format-gallery"></span></button> Insert into Post Content ';
			$images_key_legend.= '<button class="postsaint-generated-image-button"><span class="dashicons dashicons-admin-post"></span></button> Set as Featured Image ';
		} else {

			// cast as int
			//$num_results = (int)$num_results;
			//$num_images = (int)$num_images;

			// could be num_results or num_images, depending on image_generator used
			if( $image_generator == 'pexels'){

				$num_images_added = $num_results;

			} else {

				$num_images_added = $num_images;
			}

			// No buttons, just say images added to media library
			$images_key_legend.= '<span class="postsaint-succmsg">'.$num_images_added.' image(s) added to Media Library!</span>';
		}

	} else {
		$images_key_legend.= '<button class="postsaint-generated-image-button"><span class="dashicons dashicons-admin-media"></span></button> Add image into Media Library ';
	
		if($generator_standalone == 0){
			$images_key_legend.= '<button class="postsaint-generated-image-button"><span class="dashicons dashicons-format-gallery"></span></button> Insert into Post Content ';
			$images_key_legend.= '<button class="postsaint-generated-image-button"><span class="dashicons dashicons-admin-post"></span></button> Set as Featured Image ';
		}
	}

	$images_key_legend.= '</div><br>';

	$image_prompt = stripslashes($image_prompt);

	$modified_image_prompt = $image_prompt;

	// prepare image prompt
	// image style
	if( !empty($image_style) && $image_style != 'unspecified'){
		$modified_image_prompt = $modified_image_prompt . '. in '.$image_style.' style';
	}

	// artist style
	if( !empty($artist_style) && $artist_style != 'unspecified'){
		$modified_image_prompt = $modified_image_prompt . '. in '.$artist_style.' style';
	}	

	$datetime = date('Y-m-d H:i:s');

	// logging
	if(get_site_option('postsaint_settings_log_single_image','1') == 1){

		if( $image_generator == 'stable_diffusion'){

			// log
			$wpdb->insert($wpdb->prefix.'postsaint_stabilityai_image_logs', array(
				'original_image_prompt' => sanitize_text_field($image_prompt),
				'image_prompt' => sanitize_text_field($modified_image_prompt),
				'image_style' => sanitize_text_field($image_style),
				'artist_style' => sanitize_text_field($artist_style),
				'num_images' => (int)$num_images,
				'engine_id' => sanitize_text_field($stable_diffusion_engine),
				'cfg_scale' => sanitize_text_field($stable_diffusion_cfg_scale),
				'clip_guidance_preset' => '', // use API default
				'sampler' => sanitize_text_field($stable_diffusion_sampler),
				'seed' => '', // use API default
				'steps' => sanitize_text_field($stable_diffusion_steps),
				'image_width' => (int)$image_width,
				'image_height' => (int)$image_height,
				'created_at' => $datetime,
			));
		}


		if( $image_generator == 'stable_diffusion_dezgo'){

			// log
			$wpdb->insert($wpdb->prefix.'postsaint_dezgo_image_logs', array(
				'original_image_prompt' => sanitize_text_field($image_prompt),
				'image_prompt' => sanitize_text_field($modified_image_prompt),
				'negative_prompt' => sanitize_text_field($negative_prompt),
				'image_style' => sanitize_text_field($image_style),
				'artist_style' => sanitize_text_field($artist_style),
				'model_id' => sanitize_text_field($stable_diffusion_dezgo_model),
				'guidance' => sanitize_text_field($stable_diffusion_guidance),
				'sampler' => sanitize_text_field($stable_diffusion_dezgo_sampler),
				'seed' => '', // use API default
				'steps' => sanitize_text_field($stable_diffusion_steps),
				'image_width' => (int)$image_width,
				'image_height' => (int)$image_height,
				'created_at' => $datetime,
			));
		}


		if( $image_generator == 'dalle'){

			// log
			$wpdb->insert($wpdb->prefix.'postsaint_dalle_image_logs', array(
				'original_image_prompt' => sanitize_text_field($image_prompt),
				'image_prompt' => sanitize_text_field($modified_image_prompt),
				'image_style' => sanitize_text_field($image_style),
				'artist_style' => sanitize_text_field($artist_style),
				'num_images' => (int)$num_images,
				'openai_image_size' => (int)$openai_image_size,
				'created_at' => $datetime,
			));
		}

		$log_id = $wpdb->insert_id;
	}


	if( $image_generator == 'stable_diffusion'){

		$url = 'https://api.stability.ai/v1alpha/generation/'.$stable_diffusion_engine.'/text-to-image';

	    $body = array(
	        'width' => (int)$image_width,
	        'height' => (int)$image_height,
	        'samples' => (int)$num_images,
	        'cfg_scale' => (int)$stable_diffusion_cfg_scale,
	        'steps' => (int)$stable_diffusion_steps,
	    );

		$body['text_prompts'] = $arr_of_obj = array(
		  (object) [
		    'text' => $modified_image_prompt,
		    'weight' => 1
		  ]);	    

	    $args = array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'httpversion' => '1.0',
	        'headers' => array(
	            'Authorization' => get_site_option('postsaint_settings_stabilityai_api_key'),
	            'Accept' => 'application/json',
	        ),
	        'body' => json_encode($body),
	    );

	    $image_response = wp_remote_post ($url, $args);

	    if( !is_wp_error( $image_response ) ) {
			$return = json_decode($image_response['body'],true);

	    } else {

			$error_string = $image_response->get_error_message();
			$images_container = '<div id="message" class="error"><p>' . $error_string . '</p></div>';

			echo json_encode(array("done"=>1,"response"=>$response,"images_container"=>$images_container,"log_id" => null));	
			die();
	    }

		// error message
		if( !empty($return['message'])){
			$image_returned_error = 1;
			$image_returned_error_message = $return['message'];
		  	$images_container.= '<span class="postsaint-errormsg"><b>Stability.ai error:</b> '.esc_html($return['message']).'</span>';
		} else {

			// success - can't enter in log though
			$image_response = 'response too large to log.';



			foreach( $return['artifacts'] as $key => $res){

				$library_url = null;
				$attach_id = null;			

				$generated_image_url = 'data:image/png;base64,' . $res['base64'];

				// if 'add to library radio button selected' - else URLs returned to allow to save to library individually
				if( $generated_images_method == 'add_library' ){

					// add to media library
					$random6chars =  substr(md5(uniqid(mt_rand(), true)), 0, 6);

					// append random string for unique filenames
					$filename_base = postsaint_sanitize_file_name($modified_image_prompt).'-'.$random6chars;

					$attach_id = postsaint_add_image_media_library($generated_image_url, $filename_base, $modified_image_prompt, $insert_prompt_media_library_fields);

					if( $attach_id > 0 ){

						$attachment_ids_array[] = $attach_id;

						// library image url need to send to block editor
						$library_url = wp_get_attachment_url($attach_id);
					}
				}

				// open image container
				$images_container.= '<div class="generated-image-container">';

				// 'Add to Media Library' button
				// if already added to library dont show add to library button
				if( $generated_images_method != 'add_library'  ){

					// show only 1 button with class aligned right
					if($generator_standalone == 0){

						$add_to_media_button = 'postsaint-add-image-to-media';
					} else {
						$add_to_media_button = 'postsaint-add-image-to-media-right'; // alight button all the way right
					}

					$images_container.= '<button class="postsaint-generated-image-button '.esc_html($add_to_media_button).'" data-image_url="'.esc_html($generated_image_url).'" title="Add image to Media Library">
											<span class="dashicons dashicons-admin-media"></span>
										</button>';
				}

				// show these buttons on add/edit post/page only, not standalone image generator
				if($generator_standalone == 0){

					// 'Insert into Post content' button
					$images_container.= '
											<button class="postsaint-generated-image-button postsaint-add-image-to-post-content" title="Insert into Post Content">
												<span class="dashicons dashicons-format-gallery"></span>
											</button>';

					// 'Set as Featured Image' button
					$images_container.= '
											<button class="postsaint-generated-image-button postsaint-add-image-to-featured-image" title="Set as Featured Image">
												<span class="dashicons dashicons-admin-post"></span>
											</button>';

				}

				// image and close container
				$images_container.= '									

										<a href="'.esc_html($generated_image_url).'" data-lightbox="image-'.esc_attr($key).'" data-title="'.esc_html($modified_image_prompt).'"><img src="'.esc_html($generated_image_url).'" alt="'.esc_html($modified_image_prompt).'" data-library_url="'.esc_html($library_url).'" data-attachment_id="'.esc_html($attach_id).'" data-image_generator="'.esc_html($image_generator).'" data-log_id="'.esc_html($log_id).'"></a>
									</div>';

			}
		}
	}




	if( $image_generator == 'stable_diffusion_dezgo'){

		$url = 'https://api.dezgo.com/text2image';

	    $body = array(
	        'prompt' => $modified_image_prompt,
	        'negative_prompt' => $negative_prompt,
	        'model' => $stable_diffusion_dezgo_model,
	        'width' => (int)$image_width,
	        'height' => (int)$image_height,
	        'sampler' => $stable_diffusion_dezgo_sampler,
	        'guidance' => (int)$stable_diffusion_guidance,
	        'steps' => (int)$stable_diffusion_steps,
	    );

	    $args = array(
	        'method' => 'POST',
	        'timeout' => 45,
	        'httpversion' => '1.0',
	        'headers' => array(
	            'X-Dezgo-Key' => get_site_option('postsaint_settings_dezgo_api_key'),
	            'Content-Type' => 'application/json',
	        ),
	        'body' => json_encode($body),
	    );


	    // loop to create multiple images with multiple calls
	    for($x = 1; $x <= $num_images; $x++) {

			//$images_container.= $x.' <br>';

			$image_response = wp_remote_post ($url, $args);

			// wp remote error
		    if( is_wp_error( $image_response ) ) {

				$error_string = $image_response->get_error_message();
				$images_container = '<div id="message" class="error"><p>' . $error_string . '</p></div>';

				echo json_encode(array("done"=>1,"response"=>$response,"images_container"=>$images_container,"log_id" => null));	
				die();
		    } 

		    // error with respons
			$header_content_type = wp_remote_retrieve_header($image_response, 'content-type');

			if( $header_content_type == 'image/png'){
				//echo 'success';
			}

			$body = $image_response['body'];
			$response_array = $image_response['response'];

			// 400 bad request
			if( $response_array['code'] == '400' ){

				$error_string = '400 Bad Request';

				// get detailed error
				$body_arr = json_decode($body, true);

				// error messages return in different nodes
				$detailed_error_string = null;

				// no prompt
				if( !empty($body_arr['detail']) ){
					$detailed_error_string = $body_arr['detail'];
				}


				// incorrect img dimensions (height/width)
				if( !empty($body_arr['title']) ){
					$detailed_error_string = $body_arr['title'];
				}				

				$images_container = '<div id="message" class="error"><p><b>' . $error_string .'</b> '. $detailed_error_string.'</p></div>';

				echo json_encode(array("done"=>1,"response"=>$response,"images_container"=>$images_container,"log_id" => null));	
				die();
			}

			// 401 unauthorized
			if( $response_array['code'] == '401' ){

				$error_string = '401 Unauthorized. Please check your <a href="'.admin_url("admin.php?page=post-saint/settings.php#api-sources").'">Dezgo API key</a>.';
				$images_container = '<span class="postsaint-errormsg"><b>Dezgo Error:</b> ' . $error_string . '</span>';

				echo json_encode(array("done"=>1,"response"=>$response,"images_container"=>$images_container,"log_id" => null));
				die();
			}

			// success - can't enter in log though
			$image_response = 'response too large to log.';

			//$images_container.= 'BODY:'.$image_response['body'];

			$library_url = null;
			$attach_id = null;			

			$png_code = base64_encode($body); 

			$generated_image_url = 'data:image/png;base64,' . $png_code;

			// if 'add to library radio button selected' - else URLs returned to allow to save to library individually
			if( $generated_images_method == 'add_library' ){

				// add to media library
				$random6chars =  substr(md5(uniqid(mt_rand(), true)), 0, 6);

				// append random string for unique filenames
				$filename_base = postsaint_sanitize_file_name($modified_image_prompt).'-'.$random6chars;

				$attach_id = postsaint_add_image_media_library($generated_image_url, $filename_base, $modified_image_prompt, $insert_prompt_media_library_fields);

				if( $attach_id > 0 ){

					$attachment_ids_array[] = $attach_id;

					// library image url need to send to block editor
					$library_url = wp_get_attachment_url($attach_id);
				}
			}

			// open image container
			$images_container.= '<div class="generated-image-container">';

			// 'Add to Media Library' button
			// if already added to library dont show add to library button
			if( $generated_images_method != 'add_library'  ){

				// show only 1 button with class aligned right
				if($generator_standalone == 0){

					$add_to_media_button = 'postsaint-add-image-to-media';
				} else {
					$add_to_media_button = 'postsaint-add-image-to-media-right'; // alight button all the way right
				}

				$images_container.= '<button class="postsaint-generated-image-button '.esc_html($add_to_media_button).'" data-image_url="'.esc_html($generated_image_url).'" title="Add image to Media Library">
										<span class="dashicons dashicons-admin-media"></span>
									</button>';
			}

			// show these buttons on add/edit post/page only, not standalone image generator
			if($generator_standalone == 0){

				// 'Insert into Post content' button
				$images_container.= '
										<button class="postsaint-generated-image-button postsaint-add-image-to-post-content" title="Insert into Post Content">
											<span class="dashicons dashicons-format-gallery"></span>
										</button>';

				// 'Set as Featured Image' button
				$images_container.= '
										<button class="postsaint-generated-image-button postsaint-add-image-to-featured-image" title="Set as Featured Image">
											<span class="dashicons dashicons-admin-post"></span>
										</button>';

			}

			// image and close container
			$images_container.= '									

									<a href="'.esc_html($generated_image_url).'" data-lightbox="image-'.esc_attr($x).'" data-title="'.esc_html($modified_image_prompt).'"><img src="'.esc_html($generated_image_url).'" alt="'.esc_html($modified_image_prompt).'" data-library_url="'.esc_html($library_url).'" data-attachment_id="'.esc_html($attach_id).'" data-image_generator="'.esc_html($image_generator).'" data-log_id="'.esc_html($log_id).'"></a>
								</div>';

	    }
	}

	if( $image_generator == 'dalle'){

		require_once POSTSAINT_PLUGIN_PATH . 'vendor/Orhanerday/OpenAi/OpenAi.php';		

		$open_ai = new OpenAi( get_site_option('postsaint_settings_openai_api_key') );

		if( !empty($image_prompt) ){

		   $image_response = $open_ai->image([
		      "prompt" => $modified_image_prompt,
		      "n" => intval($num_images),
		      "size" => $openai_image_size,
		      "response_format" => "url",
		   ]);

		   $openai_api_image_response = json_decode($image_response , true); 

		   //success
		   if( isset($openai_api_image_response['data'][0]['url']) ){

		   		// loop through each image
				foreach($openai_api_image_response['data'] as $key => $generated_image){

					$library_url = null;
					$attach_id = null;			

					$generated_image_url = $generated_image['url'];

					// if 'add to library radio button selected' - else URLs returned to allow to save to library individually
					if( $generated_images_method == 'add_library' ){

						// add to media library
						$random6chars =  substr(md5(uniqid(mt_rand(), true)), 0, 6);

						// append random string for unique filenames
						$filename_base = postsaint_sanitize_file_name($modified_image_prompt).'-'.$random6chars;

						$attach_id = postsaint_add_image_media_library($generated_image_url, $filename_base, $modified_image_prompt, $insert_prompt_media_library_fields);

						if( $attach_id > 0 ){

							$attachment_ids_array[] = $attach_id;

							// library image url need to send to block editor
							$library_url = wp_get_attachment_url($attach_id);
						}
					}

					// open image container
					$images_container.= '<div class="generated-image-container">';

					// 'Add to Media Library' button
					// if already added to library dont show add to library button
					if( $generated_images_method != 'add_library'  ){

						// show only 1 button with class aligned right
						if($generator_standalone == 0){

							$add_to_media_button = 'postsaint-add-image-to-media';
						} else {
							$add_to_media_button = 'postsaint-add-image-to-media-right'; // alight button all the way right
						}

						$images_container.= '<button class="postsaint-generated-image-button '.esc_html($add_to_media_button).'" data-image_url="'.esc_attr($generated_image_url).'" title="Add image to Media Library">
												<span class="dashicons dashicons-admin-media"></span>
											</button>';
					}

					// show these buttons on add/edit post/page only, not standalone image generator
					if($generator_standalone == 0){

						// 'Insert into Post content' button
						$images_container.= '
												<button class="postsaint-generated-image-button postsaint-add-image-to-post-content" title="Insert into Post Content">
													<span class="dashicons dashicons-format-gallery"></span>
												</button>';

						// 'Set as Featured Image' button
						$images_container.= '
												<button class="postsaint-generated-image-button postsaint-add-image-to-featured-image" title="Set as Featured Image">
													<span class="dashicons dashicons-admin-post"></span>
												</button>';

					}

					// image and close container
					$images_container.= '									

											<a href="'.esc_attr($generated_image_url).'" data-lightbox="image-'.esc_attr($key).'" data-title="'.esc_attr($modified_image_prompt).'"><img src="'.esc_attr($generated_image_url).'" alt="'.esc_attr($modified_image_prompt).'" data-library_url="'.esc_attr($library_url).'" data-attachment_id="'.esc_attr($attach_id).'" data-image_generator="'.esc_html($image_generator).'"  data-log_id="'.esc_attr($log_id).'"></a>
										</div>';

				}
			}

		   // error
		   if( isset($openai_api_image_response['error']['message']) ){

		     	$openai_api_error_message = $openai_api_image_response['error']['message'];

		     	$image_returned_error = 1;
				$image_returned_error_message = $openai_api_image_response['error']['message'];


		  		$images_container.= '<span class="postsaint-errormsg"><b>OpenAI error:</b> '.esc_html($image_returned_error_message).'</span>';


		   }   
		}
	}



	if( $image_generator == 'pexels'){

		if( $page_num == 'undefined' ){
			$page_num = 1;
		}

		$url = 'https://api.pexels.com/v1/search?query='.$modified_image_prompt.'&orientation='.$pexels_orientation.'&size='.$pexels_size.'&color='.$pexels_color.'&per_page='.$num_results.'&page='.$page_num;

	    $args = array(
	        'method' => 'GET',
	        'timeout' => 45,
	        'httpversion' => '1.0',
	        'headers' => array(
	            'Authorization' => get_site_option('postsaint_settings_pexels_api_key'),
	            'Accept' => 'application/json',
	        ),
	        //'body' => json_encode($body),
	    );

	    $image_response = wp_remote_post ($url, $args);


	    if( !is_wp_error( $image_response ) ) {
			$return = json_decode($image_response['body'],true);

	    } else {

			$error_string = $image_response->get_error_message();
			$images_container = '<div id="message" class="error"><p>' . $error_string . '</p></div>';

			echo json_encode(array("done"=>1,"response"=>$response,"images_container"=>$images_container,"log_id" => null));	
			die();
	    }



			foreach( $return['photos'] as $key => $res){

				$library_url = null;
				$attach_id = null;			

				$original_image_url = $res['src']['original'];

				if( get_site_option('postsaint_settings_pexels_original_orientation') == 1 ){

					$tiny_image_url = $res['src']['medium'];

				} else {
					
					$tiny_image_url = $res['src']['tiny'];
				}

				// if 'add to library radio button selected' - else URLs returned to allow to save to library individually
				if( $generated_images_method == 'add_library' ){

					// add to media library
					$random6chars =  substr(md5(uniqid(mt_rand(), true)), 0, 6);

					// append random string for unique filenames
					$filename_base = postsaint_sanitize_file_name($modified_image_prompt).'-'.$random6chars;

					$attach_id = postsaint_add_image_media_library($original_image_url, $filename_base, $modified_image_prompt, $insert_prompt_media_library_fields);

					if( $attach_id > 0 ){

						$attachment_ids_array[] = $attach_id;

						// library image url need to send to block editor
						$library_url = wp_get_attachment_url($attach_id);
					}
				}

				// open image container
				$images_container.= '<div class="generated-image-container">';

				// 'Add to Media Library' button
				// if already added to library dont show add to library button
				if( $generated_images_method != 'add_library'  ){

					// show only 1 button with class aligned right
					if($generator_standalone == 0){

						$add_to_media_button = 'postsaint-add-image-to-media';
					} else {
						$add_to_media_button = 'postsaint-add-image-to-media-right'; // alight button all the way right
					}

					$images_container.= '<button class="postsaint-generated-image-button '.esc_html($add_to_media_button).'" data-image_url="'.esc_html($original_image_url).'" title="Add image to Media Library">
											<span class="dashicons dashicons-admin-media"></span>
										</button>';
				}

				// show these buttons on add/edit post/page only, not standalone image generator
				if($generator_standalone == 0){

					// 'Insert into Post content' button
					$images_container.= '
											<button class="postsaint-generated-image-button postsaint-add-image-to-post-content" title="Insert into Post Content">
												<span class="dashicons dashicons-format-gallery"></span>
											</button>';

					// 'Set as Featured Image' button
					$images_container.= '
											<button class="postsaint-generated-image-button postsaint-add-image-to-featured-image" title="Set as Featured Image">
												<span class="dashicons dashicons-admin-post"></span>
											</button>';

				}

				// image and close container
				$images_container.= '									

										<a href="'.esc_html($original_image_url).'" data-lightbox="image-'.esc_attr($key).'" data-title="'.esc_html($modified_image_prompt).'"><img src="'.esc_html($tiny_image_url).'" alt="'.esc_html($modified_image_prompt).'" data-library_url="'.esc_html($library_url).'" data-attachment_id="'.esc_html($attach_id).'" data-image_generator="'.esc_html($image_generator).'" data-log_id="'.esc_html($log_id).'"></a>
									</div>';

			}


			// total results
			$images_container.= '<br>Total Results: '.$return['total_results'].'<br>';


			// next page
			if(  !empty($return['prev_page']) ){

				$prev_page_num = $page_num - 1;

				$images_container.= '<button class="postsaint-generated-image-button" id="postsaint-paginate-generated-images" data-page_num="'.$prev_page_num.'"><span class="dashicons dashicons-controls-back"></span> Page '.$prev_page_num.'</button>';

			}

			// current page

			$images_container.= ' Current Page: '.$page_num.' ';

			// next page
			if(  !empty($return['next_page']) ){

				$next_page_num = $page_num + 1;

				$images_container.= '<button class="postsaint-generated-image-button" id="postsaint-paginate-generated-images" data-page_num="'.$next_page_num.'">Page '.$next_page_num.' <span class="dashicons dashicons-controls-forward"></span></button>';

			}
			

			$images_container.= '<br>';


	}








	// json_encode $attachment_ids_array to store in db if has values
	if( count($attachment_ids_array) > 0 ){
		$attachment_ids_array = json_encode($attachment_ids_array); 
	} else {
		$attachment_ids_array = NULL;
	}

	if(get_site_option('postsaint_settings_log_single_image','1') == 1){

		if( $image_generator == 'stable_diffusion'){
			// update
		    $wpdb->update( $wpdb->prefix.'postsaint_stabilityai_image_logs', array( 

		    	'attachment_ids' => sanitize_text_field($attachment_ids_array),
		    	'image_response' => sanitize_text_field($image_response),
		    	'image_returned_error' => sanitize_text_field($image_returned_error),
		    	'image_returned_error_message' => sanitize_text_field($image_returned_error_message),
		    ),array('id'=>$log_id)); 
		}


		if( $image_generator == 'stable_diffusion_dezgo'){
			// update
		    $wpdb->update( $wpdb->prefix.'postsaint_dezgo_image_logs', array( 

		    	'attachment_ids' => sanitize_text_field($attachment_ids_array),
		    	'image_response' => sanitize_text_field($image_response),
		    	'image_returned_error' => sanitize_text_field($image_returned_error),
		    	'image_returned_error_message' => sanitize_text_field($image_returned_error_message),
		    ),array('id'=>$log_id)); 
		}


		if( $image_generator == 'dalle'){
			// update
		    $wpdb->update( $wpdb->prefix.'postsaint_dalle_image_logs', array( 

		    	'attachment_ids' => sanitize_text_field($attachment_ids_array),
		    	'image_response' => sanitize_text_field($image_response),
		    	'image_returned_error' => sanitize_text_field($image_returned_error),
		    	'image_returned_error_message' => sanitize_text_field($image_returned_error_message),
		    ),array('id'=>$log_id)); 
		}
	}


	// append images_key_legend to images_container
	if( $images_container !== null && $image_returned_error != 1){
		$images_container = $images_key_legend.$images_container;
	}

	$done=1;

	echo json_encode(array("done"=>$done,"response"=>$response,"images_container"=>$images_container,"log_id" => $log_id));	

	die();
}
add_action('wp_ajax_generate_images', 'postsaint_generate_images_ajax_callback');

?>