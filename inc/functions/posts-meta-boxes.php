<?php


if( $debug_mode == 1 ){
    
    // load after themes and plugin loaded
    add_action( 'init', 'list_post_types', 20 );

    function list_post_types(){
        var_export( get_post_types() );
    }
}

// custom meta box preview thumbnails
function postsaint_add_custom_box(){
    
    add_meta_box(
        'postsaint_box_id',           // Unique ID
        'Post Saint Post Generator',  // Box title
        'postsaint_custom_box_html',  // Content callback, must be of type callable
        array('post','page','product','advanced_ads')              // Post type
    );
}
add_action('add_meta_boxes', 'postsaint_add_custom_box');

function postsaint_custom_box_html($post){

    $submit_disabled = $image_submit_disabled = null;

    echo'<div class="wrap postsaint-wrap">';

    // make sure text generation should be displayed
    if( get_site_option('postsaint_settings_show_text_generation','1') == '1' ){

        // check openai API key set
        if(  get_site_option('postsaint_settings_openai_api_key') == null  ){
            
            $CSSstyle = 'border-left-color: #d63638;background: #fff;border-left-width: 4px;box-shadow: 0 1px 1px rgb(0 0 0 / 4%);margin: 2px 0px;padding: 1px 12px;';

            echo '
            <div style="'.esc_attr($CSSstyle).'">
                <p>OpenAI API key needs to be set on <a href="'.admin_url("admin.php?page=post-saint/settings.php#api-sources").'">Settings</a> page. <a href="https://platform.openai.com/account/api-keys" target="_blank">Get your API key here</a></p>
            </div>';

            // disable submit button
            $submit_disabled = 'disabled';
        }

        // disable image generation submit if no options available
        if( get_site_option('postsaint_settings_openai_api_key') == null && 
            get_site_option('postsaint_settings_stabilityai_api_key') == null && 
            get_site_option('postsaint_settings_dezgo_api_key') == null &&
            get_site_option('postsaint_settings_pexels_api_key') == null
        ){

            // disable submit button
            $image_submit_disabled = 'disabled';
        }

        echo'
        <table class="form-table">
        <tbody>

          <tr>
            <th>
              <h3 class="postsaint-section-heading"><span class="dashicons dashicons-welcome-write-blog"></span> Prompt</h3>
            </th>
            <td></td>
          </tr>

          <tr>
            <th><label for="postsaint_prompt">Title / Prompt</label></th>
            <td>
                <textarea name="postsaint_prompt" id="postsaint_prompt" class="postsaint-prompt-textarea postsaint-input-full-width"></textarea>
            </td>
          </tr>

          <tr>
            <th>
              <h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-welcome-learn-more"> Writing Instructions</h3>
            </th>
            <td></td>
          </tr>

          <tr>
            <th><label for="postsaint_prepend_prompt">Prepend Prompt</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_prepend_prompt');
            
                echo'       
                <textarea name="postsaint_prepend_prompt" id="postsaint_prepend_prompt" class="postsaint-input-full-width" placeholder=\'Text to be added before prompt. Example: "write an article about"\'>'.esc_html($default_val).'</textarea>
            </td>
          </tr>

          <tr>
            <th><label for="postsaint_append_prompt">Append Prompt</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_append_prompt');
           
                echo'
                <textarea name="postsaint_append_prompt" id="postsaint_append_prompt" class="postsaint-input-full-width" placeholder=\'Text to be added after prompt. Example: "in the writing style of Shakespeare"\'>'.esc_html($default_val).'</textarea>
            </td>
          </tr>

          <tr>
            <th><label for="postsaint_writing_style">Writing Style</label></th>
            <td>';

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

                postsaint_select_field('postsaint_writing_style', $array, $default_val);

            echo'                
            </td>
          </tr>

          <tr>
            <th><label for="postsaint_writing_tone">Writing Tone</label></th>
            <td>';

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

                postsaint_select_field('postsaint_writing_tone', $array, $default_val);

             echo'                    
            </td>
          </tr>

          <tr>
            <th><label for="cron_name">Keywords</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_keywords');
                
                echo'<textarea name="postsaint_keywords" id="postsaint_keywords" class="postsaint-input-full-width" placeholder=\'Example: "history, overcoming obstacles, motivational"\'>'.esc_html($default_val).'</textarea>';
            
            echo'
            </td>
          </tr>

          <tr>
            <th>
              <h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-admin-settings"> OpenAI Parameters</span></h3>
            </th>
            <td></td>
          </tr>

          <tr>
            <th><label for="postsaint_openai_model">Model</label></th>
            <td>';
                $array = array(
                    'gpt-4' => 'gpt-4',
                    'gpt-3.5-turbo' => 'gpt-3.5-turbo',
                    'text-davinci-003' => 'text-davinci-003',
                    'text-curie-001' => 'text-curie-001',
                    'text-babbage-001' => 'text-babbage-001',
                    'text-ada-001' => 'text-ada-001',
                );

                $default_val = get_site_option('postsaint_settings_openai_model','gpt-3.5-turbo');
                
                postsaint_select_field('postsaint_openai_model', $array, $default_val);

            echo'
            </td>
          </tr>

          <tr>
            <th><label for="postsaint_openai_max_tokens">Max Tokens</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_openai_max_tokens','500');

                echo'
                <input type="range" name="postsaint_openai_max_tokens_range" id="postsaint_openai_max_tokens_range" min="0" max="8192" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_openai_max_tokens.value=this.value" />
                <input type="number" name="postsaint_openai_max_tokens" id="postsaint_openai_max_tokens" min="0" max="8192" value="'.esc_attr($default_val).'" class="postsaint-input-80" oninput="this.form.postsaint_openai_max_tokens_range.value=this.value" />
            </td>
          </tr>  


          <tr>
            <th><label for="postsaint_openai_temperature_range">Temperature</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_openai_temperature','0.7');

                echo'
                <input type="range" name="postsaint_openai_temperature_range" id="postsaint_openai_temperature_range" min="0" max="1" step="0.1" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_openai_temperature.value=this.value" />
                <input type="number" name="postsaint_openai_temperature" id="postsaint_openai_temperature" min="0" max="1" step="0.1" value="'.esc_attr($default_val).'" class="postsaint-input-60" oninput="this.form.postsaint_openai_temperature_range.value=this.value" />
            </td>
          </tr>          


          <tr>
            <th><label for="postsaint_openai_top_p">Top P</label></th>
            <td>';

                $default_val = get_site_option('postsaint_settings_openai_top_p','1');

                echo'
                <input type="range" name="postsaint_openai_top_p_range" min="0" max="1" step="0.1" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_openai_top_p.value=this.value" />
                <input type="number" name="postsaint_openai_top_p" id="postsaint_openai_top_p" min="0" max="1" step="0.1" value="'.esc_attr($default_val).'" class="postsaint-input-60" oninput="this.form.postsaint_openai_top_p_range.value=this.value" />
            </td>
          </tr>   

          <tr>
            <th><label for="postsaint_openai_frequency_penalty">Frequency Penalty</label></th>
            <td>';
            
                $default_val = get_site_option('postsaint_settings_openai_frequency_penalty','0');      

                echo'        
                <input type="range" name="postsaint_openai_frequency_penalty_range" id="postsaint_openai_frequency_penalty_range" min="0" max="2" step="0.01" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_openai_frequency_penalty.value=this.value" />
                <input type="number" name="postsaint_openai_frequency_penalty" id="postsaint_openai_frequency_penalty" min="0" max="2" step="0.1" value="'.esc_attr($default_val).'" class="postsaint-input-80" oninput="this.form.postsaint_openai_frequency_penalty_range.value=this.value" />
            </td>
          </tr>   


          <tr>
            <th><label for="postsaint_openai_presence_penalty">Presence Penalty</label></th>
            <td>';
            
                $default_val = get_site_option('postsaint_settings_openai_presence_penalty','0');      

                echo'        
                <input type="range" name="postsaint_openai_presence_penalty_range" id="postsaint_openai_presence_penalty_range" min="0" max="2" step="0.01" value="'.esc_attr($default_val).'" oninput="this.form.postsaint_openai_presence_penalty.value=this.value" />
                <input type="number" name="postsaint_openai_presence_penalty" id="postsaint_openai_presence_penalty" min="0" max="2" step="0.1" value="'.esc_attr($default_val).'" class="postsaint-input-80" oninput="this.form.postsaint_openai_presence_penalty_range.value=this.value" />
            </td>
          </tr>   

        </tbody>
        </table>

        <input type="button" value="Generate Text" class="button-primary postsaint-input-full-width postsaint-submit-button" id="postsaint-generate-content" name="Submit" '.esc_html($submit_disabled).'> <br><br>

        <div id="postsaint-generated-content-message"></div>

        <textarea rows="8" name="postsaint_result" id="postsaint_result" class="postsaint-input-full-width" style="display:none"></textarea>
        <button class="button-primary" id="postsaint-copy-to-content" style="display:none"><span class="dashicons dashicons-admin-page"></span> Copy To Post Content</button>
        <button class="button-primary" id="postsaint-copy-to-clipboard" style="display:none"><span class="dashicons dashicons-clipboard"></span> Copy To Clipboard</button>';
    }


    // make sure image generation should be displayed
    if( get_site_option('postsaint_settings_show_image_generation','1') == '1' ){

        echo'
        <table class="form-table">
        <tbody>    

          <tr>
            <th>
              <h3 class="postsaint-section-heading"><span class="dashicons dashicons-format-gallery"></span> Generate Images</h3>
            </th>
            <td></td>
          </tr>';

            if( get_site_option('postsaint_settings_openai_api_key') == null && 
                get_site_option('postsaint_settings_stabilityai_api_key') == null && 
                get_site_option('postsaint_settings_dezgo_api_key') == null &&
                get_site_option('postsaint_settings_pexels_api_key') == null
            ){

                $CSSstyle = 'border-left-color: #d63638;background: #fff;border-left-width: 4px;box-shadow: 0 1px 1px rgb(0 0 0 / 4%);margin: 2px 0px;padding: 1px 12px;';

                echo '
                <div style="'.esc_attr($CSSstyle).'">
                    <p>To generate images, an OpenAI, Stability.ai, Dezgo or Pexels API key must be set on the <a href="'.admin_url('admin.php?page=post-saint/settings.php#api-sources').'">Settings > API Sources</a> page.</p>
                </div>';
            }

          echo'
          <tr>
            <th><label for="postsaint_image_prompt">Image Description / Prompt to Generate</label></th>
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
                    $array['stable_diffusion'] = 'Stable Diffusion - Stability.ai';
                }

                if( !empty( get_site_option('postsaint_settings_dezgo_api_key') ) ){
                    $array['stable_diffusion_dezgo'] = 'Stable Diffusion - Dezgo';
                }

                if( !empty( get_site_option('postsaint_settings_pexels_api_key') ) ){
                    $array['pexels'] = 'Pexels';
                } 

                // if only 1 option don't show as select
                if( count($array) == 0){

                    echo '<a href="'.admin_url('admin.php?page=post-saint/settings.php#api-sources').'">add an image generator API key</a>';

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
                <input type="range" name="postsaint_stable_diffusion_cfg_scale_range" min="0" max="35" value="'.get_site_option('postsaint_stable_diffusion_cfg_scale','7').'" oninput="this.form.postsaint_stable_diffusion_cfg_scale.value=this.value" />
                <input type="number" name="postsaint_stable_diffusion_cfg_scale" id="postsaint_stable_diffusion_cfg_scale" min="0" max="35" value="'.get_site_option('postsaint_stable_diffusion_cfg_scale','7').'" class="postsaint-input-60" oninput="this.form.postsaint_stable_diffusion_cfg_scale_range.value=this.value" />
            </td>
          </tr>

          <tr id="postsaint-tr-stable_diffusion_steps" style="'.esc_attr($display_stable_diffusion_steps).'">
            <th><label for="postsaint_stable_diffusion_steps">Steps</label></th>
            <td>
                <input type="range" name="postsaint_stable_diffusion_steps_range" min="10" max="150" value="'.get_site_option('postsaint_stable_diffusion_steps','50').'" oninput="this.form.postsaint_stable_diffusion_steps.value=this.value" />
                <input type="number" name="postsaint_stable_diffusion_steps" id="postsaint_stable_diffusion_steps" min="10" max="150" value="'.get_site_option('postsaint_stable_diffusion_steps','50').'" class="postsaint-input-60" oninput="this.form.postsaint_stable_diffusion_steps_range.value=this.value" />
            </td>
          </tr>
              
          <tr id="postsaint-tr-stable_diffusion_guidance" style="'.esc_attr($display_stable_diffusion_guidance).'">
            <th><label for="postsaint_stable_diffusion_guidance">Guidance</label></th>
            <td>
                <input type="range" name="postsaint_stable_diffusion_guidance_range" min="-20" max="20" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_guidance','7')).'" oninput="this.form.postsaint_stable_diffusion_guidance.value=this.value" />
                <input type="number" name="postsaint_stable_diffusion_guidance" id="postsaint_stable_diffusion_guidance" min="-20" max="20" value="'.esc_attr(get_site_option('postsaint_stable_diffusion_guidance','7')).'" class="postsaint-input-60" oninput="this.form.postsaint_stable_diffusion_guidance_range.value=this.value" />
            </td>
          </tr>

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
          </tr>

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
          </tr>

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
          </tr>


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
                    echo'<label for="'.esc_attr($val).'">'.esc_html($label).'</label><br><br>';
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

                $default_val = get_site_option('postsaint_settings_insert_prompt_media_library_fields','caption_description');

                postsaint_select_field('postsaint_insert_prompt_media_library_fields', $array, $default_val);

            echo'
            </td>
          </tr>          

        </tbody>
        </table>

        <input type="button" value="Generate Image(s)" class="button-primary postsaint-input-full-width postsaint-submit-button" id="postsaint-generate-images" name="Submit" '.esc_html($image_submit_disabled).'><br><br>';
    }

    echo'
    <input type="hidden" id="postsaint_generator_standalone" value="0">

    <input type="hidden" id="postsaint_text_log_ids" name="postsaint_text_log_ids">
    <input type="hidden" id="postsaint_dalle_image_log_ids" name="postsaint_dalle_image_log_ids">
    <input type="hidden" id="postsaint_sd_image_log_ids" name="postsaint_sd_image_log_ids">

    <input type="hidden" id="plugin_dir_url" value="'.POSTSAINT_PLUGIN_URL.'">

    <div id="postsaint-generated-images-message"></div>
    <div id="postsaint-generated-images-container"></div>

</div>';
}

