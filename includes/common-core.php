<?php

/**
 * Managing the translation from plugin folder
 */
add_action( 'init', 'pluginprefix_load_textdomain' );
function pluginprefix_load_textdomain() {
    load_plugin_textdomain( 'my-plugin', false, PLUGINPREFIX_LANG_PATH ); 
}

/**
 * Register the "book" custom post type
 */
function pluginprefix_setup_post_type() {
    $post_type_arguments = array(
        'labels'      => array(
            'name'          => __('Books', 'my-plugin'),
            'singular_name' => __('Book', 'my-plugin'),
            'add_new' => __('Add New Book', 'my-plugin'),
            'add_new_item' => __('Add New Book', 'my-plugin'),
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
        'name'              => _x( 'Book Categories', 'my-plugin' ),
        'singular_name'     => _x( 'Book Category', 'my-plugin' ),
        'search_items'      => __( 'Search Book Categories', 'my-plugin' ),
        'all_items'         => __( 'All Book Categories', 'my-plugin' ),
        'parent_item'       => __( 'Parent Book Category', 'my-plugin' ),
        'parent_item_colon' => __( 'Parent Book Category:', 'my-plugin' ),
        'edit_item'         => __( 'Edit Book Category', 'my-plugin' ),
        'update_item'       => __( 'Update Book Category', 'my-plugin' ),
        'add_new_item'      => __( 'Add New Book Category', 'my-plugin' ),
        'new_item_name'     => __( 'New Book Category Name', 'my-plugin' ),
        'menu_name'         => __( 'Book Category', 'my-plugin' ),
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
    $output .= '<br><button id="get_total_books">Get Total Books</button>
    <p id="books_response"></p><br><p>The following books count will be automatically updated!</p><h2 id="auto-books-count"></h2>';
    $output .= '</div>';
    return $output;
}

// shortcode for user registration page

//Defining custom shortcode
add_shortcode('pluginprefix_register', 'pluginprefix_register_shortcode');
function pluginprefix_register_shortcode( $atts = [], $content = null) {
    $output = '<div class="register-shortcode-content">';
    $output = '<p id="register-form-message"></p>';
    $output .= '<input type="text" name="username" id="username" placeholder="Add username here..." /><br><br>';
    $output .= '<input type="email" name="email" id="email" placeholder="Add email here..." /><br><br>';
    $output .= '<input type="password" name="password" id="password" placeholder="Add password here..." /><br><br>';
    $output .= '<input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirm password here..." /><br><br>';
    $output .= '<input type="submit" name="submit" id="register_form_submit" value="Submit" /><br><br>';
    if(!is_null($content)){
        $output .= apply_filters('register_shortcode_content', $content);
    }
    $output .= '</div>';
    return $output;
}

add_filter('register_shortcode_content', function($content){
    $content .= 'This text is displayed using custom filter hook';
    return $content;
});

//demo ajax action callback function

add_action( 'wp_ajax_pluginprefix_ajax_example', 'pluginprefix_ajax_handler' ); // action hook for logged in users
add_action( 'wp_ajax_nopriv_pluginprefix_ajax_example', 'pluginprefix_ajax_handler' ); // action hook for logged out users
 
/**
 * Handles my AJAX request.
 */
function pluginprefix_ajax_handler() {
    // Handle the ajax request here
    check_ajax_referer( 'pluginprefix_ajax_example' );
    //Task: Send the total number of books available in our custom post type
    $args = array(
        'post_type' => 'book',
        'posts_per_page' => -1
    );
    $the_query = new WP_Query( $args );
    $total_books = $the_query->post_count;
    wp_send_json(esc_html($total_books));
    wp_die(); // All ajax handlers die when finished
}

//user registration ajax handler function
add_action( 'wp_ajax_pluginprefix_ajax_user_register', 'pluginprefix_ajax_register_user' ); // action hook for logged in users
add_action( 'wp_ajax_nopriv_pluginprefix_ajax_user_register', 'pluginprefix_ajax_register_user' ); // action hook for logged out users
 
/**
 * Handles my AJAX request.
 */
function pluginprefix_ajax_register_user() {
    // Handle the ajax request here
    check_ajax_referer( 'pluginprefix_ajax_example' );
    //Task: Register the user with the values received from the frontend
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user_id = wp_create_user(
        $username,
        $password,
        $email
    );

    $response = '';
    if ( is_wp_error( $user_id ) ) {
        $error_string = $user_id->get_error_message();
        $response = 'There was an error creating the user. ' . $error_string;
    }else{
        $response = 'The user is successfully created.';
    }

    wp_send_json($response);
    wp_die(); // All ajax handlers die when finished
}

//testing heartbeat api
function pluginprefix_receive_heartbeat( array $response, array $data ) {
    
    if( array_key_exists('get-books-count', $data) && $data['get-books-count'] == 'latest_books_count'){
        $args = array(
            'post_type' => 'book',
            'posts_per_page' => -1
        );
        $the_query = new WP_Query( $args );
        $total_books = $the_query->post_count;
        $response['total_books'] = esc_html($total_books);
    }

    return $response;
}
add_filter( 'heartbeat_received', 'pluginprefix_receive_heartbeat', 10, 2 );

/**
 * Adds a privacy policy statement.
 */
function pluginprefix_add_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        return;
    }
    $content = '<p class="privacy-policy-tutorial">' . __( 'Testing privacy policy content for our plugin', 'my-plugin' ) . '</p>'
            . '<strong class="privacy-policy-tutorial">' . __( 'Demo Text:', 'my-plugin' ) . '</strong> '
            . '<p>' . __( 'Here you can add the privacy policy content related to your plugin.', 'my-plugin') . '</p>';
    wp_add_privacy_policy_content( 'My Plugin', wp_kses_post( wpautop( $content, false ) ) );
}
 
add_action( 'admin_init', 'pluginprefix_add_privacy_policy_content' );

//testing wp-cron api
add_filter( 'cron_schedules', 'pluginprefix_add_cron_interval' );
function pluginprefix_add_cron_interval( $schedules ) { 
    $schedules['sixty_five_seconds'] = array(
        'interval' => 65,
        'display'  => esc_html__( 'Every Sixty Five Seconds' ), 
    );
    return $schedules;
}

add_action( 'pluginprefix_cron_hook', 'pluginprefix_cron_exec' );
function pluginprefix_cron_exec(){
    // Create blog post every 65 seconds
    $my_post = array(
        'post_title'    => wp_strip_all_tags( 'Testing Cron Functionality'),
        'post_content'  => 'Testing WP-Cron API',
        'post_status'   => 'publish',
        'post_author'   => 1
    );
    
    // Insert the post into the database
    wp_insert_post( $my_post );
}
?>