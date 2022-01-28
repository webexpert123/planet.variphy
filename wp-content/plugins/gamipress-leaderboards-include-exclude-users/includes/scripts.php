<?php
/**
 * Scripts
 *
 * @package GamiPress\Leaderboards\Include_Exclude_Users\Scripts
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_leaderboards_include_exclude_users_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-leaderboards-include-exclude-users-admin-js', GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_URL . 'assets/js/gamipress-leaderboards-include-exclude-users-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_VER, true );

}
add_action( 'admin_init', 'gamipress_leaderboards_include_exclude_users_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_leaderboards_include_exclude_users_admin_enqueue_scripts( $hook ) {

    // Localize scripts
    wp_localize_script( 'gamipress-leaderboards-include-exclude-users-admin-js', 'gamipress_leaderboards_include_exclude_users_admin', array(
        'nonce' => gamipress_get_admin_nonce(),
    ) );

    //Scripts
    wp_enqueue_script( 'gamipress-leaderboards-include-exclude-users-admin-js' );

}
add_action( 'admin_enqueue_scripts', 'gamipress_leaderboards_include_exclude_users_admin_enqueue_scripts', 100 );