<?php
/*
Plugin Name: Post Saint: ChatGPT, GPT4, DALL-E, Stable Diffusion, Pexels, Dezgo AI Text & Image Generator
Plugin URI: https://postsaint.com/blog/
Description: Create single or bulk Posts and Pages with GPT AI writer using writing prompts. 60+ AI image generation models, powered by DALL-E & Stable Diffusion allows images to be added to WordPress Media Library, inserted into post content or set as post's featured images. Auto Posts create new blog posts and entries automatically on a scheduled basis with any prompt.
Version: 1.3.1
Author: Post Saint
Author URI: https://postsaint.com
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:       post-saint
*/

define( 'POSTSAINT_VERSION', '1.3.1' );
define( 'POSTSAINT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'POSTSAINT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

$debug_mode = null;

//import js and css + admin settings page
function postsaint_admin_enqueue($hook) {

    // css
    wp_register_style( 'postsaint-style', plugins_url('/css/postsaint.css', __FILE__) );
    wp_enqueue_style( 'postsaint-style' );

    // js
    if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == plugin_basename( __DIR__ ).'/images-new.php' ) {
        wp_enqueue_script(  'post-saint-add-new-post', plugin_dir_url( __FILE__ ) . 'js/add-new-post.js' );
        wp_enqueue_script(  'lightbox', plugin_dir_url( __FILE__ ) . 'js/lightbox.js' );

        // lightbox css
        wp_register_style( 'lightbox', plugins_url('/css/lightbox.css', __FILE__) );
        wp_enqueue_style( 'lightbox' );
    }

    // plugin deactivation feedback request
    if( $hook == 'plugins.php' ){
        wp_enqueue_script(  'post-saint-plugin-feedback', plugin_dir_url( __FILE__ ) . 'js/plugin-feedback.js' );
    }

    // logs
    if ( $hook == plugin_basename( __DIR__ ).'/logs.php' ) {
      wp_enqueue_script(  'post-saint-logs', plugin_dir_url( __FILE__ ) .'js/logs.js' );
    }    

    // settings
    if ( $hook == plugin_basename( __DIR__ ).'/settings.php' ) {
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script(  'post-saint-settings', plugin_dir_url( __FILE__ ) . 'js/settings.js' );        
    }
}
add_action( 'admin_enqueue_scripts', 'postsaint_admin_enqueue' );

function postsaint_register_custom_menu_page() {

   add_menu_page('Add Import', 'Post Saint', 'manage_options', plugin_basename( __DIR__ ).'/images-new.php', '', 'dashicons-star-empty');
   add_submenu_page( plugin_basename( __DIR__ ).'/images-new.php', 'Generate Images < Post Saint', 'Generate Images', 'manage_options', plugin_basename( __DIR__ ).'/images-new.php','',30);
   add_submenu_page( plugin_basename( __DIR__ ).'/images-new.php', 'Settings < Post Saint ', 'Settings', 'manage_options', plugin_basename( __DIR__ ).'/settings.php','',40);
   add_submenu_page( plugin_basename( __DIR__ ).'/images-new.php', 'Logs < Post Saint', 'Logs', 'manage_options', plugin_basename( __DIR__ ).'/logs.php','',50);
   add_submenu_page( plugin_basename( __DIR__ ).'/images-new.php', 'Support < Post Saint', 'Support', 'manage_options', plugin_basename( __DIR__ ).'/support.php','',60);

   // hidden from menu
   add_submenu_page('null','View Log < Post Saint', 'Post Saint', 'manage_options', plugin_basename( __DIR__ ).'/view-log.php');   
}
if( !function_exists('postsaintpro_register_custom_menu_page') ){
    add_action('admin_menu', 'postsaint_register_custom_menu_page');
}

// add link to settings on plugins page
function postsaint_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page='.plugin_basename( __DIR__ ).'/settings.php">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'postsaint_add_settings_link' );

