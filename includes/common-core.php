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
        'taxonomies' => array('ebook-category')
    );
    register_post_type( 'book',  apply_filters('pluginprefix_book_post_type_arguments', $post_type_arguments)); 
} 
add_action( 'init', 'pluginprefix_setup_post_type' );

//code to define a custom taxonomy

function pluginprefix_register_taxonomy_ebooks_categories() {
    $labels = array(
        'name'              => _x( 'Book Categories', 'pluginprefix' ),
        'singular_name'     => _x( 'Book Category', 'pluginprefix' ),
        'search_items'      => __( 'Search Book Categories' ),
        'all_items'         => __( 'All Book Categories' ),
        'parent_item'       => __( 'Parent Book Category' ),
        'parent_item_colon' => __( 'Parent Book Category:' ),
        'edit_item'         => __( 'Edit Book Category' ),
        'update_item'       => __( 'Update Book Category' ),
        'add_new_item'      => __( 'Add New Book Category' ),
        'new_item_name'     => __( 'New Book Category Name' ),
        'menu_name'         => __( 'Book Category' ),
    );
    $args   = array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'book-category' ],
    );
    register_taxonomy( 'book-category', [ 'book' ], $args );
}
add_action( 'init', 'pluginprefix_register_taxonomy_ebooks_categories' );

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
    <h2><?php esc_html_e('This text is displayed using the custom hook.', 'my-plugin');?></h2>
    <?php
}
add_action('pluginprefix_after_settings_page_html', 'pluginprefix_add_content_using_custom_hook');

function pluginprefix_custom_box_html($post){
    //var_dump($post);
    $current_book_author = get_post_meta( $post->ID, '_pluginprefix_book_author', true );

    $current_book_author_email = get_post_meta( $post->ID, '_pluginprefix_book_author_email', true );

    $current_book_type = get_post_meta( $post->ID, '_pluginprefix_book_type', true );

    $current_book_downloadable = get_post_meta( $post->ID, '_pluginprefix_book_downloadable', true );

    $metabox_nonce = wp_create_nonce( 'pluginprefix_book_metabox' );

    ?>
    <label for="pluginprefix_book_author"><?php esc_html_e('Book Author Name:', 'my-plugin');?></label>
    <input name="pluginprefix_book_author" id="pluginprefix_book_author" value="<?php echo esc_attr($current_book_author);?>" type="text" />
    <br><br>
    <label for="pluginprefix_book_author_email"><?php esc_html_e('Book Author Email:', 'my-plugin');?></label>
    <input name="pluginprefix_book_author_email" id="pluginprefix_book_author_email" type="email" value="<?php echo esc_attr($current_book_author_email);?>" />
    <p><?php esc_html_e('Please enter a valid Email Address, otherwise it will not be saved.', 'my-plugin');?></p>
    <label for="pluginprefix_book_type">The book is available in</label>
    <select name="pluginprefix_book_type" id="pluginprefix_book_type">
        <option value="" <?php selected($current_book_type, "");?>>Select Option</option>
        <option value="pdf" <?php selected($current_book_type, "pdf");?>>PDF</option>
        <option value="worddoc" <?php selected($current_book_type, "worddoc");?>>Word Document</option>
        <option value="both" <?php selected($current_book_type, "both");?>>Both</option>
    </select>
    <br><br>
    <label for="pluginprefix_book_downloadable">The Book is Downloadable?</label>
    <input type="checkbox" name="pluginprefix_book_downloadable" id="pluginprefix_book_downloadable" <?php if($current_book_downloadable == 'on'){echo 'checked="checked"';}?>/>
    <input type="hidden" name="pluginprefix_book_metabox" id="pluginprefix_book_metabox" value="<?php echo esc_attr($metabox_nonce);?>" />
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
    //var_dump(wp_verify_nonce( $_POST['pluginprefix_book_metabox'], 'pluginprefix_book_metabox' )); die();
    if ( !array_key_exists( 'pluginprefix_book_metabox', $_POST ) || !wp_verify_nonce( $_POST['pluginprefix_book_metabox'], 'pluginprefix_book_metabox' ) ) {
        return;
    }

    if ( array_key_exists( 'pluginprefix_book_author', $_POST ) ) {
        $book_author = sanitize_text_field($_POST['pluginprefix_book_author']);
        update_post_meta(
            $post_id,
            '_pluginprefix_book_author',
            $book_author
        );
    }

    if ( array_key_exists( 'pluginprefix_book_author_email', $_POST ) ) {
        if(is_email($_POST['pluginprefix_book_author_email'])){
            $author_email = sanitize_email($_POST['pluginprefix_book_author_email']);
            update_post_meta(
                $post_id,
                '_pluginprefix_book_author_email',
                $author_email
            );
        }
    }

    if ( array_key_exists( 'pluginprefix_book_type', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_pluginprefix_book_type',
            $_POST['pluginprefix_book_type']
        );
    }

    $current_checkbox_value = ($_POST['pluginprefix_book_downloadable']) ? $_POST['pluginprefix_book_downloadable'] : 'off';
    //echo $current_checkbox_value; die();

    update_post_meta(
        $post_id,
        '_pluginprefix_book_downloadable',
        $current_checkbox_value
    );
}
add_action( 'save_post', 'pluginprefix_save_postdata' );

//Defining custom shortcode
add_shortcode('pluginprefix_myshortcode', 'pluginprefix_shortcode');
function pluginprefix_shortcode( $atts = [], $content = null) {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

    // override default attributes with user attributes
    $wporg_atts = shortcode_atts(
        array(
            'title' => 'My Plugin',
            'subtitle' => 'This is Subtitle'
        ), $atts, $tag
    );


    $output = '<div class="my-shortcode-content">';
    $output .= '<h2>' . $wporg_atts['title'] . '</h2>';
    $output .= '<h4>' . $wporg_atts['subtitle'] . '</h4>';
    if(!is_null($content)){
        $output .= apply_filters('the_content', $content);
    }
    $output .= '</div>';
    return $output;
}

?>