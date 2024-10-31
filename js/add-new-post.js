jQuery(document).ready(function ($) {

    // resize title/prompt textarea height 
    $('#import_data, #postsaint_title_prompt,#postsaint_result').on('change keyup keydown paste cut', function () {
            $(this).height(0).height(this.scrollHeight);
        }).find('#import_data, #postsaint_title_prompt, #postsaint_result').change();


    // TEXT
    // 'Generate Text' button
    $('#postsaint-generate-content').click( function( e, selection, media ) {

        e.preventDefault();

        // disable this button
        $("#postsaint-generate-content").prop("disabled", true);

        var generating_message = '<span class="postsaint-warnmsg">Generating text...</span>';
        
        var plugin_dir_url = jQuery('#plugin_dir_url').val();
        jQuery('#postsaint-generated-content-message').html(generating_message + '<br><img id="postsaint-loading" src="'+plugin_dir_url+'/images/loading.gif">');        
        
        var prompt = jQuery('#postsaint_prompt').val();

        if( prompt.length < 1 ){

            // show message
            jQuery('#postsaint-generated-content-message').html('<span class="postsaint-errormsg">Prompt must be entered!</span>');

            // re-enable button
            $("#postsaint-generate-content").removeAttr('disabled');              
            return;
        }

        // writing instructions
        var prepend_prompt = jQuery('#postsaint_prepend_prompt').val();
        var append_prompt = jQuery('#postsaint_append_prompt').val();
        var writing_style = jQuery('#postsaint_writing_style').val();
        var writing_tone = jQuery('#postsaint_writing_tone').val();
        var keywords = jQuery('#postsaint_keywords').val();

        // openai params
        var openai_model = jQuery('#postsaint_openai_model').val();
        var openai_max_tokens = jQuery('#postsaint_openai_max_tokens').val();
        var openai_temperature = jQuery('#postsaint_openai_temperature').val();
        var openai_top_p = jQuery('#postsaint_openai_top_p').val();
        var openai_frequency_penalty = jQuery('#postsaint_openai_frequency_penalty').val();
        var openai_presence_penalty = jQuery('#postsaint_openai_presence_penalty').val();

        var dataString = 'action=add_single_post&';

        dataString = dataString + 'prompt='+prompt+'&';
        dataString = dataString + 'prepend_prompt='+prepend_prompt+'&';
        dataString = dataString + 'append_prompt='+append_prompt+'&';
        dataString = dataString + 'writing_style='+writing_style+'&';
        dataString = dataString + 'writing_tone='+writing_tone+'&';
        dataString = dataString + 'keywords='+keywords+'&';

        dataString = dataString + 'openai_model='+openai_model+'&';
        dataString = dataString + 'openai_max_tokens='+openai_max_tokens+'&';
        dataString = dataString + 'openai_temperature='+openai_temperature+'&';
        dataString = dataString + 'openai_top_p='+openai_top_p+'&';
        dataString = dataString + 'openai_frequency_penalty='+openai_frequency_penalty+'&';
        dataString = dataString + 'openai_presence_penalty='+openai_presence_penalty+'&';

        jQuery.ajax({
          type: "POST",
          dataType: 'json',
          url: ajaxurl,
          data: dataString,
          
          success: function(json){

                // re-enable button
                $("#postsaint-generate-content").removeAttr('disabled');            

                if(json.message == null){

                    // show textarea
                    jQuery('#postsaint_result').show();

                    // insert content into textarea
                    jQuery('#postsaint_result').val(json.response);

                    // show copy to content button
                    jQuery('#postsaint-copy-to-content').show();

                    // show copy to clipboard button
                    jQuery('#postsaint-copy-to-clipboard').show();

                    // add log ids to string and send to hidden value
                    // add to array
                    var new_id = json.log_id;

                    // get value
                    var current_ids = jQuery('#postsaint_text_log_ids').val(); // comma separated

                    // append
                    var all_ids = current_ids+','+new_id;

                    // set value
                    jQuery('#postsaint_text_log_ids').val(all_ids);
                }

                // show message
                jQuery('#postsaint-generated-content-message').html(json.message);        
          }
        });
    });

    // copy to content
    $('#postsaint-copy-to-content').click( function( event ) {

        event.preventDefault();
        
        var postsaint_result = jQuery('#postsaint_result').val();

        // classic editor
        if( parent.tinyMCE.activeEditor ){
            
            parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + postsaint_result);

            // scroll to
            tinymce.activeEditor.getBody().parentElement.scrollIntoView();            
        }

        // gutenberg
        if( wp.blocks && wp.data ){

            const newBlock = wp.blocks.createBlock( "core/paragraph", {
                content: postsaint_result,
            });
            wp.data.dispatch( "core/block-editor" ).insertBlocks( newBlock );
        }

        // write message clopied to clipboard
        jQuery('#postsaint-generated-content-message').html('<span class="postsaint-succmsg">Copied to Post Content!</span>');   
    });

    // copy to clipboard
    $('#postsaint-copy-to-clipboard').click( function( event ) {

        event.preventDefault();
        
        var postsaint_result = jQuery('#postsaint_result').val();

        navigator.clipboard.writeText(postsaint_result);

        // write message clopied to clipboard
        jQuery('#postsaint-generated-content-message').html('<span class="postsaint-succmsg">Copied to Clipboard!</span>');   
    });

    // IMAGES
    function setForm(image_generator){

        if( image_generator == 'stable_diffusion'){

            jQuery('#postsaint-tr-num_images').fadeIn();

            jQuery('#postsaint-tr-stable_diffusion_sampler').fadeIn();            
            jQuery('#postsaint-tr-stable_diffusion_engine').fadeIn();
            jQuery('#postsaint-tr-stable_diffusion_cfg_scale').fadeIn();
            jQuery('#postsaint-tr-stable_diffusion_steps').fadeIn();
            jQuery('#postsaint-tr-image_width').fadeIn();
            jQuery('#postsaint-tr-image_height').fadeIn();
            jQuery('#postsaint-tr-image_style').fadeIn();     
            jQuery('#postsaint-tr-artist_style').fadeIn();                   

            jQuery('#postsaint-tr-pexels_orientation').fadeOut();
            jQuery('#postsaint-tr-pexels_size').fadeOut();
            jQuery('#postsaint-tr-pexels_color').fadeOut();
            jQuery('#postsaint-tr-num_results').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_model').fadeOut();
            jQuery('#postsaint-tr-openai_image_size').fadeOut();
            jQuery('#postsaint-tr-negative_prompt').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_guidance').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_sampler').fadeOut();
        }

        if( image_generator == 'stable_diffusion_dezgo'){

            jQuery('#postsaint-tr-num_images').fadeIn();

            jQuery('#postsaint-tr-stable_diffusion_dezgo_model').fadeIn();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_sampler').fadeIn();
            jQuery('#postsaint-tr-negative_prompt').fadeIn();
            jQuery('#postsaint-tr-stable_diffusion_guidance').fadeIn();
            jQuery('#postsaint-tr-stable_diffusion_steps').fadeIn();
            jQuery('#postsaint-tr-image_width').fadeIn();
            jQuery('#postsaint-tr-image_height').fadeIn();
            jQuery('#postsaint-tr-image_style').fadeIn();     
            jQuery('#postsaint-tr-artist_style').fadeIn();    

            jQuery('#postsaint-tr-pexels_orientation').fadeOut();
            jQuery('#postsaint-tr-pexels_size').fadeOut();
            jQuery('#postsaint-tr-pexels_color').fadeOut();
            jQuery('#postsaint-tr-num_results').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_engine').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_cfg_scale').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_sampler').fadeOut();
            jQuery('#postsaint-tr-openai_image_size').fadeOut();
        }

        if( image_generator == 'dalle'){

            // dezgo
            jQuery('#postsaint-tr-negative_prompt').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_guidance').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_model').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_sampler').fadeOut();

            // stability.ai
            jQuery('#postsaint-tr-stable_diffusion_engine').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_cfg_scale').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_sampler').fadeOut();

            // dezgo/stability
            jQuery('#postsaint-tr-stable_diffusion_steps').fadeOut();

            jQuery('#postsaint-tr-pexels_orientation').fadeOut();
            jQuery('#postsaint-tr-pexels_size').fadeOut();
            jQuery('#postsaint-tr-pexels_color').fadeOut();
            jQuery('#postsaint-tr-num_results').fadeOut();
            jQuery('#postsaint-tr-image_width').fadeOut();
            jQuery('#postsaint-tr-image_height').fadeOut();   
            jQuery('#postsaint-tr-image_style').fadeIn();
            jQuery('#postsaint-tr-artist_style').fadeIn();

            jQuery('#postsaint-tr-num_images').fadeIn();
            jQuery('#postsaint-tr-openai_image_size').fadeIn();
        }

        if( image_generator == 'pexels'){

            // pexels
            jQuery('#postsaint-tr-pexels_orientation').fadeIn();
            jQuery('#postsaint-tr-pexels_size').fadeIn();
            jQuery('#postsaint-tr-pexels_color').fadeIn();
            jQuery('#postsaint-tr-num_results').fadeIn();

            // dezgo
            jQuery('#postsaint-tr-negative_prompt').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_guidance').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_model').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_dezgo_sampler').fadeOut();

            // stability.ai
            jQuery('#postsaint-tr-stable_diffusion_engine').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_cfg_scale').fadeOut();
            jQuery('#postsaint-tr-stable_diffusion_sampler').fadeOut();

            jQuery('#postsaint-tr-stable_diffusion_steps').fadeOut();
            jQuery('#postsaint-tr-image_width').fadeOut();
            jQuery('#postsaint-tr-image_height').fadeOut();   

            // dalle
            jQuery('#postsaint-tr-num_images').fadeOut();
            jQuery('#postsaint-tr-image_style').fadeOut();
            jQuery('#postsaint-tr-artist_style').fadeOut();
            jQuery('#postsaint-tr-openai_image_size').fadeOut();
        }
    }

    // change image generator - show/hide appropriate fields
    $('select#postsaint_image_generator').change( function( event ) {

        event.preventDefault();

        var image_generator = $(this).val();
        
        setForm(image_generator);
    });

    // 'Generate Images' button
    //$('#postsaint-generate-images, #postsaint-paginate-generated-images').click( function( e, selection, media ) {
    $(document.body).on('click', '#postsaint-generate-images, #postsaint-paginate-generated-images' ,function(e, selection, media ){        

        e.preventDefault();

        // disable this button
        $("#postsaint-generate-images").prop("disabled", true);

        // unset message
        jQuery('#postsaint-generated-images-message').html('');

        var image_prompt = jQuery('#postsaint_image_prompt').val();

        if( image_prompt.length < 1 ){

            // show message
            jQuery('#postsaint-generated-images-message').html('<span class="postsaint-errormsg">Prompt must be entered!</span>');

            // re-enable button
            $("#postsaint-generate-images").removeAttr('disabled');              
            return;
        }

        var generator_standalone = jQuery('#postsaint_generator_standalone').val();
        var image_generator = jQuery('#postsaint_image_generator').val();
        var stable_diffusion_engine = jQuery('#postsaint_stable_diffusion_engine').val();
        var stable_diffusion_cfg_scale = jQuery('#postsaint_stable_diffusion_cfg_scale').val();
        var stable_diffusion_steps = jQuery('#postsaint_stable_diffusion_steps').val();
        var stable_diffusion_dezgo_model = jQuery('#postsaint_stable_diffusion_dezgo_model').val();
        var stable_diffusion_sampler = jQuery('#postsaint_stable_diffusion_sampler').val();
        var stable_diffusion_dezgo_sampler = jQuery('#postsaint_stable_diffusion_dezgo_sampler').val();
        var stable_diffusion_guidance = jQuery('#postsaint_stable_diffusion_guidance').val();

        var negative_prompt = jQuery('#postsaint_negative_prompt').val();
        var image_style = jQuery('#postsaint_image_style').val();
        var artist_style = jQuery('#postsaint_artist_style').val();
        var num_images = jQuery('#postsaint_num_images').val();
        var openai_image_size = jQuery('#postsaint_openai_image_size').val();        
        var image_width = jQuery('#postsaint_image_width').val();  
        var image_height = jQuery('#postsaint_image_height').val();

        var num_results = jQuery('#postsaint_num_results').val();
        var pexels_orientation = jQuery('#postsaint_pexels_orientation').val();
        var pexels_size = jQuery('#postsaint_pexels_size').val();
        var pexels_color = jQuery('#postsaint_pexels_color').val();
        var page_num = jQuery(this).attr('data-page_num');           

        var generated_images_method = jQuery('input[name="postsaint_generated_images_method"]:checked').val();
        var insert_prompt_media_library_fields = jQuery('#postsaint_insert_prompt_media_library_fields').val(); 

        var dataString = 'action=generate_images&';
        dataString = dataString + 'generator_standalone='+generator_standalone+'&';
        dataString = dataString + 'image_generator='+image_generator+'&';
        dataString = dataString + 'stable_diffusion_engine='+stable_diffusion_engine+'&';
        dataString = dataString + 'stable_diffusion_cfg_scale='+stable_diffusion_cfg_scale+'&';
        dataString = dataString + 'stable_diffusion_steps='+stable_diffusion_steps+'&';
        dataString = dataString + 'stable_diffusion_dezgo_model='+stable_diffusion_dezgo_model+'&';
        dataString = dataString + 'stable_diffusion_sampler='+stable_diffusion_sampler+'&';
        dataString = dataString + 'stable_diffusion_dezgo_sampler='+stable_diffusion_dezgo_sampler+'&';
        dataString = dataString + 'stable_diffusion_guidance='+stable_diffusion_guidance+'&';
        dataString = dataString + 'image_prompt='+encodeURIComponent(image_prompt)+'&';
        dataString = dataString + 'negative_prompt='+encodeURIComponent(negative_prompt)+'&';
        dataString = dataString + 'image_style='+image_style+'&';
        dataString = dataString + 'artist_style='+artist_style+'&';
        dataString = dataString + 'num_images='+num_images+'&';
        dataString = dataString + 'openai_image_size='+openai_image_size+'&';
        dataString = dataString + 'image_width='+image_width+'&';
        dataString = dataString + 'image_height='+image_height+'&';
        dataString = dataString + 'num_results='+num_results+'&';        
        dataString = dataString + 'pexels_orientation='+pexels_orientation+'&';        
        dataString = dataString + 'pexels_size='+pexels_size+'&';
        dataString = dataString + 'pexels_color='+pexels_color+'&';
        dataString = dataString + 'page_num='+page_num+'&';
        dataString = dataString + 'generated_images_method='+generated_images_method+'&';
        dataString = dataString + 'insert_prompt_media_library_fields='+insert_prompt_media_library_fields+'&';

        // insert 'Generating Images...' message in container
        if( generated_images_method == 'add_library' ){
            var generating_message = 'Generating image(s) and saving to Media Library...';
        }

        if( generated_images_method == 'preview_only' ){
            var generating_message = 'Generating image(s) to preview...';
        }        

        var plugin_dir_url = jQuery('#plugin_dir_url').val();
        jQuery('#postsaint-generated-images-container').html('<span class="postsaint-warnmsg">'+generating_message+'</span><br><img id="postsaint-loading" src="'+plugin_dir_url+'/images/loading.gif">');      

        jQuery.ajax({
          type: "POST",
          dataType: 'json',
          url: ajaxurl,
          data: dataString,
          
          success: function(json){

                // re-enable button
                $("#postsaint-generate-images").removeAttr('disabled');

                // add log ids to string and send to hidden value
                // add to array
                var new_id = json.log_id;

                if( image_generator == 'dalle' ){

                    // get value
                    var current_ids = jQuery('#postsaint_dalle_image_log_ids').val(); // comma separated

                    var all_ids = current_ids+','+new_id

                    // set value
                    jQuery('#postsaint_dalle_image_log_ids').val(all_ids);
                }

                if( image_generator == 'stable_diffusion' ){

                    // get value
                    var current_ids = jQuery('#postsaint_sd_image_log_ids').val(); // comma separated

                    var all_ids = current_ids+','+new_id

                    // set value
                    jQuery('#postsaint_sd_image_log_ids').val(all_ids);
                }

                // insert images container
                jQuery('#postsaint-generated-images-container').html(json.images_container);

                // show message
                jQuery('#postsaint-generated-images-message').html(json.response);
          }
        });
    });

    // 'Add image to Media Lbrary' button
    $(document.body).on('click', '.postsaint-add-image-to-media, .postsaint-add-image-to-media-right' ,function(e, selection, media ){

        e.preventDefault();

        var this2 = $(this);

        // show message before AJAX submit
        var message = '<span class="postsaint-warnmsg">Saving image to Media Library...</span>';
        var plugin_dir_url = jQuery('#plugin_dir_url').val();
        jQuery('#postsaint-generated-images-message').html(message + '<br><img id="postsaint-loading" src="'+plugin_dir_url+'/images/loading.gif">');

        // get img src 
        
        // use linked image!!! img src could be tiny image
        var image_url = jQuery(this).closest('.generated-image-container').find("a").attr('href');

        // get prompt used from alt tag
        var image_prompt = jQuery(this).closest('.generated-image-container').find("img").attr('alt');

        // add prompt to media library fields
        var insert_prompt_media_library_fields = jQuery('#postsaint_insert_prompt_media_library_fields').val();         

        // get image_generator of generated image
        var image_generator = jQuery(this).closest('.generated-image-container').find("img").attr('data-image_generator');

        // get log id of generated image
        var log_id = jQuery(this).closest('.generated-image-container').find("img").attr('data-log_id');

        var dataString = 'action=add_image_to_library&';
        dataString = dataString + 'image_url='+encodeURIComponent(image_url)+'&';
        dataString = dataString + 'image_prompt='+encodeURIComponent(image_prompt)+'&';
        dataString = dataString + 'insert_prompt_media_library_fields='+insert_prompt_media_library_fields+'&';
        dataString = dataString + 'image_generator='+image_generator+'&';
        dataString = dataString + 'log_id='+log_id+'&';

        jQuery.ajax({
          type: "POST",
          dataType: 'json',
          url: ajaxurl,
          data: dataString,
          
          success: function(json){

                // make sure json.library_url not null, could have been not created
                if( json.library_url != null ){

                    // add library_url param to data on img tag (nearest)
                    jQuery(this2).closest('.generated-image-container').find("img").attr('data-library_url', json.library_url);
                    jQuery(this2).closest('.generated-image-container').find("img").attr('data-attachment_id', json.attachment_id);

                    // show message
                    var message = '<span class="postsaint-succmsg">Added image to Media Library!</span>'
                    jQuery('#postsaint-generated-images-message').html(message);

                    // remove button just clicked, since added to media library
                    jQuery(this2).hide();

                } else {

                    // error creating image, json.library_url is null
                    // set empty values on this button
                    jQuery(this2).closest('.generated-image-container').find("img").attr('data-library_url', '');
                    jQuery(this2).closest('.generated-image-container').find("img").attr('data-attachment_id', '');                        

                    // show message
                    var message = '<span class="postsaint-errormsg">Image not added to Media Library. Please try again.</span>';
                    jQuery('#postsaint-generated-images-message').html(message);                        

                }
          }
        });  
    }); 

    // 'Add image to Post Content' button
    $(document.body).on('click', '.postsaint-add-image-to-post-content' ,function(e, selection, media ){

        e.preventDefault();

        var this2 = $(this);

        // need library_url to copy into post content area
        var library_url =  jQuery(this).closest('.generated-image-container').find("img").attr('data-library_url');

        // if already added to library
        if( library_url != null ){        

            // if not empty, just copy to post content
            if( library_url.length > 0 ){

                // classic editor
                if( parent.tinyMCE.activeEditor ){
                    
                    var insert_content = '<img src="'+library_url+'">';

                    parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + insert_content);
                }

                // gutenberg
                if( wp.blocks && wp.data ){

                    const newBlock = wp.blocks.createBlock( "core/image", {
                        url:library_url,
                    });
                    wp.data.dispatch( "core/block-editor" ).insertBlocks( newBlock );
                }

                // show message
                var message = '<span class="postsaint-succmsg">Added image to content block!</span>'
                jQuery('#postsaint-generated-images-message').html(message);  

            } else {

                // need to add to library in AJAX, then copy
                // show message before AJAX submit
                var message = '<span class="postsaint-warnmsg">Saving image to Media Library to add into content block...</span>';
                var plugin_dir_url = jQuery('#plugin_dir_url').val();
                jQuery('#postsaint-generated-images-message').html(message + '<br><img id="postsaint-loading" src="'+plugin_dir_url+'/images/loading.gif">');

                //var image_url = jQuery(this).closest('.generated-image-container').find("img").attr('src');

                // use linked image!!! img src could be tiny image
                var image_url = jQuery(this).closest('.generated-image-container').find("a").attr('href');

                // get prompt used from alt tag
                var image_prompt = jQuery(this).closest('.generated-image-container').find("img").attr('alt');

                var insert_prompt_media_library_fields = jQuery('#postsaint_insert_prompt_media_library_fields').val();

                // get image_generator of generated image
                var image_generator = jQuery(this).closest('.generated-image-container').find("img").attr('data-image_generator');

                // get log id of generated image
                var log_id = jQuery(this).closest('.generated-image-container').find("img").attr('data-log_id');

                var dataString = 'action=add_image_to_library&';
                dataString = dataString + 'image_url='+encodeURIComponent(image_url)+'&';
                dataString = dataString + 'image_prompt='+encodeURIComponent(image_prompt)+'&';
                dataString = dataString + 'insert_prompt_media_library_fields='+insert_prompt_media_library_fields+'&';  
                dataString = dataString + 'image_generator='+image_generator+'&';
                dataString = dataString + 'log_id='+log_id+'&';

                jQuery.ajax({
                  type: "POST",
                  dataType: 'json',
                  url: ajaxurl,
                  data: dataString,
                  
                  success: function(json){

                        // make sure json.library_url not null, could have been not created
                        if( json.library_url != null ){

                            // add library_url, attachment_id param to data on img tag (nearest)
                            jQuery(this2).closest('.generated-image-container').find("img").attr('data-library_url', json.library_url);
                            jQuery(this2).closest('.generated-image-container').find("img").attr('data-attachment_id', json.attachment_id);

                            // classic editor
                            if( parent.tinyMCE.activeEditor ){

                                var insert_content = '<img src="'+json.library_url+'">';
                                parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + insert_content);
                            }

                            // gutenberg
                            if( wp.blocks && wp.data ){

                                const newBlock = wp.blocks.createBlock( "core/image", {
                                    //id: attach_id,
                                    url:json.library_url,
                                });
                                wp.data.dispatch( "core/block-editor" ).insertBlocks( newBlock );
                            }

                            // show message
                            var message = '<span class="postsaint-succmsg">Added image to Media Library and content block!</span>'
                            jQuery('#postsaint-generated-images-message').html(message);

                            // hide closest 'Add to Media Library' button since just added 
                            jQuery(this2).closest('.generated-image-container').find(".postsaint-add-image-to-media").hide();


                        } else {

                            // error creating image, json.library_url is null
                            // set empty values on this button
                            jQuery(this2).closest('.generated-image-container').find("img").attr('data-library_url', '');
                            jQuery(this2).closest('.generated-image-container').find("img").attr('data-attachment_id', '');                        

                            // show message
                            var message = '<span class="postsaint-errormsg">Image not added to Media Library or post content. Please try again.</span>';
                            jQuery('#postsaint-generated-images-message').html(message);                        
                        }
                  }
                });      
            }
        }
    }); 

    // 'Add image to Featured Image' button
    $(document.body).on('click', '.postsaint-add-image-to-featured-image' ,function(e, selection, media ){

        e.preventDefault();

        var this2 = $(this);

        // need attach_id to copy into post content area
        var attach_id =  jQuery(this).closest('.generated-image-container').find("img").attr('data-attachment_id');

        // if already added to library
        if( attach_id != null ){

             // if not empty, just copy url to block
            if( attach_id.length > 0 ){

                // try to set feature image in classic editor
                wp.media.featuredImage.set(attach_id);

                if( wp.data.dispatch( 'core/editor' ) != null ){

                    // add featured image in gutenberg 
                    wp.data.dispatch( 'core/editor' ).editPost({ featured_media: attach_id });
                }

                // show message
                var message = '<span class="postsaint-succmsg">Set as Featured Image!</span>'
                jQuery('#postsaint-generated-images-message').html(message);                  

            } else {

                var message = '<span class="postsaint-warnmsg">Saving image to Media Library to set as Featured Image...</span>';
                var plugin_dir_url = jQuery('#plugin_dir_url').val();
                jQuery('#postsaint-generated-images-message').html(message + '<br><img id="postsaint-loading" src="'+plugin_dir_url+'/images/loading.gif">');

                // get img src 
                //var image_url = jQuery(this).closest('.generated-image-container').find("img").attr('src');     

                // use linked image!!! img src could be tiny image
                var image_url = jQuery(this).closest('.generated-image-container').find("a").attr('href');                

                // get prompt used from alt tag
                var image_prompt = jQuery(this).closest('.generated-image-container').find("img").attr('alt');

                var insert_prompt_media_library_fields = jQuery('#postsaint_insert_prompt_media_library_fields').val();                           

                // get image_generator of generated image
                var image_generator = jQuery(this).closest('.generated-image-container').find("img").attr('data-image_generator');

                // get log id of generated image
                var log_id = jQuery(this).closest('.generated-image-container').find("img").attr('data-log_id');

                var dataString = 'action=add_image_to_library&';
                dataString = dataString + 'image_url='+encodeURIComponent(image_url)+'&';
                dataString = dataString + 'image_prompt='+encodeURIComponent(image_prompt)+'&';
                dataString = dataString + 'insert_prompt_media_library_fields='+insert_prompt_media_library_fields+'&';  
                dataString = dataString + 'image_generator='+image_generator+'&';
                dataString = dataString + 'log_id='+log_id+'&';

                jQuery.ajax({
                  type: "POST",
                  dataType: 'json',
                  url: ajaxurl,
                  data: dataString,
                  
                  success: function(json){

                        // make sure json.library_url not null, could have been not created
                        if( json.library_url != null ){

                            // try to set feature image in classic editor
                            wp.media.featuredImage.set(json.attachment_id);

                            if( wp.data.dispatch( 'core/editor' ) != null ){

                                // add featured image in gutenberg 
                                wp.data.dispatch( 'core/editor' ).editPost({ featured_media: json.attachment_id });
                            }

                            // show message
                            var message = '<span class="postsaint-succmsg">Added image to Media Library and set as Featured Image!</span>'
                            jQuery('#postsaint-generated-images-message').html(message);


                            // hide closest 'Add to Media Library' button since just added 
                            jQuery(this2).closest('.generated-image-container').find(".postsaint-add-image-to-media").hide();


                            // set attachment id in case this button clicked again and don't re add to library
                            jQuery(this2).closest('.generated-image-container').find("img").attr('data-attachment_id', json.attachment_id);

                            // set library_url in case will copy to post content
                            jQuery(this2).closest('.generated-image-container').find("img").attr('data-library_url', json.library_url);


                        } else {

                            // show message
                            var message = '<span class="postsaint-errormsg">Image not added to Media Library or set as Featured Image. Please try again.</span>';
                            jQuery('#postsaint-generated-images-message').html(message);                        

                        }
                  }
                });      
            }
        }
    }); 
});