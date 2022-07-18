<?php

/**
 * Register the "book" custom post type
 */
function pluginprefix_setup_post_type() {
    $post_type_arguments = array(
        'labels'      => array(
            'name'          => __('Books', 'pluginprefix'),
            'singular_name' => __('Book', 'pluginprefix'),
            'add_new' => __('Add New Book', 'pluginprefix'),
            'add_new_item' => __('Add New Book', 'pluginprefix'),
        ),
        'public'      => true,
        'has_archive' => true,
        'rewrite'     => array( 'slug' => 'codo_books' ),
    );
    register_post_type( 'book',  apply_filters('pluginprefix_book_post_type_arguments', $post_type_arguments)); 
} 
add_action( 'init', 'pluginprefix_setup_post_type' );

/**Changing the main query to display custom post types as well */

function pluginprefix_add_custom_post_types($query) {
    if ( is_home() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'post', 'book' ) );
    }
    return $query;
}
add_action('pre_get_posts', 'pluginprefix_add_custom_post_types');

//adding demo text using custom hook
function pluginprefix_add_content_using_custom_hook(){
    ?>
    <h2>This text is displayed using the custom hook.</h2>
    <?php
}
add_action('pluginprefix_after_settings_page_html', 'pluginprefix_add_content_using_custom_hook');

function pluginprefix_custom_box_html($post){
    //var_dump($post);
    $current_book_author = get_post_meta( $post->ID, '_pluginprefix_book_author', true );
    ?>
    <label for="pluginprefix_book_author">Book Author Name:</label>
    <input name="pluginprefix_book_author" id="pluginprefix_book_author" value="<?php echo $current_book_author;?>" />
    <?php
}

function pluginprefix_add_meta_box(){
    add_meta_box(
        'pluginprefix_box_id',                 // Unique ID
        'Custom Meta Box Title',      // Box title
        'pluginprefix_custom_box_html',  // Content callback, must be of type callable
        'book'                            // Post type
    );
}
add_action('add_meta_boxes', 'pluginprefix_add_meta_box');

function pluginprefix_save_postdata( $post_id ) {
    if ( array_key_exists( 'pluginprefix_book_author', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_pluginprefix_book_author',
            $_POST['pluginprefix_book_author']
        );
    }
}
add_action( 'save_post', 'pluginprefix_save_postdata' );
?>