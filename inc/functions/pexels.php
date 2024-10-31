<?php

// called by bulk & auto post
if( $image_generator == 'pexels' && !empty($image_prompt) ){

    $url = 'https://api.pexels.com/v1/search?query='.$image_prompt.'&orientation='.$pexels_orientation.'&size='.$pexels_size.'&color='.$pexels_color.'&per_page='.$num_results.'&page='.$page_num;

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


    if( !empty( $return['status'] ) ){

        // 401 unauthorized
        if( $return['status'] == '401'){

            $error_string = '401 Unauthorized. Please check your <a href="'.admin_url("admin.php?page=post-saint/settings.php#api-sources").'">Pexels API key</a>.';
            $response = '<span class="postsaint-errormsg"><b>Pexels Error:</b> ' . $error_string . '</span><br>';

            $dezgo_image_error = 1;
            $dezgo_api_error_message = $error_string;   
        }
    }

    // loop once (for now)
    foreach( $return['photos'] as $key => $res){

        $featured_image_url = $res['src']['original'];

        // loop once
        continue;
    }

}