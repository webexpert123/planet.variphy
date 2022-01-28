<?php
/**
 * GamiPress Leaderboard User Position Shortcode
 *
 * @package     GamiPress\Leaderboards\Shortcodes\Shortcode\GamiPress_Leaderboard_User_Position
 * @since       1.0.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_leaderboard_user_position] shortcode.
 *
 * @since 1.0.8
 */
function gamipress_register_leaderboard_user_position_shortcode() {

    gamipress_register_shortcode( 'gamipress_leaderboard_user_position', array(
        'name'              => __( 'Leaderboard User Position', 'gamipress-leaderboards' ),
        'description'       => __( 'Render the user position in a leaderboard.', 'gamipress-leaderboards' ),
        'output_callback'   => 'gamipress_leaderboard_user_position_shortcode',
        'icon'              => 'leaderboard',
        'fields'            => array(
            'id' => array(
                'name'              => __( 'Leaderboard', 'gamipress-leaderboards' ),
                'desc'              => __( 'Choose the leaderboard to get the user position.', 'gamipress-leaderboards' ),
                'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type'    => 'leaderboard',
                    'data-placeholder'  => __( 'Select a Leaderboard', 'gamipress-leaderboards' ),
                ),
                'default'           => '',
                'options_cb'        => 'gamipress_options_cb_posts'
            ),
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress-leaderboards' ),
                'description' => __( 'Show position of the current logged in user.', 'gamipress-leaderboards' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress-leaderboards' ),
                'description' => __( 'Show position of a specific user.', 'gamipress-leaderboards' ),
                'type'        => 'select',
                'classes' 	  => 'gamipress-user-selector',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'text' => array(
                'name'        => __( 'Text when ranked', 'gamipress-leaderboards' ),
                'description' => __( 'Text to show to users ranked on this leaderboard. Available tags:', 'gamipress-leaderboards' )
                    . gamipress_leaderboard_user_position_get_pattern_tags_html( 'ranked' ),
                'type'        => 'textarea',
                'default'     => __( '{user}, you are in position {position} on {leaderboard_link}', 'gamipress-leaderboards' ),
            ),
            'not_ranked_text' => array(
                'name'        => __( 'Text when not ranked', 'gamipress-leaderboards' ),
                'description' => __( 'Text to show to users that has not reach any position on this leaderboard. Available tags:', 'gamipress-leaderboards' )
                    . gamipress_leaderboard_user_position_get_pattern_tags_html( 'not-ranked' ),
                'type'        => 'textarea',
                'default'     => __( '{user}, you are not ranked on {leaderboard_link}', 'gamipress-leaderboards' ),
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_leaderboard_user_position_shortcode' );

/**
 * Leaderboard User Position Shortcode.
 *
 * @since  1.0.8
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_leaderboard_user_position_shortcode( $atts = array() ) {

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

    // Setup default attrs
    $atts = shortcode_atts( array(

        'id'                => $leaderboard_id,
        'current_user'      => 'yes',
        'user_id'           => '0',
        'text'              => '',
        'not_ranked_text'   => '',

    ), $atts, 'gamipress_leaderboard_user_position' );

    $user_id = get_current_user_id();

    if( $atts['current_user'] === 'no' && absint( $atts['user_id'] ) !== 0 ) {
        $user_id = absint( $atts['user_id'] );
    }

    // Enqueue assets
    gamipress_leaderboards_enqueue_scripts();

    // Get the user position
    $position = gamipress_leaderboards_get_user_position( $leaderboard_id, $user_id );

    // Set the text pattern based on user position
    $pattern = ( $position !== false ? $atts['text'] : $atts['not_ranked_text'] );

    $output = gamipress_leaderboard_user_position_parse_pattern( $pattern, $leaderboard_id, $user_id, $position );

    // Return the rendered text with user position
    return $output;

}
