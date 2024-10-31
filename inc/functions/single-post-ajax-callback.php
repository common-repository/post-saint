<?php 

// ajax import callback
function postsaint_add_single_post_ajax_callback() {

 	global $wpdb;

	extract($_POST);

	$sandbox = null;

	$response = $returned_error = $message = $openai_api_text = $image_generator = $openai_api_error_message = $prompt_tokens = $completion_tokens = $total_tokens = $log_id = null;

	$prompt = stripslashes($prompt);

	$completion_prompt = $prompt;

	// prepare prompt - "returns" modified $completion_prompt var
	require_once POSTSAINT_PLUGIN_PATH . 'inc/functions/prepare_prompt.php';	

	require_once POSTSAINT_PLUGIN_PATH . 'inc/openai/openai.php';	

	// error
	if( isset($openai_api_response['error']['message']) ){

		$openai_api_error_message = $openai_api_response['error']['message'];

		if( !empty($openai_api_error_message) ){
			$message = '<span class="postsaint-errormsg">'.esc_html($openai_api_error_message).'</span>';
		}

	 	$returned_error = 1;
	}

	// log api call
	$datetime = date('Y-m-d H:i:s');

	// logging
	if(get_site_option('postsaint_settings_log_single_text','1') == 1){

		// log
		$wpdb->insert($wpdb->prefix.'postsaint_single_post_text_logs', array(
			'original_prompt' => sanitize_text_field($prompt),
			'prompt' => sanitize_text_field($completion_prompt),
			'prepend_prompt' => sanitize_text_field($prepend_prompt),
			'append_prompt' => sanitize_text_field($append_prompt),
			'writing_style' => sanitize_text_field($writing_style),
			'writing_tone' => sanitize_text_field($writing_tone),
			'keywords' => sanitize_text_field($keywords),
			'openai_model' => sanitize_text_field($openai_model),
			'openai_max_tokens' => sanitize_text_field($openai_max_tokens),
			'openai_temperature' => sanitize_text_field($openai_temperature),
			'openai_top_p' => sanitize_text_field($openai_top_p),
			'openai_best_of' => 1,
			'openai_frequency_penalty' => sanitize_text_field($openai_frequency_penalty),
			'openai_presence_penalty' => sanitize_text_field($openai_presence_penalty),
			'response' => sanitize_text_field($openai_api_json_response),
			'returned_error' => sanitize_text_field($returned_error),
			'returned_error_message' => sanitize_text_field($openai_api_error_message),
			'prompt_tokens' => sanitize_text_field($prompt_tokens),
			'completion_tokens' => sanitize_text_field($completion_tokens),
			'total_tokens' => sanitize_text_field($total_tokens),			
			'created_at' => $datetime,
		));

		$log_id = $wpdb->insert_id;
	}

	$done=1;
	$response = esc_html($openai_api_text);

	// trim linebreaks from response
	$response = trim($response);

	// replace line breaks with <br>
	$response = nl2br($response);	

	echo json_encode(array("done"=>$done,"response"=>$response,'log_id'=>$log_id, 'message' => $message));	

	die();
}
add_action('wp_ajax_add_single_post', 'postsaint_add_single_post_ajax_callback');

?>