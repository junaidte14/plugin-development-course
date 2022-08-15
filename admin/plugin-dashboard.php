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

/* $user_id = 3;
$user_id = wp_delete_user($user_id);
 
if ( is_wp_error( $user_id ) ) {
    echo 'The user is not deleted.';
} else {
    echo 'The user is deleted.';
} */

/* $username  = 'mynewuser';
$password  = '12345';
$website   = 'https://codoplex.com';
$first_name = 'Junaid';
$last_name = 'Hassan';
$user_data = [
    'user_login' => $username,
    'user_pass'  => $password,
    'user_url'   => $website,
    'first_name' => $first_name,
    'last_name' => $last_name
];
 
$user_id = wp_insert_user( $user_data );
 
// success
if ( ! is_wp_error( $user_id ) ) {
    echo 'User created: ' . $user_id;
} */

/**
 * Testing user meta functions
 */

/* $response = add_user_meta(
    1,
    'test_user_meta',
    'Hello World!',
    true
);

if(!$response){
    echo 'The user meta is not added.';
}else{
    echo 'The user meta is added with ID: '. $response;
} */

/* $response = delete_user_meta(
    1,
    'test_user_meta',
    'Hello World!'
);

if($response){
    echo 'The user meta is successfully deleted.';
}else{
    echo 'The user meta is not deleted.';
} */

/**
 * Testing roles and capabilities
 */
	
/* $role = get_role( 'proof_reader' );
var_dump($role); */

/* delete_transient( 'pluginprefix_github_userinfo' );

$response = get_transient( 'pluginprefix_github_userinfo' );
var_dump($response);
if ( false === $response ) {
    // Transient expired, refresh the data
    $response = wp_remote_get( 'https://api.github.com/users/junaidte14' );
    set_transient( 'pluginprefix_github_userinfo', $response, 60 * 60 );
}

var_dump($response); */

/* $body     = wp_remote_retrieve_body( $response );
$http_code = wp_remote_retrieve_response_code( $response );
$last_modified = wp_remote_retrieve_header( $response, 'last-modified' );
var_dump($last_modified);
$headers = wp_remote_retrieve_headers($response);
var_dump($headers);

	
$response_head = wp_remote_head( 'https://api.github.com/users/junaidte14' );
var_dump($response_head); */

/* if ( ! wp_next_scheduled( 'pluginprefix_cron_hook' ) ) {
    wp_schedule_event( time(), 'sixty_five_seconds', 'pluginprefix_cron_hook' );
}

$scheduled_events = _get_cron_array();
var_dump($scheduled_events); */

/* $schedules = wp_get_schedules();
var_dump($schedules); */
?>

<h2><?php _e( 'Dashboard:' , 'my-plugin'); ?></h2>
<p><?php _e( 'This is our test plugin to learn WordPress plugin development.' , 'my-plugin'); ?></p>

<button id="get_total_books"><?php _e( 'Get Total Books' , 'my-plugin'); ?></button>
<p id="books_response"></p>