// save post
function postsaint_save_postdata($post_id){

    // get hidden field values of ids from text and image log entries and assign post id

    if( !empty($_POST['postsaint_text_log_ids']) ){

        $postsaint_text_log_ids = sanitize_text_field($_POST['postsaint_text_log_ids']);
        
        $ids_array = explode(',', $postsaint_text_log_ids);

        global $wpdb;

        foreach($ids_array as $id){

            // update post id in logs?
            $wpdb->update( $wpdb->prefix.'postsaint_single_post_text_logs', array( 'post_id' => $post_id),array('id'=>$id));    
        }
    } 


    if( !empty($_POST['postsaint_dalle_image_log_ids']) ){

        $postsaint_dalle_image_log_ids = sanitize_text_field($_POST['postsaint_dalle_image_log_ids']);
        
        $ids_array = explode(',', $postsaint_dalle_image_log_ids);

        global $wpdb;

        foreach($ids_array as $id){

            // update post id in logs?
            $wpdb->update( $wpdb->prefix.'postsaint_dalle_image_logs', array( 'post_id' => $post_id),array('id'=>$id));    
        }
    }


    if( !empty($_POST['postsaint_sd_image_log_ids']) ){

        $postsaint_sd_image_log_ids = sanitize_text_field($_POST['postsaint_sd_image_log_ids']);
        
        $ids_array = explode(',', $postsaint_sd_image_log_ids);

        global $wpdb;

        foreach($ids_array as $id){

            // update post id in logs?
            $wpdb->update( $wpdb->prefix.'postsaint_stabilityai_image_logs', array( 'post_id' => $post_id),array('id'=>$id));    
        }
    }

}
add_action('save_post', 'postsaint_save_postdata');