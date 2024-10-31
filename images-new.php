<div class="wrap postsaint-wrap">
    <!-- empty form tag needed for range values-->
  <form>
	<div id="postsaint-heading-container">
		<div id="postsaint-support"><a href="https://postsaint.com/docs" target="_new">Docs</a> | <a href="https://postsaint.com/contact" target="_new">Support</a></div>
		<div id="postsaint-logo"></div>
	</div>
	<br>
	<h1 class="postsaint-heading">Generate Images</h1>

		<?php

        $submit_disabled = null;

        // check at least one image generation option available
        if(  get_site_option('postsaint_settings_openai_api_key') == null && 
            get_site_option('postsaint_settings_stabilityai_api_key') == null && 
            get_site_option('postsaint_settings_dezgo_api_key') == null &&
            get_site_option('postsaint_settings_pexels_api_key') == null  
        ){
            
            echo '<div class="error"><p>OpenAI, Stability.ai or Dezgo API key must to be set on <a href="'.admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php#api-sources').'">Settings</a> page. Get your <a href="https://platform.openai.com/account/api-keys" target="_blank">OpenAI API key here</a>, <a href="https://beta.dreamstudio.ai/" target="_blank"> Stability.ai API key here</a> and your <a href="https://dezgo.com/account/" target="_blank"> Dezgo API key here</a>.</p></div>';

            // disable submit button
            $submit_disabled = 'disabled';
        }
        	
		echo'
        <table class="form-table">
        <tbody>    

          <tr>
            <th><label for="cron_name">Image Description / Prompt to Generate</label></th>
            <td>
                <textarea name="postsaint_image_prompt" id="postsaint_image_prompt" class="postsaint-prompt-textarea postsaint-input-full-width"></textarea>
            </td>
          </tr>


          <tr>
            <th><label for="postsaint_image_generator">Image Generator</label></th>
            <td>';

                $array = array();

                if( !empty( get_site_option('postsaint_settings_openai_api_key') ) ){
                    $array['dalle'] = 'DALL-E';
                }

                if( !empty( get_site_option('postsaint_settings_stabilityai_api_key') ) ){
                    $array['stable_diffusion'] = 'Stable Diffusion: Stability.ai';
                }

                if( !empty( get_site_option('postsaint_settings_dezgo_api_key') ) ){
                    $array['stable_diffusion_dezgo'] = 'Stable Diffusion: Dezgo';
                }

                if( !empty( get_site_option('postsaint_settings_pexels_api_key') ) ){
                    $array['pexels'] = 'Pexels';
                }                

                // if only 1 option don't show as select
                if( count($array) == 0){

                    echo '<a href="'.admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/settings.php#api-sources').'">add an image generator API key</a>';

                } elseif( count($array) == 1){                

                    echo array_values($array)[0];

                    echo '<input type="hidden" id="postsaint_image_generator" value="'.array_keys($array)[0].'">';

                } else {

                    $default_val = get_site_option('postsaint_settings_image_generator','dalle');
                   
                    postsaint_select_field('postsaint_image_generator', $array, $default_val);
                }

            echo'                
            </td>
          </tr>';

          // show / hide fields based on engine
          $image_generator = get_site_option('postsaint_settings_image_generator','dalle');

          if( $image_generator == 'dalle' ){

            // display
            $display_num_images = 
            $display_image_style = 
            $display_artist_style = 
            $display_image_size = '';

            // hide
            $display_num_results = 
            $display_pexels_orientation = 
            $display_pexels_size = 
            $display_pexels_color =             
            $display_stable_diffusion_dezgo_sampler = 
            $display_stable_diffusion_sampler = 
            $display_stable_diffusion_guidance = 
            $display_stable_diffusion_dezgo_model = 
            $display_negative_prompt = 
            $display_stable_diffusion_engine = 
            $display_cfg_scale = 
            $display_stable_diffusion_steps = 
            $display_image_width = 
            $display_image_height = 'display:none';                     
          } 


          if( $image_generator == 'stable_diffusion' ){

            // hide
            $display_num_results =    
            $display_pexels_orientation = 
            $display_pexels_size = 
            $display_pexels_color =                        
            $display_image_size = 
            $display_stable_diffusion_dezgo_sampler = 
            $display_stable_diffusion_guidance = 
            $display_stable_diffusion_dezgo_model = 
            $display_negative_prompt = 'display:none';
            
            // display
            $display_num_images = 
            $display_image_style = 
            $display_artist_style =             
            $display_stable_diffusion_sampler = 
            $display_stable_diffusion_engine = 
            $display_cfg_scale = 
            $display_stable_diffusion_steps = 
            $display_image_width = 
            $display_image_height = '';     
          } 

          if( $image_generator == 'stable_diffusion_dezgo' ){

            // hide
            $display_num_results = 
            $display_pexels_orientation = 
            $display_pexels_size = 
            $display_pexels_color =                
            $display_image_size = 
            $display_stable_diffusion_engine = 
            $display_stable_diffusion_sampler = 
            $display_cfg_scale = 'display:none';

            //show
            $display_num_images = 
            $display_image_style = 
            $display_artist_style =             
            $display_stable_diffusion_dezgo_sampler = 
            $display_negative_prompt = 
            $display_stable_diffusion_dezgo_model = 
            $display_stable_diffusion_guidance = 
            $display_stable_diffusion_steps = 
            $display_image_width = 
            $display_image_height = '';     
          } 

          if( $image_generator == 'pexels' ){

            // show
            $display_num_results = 
            $display_pexels_orientation = 
            $display_pexels_size = 
            $display_pexels_color =  '';

            // hide
            $display_num_images = 
            $display_image_style = 
            $display_artist_style =             
            $display_image_size = 
            $display_stable_diffusion_dezgo_sampler = 
            $display_stable_diffusion_sampler = 
            $display_stable_diffusion_guidance = 
            $display_stable_diffusion_dezgo_model = 
            $display_negative_prompt = 
            $display_stable_diffusion_engine = 
            $display_cfg_scale = 
            $display_stable_diffusion_steps = 
            $display_image_width = 
            $display_image_height = 'display:none';                     
          } 


          echo'
          <tr id="postsaint-tr-stable_diffusion_engine" style="'.esc_attr($display_stable_diffusion_engine).'">
            <th><label for="postsaint_stable_diffusion_engine">Engine</label></th>
            <td>';

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
                
                postsaint_select_field('postsaint_stable_diffusion_engine', $array, $default_val);
                echo'
            </td>
          </tr>

          <tr id="postsaint-tr-stable_diffusion_dezgo_model" style="'.esc_attr($display_stable_diffusion_dezgo_model).'">
            <th><label for="postsaint_stable_diffusion_dezgo_model">Dezgo Model</label></th>
            <td>';

                $postsaint_dezgo_models = get_site_option('postsaint_dezgo_models');

                $array = json_decode($postsaint_dezgo_models);

                $default_val = get_site_option('postsaint_settings_stable_diffusion_dezgo_model','stablediffusion_2_1_512px');
                
                postsaint_select_field('postsaint_stable_diffusion_dezgo_model', $array, $default_val);
                echo'
            </td>
          </tr>

          <tr id="postsaint-tr-negative_prompt" style="'.esc_attr($display_negative_prompt).'">
            <th><label for="postsaint_negative_prompt">Negative Prompt</label></th>
            <td>';

                $postsaint_settings_negative_prompt = get_site_option('postsaint_settings_negative_prompt','ugly, tiling, poorly drawn hands, poorly drawn feet, poorly drawn face, out of frame, extra limbs, disfigured, deformed, body out of frame, blurry, bad anatomy, blurred, watermark, grainy, signature, cut off, draft');
                echo'<textarea name="postsaint_negative_prompt" id="postsaint_negative_prompt" class="postsaint-input-full-width">'.esc_html($postsaint_settings_negative_prompt).'</textarea>
            </td>
          </tr>

          <tr id="postsaint-tr-image_style" style="'.esc_attr($display_image_style).'">
            <th><label for="postsaint_image_style">Image Style</label></th>
            <td>';

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

                postsaint_select_field('postsaint_image_style', $array, $default_val);
                echo'                
            </td>
          </tr>

          <tr id="postsaint-tr-artist_style" style="'.esc_attr($display_artist_style).'">
            <th><label for="postsaint_artist_style">Artist Style</label></th>
            <td>';

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

                $default_val = get_site_option('postsaint_artist_style','unspecified');

                postsaint_select_field('postsaint_artist_style', $array, $default_val);

            echo'                
            </td>
          </tr>

          <tr id="postsaint-tr-num_images" style="'.esc_attr($display_num_images).'">
            <th><label for="postsaint_num_images">Number of Image(s) to Generate</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_num_images', 1);

                echo'
                <input type="range" name="postsaint_num_images_range" min="1" max="10" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_num_images.value=this.value" />
                <input type="number" name="postsaint_num_images" id="postsaint_num_images" min="1" max="10" value="'.esc_attr($default_val).'" class="postsaint-input-60" oninput="this.form.postsaint_num_images_range.value=this.value" />
            </td>
          </tr>';


          echo' 
          <tr id="postsaint-tr-num_results" style="'.esc_attr($display_num_results).'">
            <th><label for="postsaint_num_results">Number of Results</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_num_results', 15);

                echo'
                <input type="range" name="postsaint_num_results_range" min="1" max="50" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_num_results.value=this.value" />
                <input type="number" name="postsaint_num_results" id="postsaint_num_results" min="1" max="50" value="'.esc_attr($default_val).'" class="postsaint-input-60" oninput="this.form.postsaint_num_results_range.value=this.value" />
            </td>
          </tr>';

          echo'
          <tr id="postsaint-tr-stable_diffusion_cfg_scale" style="'.esc_attr($display_cfg_scale).'">
            <th><label for="postsaint_stable_diffusion_cfg_scale">CGF Scale</label></th>
            <td>
                <input type="range" name="postsaint_stable_diffusion_cfg_scale_range" min="0" max="35" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_cfg_scale','7')).'" oninput="this.form.postsaint_stable_diffusion_cfg_scale.value=this.value" />
                <input type="number" name="postsaint_stable_diffusion_cfg_scale" id="postsaint_stable_diffusion_cfg_scale" min="0" max="35" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_cfg_scale','7')).'" class="postsaint-input-60" oninput="this.form.postsaint_stable_diffusion_cfg_scale_range.value=this.value" />
            </td>
          </tr>';

          echo'
          <tr id="postsaint-tr-stable_diffusion_steps" style="'.esc_attr($display_stable_diffusion_steps).'">
            <th><label for="postsaint_stable_diffusion_steps">Steps</label></th>
            <td>
                <input type="range" name="postsaint_stable_diffusion_steps_range" min="10" max="150" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_steps','50')).'" oninput="this.form.postsaint_stable_diffusion_steps.value=this.value" />
                <input type="number" name="postsaint_stable_diffusion_steps" id="postsaint_stable_diffusion_steps" min="10" max="150" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_steps','50')).'" class="postsaint-input-60" oninput="this.form.postsaint_stable_diffusion_steps_range.value=this.value" />
            </td>
          </tr>';

          echo'
          <tr id="postsaint-tr-stable_diffusion_guidance" style="'.esc_attr($display_stable_diffusion_guidance).'">
            <th><label for="postsaint_stable_diffusion_guidance">Guidance</label></th>
            <td>
                <input type="range" name="postsaint_stable_diffusion_guidance_range" min="-20" max="20" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_guidance','7')).'" oninput="this.form.postsaint_stable_diffusion_guidance.value=this.value" />
                <input type="number" name="postsaint_stable_diffusion_guidance" id="postsaint_stable_diffusion_guidance" min="-20" max="20" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_guidance','7')).'" class="postsaint-input-60" oninput="this.form.postsaint_stable_diffusion_guidance_range.value=this.value" />
            </td>
          </tr>';

          echo'
          <tr id="postsaint-tr-openai_image_size" style="'.esc_attr($display_image_size).'">
            <th><label for="postsaint_openai_image_size">Image Size</label></th>
            <td>';
                         
                $array = array(
                    '256x256' => '256x256',
                    '512x512' => '512x512',
                    '1024x1024' => '1024x1024',
                );

                $default_val = get_site_option('postsaint_settings_openai_image_size','512x512');

                postsaint_select_field('postsaint_openai_image_size', $array, $default_val);
                echo'
            </td>
         </tr>';

          echo'
          <tr id="postsaint-tr-stable_diffusion_sampler" style="'.esc_attr($display_stable_diffusion_sampler).'">
            <th><label for="postsaint_stable_diffusion_sampler">Sampler</label></th>
            <td>';

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
                
                postsaint_select_field('postsaint_stable_diffusion_sampler', $array, $default_val);
                echo'
            </td>
          </tr>';

          echo'
          <tr id="postsaint-tr-stable_diffusion_dezgo_sampler" style="'.esc_attr($display_stable_diffusion_dezgo_sampler).'">
            <th><label for="postsaint_stable_diffusion_dezgo_sampler">Sampler</label></th>
            <td>';

                $array = array(
                    'ddim' => 'DDIM',
                    'dpm' => 'DPM',
                    'euler' => 'Euler',
                    'euler_a' => 'Euler Ancestral',
                    'k_lms' => 'K-LMS',
                    'pndm' => 'PNDM',
                );

                $default_val = get_site_option('postsaint_settings_stable_diffusion_dezgo_sampler','dpm');
                
                postsaint_select_field('postsaint_stable_diffusion_dezgo_sampler', $array, $default_val);
                echo'
            </td>
          </tr>';

          echo'
          <tr id="postsaint-tr-image_width" style="'.esc_attr($display_image_width).'">
            <th><label for="postsaint_image_width">Image Width</label></th>
            <td>';
                         
                $default_val = get_site_option('postsaint_settings_image_width','512');

                echo '<input type="number" class="postsaint-input-80" id="postsaint_image_width" name="postsaint_image_width" value="'.esc_attr($default_val).'">';
                echo'
            </td>
          </tr>

          <tr id="postsaint-tr-image_height" style="'.esc_attr($display_image_height).'">
            <th><label for="postsaint_image_height">Image Height</label></th>
            <td>';
                         
                $default_val = get_site_option('postsaint_settings_image_height','512');

                echo '<input type="number" class="postsaint-input-80" id="postsaint_image_height" name="postsaint_image_height" value="'.esc_attr($default_val).'">';
                echo'
            </td>
          </tr>';



          echo'
          <tr id="postsaint-tr-pexels_orientation" style="'.esc_attr($display_pexels_orientation).'">
            <th><label for="postsaint_pexels_orientation">Orientation</label></th>
            <td>';

                $array = array(
                    'landscape' => 'Landscape',
                    'portrait' => 'Portrait',
                    'square' => 'Square',
                    'unspecified' => '- Do Not Specify -',                          
                );

                $default_val = get_site_option('postsaint_settings_pexels_orientation','landscape');
                
                postsaint_select_field('postsaint_pexels_orientation', $array, $default_val);
                echo'
            </td>
          </tr>';


          echo'
          <tr id="postsaint-tr-pexels_size" style="'.esc_attr($display_pexels_size).'">
            <th><label for="postsaint_pexels_size">Minimum Size</label></th>
            <td>';

                $array = array(
                    'large' => 'Large (24MP)',
                    'medium' => 'Medium (12MP)',
                    'small' => 'Small (4MP)',
                );

                $default_val = get_site_option('postsaint_settings_pexels_size','small');
                
                postsaint_select_field('postsaint_pexels_size', $array, $default_val);
                echo'
            </td>
          </tr>';


          echo'
          <tr id="postsaint-tr-pexels_color" style="'.esc_attr($display_pexels_color).'">
            <th><label for="postsaint_pexels_color">Color</label></th>
            <td>';

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
                
                postsaint_select_field('postsaint_pexels_color', $array, $default_val);
                echo'
            </td>
          </tr>';

            echo'
          <tr>
            <th><label for="postsaint_generated_images_method">Add Images to Media Library</label></th>
            <td>';

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

                    echo'<input type="radio" id="'.esc_attr($val).'" name="postsaint_generated_images_method" value="'.esc_attr($val).'" '.esc_html($checked).'>';
                    echo'<label for="'.esc_attr($val).'">'.esc_attr($label).'</label><br><br>';
                }

            echo'
            </td>
          </tr>

          <tr>
            <th><label for="postsaint_insert_prompt_media_library_fields">Media Library Fields to Insert Image Prompt</label></th>
            <td>';
                         
                $array = array(
                    'none' => '-None-',
                    'caption' => 'Caption',
                    'description' => 'Description',
                    'caption_description' => 'Caption & Description',
                );  

                $default_val = get_site_option('postsaint_insert_prompt_media_library_fields','caption_description');

                postsaint_select_field('postsaint_insert_prompt_media_library_fields', $array, $default_val);

            echo'
            </td>
          </tr>

        </tbody>
        </table>

        <input type="hidden" id="postsaint_generator_standalone" value="1">

        <input type="hidden" id="postsaint_text_log_ids" name="postsaint_text_log_ids">

        <!-- todo unneded? 
        <input type="hidden" id="postsaint_image_log_ids" name="postsaint_image_log_ids">
        -->

        <input type="hidden" id="plugin_dir_url" value="'.POSTSAINT_PLUGIN_URL.'">

        <input type="button" value="Generate Image(s)" class="button-primary postsaint-input-full-width postsaint-submit-button" id="postsaint-generate-images" name="Submit" '.esc_html($submit_disabled).'><br><br>

        <div id="postsaint-generated-images-message"></div>
        <div id="postsaint-generated-images-container"></div>';
	?>
  </form>
</div>