function postsaint_create_custom_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // single post text logs
    $table_name = $wpdb->prefix . 'postsaint_single_post_text_logs';

    $sql= "CREATE TABLE " . $table_name . " (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `original_prompt` text NOT NULL,      
      `prompt` text NOT NULL,
      `prepend_prompt` text NULL,
      `append_prompt` text NULL,
      `writing_style` VARCHAR(20) NULL,
      `writing_tone` VARCHAR(20) NULL,
      `keywords` text NULL,
      `openai_model`  VARCHAR(20) NOT NULL,
      `openai_max_tokens` int(4) NOT NULL,
      `openai_temperature` float NOT NULL,
      `openai_top_p` float NOT NULL,
      `openai_best_of` float NOT NULL,
      `openai_frequency_penalty` float NOT NULL,
      `openai_presence_penalty` float NOT NULL,
      `response` text NOT NULL,
      `returned_error` tinyint(1) NULL,
      `returned_error_message` text NULL,
      `prompt_tokens` int(10) NULL,
      `completion_tokens` int(10) NULL,
      `total_tokens` int(10) NULL,
      `post_id` int(10) NOT NULL,
      `auto_post_id` int(10) NOT NULL,      
      `bulk_post_id` int(10) NOT NULL,      
      `created_at` datetime NOT NULL,
      PRIMARY KEY  (`id`)
    ) $charset_collate;";

    // single post image logs
    $table_name = $wpdb->prefix . 'postsaint_dalle_image_logs';

    // append $sql string
    $sql.= "CREATE TABLE " . $table_name . " (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `original_image_prompt` text NOT NULL,
      `image_prompt` text NOT NULL,
      `image_style` VARCHAR(30) NULL,
      `artist_style` VARCHAR(80) NULL,
      `num_images` int(10) NOT NULL,
      `openai_image_size` VARCHAR(12) NOT NULL,     
      `image_response` text NULL,
      `image_returned_error` tinyint(1) NULL,
      `image_returned_error_message` text NULL,      
      `post_id` int(10) NULL,
      `auto_post_id` int(10) NOT NULL,         
      `bulk_post_id` int(10) NOT NULL,      
      `attachment_ids` varchar(255) NULL, 
      `created_at` datetime NOT NULL,
      PRIMARY KEY  (`id`)
    ) $charset_collate;";

    // single post image logs
    $table_name = $wpdb->prefix . 'postsaint_stabilityai_image_logs';

    // append $sql string
    $sql.= "CREATE TABLE " . $table_name . " (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `original_image_prompt` text NOT NULL,
      `image_prompt` text NOT NULL,
      `image_style` VARCHAR(30) NULL,
      `artist_style` VARCHAR(80) NULL,
      `num_images` int(10) NOT NULL,
      `engine_id` VARCHAR(60) NOT NULL,
      `cfg_scale` int(10) NOT NULL,
      `clip_guidance_preset` VARCHAR(60) NULL,
      `sampler` VARCHAR(30) NULL,
      `seed` VARCHAR(30) NULL,
      `steps` int(30) NULL,
      `image_width` VARCHAR(12) NOT NULL,     
      `image_height` VARCHAR(12) NOT NULL,     
      `image_response` text NULL,
      `image_returned_error` tinyint(1) NULL,
      `image_returned_error_message` text NULL,           
      `post_id` int(10) NULL,
      `auto_post_id` int(10) NOT NULL,         
      `bulk_post_id` int(10) NOT NULL,      
      `attachment_ids` varchar(255) NULL, 
      `created_at` datetime NOT NULL,
      PRIMARY KEY  (`id`)
    ) $charset_collate;";

    // single post image logs
    $table_name = $wpdb->prefix . 'postsaint_dezgo_image_logs';

    // append $sql string
    $sql.= "CREATE TABLE " . $table_name . " (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `original_image_prompt` text NOT NULL,
      `image_prompt` text NOT NULL,
      `negative_prompt` text NULL,
      `image_style` VARCHAR(30) NULL,
      `artist_style` VARCHAR(80) NULL,
      `model_id` VARCHAR(60) NOT NULL,
      `guidance` float(4,2) NULL,
      `sampler` VARCHAR(30) NULL,
      `seed` VARCHAR(30) NULL,
      `steps` int(30) NULL,
      `image_width` VARCHAR(12) NOT NULL,     
      `image_height` VARCHAR(12) NOT NULL,     
      `image_response` text NULL,
      `image_returned_error` tinyint(1) NULL,
      `image_returned_error_message` text NULL,           
      `post_id` int(10) NULL,
      `auto_post_id` int(10) NOT NULL,         
      `bulk_post_id` int(10) NOT NULL,      
      `attachment_ids` varchar(255) NULL, 
      `created_at` datetime NOT NULL,
      PRIMARY KEY  (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // load dezgo models
    postsaint_load_dezgo_models();

    update_option('postsaint_version', '1.3.1');
}
register_activation_hook(__FILE__, 'postsaint_create_custom_tables');


