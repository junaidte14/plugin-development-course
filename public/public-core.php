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
    $content .= '<a href="'.$edit_screen_url.'">Edit</a>';
    return $content;
}
add_filter('the_content', 'pluginprefix_append_edit_button');
?>