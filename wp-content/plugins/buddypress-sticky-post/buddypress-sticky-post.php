<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Buddypress_Sticky_Post
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Sticky Post
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin helps in making a buddypress post featured on the activity stream page list so that it always appears on top.
 * Version:           1.9.5
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-sticky-post
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BPSP_NAME_VERSION', '1.9.5' );
define( 'BPSP_DIR', dirname( __FILE__ ) );
define( 'BPSP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BPSP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BP_STICKY_POSTS_PLUGIN_BASENAME',  plugin_basename( __FILE__ ) );
if ( ! defined( 'BPSP_PLUGIN_FILE' ) ) {
	define( 'BPSP_PLUGIN_FILE', __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-sticky-post-activator.php
 */
function activate_buddypress_sticky_post() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-sticky-post-activator.php';
	Buddypress_Sticky_Post_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-sticky-post-deactivator.php
 */
function deactivate_buddypress_sticky_post() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-sticky-post-deactivator.php';
	Buddypress_Sticky_Post_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_sticky_post' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_sticky_post' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-sticky-post.php';

/**
 * The license code file.
 */
require plugin_dir_path(__FILE__) . 'edd-license/edd-plugin-license.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_sticky_post() {

	$plugin = new Buddypress_Sticky_Post();
	$plugin->run();

}
run_buddypress_sticky_post();

add_action( 'bp_init', 'bpquotes_reign_restrict_hearbeat_for_bp' );
/**
*
* Function to restrict heartbeat request being send on non required bp pages.
*
*/
function bpquotes_reign_restrict_hearbeat_for_bp() {
   //if ( bp_is_activity_component() ) {
       remove_filter( 'heartbeat_received', 'bp_activity_heartbeat_last_recorded', 10, 2 );
       remove_filter( 'heartbeat_nopriv_received', 'bp_activity_heartbeat_last_recorded', 10, 2 );
   //}
}

/**
 *  Check if buddypress activate.
 */
function bpsp_requires_buddypress()
{

    if ( !class_exists( 'Buddypress' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        //deactivate_plugins('buddypress-polls/buddypress-polls.php');
        add_action( 'admin_notices', 'bpsp_required_plugin_admin_notice' );
        unset($_GET['activate']);
    }
}

add_action( 'admin_init', 'bpsp_requires_buddypress' );
/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  1.2.0
 */
function bpsp_required_plugin_admin_notice()
{

    $bpquotes_plugin          = esc_html__('BuddyPress Sticky Post', 'buddypress-sticky-post');
    $bp_plugin                = esc_html__('BuddyPress', 'buddypress-sticky-post');
    echo '<div class="error"><p>';
    echo sprintf(esc_html__('%1$s is ineffective now as it requires %2$s to be installed and active.', 'buddypress-sticky-post'), '<strong>' . esc_html($bpquotes_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>');
    echo '</p></div>';
    if (isset($_GET['activate']) ) {
        unset($_GET['activate']);
    }
}


/**
 * redirect to plugin settings page after activated
 */

add_action( 'activated_plugin', 'bpsp_activation_redirect_settings' );
function bpsp_activation_redirect_settings( $plugin ){

	if( $plugin == plugin_basename( __FILE__ ) ) {
		wp_redirect( admin_url( 'admin.php?page=buddypress-sticky-post' ) ) ;
		exit;
	}
}
