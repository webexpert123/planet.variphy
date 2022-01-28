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
 * @package           Buddypress_Status
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Reactions and Status
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       The BuddyPress Reactions and Status plugin lets user add reactions to buddypress activities and set icons to appear beside their username at profile page.
 * Version:           1.9.0
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-status
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
if( !defined( 'BPSTS_PLUGIN_VERSION' ) ) {
	define( 'BPSTS_PLUGIN_VERSION', '1.9.0' );
}

if ( ! defined( 'BPSTS_PLUGIN_FILE' ) ) {
	define( 'BPSTS_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BPSTS_PLUGIN_BASENAME' ) ) {
	define( 'BPSTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BPSTS_PLUGIN_URL' ) ) {
	define( 'BPSTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BPSTS_PLUGIN_PATH' ) ) {
	define( 'BPSTS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

register_activation_hook( __FILE__, 'activate_buddypress_status' );
function activate_buddypress_status(){
	update_option( 'bpsts_gnrl_settings', array( 'act_loop_dis_icon' => 'yes', 'prof_pg_dis_icon' => 'yes', 'reaction_dis_icon'=> 'yes'));
	$bpsts_icon_settings = get_option( 'bpsts_icon_settings' );
	if ( !empty($bpsts_icon_settings) ) {
		$bpsts_icon_settings['iconsets'][] = 'set_mood';
		$bpsts_icon_settings['iconsets'][] = 'set_activity';
		$bpsts_icon_settings['iconsets'][] = 'set_food';
		$bpsts_icon_settings['iconsets'][] = 'set_diversity';
		$bpsts_icon_settings['iconsets'][] = 'set_custom';
		update_option( 'bpsts_icon_settings', $bpsts_icon_settings );
	} else {
		$bpsts_icon_settings['iconsets'][] = 'set_mood';
		$bpsts_icon_settings['iconsets'][] = 'set_activity';
		$bpsts_icon_settings['iconsets'][] = 'set_food';
		$bpsts_icon_settings['iconsets'][] = 'set_diversity';
		$bpsts_icon_settings['iconsets'][] = 'set_custom';

		$bpsts_icon_settings['reactions'] = array(
			'mood-54' => array(
				'imgname' => 'smiling-face-with-heart-eyes.png',
				'folder'  => 'mood'
			),
			'diversity-24' => array(
				'imgname' => 'selfie-light-skin-tone.png',
				'folder'  => 'diversity'
			),
			'diversity-8' => array(
				'imgname' => 'crossed-fingers-light-skin-tone.png',
				'folder'  => 'diversity'
			),
			'activity-21' => array(
				'imgname' => 'sports-medal.png',
				'folder'  => 'activity'
			),
			'diversity-10' => array(
				'imgname' => 'folded-hands-light-skin-tone.png',
				'folder'  => 'diversity'
			),
			'mood-61' => array(
				'imgname' => 'star-struck.png',
				'folder'  => 'mood'
			),
		);
		update_option( 'bpsts_icon_settings', $bpsts_icon_settings );
	}
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-status.php';

/**
 * Require plugin license file.
 */
require plugin_dir_path( __FILE__ ) . 'edd-license/edd-plugin-license.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_status() {

	$plugin = new Buddypress_Status();
	$plugin->run();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpsts_plugin_links' );

}
add_action( 'bp_include', 'run_buddypress_status' );

function bpsts_plugin_links( $links ) {
	$bpsts_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress_status' ) . '">' . __( 'Settings', 'buddypress-status' ) . '</a>',
	);
	return array_merge( $links, $bpsts_links );
}

/**
 * Function to check buddypress is active to enable disable plugin functionality.
 */
add_action( 'plugins_loaded', 'bpsts_plugin_init' );
function bpsts_plugin_init() {
    if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
    }
    if ( ! is_plugin_active_for_network( 'buddypress/bp-loader.php' ) && ! in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_action( 'admin_notices', 'bpsts_plugin_admin_notice' );

    }
}

/**
* Check if buddypress activated or not
**/
function bpsts_check_buddypress(){
	if( !class_exists( 'BuddyPress' ) ){
		add_action( 'admin_notices', 'bpsts_plugin_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}
add_action( 'admin_init', 'bpsts_check_buddypress' );
/**
 * Function to show admin notice when BuddyPress is deactivate.
 */
function bpsts_plugin_admin_notice() {
    $plugin = esc_html('BuddyPress Reactions and Status');
    $bp_plugin   = esc_html('BuddyPress');

    echo '<div class="error"><p>'
        . sprintf( __( '%1$s is ineffective as it requires %2$s to be installed and active.', 'buddypress-status' ), '<strong>' . $plugin . '</strong>', '<strong>' . $bp_plugin . '</strong>' )
        . '</p></div>';
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }
}

/**
 * redirect to plugin settings page after activated
 */

add_action( 'activated_plugin', 'bpsts_activation_redirect_settings' );
function bpsts_activation_redirect_settings( $plugin ){

	if( $plugin == plugin_basename( __FILE__ ) ) {
		wp_redirect( admin_url( 'admin.php?page=buddypress_status' ) ) ;
		exit;
	}
}
