jQuery(document).ready(function ($) {

    $('#deactivate-post-saint').click( function( e, selection, media ) {

        e.preventDefault();

        // get deactivate plugin URL
        redirectLink = $(this).attr('href');

        // show thickbox
        tb_show("Deactivate Post Saint","#TB_inline?height=240&amp;width=355&amp;inlineId=postsaint-deactivate-plugin-tb",null);
    }); 


    $('#postaint-deactivate-btn').click( function( e, selection, media ) {

        var deactivate_feedback = jQuery('#postsaint_deactivate_feedback').val();
        
        var dataString = 'action=postsaint_deactivate_plugin&';

        dataString = dataString + 'deactivate_feedback='+encodeURIComponent(deactivate_feedback)+'&';

        jQuery.ajax({
          type: "POST",
          dataType: 'json',
          url: ajaxurl,
          data: dataString,
          
          success: function(json){

            window.location.href = redirectLink;
  
          }
        });

    }); 

});