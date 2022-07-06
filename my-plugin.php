<?php

/**
 * Plugin Name: My Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       A dummy test plugin for learning.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Junaid Hassan
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       my-plugin
 * Domain Path:       /languages
 */

/*
My Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
My Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with My Plugin. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if(!defined('PLUGINPREFIX_DIR_PATH')){
    define('PLUGINPREFIX_DIR_PATH', plugin_dir_path(__FILE__));
}

if(!defined('PLUGINPREFIX_DIR_URL')){
    define('PLUGINPREFIX_DIR_URL', plugin_dir_url(__FILE__));
}

if(!defined('PLUGINPREFIX_URL')){
    define('PLUGINPREFIX_URL', plugins_url('', __FILE__));
}

if ( is_admin() ) {
    // we are in admin mode
    include PLUGINPREFIX_DIR_PATH . 'admin/admin-core.php';
}else{
    // code which will run only on frontend
    
}

