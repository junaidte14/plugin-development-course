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

    //handle the click event on the user registration form submit button
    $("#register_form_submit").on('click', function(){
        let username = $('#username').val();
        let email = $('#email').val();
        let password = $('#password').val();
        let confirmpassword = $('#confirmpassword').val();
        if(password !== confirmpassword){
            $('#register-form-message').html('The passwords does not match!');
            return;
        }

        //console.log(formData);
        //console.log(pluginprefix_ajax_obj);
        $.post(pluginprefix_ajax_obj.ajax_url, {         //POST request
            _ajax_nonce: pluginprefix_ajax_obj.nonce,     //nonce
            action: "pluginprefix_ajax_user_register",            //action
            username: username,
            email: email,
            password: password,
        }, function(result) {                    //callback
                //alert(result);              //insert server response
                $('#register-form-message').html(result);
            }
        );
    });

    jQuery( document ).on( 'heartbeat-send', function ( event, data ) {
        console.log('The send event is fired');
        // Add additional data to Heartbeat data.
        data['get-books-count'] = 'latest_books_count';
    });

    jQuery( document ).on( 'heartbeat-tick', function ( event, data ) {
        console.log('The tick event is fired.');
        if(!data['total_books']){
            return;
        }

        $('#auto-books-count').html('The total number of books are: ' + data['total_books']);
    });
});