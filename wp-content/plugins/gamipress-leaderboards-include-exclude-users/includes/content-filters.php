<?php
/**
 * Content Filters
 *
 * @package GamiPress\Leaderboards\Include_Exclude_Users\Content_Filters
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filter leaderboard query vars to just include/exclude users
 *
 * @param array     $query_vars
 * @param int       $leaderboard_id
 *
 * @return array
 */
function gamipress_leaderboards_include_exclude_users_leaderboard_pre_query_vars( $query_vars, $leaderboard_id ) {

    global $wpdb;

    // Setup vars
    $prefix                 = '_gamipress_leaderboards_include_exclude_users_';
    $usermeta 			    = GamiPress()->db->usermeta;
    $capabilities_meta_key  = $wpdb->get_blog_prefix() . 'capabilities';

    // Include
    $include = (bool) gamipress_get_post_meta( $leaderboard_id, $prefix . 'include' );

    if( $include ) {

        // Include roles
        $include_roles = gamipress_get_post_meta( $leaderboard_id, $prefix . 'include_roles', false );

        if( is_array( $include_roles ) && ! empty( $include_roles ) ) {

            $include_roles_where = array();

            foreach( $include_roles as $role ) {
                $query_vars['join'][] = "LEFT JOIN {$usermeta} AS ium{$role} ON ( ium{$role}.user_id = u.ID AND ium{$role}.meta_key = '{$capabilities_meta_key}' )";
                $include_roles_where[] = "ium{$role}.meta_value LIKE '%\"{$role}\"%'";

            }

            $query_vars['where'][] = '( ' . implode( ') OR (', $include_roles_where ) . ' )';
        }

        // Include users
        $include_users = gamipress_get_post_meta( $leaderboard_id, $prefix . 'include_users', false );

        if( is_array( $include_users ) && ! empty( $include_users ) ) {
            $query_vars['where'][] = "u.ID IN ( " . implode( ', ', $include_users ) . " )";
        }

    }

    // Exclude
    $exclude = (bool) gamipress_get_post_meta( $leaderboard_id, $prefix . 'exclude' );

    if( $exclude ) {

        // Exclude roles
        $exclude_roles = gamipress_get_post_meta( $leaderboard_id, $prefix . 'exclude_roles', false );

        if( is_array( $exclude_roles ) && ! empty( $exclude_roles ) ) {

            foreach( $exclude_roles as $role ) {
                $query_vars['join'][] = "LEFT JOIN {$usermeta} AS eum{$role} ON ( eum{$role}.user_id = u.ID AND eum{$role}.meta_key = '{$capabilities_meta_key}' )";
                $query_vars['where'][] = "eum{$role}.meta_value NOT LIKE '%\"{$role}\"%'";
            }
        }

        // Exclude users
        $exclude_users = gamipress_get_post_meta( $leaderboard_id, $prefix . 'exclude_users', false );

        if( is_array( $exclude_users ) && ! empty( $exclude_users ) ) {
            $query_vars['where'][] = "u.ID NOT IN ( " . implode( ', ', $exclude_users ) . " )";
        }

    }

    return $query_vars;

}
add_filter( 'gamipress_leaderboards_leaderboard_pre_query_vars', 'gamipress_leaderboards_include_exclude_users_leaderboard_pre_query_vars', 10, 2 );