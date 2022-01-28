<?php
/**
 * Template Functions
 *
 * @package GamiPress\Leaderboards\Template_Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin templates directory on GamiPress template engine
 *
 * @since 1.0.0
 *
 * @param array $file_paths
 *
 * @return array
 */
function gamipress_leaderboards_template_paths( $file_paths ) {

    $file_paths[] = trailingslashit( get_stylesheet_directory() ) . 'gamipress/leaderboards/';
    $file_paths[] = trailingslashit( get_template_directory() ) . 'gamipress/leaderboards/';
    $file_paths[] =  GAMIPRESS_LEADERBOARDS_DIR . 'templates/';

    return $file_paths;

}
add_filter( 'gamipress_template_paths', 'gamipress_leaderboards_template_paths' );

/**
 * Get an array of pattern tags to being used on [gamipress_leaderboard_user_position] shortcode
 *
 * @since  1.0.8

 * @return array The registered pattern tags
 */
function gamipress_leaderboard_user_position_get_ranked_pattern_tags() {

    return apply_filters( 'gamipress_leaderboard_user_position_ranked_pattern_tags', array(
        '{user}'                =>  __( 'User display name.', 'gamipress-leaderboards' ),
        '{user_first}'          =>  __( 'User first name.', 'gamipress-leaderboards' ),
        '{user_last}'           =>  __( 'User last name.', 'gamipress-leaderboards' ),
        '{leaderboard_title}'   =>  __( 'Leaderboard title.', 'gamipress-leaderboards' ),
        '{leaderboard_url}'     =>  __( 'URL to the leaderboard.', 'gamipress-leaderboards' ),
        '{leaderboard_link}'    =>  __( 'Link to the leaderboard with the leaderboard title as text.', 'gamipress-leaderboards' ),
        '{position}'            =>  __( 'User position on this leaderboard.', 'gamipress-leaderboards' ),
    ) );

}

/**
 * Get an array of pattern tags to being used on [gamipress_leaderboard_user_position] shortcode
 *
 * @since  1.0.8

 * @return array The registered pattern tags
 */
function gamipress_leaderboard_user_position_get_not_ranked_pattern_tags() {

    return apply_filters( 'gamipress_leaderboard_user_position_not_ranked_pattern_tags', array(
        '{user}'                =>  __( 'User display name.', 'gamipress-leaderboards' ),
        '{user_first}'          =>  __( 'User first name.', 'gamipress-leaderboards' ),
        '{user_last}'           =>  __( 'User last name.', 'gamipress-leaderboards' ),
        '{leaderboard_title}'   =>  __( 'Leaderboard title.', 'gamipress-leaderboards' ),
        '{leaderboard_url}'     =>  __( 'URL to the leaderboard.', 'gamipress-leaderboards' ),
        '{leaderboard_link}'    =>  __( 'Link to the leaderboard with the leaderboard title as text.', 'gamipress-leaderboards' ),
    ) );

}

/**
 * Get a string with the desired pattern tags html markup
 *
 * @since   1.0.8
 *
 * @param string $pattern_tags
 *
 * @return string               Pattern tags html markup
 */
function gamipress_leaderboard_user_position_get_pattern_tags_html( $pattern_tags = '' ) {

    if( $pattern_tags === 'ranked' ) {
        $tags = gamipress_leaderboard_user_position_get_ranked_pattern_tags();
    } else if( $pattern_tags === 'not-ranked' ) {
        $tags = gamipress_leaderboard_user_position_get_not_ranked_pattern_tags();
    }

    $output = '<ul class="gamipress-pattern-tags-list gamipress-leaderboards-pattern-tags-list">';

    foreach( $tags as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Parse pattern tags to a given post pattern
 *
 * @since  1.0.8
 *
 * @param string    $pattern
 * @param int       $leaderboard_id
 * @param int       $user_id
 * @param int       $position
 *
 * @return string Parsed pattern
 */
function gamipress_leaderboard_user_position_parse_pattern( $pattern, $leaderboard_id = null, $user_id = null, $position = 0 ) {

    if( $leaderboard_id === null ) {
        $leaderboard_id = get_the_ID();
    }

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $leaderboard = get_post( $leaderboard_id );
    $leaderboard_link = get_the_permalink( $leaderboard->ID );

    $user = get_userdata( $user_id );

    $pattern_replacements = array(
        '{user}'                =>  $user ? $user->display_name : '',
        '{user_first}'          =>  $user ? $user->first_name : '',
        '{user_last}'           =>  $user ? $user->last_name : '',
        '{leaderboard_title}'   =>  $leaderboard->post_title,
        '{leaderboard_url}'     =>  $leaderboard_link,
        '{leaderboard_link}'    =>  sprintf( '<a href="%s" title="%s">%s</a>', $leaderboard_link, $leaderboard->post_title, $leaderboard->post_title ),
        '{position}'            =>  $position ? $position : '',
    );

    $pattern_replacements = apply_filters( 'gamipress_leaderboard_user_position_parse_pattern_replacements', $pattern_replacements, $pattern, $leaderboard_id, $user_id, $position );

    return apply_filters( 'gamipress_leaderboard_user_position_parse_pattern', str_replace( array_keys( $pattern_replacements ), $pattern_replacements, $pattern ), $pattern_replacements, $pattern, $leaderboard_id, $user_id, $position );

}