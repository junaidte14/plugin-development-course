<?php
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
        //custom hook to define custom settings on this page
        do_action( 'pluginprefix_after_settings_page_html' );
        // output save settings button
        submit_button( 'Save Settings' );
        ?>
    </form>
</div>