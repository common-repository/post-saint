jQuery( function($) {       
    $('#clear-logs').click( function( event ) {
        if( ! confirm( 'Are you sure you want to delete the logs?' ) ) {
            event.preventDefault();
        }           
    });
});