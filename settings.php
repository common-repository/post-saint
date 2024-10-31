<div class="wrap postsaint-wrap">

 	<div id="postsaint-heading-container">
		<div id="postsaint-support"><a href="https://postsaint.com/docs" target="_new">Docs</a> | <a href="https://postsaint.com/contact" target="_new">Support</a></div>
		<div id="postsaint-logo"></div>
	</div>
	<br>
	<h1 class="postsaint-heading">Settings</h1>

	<script>
		jQuery( function() {
		jQuery( "#tabs" ).tabs();
		} );
	</script>

	<?php

	// form submitted, update values
	if( isset($_REQUEST['Submit']) ){

		$error_msg = null;

		// get all $_POST values
		foreach ($_POST as $key => $value) {

			// ignore $_POST['Submit']
			if($key == 'Submit'){
				continue;
			}

		    // don't update api key with empty value if already set
		    if( strlen($value) < 1 && $key == 'postsaint_settings_openai_api_key' && get_site_option('postsaint_settings_openai_api_key') !== null){
		    	continue;
		    }

		    // don't update api key with empty value if already set
		    if( strlen($value) < 1 && $key == 'postsaint_settings_stabilityai_api_key' && get_site_option('postsaint_settings_stabilityai_api_key') !== null){
		    	continue;
		    }

		    // don't update api key with empty value if already set
		    if( strlen($value) < 1 && $key == 'postsaint_settings_dezgo_api_key' && get_site_option('postsaint_settings_dezgo_api_key') !== null){
		    	continue;
		    }

		    // don't update api key with empty value if already set
		    if( strlen($value) < 1 && $key == 'postsaint_settings_pexels_api_key' && get_site_option('postsaint_settings_pexels_api_key') !== null){
		    	continue;
		    }

		    // validate other required values (optional values)
		    // make array for exceptions
		    $optional_values_array = array('postsaint_settings_openai_api_key', 'postsaint_settings_stabilityai_api_key','postsaint_settings_dezgo_api_key','postsaint_settings_pexels_api_key', 'postsaint_settings_prepend_prompt','postsaint_settings_append_prompt','postsaint_settings_keywords');

		    if( strlen($value) < 1  && !in_array($key, $optional_values_array) ){
		    	$error_msg = 'All fields required.';
		    	continue;
		    }

		    // sanitize api key
			if( $key == 'postsaint_settings_openai_api_key' || $key == 'postsaint_settings_stabilityai_api_key' || $key == 'postsaint_settings_dezgo_api_key' || $key == 'postsaint_settings_pexels_api_key'){

				// only alphanumeric underscore, dash
				$value = preg_replace("/[^a-z0-9_-]+/i", "", $value);
			}

			// if default image generator selected api key not set, switch to fallback if able
			if($key == 'postsaint_settings_image_generator'){

				$message_ext = null;

				// dalle
				if( $value == 'dalle' && empty(get_site_option('postsaint_settings_openai_api_key')) && empty($_POST['postsaint_settings_openai_api_key']) ){

					if ( strlen($_POST['postsaint_settings_stabilityai_api_key']) > 0 || strlen(get_site_option('postsaint_settings_stabilityai_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Stable Diffusion.';
						$value = 'stable_diffusion';
					}

					if ( strlen($_POST['postsaint_settings_dezgo_api_key']) > 0 || strlen(get_site_option('postsaint_settings_dezgo_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Stable Diffusion - Dezgo.';
						$value = 'stable_diffusion_dezgo';
					}

					if ( strlen($_POST['postsaint_settings_pexels_api_key']) > 0 || strlen(get_site_option('postsaint_settings_pexels_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Pexels.';
						$value = 'pexels';
					}					

					echo'<div class="update-nag notice"><p><b>Can\'t set Default Image Generator to DALL-E unless <a href="'.admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php#api-sources').'">OpenAI API key set</a>. '.$message_ext.'</b></p></div>';
				}

				// stable_diffusion
				if( $value == 'stable_diffusion' && empty(get_site_option('postsaint_settings_stabilityai_api_key')) && empty($_POST['postsaint_settings_stabilityai_api_key'])  ){

					if ( strlen($_POST['postsaint_settings_openai_api_key']) > 0 || strlen(get_site_option('postsaint_settings_openai_api_key')) > 0 ){

						$message_ext = 'Default Image Generateor set to DALL-E.';
						$value = 'dalle';
					}

					if ( strlen($_POST['postsaint_settings_dezgo_api_key']) > 0 || strlen(get_site_option('postsaint_settings_dezgo_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Stable Diffusion - Dezgo.';
						$value = 'stable_diffusion_dezgo';
					}

					if ( strlen($_POST['postsaint_settings_pexels_api_key']) > 0 || strlen(get_site_option('postsaint_settings_pexels_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Pexels.';
						$value = 'pexels';
					}						

					echo'<div class="update-nag notice"><p><b>Can\'t set Default Image Generator to Stable Diffusion unless <a href="'.admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php#api-sources').'">Stability.AI API key set</a>. '.$message_ext.'</b></p></div>';
				}

				// stable_diffusion_dezgo
				if( $value == 'stable_diffusion_dezgo' && empty(get_site_option('postsaint_settings_dezgo_api_key')) && empty($_POST['postsaint_settings_dezgo_api_key']) ){

					if ( strlen($_POST['postsaint_settings_openai_api_key']) > 0 || strlen(get_site_option('postsaint_settings_openai_api_key')) > 0 ){

						$message_ext = 'Default Image Generateor set to DALL-E.';
						$value = 'dalle';
					}

					if ( strlen($_POST['postsaint_settings_stabilityai_api_key']) > 0 || strlen(get_site_option('postsaint_settings_stabilityai_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Stable Diffusion - Stability.ai.';
						$value = 'stable_diffusion';
					}

					if ( strlen($_POST['postsaint_settings_pexels_api_key']) > 0 || strlen(get_site_option('postsaint_settings_pexels_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Pexels.';
						$value = 'pexels';
					}							

					echo'<div class="update-nag notice"><p><b>Can\'t set Default Image Generator to Stable Diffusion - Dezgo unless <a href="'.admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php#api-sources').'">Dezgo API key set</a>. . '.$message_ext.'</b></p></div>';
				}

				// pexels
				if( $value == 'pexels' && empty(get_site_option('postsaint_settings_pexels_api_key')) && empty($_POST['postsaint_settings_pexels_api_key']) ){

					if ( strlen($_POST['postsaint_settings_openai_api_key']) > 0 || strlen(get_site_option('postsaint_settings_openai_api_key')) > 0 ){

						$message_ext = 'Default Image Generateor set to DALL-E.';
						$value = 'dalle';
					}

					if ( strlen($_POST['postsaint_settings_stabilityai_api_key']) > 0 || strlen(get_site_option('postsaint_settings_stabilityai_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Stable Diffusion - Stability.ai.';
						$value = 'stable_diffusion';
					}

					if ( strlen($_POST['postsaint_settings_dezgo_api_key']) > 0 || strlen(get_site_option('postsaint_settings_dezgo_api_key')) > 0 ){

						$message_ext = 'Default Image Generator set to Stable Diffusion - Dezgo.';
						$value = 'stable_diffusion_dezgo';
					}						

					echo'<div class="update-nag notice"><p><b>Can\'t set Default Image Generator to Pexels unless <a href="'.admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php#api-sources').'">Pexels API key set</a>. . '.$message_ext.'</b></p></div>';
				}
			}

			// delete cron hook if possibly no longer using wp cron
			if($key == 'postsaint_auto_post_trigger' && $value != 'wp_cron'){
				wp_clear_scheduled_hook( 'postsaint_cron_hook' );
			}

			$value = sanitize_text_field($value);

		    // still update values even if error
		    update_site_option($key, $value);
		}

		// unchecked checkboxes don't have names passed, so must force check for them
		$checkboxes_array = array(
			'postsaint_settings_show_text_generation',			
			'postsaint_settings_show_image_generation',
			'postsaint_settings_pexels_original_orientation',
			'postsaint_settings_log_bulk_posts',
			'postsaint_settings_log_single_image',
			'postsaint_settings_log_single_text',
			'postsaint_settings_delete_log_data_deactivation', 
			'postsaint_settings_delete_default_settings_deactivation',
			'postsaint_settings_skip_bulk_post_image_error',
			'postsaint_settings_halt_bulk_post_first_error'
		);

		foreach ($checkboxes_array as $field) {
			
			if( !isset($_POST[$field])){

				// unchecked, delete
				update_site_option($field, 0);
			} else {

				// checked, update
				update_site_option($field, 1);
			}
		}

		// display notice
		if( $error_msg !== null){
			echo'<div class="error"><p><b>'.esc_html($error_msg).'</b></p></div>';
		} else {
			echo'<div class="updated"><p><b>Settings saved.</b></p></div>';
		}
	}
	?>

	<form action="<?php echo admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php'); ?>" method="POST">

		<div id="tabs">
  			<ul class="nav-tab-wrapper">
    			<li><a href="#text-generation" class="nav-tab">Text Generation</a></li>
    			<li><a href="#image-generation" class="nav-tab">Image Generation</a></li>
    			<li><a href="#api-sources" class="nav-tab">API Sources</a></li>
    			<li><a href="#bulk-auto-import" class="nav-tab">Bulk & Auto Import</a></li>
    			<li><a href="#misc" class="nav-tab">Misc</a></li>
  			</ul>

  			<div id="text-generation">

	 			<table class="form-table">
	    			<tbody>
	      				<tr>
	    					<th><label for="postsaint_settings_show_text_generation">Show Text Generation Options on Add/Edit Posts & Pages</label></th>
	        				<td>
		            			<?php

		            			$postsaint_settings_show_text_generation = get_site_option('postsaint_settings_show_text_generation','1');

		            			$checked = null;

		            			if( $postsaint_settings_show_text_generation == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_show_text_generation" id="postsaint_settings_show_text_generation" <?php echo esc_html($checked); ?>>
	        				</td>
	      				</tr>	
					</tbody>
	  			</table>


	  			<h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-welcome-learn-more"> Writing Instructions Default Settings</h3>
	 			<table class="form-table">
	    			<tbody>


          				<tr>
            				<th><label for="postsaint_settings_prepend_prompt">Prepend Prompt</label></th>
            				<td>
            					<?php $postsaint_settings_prepend_prompt = get_site_option('postsaint_settings_prepend_prompt');?>
								<textarea name="postsaint_settings_prepend_prompt" id="postsaint_settings_prepend_prompt" class="postsaint-input-full-width" placeholder='Text to be added before prompt. Example: "write an article about"'><?php echo esc_html($postsaint_settings_prepend_prompt) ?></textarea>
            				</td>
          				</tr>
						<tr>
            				<th><label for="postsaint_settings_append_prompt">Append Prompt</label></th>
            				<td>
            					<?php $postsaint_settings_append_prompt = get_site_option('postsaint_settings_append_prompt');?>
								<textarea name="postsaint_settings_append_prompt" id="postsaint_settings_append_prompt" class="postsaint-input-full-width" placeholder='Text to be added after prompt. Example: "in the writing style of Shakespeare"'><?php echo esc_html($postsaint_settings_append_prompt) ?></textarea>
            				</td>
          				</tr>
		  				<tr>
							<th><label for="postsaint_settings_writing_style">Writing Style</label></th>
							<td>
								<?php
								$array = array(
									'informative' => 'Informative',
									'descriptive' => 'Descriptive',
									'creative' => 'Creative',
									'narrative' => 'Narrative',
									'persuasive' => 'Persuasive',
									'expository' => 'Expository',
									'reflective' => 'Reflective',
									'argumentative' => 'Argumentative',
									'analytical' => 'Analytical',
									'critical' => 'Critical',
									'evaluative' => 'Evaluative',
									'journalistic' => 'Journalistic',
									'technical' => 'Technical',
									'report' => 'Report',
									'research' => 'Research',
									'unspecified' => '- Do Not Specify -',
								);

								$default_val = get_site_option('postsaint_settings_writing_style','informative');
            	 
								postsaint_select_field('postsaint_settings_writing_style', $array, $default_val);
								?>
							</td>
		  				</tr>	 
						<tr>
							<th><label for="postsaint_settings_writing_tone">Writing Tone</label></th>
							<td>
								<?php
								$array = array(
									'formal' => 'Formal',
									'neutral' => 'Neutral',
									'assertive' => 'Assertive',
									'cheerful' => 'Cheerful',
									'humorous' => 'Humorous',
									'informal' => 'Informal',
									'inspirational' => 'Inspirational',
									'sarcastic' => 'Sarcastic',
									'skeptical' => 'Skeptical',
									'optimistic' => 'Optimistic',
									'worried' => 'Worried',
									'curious' => 'Curious',
									'surprise' => 'Surprised',
									'encouraged' => 'Encouraging',
									'disappointed' => 'Disappointed',
									'unspecified' => '- Do Not Specify -',					
								);

								$default_val = get_site_option('postsaint_settings_writing_tone','formal');
            	 
								postsaint_select_field('postsaint_settings_writing_tone', $array, $default_val);
								?>
							</td>
		  				</tr>	 
		  				<tr>
							<th><label for="postsaint_settings_keywords">Keywords for Context</label></th>
							<td>
								<?php $postsaint_settings_keywords = get_site_option('postsaint_settings_keywords');?>
								<textarea name="postsaint_settings_keywords" id="postsaint_settings_keywords" class="postsaint-input-full-width" placeholder='Example: "history, overcoming obstacles, motivational"'><?php echo esc_html($postsaint_settings_keywords) ?></textarea>
							</td>
		  				</tr>
					</tbody>
	  			</table>	

  				<h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-admin-settings"> OpenAI Default Settings</h3>
				<table class="form-table">
	    			<tbody>
          				<tr>
	            			<th><label for="postsaint_settings_openai_model">Model</label></th>
	            			<td>
								<?php
								$array = array(
									'gpt-4' => 'gpt-4',
									'gpt-3.5-turbo' => 'gpt-3.5-turbo',
									'text-davinci-003' => 'text-davinci-003',
									'text-curie-001' => 'text-curie-001',
									'text-babbage-001' => 'text-babbage-001',
									'text-ada-001' => 'text-ada-001',
								);

	            				$default_val = get_site_option('postsaint_settings_openai_model','gpt-3.5-turbo');
	            	
								postsaint_select_field('postsaint_settings_openai_model', $array, $default_val);
								?>
	            			</td>
          				</tr>

	      				<tr>
	    					<th><label for="postsaint_settings_openai_max_tokens">Max Tokens</label></th>
	        				<td>
                				<input type="range" name="postsaint_settings_openai_max_tokens_range" min="0" max="8192" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_max_tokens','500')) ?>" oninput="this.form.postsaint_settings_openai_max_tokens.value=this.value" />
                				<input type="number" name="postsaint_settings_openai_max_tokens" min="0" max="8192" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_max_tokens','500')) ?>" class="postsaint-input-80" oninput="this.form.postsaint_settings_openai_max_tokens_range.value=this.value" />
	        				</td>
	      				</tr>	      
	      				<tr>
	    					<th><label for="postsaint_settings_openai_temperature">Temperature</label></th>
	        				<td>
                				<input type="range" name="postsaint_settings_openai_temperature_range" min="0" max="1" step="0.1" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_temperature','0.7')) ?>" oninput="this.form.postsaint_settings_openai_temperature.value=this.value" />
                				<input type="number" name="postsaint_settings_openai_temperature" min="0" max="1" step="0.1" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_temperature','0.7')) ?>" class="postsaint-input-60" oninput="this.form.postsaint_settings_openai_temperature_range.value=this.value" />
	        				</td>
	      				</tr>	    
	      				<tr>
	    					<th><label for="postsaint_settings_openai_top_p">Top P</label></th>
	        				<td>
                				<input type="range" name="postsaint_settings_openai_top_p_range" min="0" max="1" step="0.1" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_top_p','1')) ?>" oninput="this.form.postsaint_settings_openai_top_p.value=this.value" />
                				<input type="number" name="postsaint_settings_openai_top_p" min="0" max="1" step="0.1" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_top_p','1')) ?>" class="postsaint-input-60" oninput="this.form.postsaint_settings_openai_top_p_range.value=this.value" />
	        				</td>
	      				</tr>	  
	      				<tr>
	    					<th><label for="postsaint_settings_openai_frequency_penalty">Frequency Penalty</label></th>
	        				<td>
                				<input type="range" name="postsaint_settings_openai_frequency_penalty_range" min="0" max="2" step="0.01" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_frequency_penalty','0')) ?>" oninput="this.form.postsaint_settings_openai_frequency_penalty.value=this.value" />
                				<input type="number" name="postsaint_settings_openai_frequency_penalty" min="0" max="2" step="0.1" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_frequency_penalty','0')) ?>" class="postsaint-input-80" oninput="this.form.postsaint_settings_openai_frequency_penalty_range.value=this.value" />
	        				</td>
	      				</tr>	  
	      				<tr>
	    					<th><label for="postsaint_settings_openai_presence_penalty">Presence Penalty</label></th>
	        				<td>
                				<input type="range" name="postsaint_settings_openai_presence_penalty_range" min="0" max="2" step="0.01" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_presence_penalty','0')) ?>" oninput="this.form.postsaint_settings_openai_presence_penalty.value=this.value" />
                				<input type="number" name="postsaint_settings_openai_presence_penalty" min="0" max="2" step="0.1" value="<?php echo esc_attr(get_site_option('postsaint_settings_openai_presence_penalty','0')) ?>" class="postsaint-input-80" oninput="this.form.postsaint_settings_openai_presence_penalty_range.value=this.value" />
	        				</td>
	      				</tr>	 
					</tbody>
	  			</table>	
			</div>

  			<div id="image-generation">


 				<table class="form-table">
	    			<tbody>
	      				<tr>
	    					<th><label for="postsaint_settings_show_image_generation">Show Image Generation Options on Add/Edit Posts & Pages</label></th>
	        				<td>

		            			<?php

		            			$postsaint_settings_show_image_generation = get_site_option('postsaint_settings_show_image_generation','1');

		            			$checked = null;

		            			if( $postsaint_settings_show_image_generation == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_show_image_generation" id="postsaint_settings_show_image_generation" <?php echo esc_html($checked); ?>>
	        				</td>
	      				</tr>	
					</tbody>
	  			</table>

	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-art"></span> Stable Diffusion Default Settings</h3>
	  			<table class="form-table">
	   				<tbody>

        				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_steps">Steps</label></th>
            				<td>
                				<input type="range" name="postsaint_settings_stable_diffusion_steps_range" min="10" max="150" value="<?php echo esc_attr(get_site_option('postsaint_settings_stable_diffusion_steps','50')) ?>" oninput="this.form.postsaint_settings_stable_diffusion_steps.value=this.value" />
                				<input type="number" name="postsaint_settings_stable_diffusion_steps" min="10" max="150" value="<?php echo esc_attr(get_site_option('postsaint_settings_stable_diffusion_steps','50')) ?>" class="postsaint-input-80" oninput="this.form.postsaint_settings_stable_diffusion_steps_range.value=this.value" />
            				</td>
          				</tr>
          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_image_width">Image Width</label></th>
            				<td>
								<?php

	            				$default_val = get_site_option('postsaint_settings_stable_diffusion_image_width','512');
	            	
								echo '<input type="number" class="postsaint-input-80" id="postsaint_settings_stable_diffusion_image_width" name="postsaint_settings_stable_diffusion_image_width" value="'.esc_attr($default_val).'">';
								?>
            				</td>
          				</tr>
          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_image_height">Image Height</label></th>
        					<td>
								<?php

        						$default_val = get_site_option('postsaint_settings_stable_diffusion_image_height','512');
        	
								echo '<input type="number" class="postsaint-input-80" id="postsaint_settings_stable_diffusion_image_height" name="postsaint_settings_stable_diffusion_image_height" value="'.esc_attr($default_val).'">';
								?>
        					</td>
          				</tr>

					</tbody>
	  			</table>	



	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-admin-tools"></span> Stable Diffusion - Stability.ai Default Settings</h3>
	  			<table class="form-table">
	   				<tbody>
          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_engine">Engine</label></th>
            				<td>
								<?php
								$array = array(
									'stable-diffusion-v1' => 'Stable Diffusion v1.4',
									'stable-diffusion-v1-5' => 'Stable Diffusion v1.5',
									'stable-diffusion-512-v2-0' => 'Stable Diffusion v2.0',
									'stable-diffusion-768-v2-0' => 'Stable Diffusion v2.0-768',
									'stable-diffusion-512-v2-1' => 'Stable Diffusion v2.1',
									'stable-diffusion-768-v2-1' => 'Stable Diffusion v2.1-768',
									'stable-inpainting-v1-0' => 'Stable Inpainting v1.0',
									'stable-inpainting-512-v2-0' => 'Stability-AI Stable Inpainting v2.0',
								);

	            				$default_val = get_site_option('postsaint_settings_stable_diffusion_engine','stable-diffusion-512-v2-1');
	            	
								postsaint_select_field('postsaint_settings_stable_diffusion_engine', $array, $default_val);
								?>
            				</td>
          				</tr>
          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_cfg_scale">CGF Scale</label></th>
            				<td>
                				<input type="range" name="postsaint_settings_stable_diffusion_cfg_scale_range" min="0" max="35" value="<?php echo esc_attr(get_site_option('postsaint_settings_stable_diffusion_cfg_scale','7')) ?>" oninput="this.form.postsaint_settings_stable_diffusion_cfg_scale.value=this.value" />
                				<input type="number" name="postsaint_settings_stable_diffusion_cfg_scale" min="0" max="35" value="<?php echo esc_attr(get_site_option('postsaint_settings_stable_diffusion_cfg_scale','7')) ?>" class="postsaint-input-80" oninput="this.form.postsaint_settings_stable_diffusion_cfg_scale_range.value=this.value" />
            				</td>
          				</tr>

          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_sampler">Sampler</label></th>
            				<td>

            					<?php

				                $array = array(
				                    'DDIM' => 'DDIM',
				                    'DDPM' => 'DDPM',
				                    'K_DPMPP_2M' => 'K_DPMPP_2M',
				                    'K_DPMPP_2S_ANCESTRAL' => 'K_DPMPP_2S_ANCESTRAL',
				                    'K_DPM_2' => 'K_DPM_2',
				                    'K_DPM_2_ANCESTRAL' => 'K_DPM_2_ANCESTRAL',
				                    'K_EULER' => 'K_EULER',
				                    'K_EULER_ANCESTRAL' => 'K_EULER_ANCESTRAL',
				                    'K_HEUN' => 'K_HEUN',
				                    'K_LMS' => 'K_LMS',
				                );

				                $default_val = get_site_option('postsaint_settings_stable_diffusion_sampler','dpm');
				                
				                postsaint_select_field('postsaint_settings_stable_diffusion_sampler', $array, $default_val);
				                ?>
				            </td>
				         </tr>


					</tbody>
	  			</table>	




	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-rest-api"></span> Stable Diffusion - Dezgo Default Settings</h3>
	  			<table class="form-table">
	   				<tbody>
          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_dezgo_model">Model</label></th>
            				<td>
								<?php

                				$postsaint_dezgo_models = get_site_option('postsaint_dezgo_models');

                				$array = json_decode($postsaint_dezgo_models);

	            				$default_val = get_site_option('postsaint_settings_stable_diffusion_dezgo_model','stablediffusion_2_1_512px');
	            	
								postsaint_select_field('postsaint_settings_stable_diffusion_dezgo_model', $array, $default_val);
								?>

								<button id="update-dezgo-models">Update Dezgo Models</button>
            				</td>
          				</tr>


			         	<tr>
			            	<th><label for="postsaint_settings_negative_prompt">Negative Prompt</label></th>
			            	<td>

			            		<?php $postsaint_settings_negative_prompt = get_site_option('postsaint_settings_negative_prompt','ugly, tiling, poorly drawn hands, poorly drawn feet, poorly drawn face, out of frame, extra limbs, disfigured, deformed, body out of frame, blurry, bad anatomy, blurred, watermark, grainy, signature, cut off, draft');?>
			                	<textarea name="postsaint_settings_negative_prompt" id="postsaint_settings_negative_prompt" class="postsaint-input-full-width"><?php echo esc_html($postsaint_settings_negative_prompt) ?></textarea>
			            	</td>
			          	</tr>

          				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_guidance">Guidance</label></th>
            				<td>
                				<input type="range" name="postsaint_settings_stable_diffusion_guidance_range" min="-20" max="20" value="<?php echo esc_attr(get_site_option('postsaint_settings_stable_diffusion_guidance','7')) ?>" oninput="this.form.postsaint_settings_stable_diffusion_guidance.value=this.value" />
                				<input type="number" name="postsaint_settings_stable_diffusion_guidance" min="-20" max="20" value="<?php echo esc_attr(get_site_option('postsaint_settings_stable_diffusion_guidance','7')) ?>" class="postsaint-input-80" oninput="this.form.postsaint_settings_stable_diffusion_guidance_range.value=this.value" />
            				</td>
          				</tr>
         				<tr>
            				<th><label for="postsaint_settings_stable_diffusion_dezgo_sampler">Sampler</label></th>
            				<td><?php

				                $array = array(
				                    'ddim' => 'DDIM',
				                    'dpm' => 'DPM',
				                    'euler' => 'Euler',
				                    'euler_a' => 'Euler Ancestral',
				                    'k_lms' => 'K-LMS',
				                    'pndm' => 'PNDM',
				                );

				                $default_val = get_site_option('postsaint_settings_stable_diffusion_dezgo_sampler','dpm');
				                
				                postsaint_select_field('postsaint_settings_stable_diffusion_dezgo_sampler', $array, $default_val);
				                ?>
				            </td>
				         </tr>
					</tbody>
	  			</table>	

	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-admin-generic"></span> DALL-E Default Settings</h3>
	  			<table class="form-table">
	   				<tbody>
          				<tr>
            				<th><label for="postsaint_settings_openai_image_size">Image Size</label></th>
            				<td>
								<?php

								$array = array(
									'256x256' => '256x256',
									'512x512' => '512x512',
									'1024x1024' => '1024x1024',
								);

				    			$default_val = get_site_option('postsaint_settings_openai_image_size','512x512');

								postsaint_select_field('postsaint_settings_openai_image_size', $array, $default_val);
								?>
            				</td>
          				</tr>
					</tbody>
	  			</table>	

	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-camera"></span> Pexels Default Settings</h3>
	  			<table class="form-table">
	   				<tbody>


          				<tr>
            				<th><label for="postsaint_settings_pexels_orientation">Orientation</label></th>
            				<td>
								<?php

								$array = array(
									'landscape' => 'Landscape',
									'portrait' => 'Portrait',
									'square' => 'Square',
				                    'unspecified' => '- Do Not Specify -',										
								);

				    			$default_val = get_site_option('postsaint_settings_pexels_orientation','landscape');

								postsaint_select_field('postsaint_settings_pexels_orientation', $array, $default_val);
								?>
            				</td>
          				</tr>

          				<tr>
            				<th><label for="postsaint_settings_pexels_size">Minimum Size</label></th>
            				<td>
								<?php

								$array = array(
									'large' => 'Large (24MP)',
									'medium' => 'Medium (12MP)',
									'small' => 'Small (4MP)',
								);

				    			$default_val = get_site_option('postsaint_settings_pexels_size','small');

								postsaint_select_field('postsaint_settings_pexels_size', $array, $default_val);
								?>
            				</td>
          				</tr>          				

          				<tr>
            				<th><label for="postsaint_settings_pexels_color">Color</label></th>
            				<td>
								<?php

								$array = array(
									'red' => 'Red',
									'orange' => 'Orange',
									'yellow' => 'Yellow',
									'green' => 'Green',
									'turquoise' => 'Turquoise',
									'blue' => 'Blue',
									'violet' => 'Violet',
									'pink' => 'Pink',
									'brown' => 'Brown',
									'black' => 'Black',
									'gray' => 'Gray',
									'white' => 'White',
				                    'unspecified' => '- Do Not Specify -',									
								);

				    			$default_val = get_site_option('postsaint_settings_pexels_color','unspecified');

								postsaint_select_field('postsaint_settings_pexels_color', $array, $default_val);
								?>
            				</td>
          				</tr>  


	      				<tr>
	    					<th><label for="postsaint_settings_num_results">Number of Results</label></th>
	        				<td>
								<?php

								$default_val = get_site_option('postsaint_settings_num_results', 15);

								echo '
		            			<input type="range" name="postsaint_settings_num_results_range" min="1" max="50" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_settings_num_results.value=this.value" />
		            			<input type="number" name="postsaint_settings_num_results" id="postsaint_settings_num_results" min="1" max="50" value="'.esc_attr($default_val).'" class="postsaint-input-60" oninput="this.form.postsaint_settings_num_results_range.value=this.value" />';
								?>
	        				</td>
	      				</tr>

	      				<tr>
	    					<th><label for="postsaint_settings_pexels_original_orientation">Show Result's Thumbnails in Original Orientation</label></th>
	        				<td>

		            			<?php

		            			$postsaint_settings_pexels_original_orientation = get_site_option('postsaint_settings_pexels_original_orientation','1');

		            			$checked = null;

		            			if( $postsaint_settings_pexels_original_orientation == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_pexels_original_orientation" id="postsaint_settings_pexels_original_orientation" <?php echo esc_html($checked); ?>>
		            		
	        				</td>
	      				</tr>	      				

					</tbody>
	  			</table>	


	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-format-gallery"></span> Image Generation Default Settings</h3>
	  			<table class="form-table">
	    			<tbody>
          				<tr>
            				<th><label for="postsaint_settings_image_generator">Image Generator</label></th>
            				<td>
	            				<?php

	                			$array = array(
	                    			'stable_diffusion' => 'Stable Diffusion - Stability.ai',
	                    			'stable_diffusion_dezgo' => 'Stable Diffusion - Dezgo',
	                    			'dalle' => 'DALL-E',
	                    			'pexels' => 'Pexels',

	                			);

	                			$default_val = get_site_option('postsaint_settings_image_generator','dalle');

	                			postsaint_select_field('postsaint_settings_image_generator', $array, $default_val);
	            				?>             
            				</td>
          				</tr>
	      				<tr>
	    					<th><label for="postsaint_settings_image_style">Image Style</label></th>
	        				<td>
								<?php

				                $array = array(
				                    '3d' => '3D',
				                    '60s Flat Illustration' => '60s Flat Illustration',
				                    'Abstract' => 'Abstract',
				                    'Cartoon' => 'Cartoon',
				                    'Comic Book' => 'Comic Book',
				                    'Cyberpunk' => 'Cyberpunk',
				                    'Fantasy' => 'Fantasy',
				                    'Futurism' => 'Futurism',
				                    'Noir' => 'Noir',
				                    'Oil Painting' => 'Oil Painting',
				                    'Pencil Sketch' => 'Pencil Sketch',
				                    'Photorealism' => 'Photorealism',
				                    'Stained Glass' => 'Stained Glass',
				                    'Synthwave' => 'Synthwave',
				                    'Watercolor' => 'Watercolor',
				                    'unspecified' => '- Do Not Specify -',
				                );

                				$default_val = get_site_option('postsaint_settings_image_style','unspecified');

                				postsaint_select_field('postsaint_settings_image_style', $array, $default_val);
								?>
	        				</td>
	      				</tr>
	      				<tr>
	    					<th><label for="postsaint_settings_artist_style">Artist Style</label></th>
	        				<td>
								<?php

	                			$array = array(
				                    'Fernando Botero' => 'Fernando Botero',           
				                    'Salvador Dali' => 'Salvador Dali',           
				                    'Edgar Degas' => 'Edgar Degas',           
				                    'Wassily Kandinsky' => 'Wassily Kandinsky',
				                    'Roy Lichtenstein' => 'Roy Lichtenstein',
				                    'Henri Matisse' => 'Henri Matisse',
				                    'Claude Monet' => 'Claude Monet',
				                    'Michelangelo' => 'Michelangelo',                    
				                    'Pablo Picasso' => 'Pablo Picasso',
				                    'Jackson Pollock' => 'Jackson Pollock',
				                    'Rembrandt' => 'Rembrandt',
				                    'Vincent van Gogh' => 'Vincent van Gogh',
				                    'Andy Warhol' => 'Andy Warhol',
				                    'unspecified' => '- Do Not Specify -',
				                );

	                			$default_val = get_site_option('postsaint_settings_artist_style','unspecified');

	                			postsaint_select_field('postsaint_settings_artist_style', $array, $default_val);
								?>
	        				</td>
	      				</tr>
	      				<tr>
	    					<th><label for="postsaint_settings_num_images">Number of Images</label></th>
	        				<td>
								<?php

								$default_val = get_site_option('postsaint_settings_num_images', 1);

								echo '
		            			<input type="range" name="postsaint_settings_num_images_range" min="1" max="10" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_settings_num_images.value=this.value" />
		            			<input type="number" name="postsaint_settings_num_images" id="postsaint_settings_num_images" min="1" max="10" value="'.esc_attr($default_val).'" class="postsaint-input-60" oninput="this.form.postsaint_settings_num_images_range.value=this.value" />';
								?>
	        				</td>
	      				</tr>

          				<tr>
            				<th><label for="postsaint_settings_generated_images_method">Add Images to Media Library</label></th>
            				<td>
	            				<?php

	                			$array = array(
	                    			'add_library' => 'Add generated image(s) to Media Library automatically',
	                    			'preview_only' => 'Preview and select image(s) to add',
	                			);  

	                			$postsaint_settings_generated_images_method = get_site_option('postsaint_settings_generated_images_method','preview_only');
	                
	                			foreach($array as $val => $label){

	                    			$checked = null;

	                    			if( $postsaint_settings_generated_images_method == $val ){
	                        			$checked = 'checked="checked"';
	                    			}

	                    			echo'<input type="radio" id="'.esc_attr($val).'" name="postsaint_settings_generated_images_method" value="'.esc_attr($val).'" '.esc_html($checked).'>';
	                    			echo'<label for="'.esc_attr($val).'">'.esc_attr($label).'</label><br><br>';
	                			}
	                			?>
            				</td>
          				</tr>
          				<tr>
            				<th><label for="postsaint_settings_insert_prompt_media_library_fields">Media Library Fields to Insert Image Prompt</label></th>
            				<td>
	            				<?php

	                			$array = array(
	                    			'none' => '-None-',
	                    			'caption' => 'Caption',
	                    			'description' => 'Description',
	                    			'caption_description' => 'Caption & Description',
	                			);  

				    			$default_val = get_site_option('postsaint_settings_insert_prompt_media_library_fields','caption_description');

								postsaint_select_field('postsaint_settings_insert_prompt_media_library_fields', $array, $default_val);	
	               				?>
            				</td>
          				</tr>          
					</tbody>
	  			</table>	
			</div>

			<div id="api-sources">
	  			<table class="form-table">
	    			<tbody>
		      			<tr>
		    				<th><label for="postsaint_settings_openai_api_key">OpenAI API Key</label></th>
		        			<td>
				    			<?php

				    			if( get_site_option('postsaint_settings_openai_api_key') == null ){

				    				// display (not set) warning
				    				$current_openai_api_key = '<span class="postsaint-errormsg">(not set)</span> API Key is required. <a href="https://platform.openai.com/account/api-keys" target="_blank">Get your API Key here</a>';
				    	
				    			} else {

				    				// truncate key for security
				    				$key = get_site_option('postsaint_settings_openai_api_key');

				    				// first 3 chars
				    				$first_3_chars = substr($key, 0, 6);

				    				// last 3 chars
				    				$last_3_chars = substr($key, -4, 4);

				    				$current_openai_api_key = $first_3_chars.'.......'.$last_3_chars ;
				    			} 
				    			?>

		        				Current Key: <code><span class="postsaint-succmsg"><?php echo wp_kses_post($current_openai_api_key); ?></span></code><br />
								Enter New Key: <br />
								<input class="postsaint-input-320" type="text" name="postsaint_settings_openai_api_key" id="postsaint_settings_openai_api_key" placeholder="sk-..."><br /> 
		        			</td>
		      			</tr>
		      			<tr>
		    				<th><label for="postsaint_settings_stabilityai_api_key">Stability.AI API Key</label></th>
		        			<td>
					    		<?php

					    		if( get_site_option('postsaint_settings_stabilityai_api_key') == null ){

					    			// display (not set) warning
					    			$current_stabilityai_api_key = '<span class="postsaint-errormsg">(not set)</span> API Key is required. <a href="https://beta.dreamstudio.ai/" target="_blank">Get your API Key here</a>';
					    	
					    		} else {

					    			// truncate key for security
					    			$key = esc_attr(get_site_option('postsaint_settings_stabilityai_api_key'));

					    			// first 3 chars
					    			$first_3_chars = substr($key, 0, 6);

					    			// last 3 chars
						    		$last_3_chars = substr($key, -4, 4);

					    			$current_stabilityai_api_key = $first_3_chars.'.......'.$last_3_chars ;
					    		} 
					    		?>

			        			Current Key: <code><span class="postsaint-succmsg"><?php echo wp_kses_post($current_stabilityai_api_key); ?></span></code><br />
								Enter New Key: <br />
								<input class="postsaint-input-320" type="text" name="postsaint_settings_stabilityai_api_key" id="postsaint_settings_stabilityai_api_key" placeholder="sk-..."><br /> 
		        			</td>
		      			</tr>


		      			<tr>
		    				<th><label for="postsaint_settings_dezgo_api_key">Dezgo API Key</label></th>
		        			<td>
					    		<?php

					    		if( get_site_option('postsaint_settings_dezgo_api_key') == null ){

					    			// display (not set) warning
					    			$current_dezgo_api_key = '<span class="postsaint-errormsg">(not set)</span> API Key is required. <a href="https://dezgo.com/account" target="_blank">Get your API Key here</a>';
					    	
					    		} else {

					    			// truncate key for security
					    			$key = esc_attr(get_site_option('postsaint_settings_dezgo_api_key'));

					    			// first 3 chars
					    			$first_3_chars = substr($key, 0, 9);

					    			// last 3 chars
						    		$last_3_chars = substr($key, -4, 4);

					    			$current_dezgo_api_key = $first_3_chars.'.......'.$last_3_chars ;
					    		} 
					    		?>

			        			Current Key: <code><span class="postsaint-succmsg"><?php echo wp_kses_post($current_dezgo_api_key); ?></span></code><br />
								Enter New Key: <br />
								<input class="postsaint-input-320" type="text" name="postsaint_settings_dezgo_api_key" id="postsaint_settings_dezgo_api_key" placeholder="DEZGO-..."><br /> 
		        			</td>
		      			</tr>





		      			<tr>
		    				<th><label for="postsaint_settings_pexels_api_key">Pexels API Key</label></th>
		        			<td>
					    		<?php

					    		if( get_site_option('postsaint_settings_pexels_api_key') == null ){

					    			// display (not set) warning
					    			$current_pexels_api_key = '<span class="postsaint-errormsg">(not set)</span> API Key is required. <a href="https://www.pexels.com/api" target="_blank">Get your API Key here</a>';
					    	
					    		} else {

					    			// truncate key for security
					    			$key = esc_attr(get_site_option('postsaint_settings_pexels_api_key'));

					    			// first 3 chars
					    			$first_3_chars = substr($key, 0, 9);

					    			// last 3 chars
						    		$last_3_chars = substr($key, -4, 4);

					    			$current_pexels_api_key = $first_3_chars.'.......'.$last_3_chars ;
					    		} 
					    		?>

			        			Current Key: <code><span class="postsaint-succmsg"><?php echo wp_kses_post($current_pexels_api_key); ?></span></code><br />
								Enter New Key: <br />
								<input class="postsaint-input-320" type="text" name="postsaint_settings_pexels_api_key" id="postsaint_settings_pexels_api_key" placeholder="ABC123..."><br /> 
		        			</td>
		      			</tr>






					</tbody>
	  			</table>	
			</div>


 			<div id="bulk-auto-import">
	  			<h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-media-spreadsheet"> Bulk Import Posts Default Settings (Pro Version)</h3>
	  			<table class="form-table">
	    			<tbody>
	      				<tr>
	    					<th><label for="postsaint_settings_delimiter">Field Separator (delimiter)</label></th>
        					<td>
								<?php

								$array = array( 
								'pipe' => 'Pipe |',
								'comma' => 'Comma , (.csv file)',
								'semicolon' => 'Semicolon ;',
								);

								$default_val = get_site_option('postsaint_settings_delimiter','pipe');
	        	 
								postsaint_select_field('postsaint_settings_delimiter', $array, $default_val);
								?>
        					</td>
	      				</tr>

         				<tr>
            				<th><label for="postsaint_settings_field_order">Field Order</label></th>
            				<td>
	            				<?php

								$array = array(
									'title' => 'Title',
									'title_prompt' => 'Title | Prompt',
									'title_prompt_image' => 'Title | Prompt | Featured Image Prompt',
									'title_image' => 'Title | Featured Image Prompt',
								);  

	               				$postsaint_settings_field_order = get_site_option('postsaint_settings_field_order','title');
	    	
						        foreach($array as $val => $label){

		            				$checked = null;

									if( $postsaint_settings_field_order == $val ){
										$checked = 'checked="checked"';
									}

									echo'<input type="radio" id="'.esc_attr($val).'" name="postsaint_settings_field_order" value="'.esc_attr($val).'" '.esc_html($checked).'>';
									echo'<label for="'.esc_attr($val).'">'.esc_attr($label).'</label><br><br>';
		        				} 
		        				?>
            				</td>
          				</tr>

	      				<tr>
	    					<th><label for="postsaint_settings_line_separator">Prompt Line Separator</label></th>
	        				<td>
								<?php
								$array = array(
								'newline' => 'Newline (Enter/Return)',
								'three_hyphens' => '--- (Three hyphens)',
								'three_underscores' => '___ (Three underscores)',
								);

								$default_val = get_site_option('postsaint_settings_line_separator','newline');
            	 
								postsaint_select_field('postsaint_settings_line_separator', $array, $default_val);
								?>
	        				</td>
	      				</tr>


	      				<tr>
	    					<th><label for="postsaint_settings_skip_bulk_post_image_error">Skip Post Creation if Image Not Created</label></th>
	        				<td>

		            			<?php

		            			$postsaint_settings_skip_bulk_post_image_error = get_site_option('postsaint_settings_skip_bulk_post_image_error','0');

		            			$checked = null;

		            			if( $postsaint_settings_skip_bulk_post_image_error == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_skip_bulk_post_image_error" id="postsaint_settings_skip_bulk_post_image_error" <?php echo esc_html($checked); ?>>
		            		
	        				</td>
	      				</tr>



	      				<tr>
	    					<th><label for="postsaint_settings_halt_bulk_post_first_error">Halt Bulk Process on First Encountered Error</label></th>
	        				<td>

		            			<?php

		            			$postsaint_settings_halt_bulk_post_first_error = get_site_option('postsaint_settings_halt_bulk_post_first_error','0');

		            			$checked = null;

		            			if( $postsaint_settings_halt_bulk_post_first_error == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_halt_bulk_post_first_error" id="postsaint_settings_halt_bulk_post_first_error" <?php echo esc_html($checked); ?>>
		            		
	        				</td>
	      				</tr>


					</tbody>
	  			</table>	

				<br />
	  			<h3 class="postsaint-section-heading"><span class="dashicons dashicons-clock"></span> Auto Post Default Settings (Pro Version)</h3>
	  			<table class="form-table">
	    			<tbody>
		     			<tr>
		    				<th><label for="postsaint_auto_post_trigger">Function Trigger</label></th>
		        			<td>
								<?php

								$array = array(
									'wp_cron' => 'WP Cron',
									'timestamp' => 'Check Timestamp on Page Load',
									'disable' => 'Disable Auto Posts',					
								);

								$default_val = get_site_option('postsaint_auto_post_trigger','wp_cron');
	            	 
								postsaint_select_field('postsaint_auto_post_trigger', $array, $default_val);
								?>
		        			</td>
		      			</tr>
					</tbody>
	  			</table>	
  			</div>  

  			<div id="misc">

	  			<h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-admin-page"> Logging Settings</h3>
	  			<table class="form-table">
	    			<tbody>
	          			<tr>
	            			<th><label for="postsaint_settings_log_bulk_posts">Log Bulk Import Posts Data</label></th>
	            			<td>
	            				<?php

	            				$postsaint_settings_log_bulk_posts = get_site_option('postsaint_settings_log_bulk_posts','1');

	            				$checked = null;

	            				if( $postsaint_settings_log_bulk_posts == '1' ){

	            					$checked = 'checked';
	            				} 
	            				?>
	            				<input type="checkbox" value="1" name="postsaint_settings_log_bulk_posts" id="postsaint_settings_log_bulk_posts" <?php echo esc_html($checked); ?>>
	            			</td>
	          			</tr>          
	          			<tr>
	            		<th><label for="postsaint_settings_log_single_image">Log Image Generation Data</label></th>
		            		<td>
			            		<?php

			            		$postsaint_settings_log_single_image = get_site_option('postsaint_settings_log_single_image','1');

			            		$checked = null;

			            		if( $postsaint_settings_log_single_image == '1' ){

			            			$checked = 'checked';
			            		} 
			            		?>
			            		<input type="checkbox" value="1" name="postsaint_settings_log_single_image" id="postsaint_settings_log_single_image" <?php echo esc_html($checked); ?>>
		            		</td>
	          			</tr>
	          			<tr>
	            			<th><label for="postsaint_settings_log_single_text">Log Text Generation Data</label></th>
	            			<td>
		            			<?php

		            			$postsaint_settings_log_single_text = get_site_option('postsaint_settings_log_single_text','1');

		            			$checked = null;

		            			if( $postsaint_settings_log_single_text == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_log_single_text" id="postsaint_settings_log_single_text" <?php echo esc_html($checked); ?>>
	            			</td>
	          			</tr>
	          			<tr>
		            		<th><label for="postsaint_settings_delete_log_data_deactivation">Delete Logged Data Upon Plugin Deactivation</label></th>
		            		<td>
		            			<?php

		            			$postsaint_settings_delete_log_data_deactivation = get_site_option('postsaint_settings_delete_log_data_deactivation','0');

		            			$checked = null;

		            			if( $postsaint_settings_delete_log_data_deactivation == '1' ){

		            				$checked = 'checked';
		            			} 
		            			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_delete_log_data_deactivation" id="postsaint_settings_delete_log_data_deactivation" <?php echo esc_html($checked); ?>>
		            		</td>
	          			</tr>

		          		<tr>
		        			<td><td>
		        			<td><td>
		          			</tr>
		          		<tr>
		            		<th><label for="postsaint_settings_delete_default_settings_deactivation">Delete Default Settings Upon Plugin Deactivation</label></th>
		            		<td>
			        			<?php

			        			$postsaint_settings_delete_default_settings_deactivation = get_site_option('postsaint_settings_delete_default_settings_deactivation','1');

			        			$checked = null;

			        			if( $postsaint_settings_delete_default_settings_deactivation == '1' ){

			        				$checked = 'checked';
			        			} 
			        			?>
		            			<input type="checkbox" value="1" name="postsaint_settings_delete_default_settings_deactivation" id="postsaint_settings_delete_default_settings_deactivation" <?php echo esc_html($checked); ?>>
		            		</td>
	          			</tr>             
					</tbody>
	  			</table>	
  			</div>  
		</div>
		<!-- / tabs -->

	  <input type="submit" value="Update Settings" class="button-primary postsaint-input-full-width postsaint-submit-button" name="Submit">
	</form>
</div>	      