<?php
//custom function to create a new user
/**
 * $username: username should be unique
 * $email: email address of the user should be unique
 * Return: it return the ID of the newly created user
 */
function pluginprefix_create_new_user($username, $email){
    // check if the username is taken
    $user_id = username_exists( $username ); //ID/False
    $user_email = email_exists( $email ); //ID/False

    if($user_id){
        return array('message' => 'The user ID already exists', 'result' => false);
    }

    if($user_email){
        return array('message' => 'The user email already exists', 'result' => false);
    }
    
    // check that the email address does not belong to a registered user
    if ( !$user_id && !$user_email) {
        // create a random password
        $random_password = wp_generate_password( 12, false );
        // create the user
        $user_id = wp_create_user(
            $username,
            $random_password,
            $email
        );

        if($user_id){
            return array('message' => 'The user is successfully created', 'result' => true);
        }
    }

}

/* $response = pluginprefix_create_new_user('newuser123', 'newuser123@gmail.com');
if($response['result']){
    echo $response['message'];
}else{
    echo $response['message'];
} */

$user_id = 3;
 
$user_id = wp_delete_user($user_id);
 
if ( is_wp_error( $user_id ) ) {
    echo 'The user is not deleted.';
} else {
    echo 'The user is deleted.';
}
?>

<h2>Dashboard:</h2>
<p>This is our test plugin to learn WordPress plugin development.</p>