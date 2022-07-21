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

//Code for Menu Button

function pluginprefix_menu_content() {
    include PLUGINPREFIX_DIR_PATH . 'admin/plugin-settings.php';
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
        'pluginprefix_add_submenu_page_content'
    );
}

function pluginprefix_add_submenu_page_content(){
    // check user capabilities
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }

    // WordPress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'pluginprefix_messages', 'pluginprefix_message', __( 'Settings Saved', 'pluginprefix' ), 'updated' );
    }

    // show error/update messages
    settings_errors( 'pluginprefix_messages' );
    ?>
    <div class="wrap">
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "submenu"
            settings_fields( 'submenu' );
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections( 'submenu' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
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

?>