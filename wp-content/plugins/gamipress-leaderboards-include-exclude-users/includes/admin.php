<?php
/**
 * Admin
 *
 * @package GamiPress\Leaderboards\Include_Exclude_Users\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Plugin automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_leaderboards_include_exclude_users_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-leaderboards-include-exclude-users'] = __( 'GamiPress - Block Users', 'gamipress-leaderboards-include-exclude-users' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_leaderboards_include_exclude_users_automatic_updates' );

/**
 * Register custom meta boxes used throughout GamiPress
 *
 * @since  1.0.0
 */
function gamipress_leaderboards_include_exclude_users_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_leaderboards_include_exclude_users_';

    // Leaderboard Data
    gamipress_add_meta_box(
        'leaderboard-include-exclude-users',
        __( 'Include/Exclude Users', 'gamipress-leaderboards-include-exclude-users' ),
        'leaderboard',
        array(
            $prefix . 'include' => array(
                'name'          => __( 'Include', 'gamipress-leaderboards-include-exclude-users' ),
                'desc'          => __( 'Limit leaderboard to a specific group of users only.', 'gamipress-leaderboards-include-exclude-users' ),
                'type'          => 'checkbox',
                'classes'       => 'gamipress-switch',
            ),
            $prefix . 'include_roles' => array(
                'name'          => __( 'Included Roles', 'gamipress-leaderboards-include-exclude-users' ),
                'desc'          => __( 'Limit leaderboard to users with this role.', 'gamipress-leaderboards-include-exclude-users' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'options_cb'    => 'gamipress_leaderboards_include_exclude_users_get_roles_options',
            ),
            $prefix . 'include_users' => array(
                'name'          => __( 'Included Users', 'gamipress-leaderboards-include-exclude-users' ),
                'desc'          => __( 'Limit leaderboard to users on this.', 'gamipress-leaderboards-include-exclude-users' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'options_cb'    => 'gamipress_options_cb_users',
            ),
            $prefix . 'exclude' => array(
                'name'          => __( 'Exclude', 'gamipress-leaderboards-include-exclude-users' ),
                'desc'          => __( 'Exclude a specific group of users to appear on this leaderboard.', 'gamipress-leaderboards-include-exclude-users' ),
                'type'          => 'checkbox',
                'classes'       => 'gamipress-switch',
            ),
            $prefix . 'exclude_roles' => array(
                'name'          => __( 'Excluded Roles', 'gamipress-leaderboards-include-exclude-users' ),
                'desc'          => __( 'Users with this roles won\'t appear on the leaderboard.', 'gamipress-leaderboards-include-exclude-users' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'options_cb'    => 'gamipress_leaderboards_include_exclude_users_get_roles_options',
            ),
            $prefix . 'exclude_users' => array(
                'name'          => __( 'Excluded Users', 'gamipress-leaderboards-include-exclude-users' ),
                'desc'          => __( 'Users on this list won\'t appear on the leaderboard.', 'gamipress-leaderboards-include-exclude-users' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'options_cb'    => 'gamipress_options_cb_users',
            ),
        )
    );

}
add_action( 'gamipress_init_leaderboard_meta_boxes', 'gamipress_leaderboards_include_exclude_users_meta_boxes' );

// Callback to retrieve user roles as select options
function gamipress_leaderboards_include_exclude_users_get_roles_options() {

    $options = array();

    $editable_roles = array_reverse( get_editable_roles() );

    foreach ( $editable_roles as $role => $details ) {

        $options[$role] = translate_user_role( $details['name'] );

    }

    return $options;
}