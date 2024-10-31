<?php

global $wpdb;

if( isset($_REQUEST['Submit']) ){

	global $wpdb;

	if( function_exists('postsaintpro_register_custom_menu_page') ){
		$table_name = $wpdb->base_prefix.'postsaint_bulk_post_logs';
		$wpdb->query("TRUNCATE TABLE $table_name");

		$table_name = $wpdb->base_prefix.'postsaint_bulk_post_results_logs';
		$wpdb->query("TRUNCATE TABLE $table_name");
	}

	$table_name = $wpdb->base_prefix.'postsaint_dalle_image_logs';
	$wpdb->query("TRUNCATE TABLE $table_name");	

	$table_name = $wpdb->base_prefix.'postsaint_single_post_text_logs';
	$wpdb->query("TRUNCATE TABLE $table_name");	

	$table_name = $wpdb->base_prefix.'postsaint_stabilityai_image_logs';
	$wpdb->query("TRUNCATE TABLE $table_name");	

	echo'<div class="updated"><p><b>Logs cleared!</b></p></div>';
}

?>

<div class="wrap postsaint-wrap">
  <div id="postsaint-heading-container">
  <div id="postsaint-support"><a href="https://postsaint.com/docs" target="_new">Docs</a> | <a href="https://postsaint.com/contact" target="_new">Support</a></div>
	<div id="postsaint-logo"></div>
  </div>
  <br>

  <h1 class="postsaint-heading">Logs</h1>

	<h3 class="postsaint-section-heading">Bulk Import Posts</h3>
	
	<?php

	if( !function_exists('postsaintpro_register_custom_menu_page') ){

		echo'Pro Version Feature';

	} else {

		$rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postsaint_bulk_post_logs LIMIT 1000');

		$count = 0;

		echo '<div class="table-responsive">';
		echo '<table class="table table-striped">';

		echo'<tr>';
		echo'<th style="min-width:370px">Original Full Post Data</th>';
		echo'<th>Date & Time</th>';
		echo'<th>Prepend Prompt</th>';
		echo'<th>Append Prompt</th>';
		echo'<th>Writing Style</th>';
		echo'<th>Writing Tone</th>';
		echo'<th>Keywords</th>';
		echo'<th>Post Status</th>';
		echo'<th>Author</th>';
		echo'<th>Post Category</th>';
		echo'<th>Post Tags</th>';
		echo'</tr>';

		foreach($rows as $row){

			echo'<tr>';

			$bulk_post_lines = null;

			if($row->line_separator == 'newline'){
				$line_separator_char = "\n";
			}

			// use line separator 
			$original_bulk_post_data_array = explode($line_separator_char, $row->original_bulk_post_data);

			foreach ($original_bulk_post_data_array as $line) {

				// truncate line
				// first 3 chars
				// if more than 60 char truncate
				if( strlen($line) > 60 ){

					$first_50_chars = substr($line, 0, 55);

					// 50th char could be a space, so trim
					$first_50_chars = rtrim($first_50_chars);

					// append ellipsis
					$line = $first_50_chars.'...';
				}
		    	
				$bulk_post_lines.= $line.'<br>';
			}

			echo'<td><a href="admin.php?page='.plugin_basename( __DIR__ ).'/view-log.php&id='.(int)$row->id.'">'.wp_kses_post($bulk_post_lines).'</a></td>';

			// date & time
			$date_format = 'Y/m/d';
			$time_format = get_option( 'time_format' );

			$s = strtotime($row->created_at);

			$date = date($date_format, $s);
			$time = date($time_format, $s);

			echo'<td>'.esc_html($date) .' '.esc_html($time).'</td>';
			echo'<td>'.esc_html($row->prepend_prompt).'</td>';
			echo'<td>'.esc_html($row->append_prompt).'</td>';
			echo'<td>'.esc_html($row->writing_style).'</td>';
			echo'<td>'.esc_html($row->writing_tone).'</td>';
			echo'<td>'.esc_html($row->keywords).'</td>';
			echo'<td>'.esc_html($row->post_status).'</td>';

			// Get user object
			$recent_author = get_user_by( 'ID', $row->post_author);
			// Get user display name
			$author_display_name = $recent_author->display_name;

			echo'<td>'.esc_html($author_display_name).'</td>';

			// post categories 
			if( !empty( $row->post_category ) ) {

				$cats_array = json_decode($row->post_category);
				$cats_str = implode(",", $cats_array);

		        $getcats = get_categories(array(
		        	'include' => $cats_str,
		    		'hide_empty' => 0
		        ));

		    	$getcats = wp_list_pluck($getcats,'name');
		    	$getcats = implode(', ',$getcats);	

			} else {

				$getcats = '-';
			}

			echo'<td>'.esc_html($getcats).'</td>';
			echo'<td>'.esc_html($row->post_tags).'</td>';	
			echo'</tr>';
		}
		echo '</table>';
		echo '</div>';

	}

	?>	

	<h3 class="postsaint-section-heading">Text Generation</h3>
	<?php

	$rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postsaint_single_post_text_logs LIMIT 1000');

	echo '<div class="table-responsive">';
	echo '<table class="table table-striped">';

	echo'<tr>';
		echo'<th style="min-width:370px">Prompt</th>';
		echo'<th>Response</th>';
		echo'<th>Post</th>';
		echo'<th>Date & Time</th>';
		echo'<th>Original Prompt</th>';
		echo'<th>Prepend Prompt</th>';
		echo'<th>Append Prompt</th>';
		echo'<th>Writing Style</th>';
		echo'<th>Writing Tone</th>';
		echo'<th>Keywords</th>';
	echo'</tr>';

	foreach($rows as $row){

		$row_class = $returned_error_message = null;
		if( $row->returned_error == 1 ){
			$row_class = 'postsaint-row-error';
			$returned_error_message =  '<br><span class="postsaint-errormsg"><b>Error:</b> '.$row->returned_error_message.'<span>';
		}

		echo'<tr class="'.$row_class.'">';
			echo'<td>'.esc_html($row->prompt).wp_kses_post($returned_error_message).'</td>';

			$post = '<a href="'.get_the_permalink($row->post_id).'">'.get_the_title($row->post_id).'</a>';

			echo'<td>'.esc_html($row->response).'</td>';

			echo'<td>'.$post.'</td>';

			// date & time
			$date_format = 'Y/m/d';
			$time_format = get_option( 'time_format' );

			$s = strtotime($row->created_at);

			$date = date($date_format, $s);
			$time = date($time_format, $s);

			echo'<td>'.esc_html($date) .' '.esc_html($time).'</td>';
			echo'<td>'.esc_html($row->original_prompt).'</td>';
			echo'<td>'.esc_html($row->prepend_prompt).'</td>';
			echo'<td>'.esc_html($row->append_prompt).'</td>';
			echo'<td>'.esc_html($row->writing_style).'</td>';
			echo'<td>'.esc_html($row->writing_tone).'</td>';
			echo'<td>'.esc_html($row->keywords).'</td>';
		echo'</tr>';
	}

	echo '</table>';
	echo '</div>';
	?>

	<h3 class="postsaint-section-heading">DALL-E Images</h3>
	<?php

	$rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postsaint_dalle_image_logs LIMIT 1000');

	echo '<div class="table-responsive">';
	echo '<table class="table table-striped">';

	echo'<tr>';
		echo'<th style="min-width:370px">Prompt</th>';
		echo'<th>Image(s)</th>';
		echo'<th>Post</th>';
		echo'<th>Date & Time</th>';
		echo'<th>Original Prompt</th>';	
		echo'<th>Image Style</th>';
		echo'<th>Artist Style</th>';
		echo'<th>Number of Images</th>';
		echo'<th>Image Size</th>';
	echo'</tr>';

	foreach($rows as $row){


		$row_class = $returned_error_message = null;
		if( $row->image_returned_error == 1 ){
			$row_class = 'postsaint-row-error';
			$returned_error_message =  '<br><span class="postsaint-errormsg"><b>Error:</b> '.$row->image_returned_error_message.'<span>';
		}

		echo'<tr class="'.$row_class.'">';
			echo'<td>'.esc_html($row->image_prompt).wp_kses_post($returned_error_message).'</td>';

			// images
			$attachment_ids_array = json_decode($row->attachment_ids);

			$images = null;

			if( $attachment_ids_array != null ){

					foreach( $attachment_ids_array as $id){

							// return array of image data
							$img_src = wp_get_attachment_image_src($id);

							if( is_array($img_src) ){

								$images.= '<a href="post.php?post='.(int)$id.'&action=edit"><img src="'.$img_src[0].'"></a> ';
							}

					}
			}

			echo'<td>'.$images.'</td>';

			$post = '<a href="'.get_the_permalink($row->post_id).'">'.get_the_title($row->post_id).'</a>';

			echo'<td>'.$post.'</td>';

			// date & time
			$date_format = 'Y/m/d';
			$time_format = get_option( 'time_format' );

			$s = strtotime($row->created_at);

			$date = date($date_format, $s);
			$time = date($time_format, $s);

			echo'<td>'.esc_html($date) .' '.esc_html($time).'</td>';
			echo'<td>'.esc_html($row->original_image_prompt).'</td>';			
			echo'<td>'.esc_html($row->image_style).'</td>';	
			echo'<td>'.esc_html($row->artist_style).'</td>';	
			echo'<td>'.esc_html($row->num_images).'</td>';
			echo'<td>'.esc_html($row->openai_image_size).'x'.esc_html($row->openai_image_size).'</td>';
		echo'</tr>';
	}

	echo '</table>';
	echo '</div>';
	?>


	<h3 class="postsaint-section-heading">Stable Diffusion Stabiity.AI Images</h3>
	<?php

	$rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postsaint_stabilityai_image_logs LIMIT 1000');

	echo '<div class="table-responsive">';
	echo '<table class="table table-striped">';

	echo'<tr>';
		echo'<th style="min-width:370px">Prompt</th>';
		echo'<th>Image(s)</th>';
		echo'<th>Post</th>';
		echo'<th>Date & Time</th>';
		echo'<th>Original Prompt</th>';	
		echo'<th>Image Style</th>';
		echo'<th>Artist Style</th>';
		echo'<th>Number of Images</th>';
		echo'<th>Image Size</th>';
	echo'</tr>';

	foreach($rows as $row){

		$row_class = $returned_error_message = null;
		if( $row->image_returned_error == 1 ){
			$row_class = 'postsaint-row-error';
			$returned_error_message =  '<br><span class="postsaint-errormsg"><b>Error:</b> '.$row->image_returned_error_message.'<span>';
		}

		echo'<tr class="'.$row_class.'">';
		
			echo'<td>'.esc_html($row->image_prompt).wp_kses_post($returned_error_message).'</td>';

			// images
			$attachment_ids_array = json_decode($row->attachment_ids);

			$images = null;

			if( $attachment_ids_array != null ){

				foreach( $attachment_ids_array as $id){

					// return array of image data
					$img_src = wp_get_attachment_image_src($id);

					if( is_array($img_src) ){

						$images.= '<a href="post.php?post='.(int)$id.'&action=edit"><img src="'.$img_src[0].'"></a> ';
					}
				}
			}

			echo'<td>'.$images.'</td>';

			$post = '<a href="'.get_the_permalink($row->post_id).'">'.get_the_title($row->post_id).'</a>';

			echo'<td>'.$post.'</td>';

			// date & time
			$date_format = 'Y/m/d';
			$time_format = get_option( 'time_format' );

			$s = strtotime($row->created_at);

			$date = date($date_format, $s);
			$time = date($time_format, $s);

			echo'<td>'.esc_html($date) .' '.esc_html($time).'</td>';
			echo'<td>'.esc_html($row->original_image_prompt).'</td>';			
			echo'<td>'.esc_html($row->image_style).'</td>';	
			echo'<td>'.esc_html($row->artist_style).'</td>';	
			echo'<td>'.esc_html($row->num_images).'</td>';
			echo'<td>'.esc_html($row->image_width).'x'.esc_html($row->image_height).'</td>';
		echo'</tr>';
	}

	echo '</table>';
	echo '</div>';
	?>

	<h3 class="postsaint-section-heading">Stable Diffusion (Dezgo) Images</h3>
	<?php

	$rows = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postsaint_dezgo_image_logs LIMIT 1000');

	echo '<div class="table-responsive">';
	echo '<table class="table table-striped">';

	echo'<tr>';
		echo'<th style="min-width:370px">Prompt</th>';
		echo'<th>Image(s)</th>';
		echo'<th>Post</th>';		
		echo'<th>Date & Time</th>';
		echo'<th>Original Prompt</th>';
		echo'<th>Model</th>';			
		echo'<th>Image Style</th>';	
		echo'<th>Artist Style</th>';
		echo'<th>Image Size</th>';
	echo'</tr>';

	foreach($rows as $row){

		$row_class = $returned_error_message = null;
		if( $row->image_returned_error == 1 ){
			$row_class = 'postsaint-row-error';
			$returned_error_message =  '<br><span class="postsaint-errormsg"><b>Error:</b> '.$row->image_returned_error_message.'<span>';
		}

		echo'<tr class="'.$row_class.'">';
		
			echo'<td>'.esc_html($row->image_prompt).wp_kses_post($returned_error_message).'</td>';

			// images
			$attachment_ids_array = json_decode($row->attachment_ids);

			$images = null;

			if( $attachment_ids_array != null ){

				foreach( $attachment_ids_array as $id){

					// return array of image data
					$img_src = wp_get_attachment_image_src($id);

					if( is_array($img_src) ){

						$images.= '<a href="post.php?post='.(int)$id.'&action=edit"><img src="'.$img_src[0].'"></a> ';
					}
				}
			}

			echo'<td>'.$images.'</td>';

			$post = '<a href="'.get_the_permalink($row->post_id).'">'.get_the_title($row->post_id).'</a>';

			echo'<td>'.$post.'</td>';

			// date & time
			$date_format = 'Y/m/d';
			$time_format = get_option( 'time_format' );

			$s = strtotime($row->created_at);

			$date = date($date_format, $s);
			$time = date($time_format, $s);

			echo'<td>'.esc_html($date) .' '.esc_html($time).'</td>';
			echo'<td>'.esc_html($row->original_image_prompt).'</td>';			
			echo'<td>'.esc_html($row->model_id).'</td>';				
			echo'<td>'.esc_html($row->image_style).'</td>';	
			echo'<td>'.esc_html($row->artist_style).'</td>';	
			echo'<td>'.esc_html($row->image_width).'x'.esc_html($row->image_height).'</td>';
		echo'</tr>';
	}

	echo '</table>';
	echo '</div>';

	?>

  <form action="<?php echo admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/logs.php'); ?>" method="POST">
    <input type="submit" value="Clear Logs" id="clear-logs" class="button-primary postsaint-submit-button" name="Submit" >	
  </form>
</div>