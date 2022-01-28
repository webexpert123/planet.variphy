<?php
/**
 * Content Filters
 *
 * @package     GamiPress\Leaderboards\Content_Filters
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filter leaderboard content to add the leaderboard table
 *
 * @since  1.0.0
 *
 * @param  string $content The page content
 *
 * @return string          The page content after reformat
 */
function gamipress_leaderboards_reformat_entries( $content ) {

    // Filter, but only on the main loop!
    if ( ! gamipress_leaderboards_is_main_loop( get_the_ID() ) ) {
        return $content;
    }

    // Prevent auto p for leaderboards entries
    remove_filter( 'the_content', 'wpautop' );

    // now that we're where we want to be, tell the filters to stop removing
    $GLOBALS['gamipress_leaderboards_reformat_content'] = true;

    global $gamipress_leaderboards_template_args;

    // Initialize template args global
    $gamipress_leaderboards_template_args = array();

    $gamipress_leaderboards_template_args['original_content'] = $content;

    ob_start();

    gamipress_get_template_part( 'single-leaderboard' );

    $new_content = ob_get_clean();

    // Ok, we're done reformating
    $GLOBALS['gamipress_leaderboards_reformat_content'] = false;

    return $new_content;
}
add_filter( 'the_content', 'gamipress_leaderboards_reformat_entries', 9 );

/**
 * Helper function tests that we're in the main loop
 *
 * @since  1.0.0
 * @param  bool|integer $id The page id
 * @return boolean     A boolean determining if the function is in the main loop
 */
function gamipress_leaderboards_is_main_loop( $id = false ) {

    // only run our filters on the gamipress leaderboard singular pages
    if ( is_admin() || ! is_singular( 'leaderboard' ) ) {
        return false;
    }

    // w/o id, we're only checking template context
    if ( ! $id ) {
        return true;
    }

    // Checks several variables to be sure we're in the main loop (and won't effect things like post pagination titles)
    return ( ( $GLOBALS['post']->ID == $id ) && in_the_loop() && empty( $GLOBALS['gamipress_leaderboards_reformat_content'] ) );

}