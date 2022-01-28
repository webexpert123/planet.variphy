<?php
/**
 * GamiPress Leaderboard Shortcode
 *
 * @package     GamiPress\Leaderboards\Shortcodes\Shortcode\GamiPress_Leaderboard
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_leaderboard] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_leaderboard_shortcode() {
    gamipress_register_shortcode( 'gamipress_leaderboard', array(
        'name'              => __( 'Leaderboard', 'gamipress-leaderboards' ),
        'description'       => __( 'Render the desired leaderboard.', 'gamipress-leaderboards' ),
        'output_callback'   => 'gamipress_leaderboard_shortcode',
        'icon'              => 'leaderboard',
        'fields'            => array(
            'title' => array(
                'name' => __( 'Title', 'gamipress-leaderboards' ),
                'type' => 'text',
                'default' => ''
            ),
            'id' => array(
                'name'              => __( 'Leaderboard', 'gamipress-leaderboards' ),
                'desc'              => __( 'Choose the leaderboard to display.', 'gamipress-leaderboards' ),
                'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type'    => 'leaderboard',
                    'data-placeholder'  => __( 'Select a Leaderboard', 'gamipress-leaderboards' ),
                ),
                'default'           => '',
                'options_cb'        => 'gamipress_options_cb_posts'
            ),
            'excerpt' => array(
                'name'        => __( 'Show Excerpt', 'gamipress-leaderboards' ),
                'description' => __( 'Display the leaderboard short description.', 'gamipress-leaderboards' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'search' => array(
                'name' 	=> __( 'Show Search', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Display a search input.', 'gamipress-leaderboards' ),
                'type' 	=> 'checkbox',
                'classes'   => 'gamipress-switch',
                'default'   => 'yes',
            ),
            'sort' => array(
                'name' 	=> __( 'Enable Sort', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Enable live column sorting.', 'gamipress-leaderboards' ),
                'type' 	=> 'checkbox',
                'classes'   => 'gamipress-switch',
                'default'   => 'yes',
            ),
            'hide_admins' => array(
                'name' 	=> __( 'Hide Administrators', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Hide website administrators.', 'gamipress-leaderboards' ),
                'type' 	=> 'checkbox',
                'classes'   => 'gamipress-switch',
                'default'   => 'yes',
            ),
            'force_responsive' => array(
                'name' 	=> __( 'Force Responsive', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Force leaderboard to display with the responsive style even if leaderboard is displayed in a big screen.', 'gamipress-leaderboards' ),
                'type' 	=> 'checkbox',
                'classes'   => 'gamipress-switch',
            ),
        ),
    ) );
}
add_action( 'init', 'gamipress_register_leaderboard_shortcode' );

/**
 * Leaderboard Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_leaderboard_shortcode( $atts = array() ) {

    global $post, $gamipress_leaderboards_template_args;

    // Leaderboard post vars
    $leaderboard_id = isset( $atts['id'] ) && ! empty( $atts['id'] ) ? $atts['id'] : get_the_ID();
    $leaderboard_post = gamipress_get_post( $leaderboard_id );

    // Return if leaderboard post does not exists
    if( ! $leaderboard_post ) {
        return '';
    }

    // Return if not is a leaderboard
    if( $leaderboard_post->post_type !== 'leaderboard' ) {
        return '';
    }

    // Return if leaderboard was not published
    if( $leaderboard_post->post_status !== 'publish' ) {
        return '';
    }

    // Fields prefix
    $prefix = '_gamipress_leaderboards_';

    // For missing attributes, load the leaderboard setup
    if( ! isset( $atts['title'] ) ) {
        $atts['title'] = gamipress_get_post_field( 'post_title', $leaderboard_id );
    }

    if( ! isset( $atts['search'] ) ) {
        $atts['search'] = ( (bool) gamipress_get_post_meta( $leaderboard_id, $prefix . 'search', true ) ? 'yes' : 'no' );
    }

    if( ! isset( $atts['sort'] ) ) {
        $atts['sort'] = ( (bool) gamipress_get_post_meta( $leaderboard_id, $prefix . 'sort', true ) ? 'yes' : 'no' );
    }

    if( ! isset( $atts['hide_admins'] ) ) {
        $atts['hide_admins'] = ( (bool) gamipress_get_post_meta( $leaderboard_id, $prefix . 'hide_admins', true ) ? 'yes' : 'no' );
    }

    // Setup default attrs
    $atts = shortcode_atts( array(

        'title'             => '',
        'id'                => $leaderboard_id,
        'excerpt'           => 'yes',
        'search'            => 'yes',
        'sort'              => 'yes',
        'hide_admins'       => 'yes',
        'force_responsive'  => 'no',

    ), $atts, 'gamipress_leaderboard' );

    // Initialize template args
    $gamipress_leaderboards_template_args = array();

    $gamipress_leaderboards_template_args = $atts;

    // Enqueue assets
    gamipress_leaderboards_enqueue_scripts();

    $post = $leaderboard_post;

    setup_postdata( $post );

    // Render the leaderboard
    ob_start();
    gamipress_get_template_part( 'leaderboard' );
    $output = ob_get_clean();

    wp_reset_postdata();

    // Return our rendered leaderboard
    return $output;

}
