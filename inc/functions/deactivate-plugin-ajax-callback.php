<?php 
// ajax import callback
function postsaint_deactivate_plugin_ajax_callback() {

 	global $wpdb;

	extract($_POST);

	// send email to support
	if( !empty($deactivate_feedback) ){

		$to_address = 'support@postsaint.com';
		$subject = 'Post Saint Deactivation';
		$message = 'feedback: '.sanitize_text_field($deactivate_feedback);

		wp_mail($to_address,$subject,$message);
	}

	$done=1;

	echo json_encode(array("done"=>$done,));	

	die();
}
add_action('wp_ajax_postsaint_deactivate_plugin', 'postsaint_deactivate_plugin_ajax_callback');
?>