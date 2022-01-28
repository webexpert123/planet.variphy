<?php
/**
 * Post Types
 *
 * @package     GamiPress\Leaderboards\Post_Types
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Leaderboard CPT
 *
 * @since  1.0.0
 */
function gamipress_leaderboards_register_post_types() {

    $labels = gamipress_leaderboards_labels();

    $public_leaderboards = (bool) gamipress_leaderboards_get_option( 'public', false );
    $supports = gamipress_leaderboards_get_option( 'supports', array( 'title', 'editor', 'excerpt' ) );

    if( ! is_array( $supports ) ) {
        $supports =  array( 'title', 'editor', 'excerpt' );
    }

    // Register Leaderboard
    register_post_type( 'leaderboard', array(
        'labels'             => array(
            'name'               => $labels['plural'],
            'singular_name'      => $labels['singular'],
            'add_new'            => __( 'Add New', 'gamipress-leaderboards' ),
            'add_new_item'       => sprintf( __( 'Add New %s', 'gamipress-leaderboards' ), $labels['singular'] ),
            'edit_item'          => sprintf( __( 'Edit %s', 'gamipress-leaderboards' ), $labels['singular'] ),
            'new_item'           => sprintf( __( 'New %s', 'gamipress-leaderboards' ), $labels['singular'] ),
            'all_items'          => $labels['plural'],
            'view_item'          => sprintf( __( 'View %s', 'gamipress-leaderboards' ), $labels['singular'] ),
            'search_items'       => sprintf( __( 'Search %s', 'gamipress-leaderboards' ), $labels['plural'] ),
            'not_found'          => sprintf( __( 'No %s found', 'gamipress-leaderboards' ), strtolower( $labels['plural'] ) ),
            'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'gamipress-leaderboards' ), strtolower( $labels['plural'] ) ),
            'parent_item_colon'  => '',
            'menu_name'          => $labels['plural'],
        ),
        'public'             => $public_leaderboards,
        'publicly_queryable' => $public_leaderboards,
        'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
        'show_in_menu'       => 'gamipress',
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => gamipress_leaderboards_get_option( 'slug', 'leaderboards' ) ),
        'capability_type'    => 'page',
        'has_archive'        => $public_leaderboards,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => $supports
    ) );

}
add_action( 'init', 'gamipress_leaderboards_register_post_types', 11 );

/**
 * Leaderboards labels
 *
 * @since  1.0.0
 * @return array
 */
function gamipress_leaderboards_labels() {
    return apply_filters( 'gamipress_leaderboards_labels' , array(
        'singular' => __( 'Leaderboard', 'gamipress-leaderboards' ),
        'plural' => __( 'Leaderboards', 'gamipress-leaderboards' )
    ));
}