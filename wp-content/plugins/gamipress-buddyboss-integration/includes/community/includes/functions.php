<?php
/**
 * Functions
 *
 * @package GamiPress\BuddyPress\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Compatibility function to check if a BuddyPress module is active
 *
 * @since 1.2.0
 *
 * @param string $component The component name.
 *
 * @return bool
 */
function gamipress_bp_is_active( $component = '' ) {

    if( function_exists( 'bp_is_active' ) ) {
        return bp_is_active( $component );
    }

    return true;

}

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_bp_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'bp_groups', $_REQUEST['post_type'] ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : false;

        $bp_groups = groups_get_groups( array(
            'search_terms' => $search,
            'show_hidden' => true,
            'per_page' => 300
        ) );

        if ( ! empty( $bp_groups ) ) {
            foreach ( $bp_groups['groups'] as $group ) {
                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $group->id,
                    'post_title' => $group->name,
                );
            }
        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_bp_ajax_get_posts', 5 );