jQuery(document).ready(function($){
    //alert(pluginprefix_ajax_obj.ajax_url);
    $("#get_total_books").on('click', function(){
        $.post(pluginprefix_ajax_obj.ajax_url, {         //POST request
            _ajax_nonce: pluginprefix_ajax_obj.nonce,     //nonce
            action: "pluginprefix_ajax_example",            //action
            }, function(data) {                    //callback
                //alert(data);              //insert server response
                $('#books_response').html(data);
            }
        );
    });
});