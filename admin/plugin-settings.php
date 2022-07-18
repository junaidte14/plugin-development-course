<?php
   echo plugins_url('', __FILE__);
   echo '<br>';
   echo plugin_dir_url( __FILE__ );
   echo '<br>';
   echo plugin_dir_path( __FILE__ );
   echo '<br>';
   echo plugin_basename( __FILE__ );

   echo '<br>';
   echo PLUGINPREFIX_DIR_PATH;

   echo '<br>';
   echo PLUGINPREFIX_DIR_PATH . 'uninstall.php';
   echo '<br>';
   echo PLUGINPREFIX_DIR_PATH . 'includes/test.php';

   echo '<br>';
   echo '<br>';
   echo '<br>';
   echo PLUGINPREFIX_DIR_PATH;
   echo '<br>';
   echo PLUGINPREFIX_DIR_URL;
   echo '<br>';
   echo PLUGINPREFIX_URL;

   do_action( 'pluginprefix_after_settings_page_html' );

   //add_post_meta(154, 'my-custom-key', 'any custom value', true);

   //$response = update_post_meta(154, 'new-key', 'another value', true);

   $response = delete_post_meta(154, 'my-custom-key');
   var_dump($response);
?>

<h2>My Plugin Settings Page:</h2>
<p>Testing menu button functionality.</p>