// updater
function postsaint_update_db_check() {

    $installed_ver = get_option('postsaint_version', '1.0.4');

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    if( version_compare($installed_ver, '1.1.1', '<') ){

        // add columnns for error messages to 3 tables
        // text
        $table = $wpdb->prefix . 'postsaint_single_post_text_logs';
        $sql = "ALTER TABLE `{$table}`
                ADD `returned_error_message` text NULL 
                AFTER `returned_error`;";

        $query_result = $wpdb->query( $sql );

        // dalle
        $table = $wpdb->prefix . 'postsaint_dalle_image_logs';
        $sql = "ALTER TABLE `{$table}`
                ADD `image_returned_error_message` text NULL 
                AFTER `image_returned_error`;";

        $query_result = $wpdb->query( $sql );


        // stable diffusion
        $table = $wpdb->prefix . 'postsaint_stabilityai_image_logs';
        $sql = "ALTER TABLE `{$table}`
                ADD `image_returned_error_message` text NULL 
                AFTER `image_returned_error`;";

        $query_result = $wpdb->query( $sql );

        $installed_ver = '1.1.1';

        update_option('postsaint_version', $installed_ver);
    }

    if( version_compare($installed_ver, '1.2', '<') ){

        // add dezgo_image_logs table

        // single post image logs
        $table_name = $wpdb->prefix . 'postsaint_dezgo_image_logs';

        // append $sql string
        $sql= "CREATE TABLE " . $table_name . " (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `original_image_prompt` text NOT NULL,
          `image_prompt` text NOT NULL,
          `negative_prompt` text NULL,
          `image_style` VARCHAR(30) NULL,
          `artist_style` VARCHAR(80) NULL,
          `model_id` VARCHAR(60) NOT NULL,
          `guidance` float(4,2) NULL,
          `sampler` VARCHAR(30) NULL,
          `seed` VARCHAR(30) NULL,
          `steps` int(30) NULL,
          `image_width` VARCHAR(12) NOT NULL,     
          `image_height` VARCHAR(12) NOT NULL,     
          `image_response` text NULL,
          `image_returned_error` tinyint(1) NULL,
          `image_returned_error_message` text NULL,           
          `post_id` int(10) NULL,
          `auto_post_id` int(10) NOT NULL,         
          `bulk_post_id` int(10) NOT NULL,      
          `attachment_ids` varchar(255) NULL, 
          `created_at` datetime NOT NULL,
          PRIMARY KEY  (`id`)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // load dezgo models
        postsaint_load_dezgo_models();

        $installed_ver = '1.2';

        update_option('postsaint_version', $installed_ver);
    }    
}
add_action( 'plugins_loaded', 'postsaint_update_db_check' );

function postsaint_load_dezgo_models(){

    $url = 'https://api.dezgo.com/info';

        $args = array(
            'method' => 'GET',
            'timeout' => 45,
            'httpversion' => '1.0',
            'headers' => array(
                'Authorization' => get_site_option('postsaint_settings_dezgo_api_key'),
                'Accept' => 'application/json',
            ),
            //'body' => json_encode($body),
        );

        $response = wp_remote_post ($url, $args);

        if( !is_wp_error( $response ) ) {
            $return = json_decode($response['body'],true);
        }

        $models_array = array();

        foreach( $return['models'] as $model) {

            // if text2image in functions array
            if( in_array('text2image', $model['functions']) ){
                $models_array[$model['id']] = $model['name'];
            }
        }

        //sort
        ksort($models_array);

    // update
    update_option('postsaint_dezgo_models', json_encode($models_array));
}

