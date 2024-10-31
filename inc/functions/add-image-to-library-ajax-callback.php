<?php 

// ajax import callback
function postsaint_add_image_to_library_ajax_callback() {

	extract($_POST);

	// add to media library
	$random6chars =  substr(md5(uniqid(mt_rand(), true)), 0, 6);

	// append random string for unique filenames
	$filename_base = postsaint_sanitize_file_name($image_prompt).'-'.$random6chars;

	$attach_id = postsaint_add_image_media_library($image_url, $filename_base, $image_prompt, $insert_prompt_media_library_fields);

	// may have failed
	if( $attach_id > 0 ){

		// get url by attachment id
		$library_url = wp_get_attachment_url($attach_id);

		// update logs - get existing attachment_ids
		// skip if using pexels, no need to log
		if( $image_generator != 'pexels' ){

			// update attachment id in postsaint_dalle_image_logs for log of API call
			if( $image_generator == 'dalle' ){
				$table = 'postsaint_dalle_image_logs';
			}

			if( $image_generator == 'stable_diffusion' ){
				$table = 'postsaint_stabilityai_image_logs';
			}	

			if( $image_generator == 'stable_diffusion_dezgo' ){
				$table = 'postsaint_dezgo_image_logs';
			}	

			global $wpdb;

			$thelog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$table." WHERE ID = %s", $log_id ));

			// if values already there, add to array
			if( !empty($thelog->attachment_ids) ){
				$attachment_ids_array = json_decode($thelog->attachment_ids);

				// push value to attachment_ids_array
				array_push($attachment_ids_array, $attach_id);

			} else { // no existing value, add single value to new array

				$attachment_ids_array = array($attach_id);
			}

			$attachment_ids_array = json_encode($attachment_ids_array);

			// update
		    $wpdb->update( $wpdb->prefix.$table, array( 
		    	'attachment_ids' => sanitize_text_field($attachment_ids_array),
		    ),array('id'=>$log_id)); 
		}
	} else {
		$library_url = null;
	}

	$done=1;

	echo json_encode(array("done"=>$done,"library_url"=>$library_url,"attachment_id"=>$attach_id,));	

	die();
}
add_action('wp_ajax_add_image_to_library', 'postsaint_add_image_to_library_ajax_callback');

?>