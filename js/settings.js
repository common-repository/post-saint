jQuery( function($) {       


    $('#update-dezgo-models').click( function( event ) {

        event.preventDefault();

        var prompt = 'hi';

        var dataString = 'action=update_dezgo_models&';

        dataString = dataString + 'prompt='+prompt+'&';
        
        jQuery.ajax({
          type: "POST",
          dataType: 'json',
          url: ajaxurl,
          data: dataString,
          
          success: function(json){

            var newOptions = json;

            var defValOfModel = $('#postsaint_settings_stable_diffusion_dezgo_model').val();

            var $el = $("#postsaint_settings_stable_diffusion_dezgo_model");
            $el.empty(); // remove old options
            $.each(newOptions, function(key,value) {
              $el.append($("<option></option>")
                 .attr("value", key).text(value));
            });

            $('#postsaint_settings_stable_diffusion_dezgo_model').val(defValOfModel);

            jQuery('#update-dezgo-models').replaceWith('Models updated!');   

          }
        });

    });

});