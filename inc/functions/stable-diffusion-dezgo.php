<?php

// called by bulk & auto post

if( $image_generator == 'stable_diffusion_dezgo' && !empty($image_prompt) ){

    $url = 'https://api.dezgo.com/text2image';

    $body = array(
       'prompt' => $image_prompt,
       'model' => $stable_diffusion_dezgo_model,
       'width' => (int)$image_width,
       'height' => (int)$image_height,
       'sampler' => $stable_diffusion_dezgo_sampler,
       'guidance' => (int)$stable_diffusion_guidance,
       'steps' => (int)$stable_diffusion_steps,
    );

    // negative_prompt can't be null, add to array if not empty
    if( !empty($negative_prompt) ){
        $body['negative_prompt'] = $negative_prompt;
    }

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

    $image_response = wp_remote_post ($url, $args);

    if( is_wp_error( $image_response ) ) {

        $error_string = $image_response->get_error_message();
        $response.= '<span class="postsaint-errormsg"><b>Dezgoerror:</b> '.$error_string.'</span>';
    }

    $header_content_type = wp_remote_retrieve_header($image_response, 'content-type');

    // check for error messages
    $body = $image_response['body'];
    $response_array = $image_response['response'];

    // success
    if( $header_content_type == 'image/png'){

        $response = '<span class="postsaint-succmsg"><b>Image Generated with Dezgo API using prompt "'.$prompt.'"</b></span><br>';

        $png_code = base64_encode($body); 

        $featured_image_url = 'data:image/png;base64,' . $png_code;        
    }


    // 400 bad request
    if( $response_array['code'] == '400' ){

        $error_string = 'Dezgo Error: 400 Bad Request';

        // get detailed error
        $body_arr = json_decode($body, true);

        // error messages return in different nodes
        $detailed_error_string = null;

        // incorrect img dimensions (height/width)
        if( !empty($body_arr['title']) ){
            $detailed_error_string = $body_arr['title'];
        } 

        // no prompt
        if( !empty($body_arr['detail']) ){
            $detailed_error_string = $body_arr['detail'];
        }

        $response = '<span class="postsaint-errormsg"><b>' . $error_string .'</b> '. $detailed_error_string.'</span><br>';

        $dezgo_image_error = 1;
        $dezgo_api_error_message = $detailed_error_string;
    }

    // 401 unauthorized
    if( $response_array['code'] == '401' ){

        $error_string = '401 Unauthorized. Please check your <a href="'.admin_url("admin.php?page=post-saint/settings.php#api-sources").'">Dezgo API key</a>.';
        $response = '<span class="postsaint-errormsg"><b>Dezgo Error:</b> ' . $error_string . '</span><br>';

        $dezgo_image_error = 1;
        $dezgo_api_error_message = $error_string;        
    }
}
