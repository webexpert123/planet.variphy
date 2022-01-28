<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Leaderboards\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler to get a leaderboard results
 *
 * @since 1.2.6
 */
function gamipress_leaderboards_ajax_get_leaderboard_results() {

    if( ! isset( $_REQUEST['leaderboard_id'] ) ) {
        wp_send_json_error( array( __( 'Please, provide the leaderboard ID.', 'gamipress-leaderboards' ) ) );
    }

    $leaderboard_id = absint( $_REQUEST['leaderboard_id'] );

    if( gamipress_get_post_field( 'post_type', $leaderboard_id ) !== 'leaderboard' ) {
        wp_send_json_error( array( __( 'Invalid leaderboard.', 'gamipress-leaderboards' ) ) );
    }

    // Setup the leaderboard table
    $leaderboard_table = new GamiPress_Leaderboard_Table( $leaderboard_id );

    wp_send_json_success( $leaderboard_table->to_array() );

}
add_action( 'wp_ajax_gamipress_leaderboards_get_leaderboard_results', 'gamipress_leaderboards_ajax_get_leaderboard_results' );
add_action( 'wp_ajax_nopriv_gamipress_leaderboards_get_leaderboard_results', 'gamipress_leaderboards_ajax_get_leaderboard_results' );

/**
 * AJAX handler to clear a leaderboard cache
 *
 * @since 1.0.0
 */
function gamipress_leaderboards_ajax_clear_cache() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    if( ! isset( $_REQUEST['leaderboard_id'] ) ) {
        wp_send_json_error( __( 'Not leaderboard provided.', 'gamipress-leaderboards' ) );
    }

    $leaderboard_id = absint( $_REQUEST['leaderboard_id'] );

    gamipress_leaderboards_clear_leaderboard_cache( $leaderboard_id );

    wp_send_json_success( __( 'Cache cleared successfully.', 'gamipress-leaderboards' ) );
}
add_action( 'wp_ajax_gamipress_leaderboards_clear_cache', 'gamipress_leaderboards_ajax_clear_cache' );