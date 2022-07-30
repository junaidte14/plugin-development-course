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
        '1.0.0',
        true
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
?>