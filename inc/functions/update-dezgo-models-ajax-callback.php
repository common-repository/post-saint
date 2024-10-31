<?php 

// ajax import callback
function postsaint_update_dezgo_models_ajax_callback() {

	// do update
	postsaint_load_dezgo_models();

	$done=1;

	// get stored value
	$response = get_site_option('postsaint_dezgo_models');

	echo $response;

	die();

	echo json_encode(array("done"=>$done,"response"=>$response));	

	die();
}
add_action('wp_ajax_update_dezgo_models', 'postsaint_update_dezgo_models_ajax_callback');

?>