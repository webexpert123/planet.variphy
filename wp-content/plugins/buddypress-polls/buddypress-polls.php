<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.wbcomdesigns.com
 * @since             1.0.0
 * @package           Buddypress_Polls
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs – BuddyPress Polls
 * Plugin URI:        http://www.wbcomdesigns.com/plugins/
 * Description:       BuddyPress Polls plugin allows you and your community to create polls in posts. The polls can be placed in the main activity stream, in users’ profiles and even in groups.
 * Version:           3.7.1
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-polls
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
if( ! defined( 'BPOLLS_PLUGIN_VERSION' ) )
	define( 'BPOLLS_PLUGIN_VERSION', '3.7.1' );

if( ! defined( 'BPOLLS_PLUGIN_FILE' ) )
	define( 'BPOLLS_PLUGIN_FILE', __FILE__ );

if( ! defined( 'BPOLLS_PLUGIN_URL' ) )
	define( 'BPOLLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if( ! defined( 'BPOLLS_PLUGIN_PATH' ) )
	define( 'BPOLLS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
if( ! defined( 'BPOLLS_PLUGIN_BASENAME' ) )
	define( 'BPOLLS_PLUGIN_BASENAME',  plugin_basename( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-polls-activator.php
 */
function activate_buddypress_polls() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	}
	global $wp_roles;
	$bpolls_settings['limit_poll_activity'] = 'no';
	$roles = $wp_roles->get_names();
	foreach( $roles as $role => $role_name ) {
		$bpolls_settings['poll_user_role'][] = $role;
	}	
	update_option('bpolls_settings', $bpolls_settings);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-polls-deactivator.php
 */
function deactivate_buddypress_polls() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	}
}

register_activation_hook( __FILE__, 'activate_buddypress_polls' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_polls' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-polls.php';

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
function run_buddypress_polls() {

	$plugin = new Buddypress_Polls();
	$plugin->run();

}

add_action( 'bp_include', 'bpolls_plugin_init' );
/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */
function bpolls_plugin_init() {
	if ( bp_polls_check_config() ){
		run_buddypress_polls();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpolls_plugin_links' );
	}
}
/**
 * Function to check configurations.
 */
function bp_polls_check_config(){
	global $bp;
	$check =  array();
	$config = array(
		'blog_status'    => false,
		'network_active' => false,
		'network_status' => true
	);
	if ( get_current_blog_id() == bp_get_root_blog_id() ) {
		$config['blog_status'] = true;
	}

	$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

	// No Network plugins
	if ( empty( $network_plugins ) )

	// Looking for BuddyPress and bp-activity plugin
	$check[] = $bp->basename;
	$check[] = BPOLLS_PLUGIN_BASENAME;

	// Are they active on the network ?
	$network_active = array_diff( $check, array_keys( $network_plugins ) );

	// If result is 1, your plugin is network activated
	// and not BuddyPress or vice & versa. Config is not ok
	if ( count( $network_active ) == 1 )
		$config['network_status'] = false;

	// We need to know if the plugin is network activated to choose the right
	// notice ( admin or network_admin ) to display the warning message.
	$config['network_active'] = isset( $network_plugins[ BPOLLS_PLUGIN_BASENAME ] );

	// if BuddyPress config is different than bp-activity plugin
	if ( !$config['blog_status'] || !$config['network_status'] ) {

		$warnings = array();
		if ( !bp_core_do_network_admin() && !$config['blog_status'] ) {
			add_action( 'admin_notices', 'bpolls_same_blog' );
			$warnings[] = __( 'BuddyPress Polls requires to be activated on the blog where BuddyPress is activated.', 'buddypress-polls' );
		}

		if ( bp_core_do_network_admin() && !$config['network_status'] ) {
			add_action( 'admin_notices', 'bpolls_same_network_config' );
			$warnings[] = __( 'BuddyPress Polls and BuddyPress need to share the same network configuration.', 'buddypress-polls' );
		}

		if ( ! empty( $warnings ) ) :
			return false;
		endif;
		// Display a warning message in network admin or admin
	}
	return true;
}

function bpolls_same_blog(){
	echo '<div class="error"><p>'
	. esc_html( __( 'BuddyPress Polls requires to be activated on the blog where BuddyPress is activated.', 'buddypress-polls' ) )
	. '</p></div>';
}

function bpolls_same_network_config(){
	echo '<div class="error"><p>'
	. esc_html( __( 'BuddyPress Polls and BuddyPress need to share the same network configuration.', 'buddypress-polls' ) )
	. '</p></div>';
}

/**
 * Function to add plugin links.
 *
 * @param array $links Plugin action links array.
 */
function bpolls_plugin_links( $links ) {
	$bpolls_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress-polls' ) . '">' . __( 'Settings', 'buddypress-polls' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'buddypress-polls' ) . '</a>',
	);
	return array_merge( $links, $bpolls_links );
}


/**
 *  Check if buddypress activate.
 */
 function bpolls_requires_buddypress()
{

    if ( !class_exists( 'Buddypress' ) ) {
         deactivate_plugins( plugin_basename( __FILE__ ) );
        //deactivate_plugins('buddypress-polls/buddypress-polls.php');
        add_action( 'admin_notices', 'bpolls_required_plugin_admin_notice' );
        unset($_GET['activate']);
    }
}

add_action( 'admin_init', 'bpolls_requires_buddypress' );
/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  2.5.0
 */
function bpolls_required_plugin_admin_notice()
{

    $bpquotes_plugin          = esc_html__('BuddyPress Polls', 'buddypress-polls');
    $bp_plugin                = esc_html__('BuddyPress', 'buddypress-polls');
    echo '<div class="error"><p>';
    echo sprintf(esc_html__('%1$s is ineffective now as it requires %2$s to be installed and active.', 'buddypress-polls'), '<strong>' . esc_html($bpquotes_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>');
    echo '</p></div>';
    if (isset($_GET['activate']) ) {
        unset($_GET['activate']);
    }
}

/**
 * Update existing polls user data into polls activity meta.
 *
 * @author wbcomdesigns
 * @since  3.2.1
 */

add_action( 'admin_init', 'buddypress_polls_migration', 20);

function buddypress_polls_migration(){
	global $wpdb, $pagenow;
	
	$buddypress_polls_migration_3_2_1 = get_option( 'buddypress_polls_migration_3_2_1' );
	
	if ( ( $pagenow == 'plugins.php' || $pagenow == 'update-core.php' ) && $buddypress_polls_migration_3_2_1 != 'update') {
		$polls_activity_results = $wpdb->get_results( "SELECT * from {$wpdb->prefix}bp_activity where type = 'activity_poll' group by id having date_recorded=max(date_recorded) order by date_recorded desc" );
		
		
		
		if ( ! empty( $polls_activity_results ) ) {
			foreach ( $polls_activity_results as  $activity ) {
				$activity_id = $activity->id;
				$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
				
				$usermeta_query = array();
				$usermeta_query['relation'] = 'AND';
				$usermeta_query[] = array(
						'key'     => 'bpoll_user_vote',
						'value'   => '.*i:'. $activity_id .';a:[0-9]+:*',
						'compare' => 'REGEXP',
					);

				$args = array(
					'meta_query' => $usermeta_query
				);
				$users = new WP_User_Query( $args  );
				$users_found = $users->get_results();
				foreach($users_found as $user) {
					$user_id 			= $user->ID;
					$user_polls_data 	= get_user_meta( $user_id, 'bpoll_user_vote', true );
					if ( isset($user_polls_data[ $activity_id ]) && !empty($user_polls_data[ $activity_id ])) {
						
						$user_activity_poll_data = isset($user_polls_data[$activity_id]) ? $user_polls_data[$activity_id] : array();
						
						foreach($activity_meta['poll_option'] as $key=>$value) {
							if ( in_array($key, $user_activity_poll_data )) {

								$polls_existing_useid = isset($activity_meta['poll_optn_user_votes'][ $key ]) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();
								$activity_meta['poll_optn_user_votes'][ $key ] = array_unique(array_merge( $polls_existing_useid, array($user_id) ) );
							} 
						}
						
						
						/* saved User id in activity meta */
						$existing_useid = isset($activity_meta['poll_users']) ? $activity_meta['poll_users'] : array();
						$activity_meta['poll_users'] =  array_unique(array_merge( $existing_useid, array($user_id) ) );
											
						bp_activity_update_meta( $activity_id, 'bpolls_meta', $activity_meta );
					}
					
				}
				
				
			}
			
			update_option( 'buddypress_polls_migration_3_2_1', 'update' );
		}		
	}	
	
}


/**
 * redirect to plugin settings page after activated
 */

add_action( 'activated_plugin', 'buddypress_polls_activation_redirect_settings' );
function buddypress_polls_activation_redirect_settings( $plugin ){

	if( $plugin == plugin_basename( __FILE__ ) ) {
		wp_redirect( admin_url( 'admin.php?page=buddypress-polls' ) ) ;
		exit;
	}
}
