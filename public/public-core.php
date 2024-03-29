<?php
/**
 * The code for public side of the plugin
 */

/**
 * Task: 
 * 1) Posts shown on frontend - edit button - redirect to the post edit screen
 * 2) Show the edit link only to the roles having the capability to edit the posts (edit_posts)
 * Scenarios to check user capability: 
 * 1) Conditionally show data to the user 2) save data to the database
 */

function pluginprefix_append_edit_button($content){
    if(!current_user_can( 'edit_posts' )){
        return $content;
    }

    $post_id = get_the_ID();
    $edit_screen_url = admin_url('post.php?post='.$post_id.'&action=edit');
    $content .= '<a href="'.esc_url($edit_screen_url).'">Edit</a>';
    return $content;
}
add_filter('the_content', 'pluginprefix_append_edit_button');

//enqueuing frontend js file
function pluginprefix_enqueue_public_files(){
    wp_enqueue_script('heartbeat');

    wp_enqueue_script(
        'public-script',
        PLUGINPREFIX_DIR_URL . '/public/js/public.js',
        array( 'jquery', 'heartbeat'),
        '1.0.0',
        true
    );

    wp_localize_script(
        'public-script',
        'pluginprefix_ajax_obj', //pluginprefix_ajax_obj.nonce
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'pluginprefix_ajax_example' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'pluginprefix_enqueue_public_files' );

?>