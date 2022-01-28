<?php
/**
 * Single Leaderboard template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/leaderboards/single-leaderboard.php
 */
global $gamipress_leaderboards_template_args; ?>

<div id="gamipress-leaderboard-<?php the_ID(); ?>" class="single-gamipress-leaderboard">

    <?php
    /**
     * Before single leaderboard
     *
     * @param integer   $leaderboard_id The leaderboard ID
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_before_single_leaderboard', get_the_ID(), $gamipress_leaderboards_template_args ); ?>

    <?php // Leaderboard content
    if( isset( $gamipress_leaderboards_template_args['original_content'] ) ) :
        echo wpautop( $gamipress_leaderboards_template_args['original_content'] );
    endif; ?>

    <?php
    /**
     * After single leaderboard content
     *
     * @param integer   $leaderboard_id The leaderboard ID
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_after_single_leaderboard_content', get_the_ID(), $gamipress_leaderboards_template_args ); ?>

    <?php
    // Setup the leaderboard table
    $leaderboard_table = new GamiPress_Leaderboard_Table( get_the_ID() );

    // Display the leaderboard table
    $leaderboard_table->display();
    ?>

    <?php
    /**
     * After single leaderboard
     *
     * @param integer   $leaderboard_id The leaderboard ID
     * @param array     $template_args  Template received arguments
     * @param GamiPress_Leaderboard_Table   $leaderboard_table  leaderboard table object
     */
    do_action( 'gamipress_after_single_leaderboard', get_the_ID(), $gamipress_leaderboards_template_args, $leaderboard_table ); ?>

</div>
