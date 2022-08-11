<?php
// admin specific code
/**
 * Activate the plugin.
 */
function pluginprefix_activate() { 
    // Trigger our function that registers the custom post type plugin.
    pluginprefix_setup_post_type(); 
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'pluginprefix_activate' );

/**
 * Deactivation hook.
 */
function pluginprefix_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'book' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pluginprefix_deactivate' );

//enqueuing admin js file
function pluginprefix_enqueue_admin_files(){
    wp_enqueue_script(
        'admin-script',
        PLUGINPREFIX_DIR_URL . '/admin/js/admin.js',
        array( 'jquery' ),
        '1.0.1',
        true
    );

    wp_localize_script(
        'admin-script',
        'pluginprefix_ajax_obj', //pluginprefix_ajax_obj.nonce
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'pluginprefix_ajax_example' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'pluginprefix_enqueue_admin_files' );

//Code for Menu Button

function pluginprefix_menu_content() {
    include PLUGINPREFIX_DIR_PATH . 'admin/plugin-dashboard.php';
}

// code to define menu button
add_action( 'admin_menu', 'pluginprefix_menu_button' );
function pluginprefix_menu_button() {
    add_menu_page(
        'My Plugin Title',
        'My Plugin',
        'manage_options',
        'myplugin',
        'pluginprefix_menu_content',
        'dashicons-admin-tools',
        60
    );

    add_submenu_page(
        'myplugin',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'myplugin',
        'pluginprefix_menu_content'
    );

    add_submenu_page(
        'myplugin',
        'Settings',
        'Settings',
        'manage_options',
        'submenu',
        'pluginprefix_add_settings_page_content'
    );
}

//callback function for our settings page content
function pluginprefix_add_settings_page_content(){
    include PLUGINPREFIX_DIR_PATH . 'admin/plugin-settings.php';
}

//using settings API
function pluginprefix_my_plugin_settings(){
    // register a new setting for "submenu" page
    register_setting('submenu', 'pluginprefix_my_custom_setting');
 
    // register a new section in the "submenu" page
    add_settings_section(
        'pluginprefix_settings_section',
        'My Plugin Settings Section', 'pluginprefix_settings_section_callback',
        'submenu'
    );
 
    // register a new field in the "pluginprefix_settings_section" section, inside the "submenu" page
    add_settings_field(
        'pluginprefix_settings_field',
        'My Plugin Setting', 'pluginprefix_settings_field_callback',
        'submenu',
        'pluginprefix_settings_section'
    );
}

add_action('admin_init', 'pluginprefix_my_plugin_settings');

function pluginprefix_settings_section_callback(){
    ?>
    <?php
}

function pluginprefix_settings_field_callback(){
    $setting = get_option('pluginprefix_my_custom_setting');
    ?>
    <input type="text" name="pluginprefix_my_custom_setting" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

/**
 * Learning how to add/save custom information into user's meta table
 */

function pluginprefix_usermeta_form_field_birthday( $user )
{
    ?>
    <h3>It's Your Birthday</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="birthday">Birthday</label>
            </th>
            <td>
                <input type="date"
                       class="regular-text ltr"
                       id="birthday"
                       name="birthday"
                       value="<?= esc_attr( get_user_meta( $user->ID, 'birthday', true ) ) ?>"
                       title="Please use YYYY-MM-DD as the date format."
                       pattern="(19[0-9][0-9]|20[0-9][0-9])-(1[0-2]|0[1-9])-(3[01]|[21][0-9]|0[1-9])"
                       required>
                <p class="description">
                    Please enter your birthday date.
                </p>
            </td>
        </tr>
    </table>
    <?php
}

function pluginprefix_usermeta_form_field_birthday_update( $user_id )
{
    // check that the current user have the capability to edit the $user_id
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
  
    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'birthday',
        $_POST['birthday']
    );
}
// Add the field to user's own profile editing screen.
add_action(
    'show_user_profile',
    'pluginprefix_usermeta_form_field_birthday'
);
  
// Add the field to user profile editing screen.
add_action(
    'edit_user_profile',
    'pluginprefix_usermeta_form_field_birthday'
);
  
// Add the save action to user's own profile editing screen update.
add_action(
    'personal_options_update',
    'pluginprefix_usermeta_form_field_birthday_update'
);
  
// Add the save action to user profile editing screen update.
add_action(
    'edit_user_profile_update',
    'pluginprefix_usermeta_form_field_birthday_update'
);

/**
 * Learning roles and capabilities
 */

function pluginprefix_simple_role() {
    add_role(
        'proof_reader',
        'Proof Reader',
        array(
            'read'         => true,
            'edit_posts'   => true,
            'upload_files' => true,
        ),
    );
}
 
// Add the simple_role.
add_action( 'init', 'pluginprefix_simple_role' );

/* function pluginprefix_simple_role_remove() {
    remove_role( 'proof_reader' );
}
 
// Remove the simple_role.
add_action( 'init', 'pluginprefix_simple_role_remove' ); */

function pluginprefix_simple_role_caps() {
    // Gets the simple_role role object.
    $role = get_role( 'proof_reader' );
 
    // Add a new capability.
    $role->add_cap( 'edit_others_posts', true );
}
 
// Add simple_role capabilities, priority must be after the initial role definition.
add_action( 'init', 'pluginprefix_simple_role_caps', 11 );

/**
 * Export user meta for a user using the supplied email.
 *
 * @param string $email_address   email address to manipulate
 * @param int    $page            pagination
 *
 * @return array
 */
function pluginprefix_export_user_data_by_email( $email_address, $page = 1 ) {
    $export_items = array();
 
    $user_data = get_user_by( 'email', $email_address );

    if($user_data == false){
        return;
    }

    $birthday = get_user_meta( $user_data->ID, 'birthday', true );
 
    // Only add birthday data to the export if it is not empty.
    if ( ! empty( $birthday ) ) {
        // Most item IDs should look like postType-postID. If you don't have a post, comment or other ID to work with,
        // use a unique value to avoid having this item's export combined in the final report with other items
        // of the same id.
        $item_id = "userbirthday-{$user_data->ID}";

        // Core group IDs include 'comments', 'posts', etc. But you can add your own group IDs as needed
        $group_id = 'user';

        // Optional group label. Core provides these for core groups. If you define your own group, the first
        // exporter to include a label will be used as the group label in the final exported report.
        $group_label = __( 'User', 'my-plugin' );

        // Plugins can add as many items in the item data array as they want.
        $data = array(
            array(
                'name'  => __( 'User Birthday', 'my-plugin' ),
                'value' => $birthday,
            )
        );

        $export_items[] = array(
            'group_id'    => $group_id,
            'group_label' => $group_label,
            'item_id'     => $item_id,
            'data'        => $data,
        );
    }
 
    // Tell core if we have more comments to work on still.
    $done = true;
    return array(
        'data' => $export_items,
        'done' => $done,
    );
}

function pluginprefix_register_user_data_exporters( $exporters ) {
    $exporters['my-plugin'] = array(
        'exporter_friendly_name' => __( 'My Plugin Birthday Info', 'my-plugin' ),
        'callback'               => 'pluginprefix_export_user_data_by_email',
    );
    return $exporters;
}
 
add_filter( 'wp_privacy_personal_data_exporters', 'pluginprefix_register_user_data_exporters' );

/**
 * Removes any stored location data from a user's comment meta for the supplied email address.
 *
 * @param string $email_address   email address to manipulate
 * @param int    $page            pagination
 *
 * @return array
 */
function pluginprefix_remove_location_meta_from_comments_for_email( $email_address, $page = 1 ) {
 
    $user_data = get_user_by( 'email', $email_address );

    if($user_data == false){
        return;
    }

    $birthday = get_user_meta( $user_data->ID, 'birthday', true );

    $items_removed = false;
 
    if ( ! empty( $birthday ) ) {
        delete_user_meta( $user_data->ID, 'birthday' );
        $items_removed = true;
    }
 
    // Tell core if we have more comments to work on still
    $done = true;
    return array(
        'items_removed'  => $items_removed,
        'items_retained' => false, // always false in this example
        'messages'       => array(), // no messages in this example
        'done'           => $done,
    );
}

function pluginprefix_register_privacy_erasers( $erasers ) {
    $erasers['my-plugin'] = array(
        'eraser_friendly_name' => __( 'My Plugin', 'my-plugin' ),
        'callback'             => 'pluginprefix_remove_location_meta_from_comments_for_email',
    );
    return $erasers;
}
 
add_filter( 'wp_privacy_personal_data_erasers', 'pluginprefix_register_privacy_erasers' );
?>