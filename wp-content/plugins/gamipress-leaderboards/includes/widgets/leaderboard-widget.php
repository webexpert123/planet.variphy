<?php
/**
 * Leaderboard Widget
 *
 * @package     GamiPress\Leaderboards\Widgets\Widget\Leaderboard
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Leaderboard_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_leaderboard';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Leaderboard', 'gamipress-leaderboards' ),
            __( 'Render a desired leaderboard.', 'gamipress-leaderboards' )
        );
    }

    public function get_fields() {

        // Need to change field id to leaderboard_id to avoid problems with GamiPress javascript selectors
        $fields = GamiPress()->shortcodes[$this->shortcode]->fields;

        // Get the fields keys
        $keys = array_keys( $fields );

        // Get the numeric index of the field 'id'
        $index = array_search( 'id', $keys );

        // Replace the 'id' key by 'leaderboard_id'
        $keys[$index] = 'leaderboard_id';

        // Combine new array with new keys with an array of values
        $fields = array_combine( $keys, array_values( $fields ) );

        return $fields;
    }

    public function get_widget( $args, $instance ) {

        // Get back replaced fields
        $instance['id'] = $instance['leaderboard_id'];

        // Title is rendered from widget
        $instance['title'] = '';

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );
    }

}