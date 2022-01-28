<?php
/*
Plugin Name: Restore Classic Widgets
Description: Description: Restore and enable the previous classic widgets settings screens and disables the Gutenberg block editor from managing widgets.
Version: 1.4
Text Domain: restoreclassic
Domain Path: /language
Author: Bill Minozzi
Author URI: http://billminozzi.com
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// Make sure the file is not directly accessible.
if (!defined('ABSPATH')) {
    die('We\'re sorry, but you can not directly access this file.');
}
$restoreclassic_plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$restoreclassic_plugin_version = $restoreclassic_plugin_data['Version'];
define('RESTORECLASSICPATH', plugin_dir_path(__file__));
define('RESTORECLASSICURL', plugin_dir_url(__file__));
define('RESTORECLASSICVERSION', $restoreclassic_plugin_version);
add_filter('gutenberg_use_widgets_block_editor', '__return_false');
add_filter('use_widgets_block_editor', '__return_false');
function wptools_custom_plugin_row_meta($links, $file)
{
    if (strpos($file, 'restore_classic_widgets.php') !== false) {
        if (rand(0, 1) == 0)
            $new_links['other'] = '<a id="restoreclassicmore" href="#" target="_blank"><b><font color="#FF6600">Click To Get More 35 Free Tools</font></b></a>';
        else
            $new_links['other'] = '<a id="restoreclassicmore" href="#" target="_blank"><b><font color="#FF3131">Click To Get More 35 Free Tools</font></b></a>';
        $links = array_merge($links, $new_links);
    }
    return $links;
}
if (is_admin() and !is_multisite() and !restore_classic_wptools_installed())
    add_filter('plugin_row_meta', 'wptools_custom_plugin_row_meta', 10, 2);
function restore_classic_wptools_installed()
{
    $slug = 'wptools';
    if ( ! function_exists( 'get_plugins' ) )
       require_once ABSPATH . 'wp-admin/includes/plugin.php'; 

    $all_plugins = get_plugins();
    foreach ($all_plugins as $key => $value) {
        $plugin_file = $key;
        $slash_position = strpos($plugin_file, '/');
        $folder = substr($plugin_file, 0, $slash_position);
        if ($slug == $folder) {
            return true;
        }
    }
    return false;
}
function restore_classic_load_upsell()
{
    if (is_admin()) {
        require_once(RESTORECLASSICPATH . "includes/more/more.php");
    }
}
add_action('wp_loaded', 'restore_classic_load_upsell');
add_action('wp_head', 'restoreclassic_ajaxurl');
add_action('wp_ajax_restoreclassic_install_wptools', 'restoreclassic_install_wptools');
function restoreclassic_ajaxurl()
{
    echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}
function restoreclassic_install_wptools()
{
    $slug = 'wptools';
    $plugin['source'] = 'repo'; // $_GET['plugin_source']; // Plugin source.
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api.
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes.
    // get plugin information
    $api = plugins_api('plugin_information', array('slug' => $slug, 'fields' => array('sections' => false)));
    if (is_wp_error($api)) {
        echo 'Fail error (-1)';
        wp_die();
        // proceed
    } else {
        // Set plugin source to WordPress API link if available.
        if (isset($api->download_link)) {
            $plugin['source'] = $api->download_link;
            $source =  $api->download_link;
        } else {
            echo 'Fail error (-2)';
            wp_die();
        }
        $nonce = 'install-plugin_' . $api->slug;
        /*
        $type = 'web';
        $url = $source;
        $title = 'wptools';
        */
        $plugin = $slug;
        // verbose...
        //    $upgrader = new Plugin_Upgrader($skin = new Plugin_Installer_Skin(compact('type', 'title', 'url', 'nonce', 'plugin', 'api')));
        class restoreclassic_QuietSkin extends \WP_Upgrader_Skin
        {
            public function feedback($string, ...$args)
            { /* no output */
            }
            public function header()
            { /* no output */
            }
            public function footer()
            { /* no output */
            }
        }
        $skin = new restoreclassic_QuietSkin(array('api' => $api));
        $upgrader = new Plugin_Upgrader($skin);
        // var_dump($upgrader);
        try {
            $upgrader->install($source);
            //	get all plugins
            $all_plugins = get_plugins();
            // scan existing plugins
            foreach ($all_plugins as $key => $value) {
                // get full path to plugin MAIN file
                // folder and filename
                $plugin_file = $key;
                $slash_position = strpos($plugin_file, '/');
                $folder = substr($plugin_file, 0, $slash_position);
                // match FOLDER against SLUG
                // if matched then ACTIVATE it
                if ($slug == $folder) {
                    // Activate
                    $result = activate_plugin(ABSPATH . 'wp-content/plugins/' . $plugin_file);
                    if (is_wp_error($result)) {
                        // Process Error
                        echo 'Fail error (-3)';
                        wp_die();
                    }
                } // if matched
            }
        } catch (Exception $e) {
            echo 'Fail error (-4)';
            wp_die();
        }
    } // activation
    echo 'OK';
    wp_die();
}