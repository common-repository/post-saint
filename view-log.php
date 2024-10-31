<div class="wrap postsaint-wrap">

  <div id="postsaint-heading-container">
    <div id="postsaint-support"><a href="https://postsaint.com/docs" target="_new">Docs</a> | <a href="https://postsaint.com/contact" target="_new">Support</a></div>
	<div id="postsaint-logo"></div>
  </div>
  <br>
  <h1 class="postsaint-heading"> <a href="<?php echo admin_url('admin.php?page='.plugin_basename( __DIR__ ).'/logs.php'); ?>">Logs</a> > View Log</h1>

  <?php

  $id = (int)$_GET['id'];
  global $wpdb;

  $row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'postsaint_bulk_post_logs WHERE id ='.$id);

  if(empty($row)){
  		echo 'No such log.';
  } else {
  
  ?>

  <h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-media-spreadsheet"> Title / Prompt / Image Data</h3>

  <table class="table table-striped">
    <tbody>  

      <tr>
        <th>Original Full Post Data</th>
        <th>Field Order</th>
        <th>Line Separator</th>
      </tr>
      <tr>
        <td><?php echo esc_html($row->original_bulk_post_data); ?></td>
        <td><?php 

			$array = array(
				'title' => 'Title',
				'title_prompt' => 'Title | Prompt',
				'title_prompt_image' => 'Title | Prompt | Featured Image Prompt/URL',
				'title_image' => 'Title | Featured Image Prompt/URL',
			);

			foreach ($array as $key => $value) {
					
				if($key == $row->field_order){
					echo esc_html($value); 
				}
			} 
			?>  
	    </td>
	    <td>
	    	<?php 

			$array = array(
				'newline' => 'Newline (Enter/Return)',
				'three_hyphens' => '--- (Three hyphens)',
				'three_underscores' => '___ (Three underscores)',
			);

			foreach ($array as $key => $value) {
					
				if($key == $row->line_separator){
					echo esc_html($value); 
				}
			}
			?>
	    </td>
      </tr>
	</tbody>
  </table>	

  <h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-welcome-learn-more"> Writing Instructions</h3>

  <table class="table table-striped">
    <tbody> 

      <tr>
        <th>Prepend Prompt</th>
        <th>Append Prompt</th>
        <th>Writing Style</th>
        <th>Writing Tone</th>
        <th>Keywords for Context</th>
      </tr>

      <tr>
        <td>
			<?php 

			if( !empty($row->prepend_prompt) ){
				echo esc_html($row->prepend_prompt);
			} else {
				echo'-';
			}
			?> 
      	</td>
        <td>
        	<?php 

	        if( !empty($row->append_prompt) ){
	        	echo esc_html($row->append_prompt);
	        } else {
	        	echo'-';
	        }
        	?>           	
        </td>
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

			foreach ($array as $key => $value) {
					
				if($key == $row->writing_style){
					echo esc_html($value); 
				}
			} 
			?>		
		</td>
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

			foreach ($array as $key => $value) {
					
				if($key == $row->writing_tone){
					echo esc_html($value); 
				}
			} 
			?>          	
        </td>
        <td>
          	<?php 
        	if( !empty($row->keywords) ){
        		echo esc_html($row->keywords);
        	} else {
        		echo'-';
        	}
        	?>            	
        </td>
	</tbody>
  </table>	

  <h3 class="postsaint-section-heading"><span class="wp-menu-image dashicons-before dashicons-admin-settings"> OpenAI Parameters</h3>
  <table class="table table-striped">
    <tbody> 
        <tr>
          <th>Model</th>
          <th>Max Tokens</th>
          <th>Temperature</th>
          <th>Top P</th>
          <th>Frequency Penalty</th>
          <th>Presence Penalty</th>
          <th>Image Style</th>
          <th>Image Size</th>
        </tr>

        <tr>
          <td><?php echo esc_html($row->openai_model); ?></td>
          <td><?php echo esc_html($row->openai_max_tokens); ?></td>
          <td><?php echo esc_html($row->openai_temperature); ?></td>
          <td><?php echo esc_html($row->openai_top_p); ?></td>
          <td><?php echo esc_html($row->openai_frequency_penalty); ?> </td>
          <td><?php echo esc_html($row->openai_presence_penalty); ?> </td>
          <td><?php echo esc_html($row->image_style); ?> </td>
          <td><?php echo esc_html($row->openai_image_size); ?> </td>
        </tr>
		</tbody>
	</table>	

<?php
}
?>

</div>