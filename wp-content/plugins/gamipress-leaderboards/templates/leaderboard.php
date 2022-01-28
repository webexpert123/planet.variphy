<?php
/**
 * Leaderboard template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/leaderboards/leaderboard.php
 */
global $gamipress_leaderboards_template_args;

// Shorthand
$a = $gamipress_leaderboards_template_args; ?>

<div id="gamipress-leaderboard-<?php the_ID(); ?>" class="gamipress-leaderboard">

    <?php
    /**
     * Before render leaderboard
     *
     * @param integer   $leaderboard_id The leaderboard ID
     * @param array     $template_args  Template received arguments
     */
    do_action( 'gamipress_before_render_leaderboard', get_the_ID(), $a ); ?>

    <?php if( ! empty( $a['title'] ) ) : ?>
        <h2 class="gamipress-leaderboard-title"><?php echo $a['title']; ?></h2>

        <?php
        /**
         * After leaderboard title
         *
         * @param integer   $leaderboard_id The leaderboard ID
         * @param array     $template_args  Template received arguments
         */
        do_action( 'gamipress_after_leaderboard_title', get_the_ID(), $a ); ?>
    <?php endif; ?>

    <?php // Leaderboard Short Description
    if( $a['excerpt'] === 'yes' ) :  ?>
        <div class="gamipress-leaderboard-excerpt">
            <?php
            $excerpt = has_excerpt() ? gamipress_get_post_field( 'post_excerpt', get_the_ID() ) : gamipress_get_post_field( 'post_content', get_the_ID() );
            echo wpautop( do_blocks( apply_filters( 'get_the_excerpt', $excerpt, get_post() ) ) );
            ?>
        </div><!-- .gamipress-achievement-excerpt -->

        <?php
        /**
         * After leaderboard excerpt
         *
         * @param integer   $leaderboard_id The leaderboard ID
         * @param array     $template_args  Template received arguments
         */
        do_action( 'gamipress_after_leaderboard_excerpt', get_the_ID(), $a ); ?>
    <?php endif; ?>

    <?php

    $args = array(
        'search'            => ( $a['search'] === 'yes' ),
        'sort'              => ( $a['sort'] === 'yes' ),
        'hide_admins'       => ( $a['hide_admins'] === 'yes' ),
        'force_responsive'  => ( $a['force_responsive'] === 'yes' ),
    );

    // Setup the leaderboard table
    $leaderboard_table = new GamiPress_Leaderboard_Table( get_the_ID(), $args );

    // Display the leaderboard table
    $leaderboard_table->display();
    ?>

    <?php
    /**
     * After render leaderboard
     *
     * @param integer                       $leaderboard_id     The leaderboard ID
     * @param array                         $template_args      Template received arguments
     * @param GamiPress_Leaderboard_Table   $leaderboard_table  leaderboard table object
     */
    do_action( 'gamipress_after_render_leaderboard', get_the_ID(), $a, $leaderboard_table ); ?>

</div>
