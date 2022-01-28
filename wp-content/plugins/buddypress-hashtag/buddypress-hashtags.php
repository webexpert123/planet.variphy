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
 * @package           Buddypress_Hashtags
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Hashtags
 * Plugin URI:        https://wbcomdesigns.com/buddypress-hashtags
 * Description:       The plugin gives the ability to use hashtags on any buddypress,bbpress and wordpress posts and pages.
 * Version:           2.5.1
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-hashtags
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
if ( ! defined( 'BPHT_PLUGIN_VERSION' ) ) {
	define( 'BPHT_PLUGIN_VERSION', '2.5.1' );
}
if ( ! defined( 'BPHT_PLUGIN_FILE' ) ) {
	define( 'BPHT_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BPHT_PLUGIN_BASENAME' ) ) {
	define( 'BPHT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BPHT_PLUGIN_URL' ) ) {
	define( 'BPHT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BPHT_PLUGIN_PATH' ) ) {
	define( 'BPHT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-hashtags.php';

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
function run_buddypress_hashtags() {

	$plugin = new Buddypress_Hashtags();
	$plugin->run();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'buddypress_hashtags_plugin_links' );
	
}

add_action( 'plugins_loaded', 'bpht_loaded', 9 );
function bpht_loaded() {
	
	if ( has_action( 'bp_loaded' ) ) {
		add_action( 'bp_include', 'run_buddypress_hashtags' );
	} else if( has_action( 'bbp_loaded' ) ) {
		add_action( 'bbp_includes', 'run_buddypress_hashtags' );		
	}
}
/**
 *
 * Function to create table on plugin registration.
 *
 */

register_activation_hook( __FILE__, 'bpht_create_hashtag_table' );
function bpht_create_hashtag_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bpht_hashtags';

	$bpht_charset = $wpdb->get_charset_collate();
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$bpht_sql = "CREATE TABLE $table_name (ht_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,ht_name varchar(128),ht_type varchar(28),ht_count bigint(20) UNSIGNED NULL DEFAULT '0',ht_last_count TIMESTAMP DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (ht_id),UNIQUE INDEX ( `ht_name`, `ht_type` )) $bpht_charset;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $bpht_sql );
	}
}

function buddypress_hashtags_plugin_links( $links ) {
	$bp_hashtag_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress_hashtags' ) . '">' . esc_html__( 'Settings', 'buddypress-hashtags' ) . '</a>',
	);
	return array_merge( $links, $bp_hashtag_links );
}


/**
 * redirect to plugin settings page after activated
 */

add_action( 'activated_plugin', 'buddypress_hashtags_activation_redirect_settings' );
function buddypress_hashtags_activation_redirect_settings( $plugin ){

	if( $plugin == plugin_basename( __FILE__ ) ) {
		wp_redirect( admin_url( 'admin.php?page=buddypress_hashtags' ) ) ;
		exit;
	}
}
