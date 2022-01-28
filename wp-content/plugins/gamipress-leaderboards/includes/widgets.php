<?php
/**
 * Widgets
 *
 * @package     GamiPress\Leaderboards\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_LEADERBOARDS_DIR .'includes/widgets/leaderboard-widget.php';
require_once GAMIPRESS_LEADERBOARDS_DIR .'includes/widgets/leaderboard-user-position-widget.php';

// Register plugin widgets
function gamipress_leaderboards_register_widgets() {

    register_widget( 'gamipress_leaderboard_widget' );
    register_widget( 'gamipress_leaderboard_user_position_widget' );

}
add_action( 'widgets_init', 'gamipress_leaderboards_register_widgets' );