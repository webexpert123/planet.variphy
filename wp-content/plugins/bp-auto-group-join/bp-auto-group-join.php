<?php
/**
 * Plugin Name: BP Auto Group Join
 * Plugin URI:  https://wordpress.org/plugins/bp-auto-group-join/
 * Description: Automatically join BuddyPress members to Groups
 * Author:      BuddyBoss
 * Author URI:  http://buddyboss.com
 * Version:     1.0.4
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * ========================================================================
 * CONSTANTS
 * ========================================================================
 */
// Codebase version
if ( ! defined( 'BP_AUTO_GROUP_JOIN_PLUGIN_VERSION' ) ) {
	define( 'BP_AUTO_GROUP_JOIN_PLUGIN_VERSION', '1.0.4' );
}

// Database version
if ( ! defined( 'BP_AUTO_GROUP_JOIN_PLUGIN_DB_VERSION' ) ) {
	define( 'BP_AUTO_GROUP_JOIN_PLUGIN_DB_VERSION', 1 );
}

// Directory
if ( ! defined( 'BP_AUTO_GROUP_JOIN_PLUGIN_DIR' ) ) {
	define( 'BP_AUTO_GROUP_JOIN_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if ( ! defined( 'BP_AUTO_GROUP_JOIN_PLUGIN_URL' ) ) {
	$plugin_url = plugin_dir_url( __FILE__ );

	// If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
	if ( is_ssl() )
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );

	define( 'BP_AUTO_GROUP_JOIN_PLUGIN_URL', $plugin_url );
}

// File
if ( ! defined( 'BP_AUTO_GROUP_JOIN_PLUGIN_FILE' ) ) {
	define( 'BP_AUTO_GROUP_JOIN_PLUGIN_FILE', __FILE__ );
}

/**
 * ========================================================================
 * MAIN FUNCTIONS
 * ========================================================================
 */

/**
 * Main
 *
 * @return void
 */
add_action( 'plugins_loaded', 'BP_AUTO_GROUP_JOIN_init' );

function BP_AUTO_GROUP_JOIN_init() {
	if ( ! function_exists( 'bp_is_active' ) ) {
		add_action('admin_notices','bb_auto_group_admin_notice');
		return;
	}

	global $BP_AUTO_GROUP_JOIN;

	$main_include = BP_AUTO_GROUP_JOIN_PLUGIN_DIR . 'includes/main-class.php';

	try {
		if ( file_exists( $main_include ) ) {
			require( $main_include );
		} else {
			$msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'bp-auto-group-join' ), $main_include );
			throw new Exception( $msg, 404 );
		}
	} catch ( Exception $e ) {
		$msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'bp-auto-group-join' ), $e->getMessage() );
		echo $msg;
	}

	$BP_AUTO_GROUP_JOIN = BP_AUTO_GROUP_JOIN_Plugin::instance();
}

/**
 * Must be called after hook 'plugins_loaded'
 * @return BP Auto Group Join Plugin main controller object
 */
function bp_auto_group_join() {

	global $BP_AUTO_GROUP_JOIN;
	return $BP_AUTO_GROUP_JOIN;

}

function bb_auto_group_admin_notice() { ?>
	<div class='error'><p>
			<?php _e( 'BuddyBoss Auto Group Join needs BuddyPress activated!', 'bp-auto-group-join' ); ?>
		</p></div>
	<?php
}