function postsaint_get_users_with_role( $roles, $current_selected ) {

    global $wpdb;
    if ( ! is_array( $roles ) )
        $roles = array_walk( explode( ",", $roles ), 'trim' );
        $sql = '
        SELECT  ID, display_name
        FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
        ON          ' . $wpdb->users . '.ID             =       ' . $wpdb->usermeta . '.user_id
        WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'
        AND     (
    ';
    $i = 1;
    foreach ( $roles as $role ) {
        $sql .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%"' . $role . '"%\' ';
        if ( $i < count( $roles ) ) $sql .= ' OR ';
        $i++;
    }
    $sql .= ' ) ';
    $sql .= ' ORDER BY display_name ';

    $results = $wpdb->get_results( $sql);
    foreach ($results as $result){

        $selected = null;

        if($result->ID == $current_selected){
            $selected = "selected =\"selected\"";
        }

        echo "<option value='" . esc_attr($result->ID) . "' $selected>" . esc_html($result->display_name) . "</option>\n";
    }
}

function postsaint_select_field($field_name, $options_array, $default){

    $field = '<select name="'.esc_attr($field_name).'" id="'.esc_attr($field_name).'">';

    foreach($options_array as $val => $label){

        $selected = null;

        if($default == $val){
            $selected = 'selected';
        }

        $field.= '<option value="'.esc_attr($val).'" '.$selected.'>'.esc_attr($label).'</option>';
    }
    $field.= '</select>';

    echo $field;
}

function postsaint_sanitize_file_name($string){

    // replace non alphanumeric chars with hyphen
    $string = preg_replace('/[^a-z0-9]+/', '-', strtolower($string));

    // trim consecutive hyphens
    $string = trim(preg_replace('/-+/', '-', $string), '-');

    return $string;
}

// plugin deactivation
function deactivate_postsaint() {

    // delete log tables
    global $wpdb;

    if( get_site_option('postsaint_settings_delete_log_data_deactivation') == 1){

        $sql= 'DROP TABLE IF EXISTS '.$wpdb->prefix.'postsaint_dalle_image_logs;';   
        $wpdb->query($sql); 

        $sql= 'DROP TABLE IF EXISTS '.$wpdb->prefix.'postsaint_stabilityai_image_logs;';   
        $wpdb->query($sql); 

        $sql= 'DROP TABLE IF EXISTS '.$wpdb->prefix.'postsaint_single_post_text_logs;';   
        $wpdb->query($sql); 
    }

    // cron, deactivate cron
    if( get_option('postsaint_auto_post_trigger','wp_cron') == 'wp_cron' ){

        // if no more active clear
        wp_clear_scheduled_hook( 'postsaint_cron_hook' );
    }    

    // delete auto_update_next_run option
    delete_option('postsaint_auto_update_next_run');    


    // delete default settings
    if( get_site_option('postsaint_settings_delete_default_settings_deactivation','1') == 1 ){

        $sql = 'DELETE FROM '.$wpdb->prefix.'options WHERE option_name LIKE "postsaint_settings_%"';
        $wpdb->query($sql);  
    }
}
register_deactivation_hook( __FILE__, 'deactivate_postsaint' );

// create featured image
function postsaint_create_featured_image($image_url, $lastID, $image_prompt, $insert_prompt_media_library_fields){

    $upload_dir = wp_upload_dir();

    // surpress errors in case image doesn't open
    $image_data = file_get_contents($image_url);

    if(!$image_data){
        return;
    }

    $basename = basename($image_url);
    $ext = pathinfo($basename, PATHINFO_EXTENSION);

    // image url could have no file extention
    if(empty($ext)){
        $ext = 'jpg';
    }

    $filename = $lastID.'.'.$ext;
    if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $image_prompt,
        'post_status' => 'inherit'
    );

    // caption
    if( $insert_prompt_media_library_fields == 'caption' || $insert_prompt_media_library_fields == 'caption_description' ){
        $attachment['post_excerpt'] = $image_prompt; // caption
    }

    // description
    if( $insert_prompt_media_library_fields == 'description' || $insert_prompt_media_library_fields == 'caption_description' ){
        $attachment['post_content'] = $image_prompt; // description
    }    

    $attach_id = wp_insert_attachment( $attachment, $file, $lastID );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    // update alt text
    update_post_meta($attach_id, '_wp_attachment_image_alt', $image_prompt);

    set_post_thumbnail( $lastID, $attach_id );

    // return attachment id
    return $attach_id;
}

// add image to media library
function postsaint_add_image_media_library($image_url, $file_name, $image_prompt, $insert_prompt_media_library_fields){

    $upload_dir = wp_upload_dir();

    // surpress errors in case image doesn't open
    $image_data = file_get_contents($image_url);

    if(!$image_data){
        return;
    }

    $basename = basename($image_url);
    $ext = pathinfo($basename, PATHINFO_EXTENSION);

    // image url could have no file extention
    if(empty($ext)){
        $ext = 'jpg';
    }

    // truncate long filename
    $file_name = substr($file_name, 0, 240);

    $filename = $file_name.'.'.$ext;

    if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $image_prompt,
        'post_status' => 'inherit'
    );

    // caption
    if( $insert_prompt_media_library_fields == 'caption' || $insert_prompt_media_library_fields == 'caption_description' ){
        $attachment['post_excerpt'] = $image_prompt; // caption
    }

    // description
    if( $insert_prompt_media_library_fields == 'description' || $insert_prompt_media_library_fields == 'caption_description' ){
        $attachment['post_content'] = $image_prompt; // description
    }

    $attach_id = wp_insert_attachment( $attachment, $file );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    // update alt text
    update_post_meta($attach_id, '_wp_attachment_image_alt', $image_prompt);

    // return attachment id
    return $attach_id;
}

// request feedback on deactivation
global $pagenow;

if($pagenow == 'plugins.php'){
    function deactivation_feedback_dialog_box(){
        add_thickbox(); ?>
        <div id="postsaint-deactivate-plugin-tb" style="display:none;">
             <p>
                  <b>Please help us improve Post Saint plugin!</b><br> 
                  Tell us why you are deactivating: (optional)<br>
                  <textarea id="postsaint_deactivate_feedback"></textarea><br>
                  <button id="postaint-deactivate-btn" class="button action">Deactivate</button>
             </p>
        </div>
        <?php     
    }
    add_action( 'admin_footer', 'deactivation_feedback_dialog_box' );
}

require_once 'inc/functions/actions.php';
require_once 'inc/functions/posts-meta-boxes.php';