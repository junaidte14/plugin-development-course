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
?>

<h2>My Plugin Settings Page:</h2>
<p>Testing menu button functionality.</p>