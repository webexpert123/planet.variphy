<?php
/**
 * BuddyPress Poll Activity Graph Widget
 *
 * @package Buddypress_Polls
 * @subpackage Buddypress_Polls/public/inc
 * @since 2.8.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Poll Activity Graph Widget.
 *
 * @since 2.8.0
 */
class BP_Poll_Activity_Widget extends WP_Widget {

	/**
	 * Working as a poll activity, we get things done better.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		 $widget_ops = array(
			 'description'                 => __( 'Display the results for the polls of your choice.', 'buddypress-polls' ),
			 'classname'                   => 'widget_bp_poll_activity_widget buddypress widget',
			 'customize_selective_refresh' => true,
		 );
		 parent::__construct( false, _x( '(BuddyPress) Poll Results', 'widget name', 'buddypress-polls' ), $widget_ops );

		 if ( ! is_customize_preview() ) {
			 global $pagenow;
			 add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			 if ( is_admin() && $pagenow == 'index.php' ) {
				 add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			 }
		 }
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.8.0
	 */

	public function enqueue_scripts( $hook ) {

		global $wpdb;
		$results      = $wpdb->get_results( "SELECT * from {$wpdb->prefix}bp_activity where type = 'activity_poll' group by id having date_recorded=max(date_recorded) order by date_recorded desc" );
		$activity_ids = array();
		if ( ! empty( $results ) ) {
			foreach ( $results as  $result ) {
				$activity_ids[]['activity_id'] = $result->id;
			}
		}

		$_instance                = array(
			'title'       => __( 'Poll Activity', 'buddypress-polls' ),
			'activity_id' => ( ! empty( $activity_ids ) ) ? $activity_ids : array(),
		);
		$poll_wdgt                = new BP_Poll_Activity_Widget();
		$poll_wdgt_stngs          = $poll_wdgt->get_settings();
		$time                     = time();
		$poll_wdgt_stngs[ $time ] = $_instance;
		$uptd_votes               = array();

		if ( is_array( $poll_wdgt_stngs ) ) {
			foreach ( $poll_wdgt_stngs[ $time ]['activity_id'] as $key => $value ) {
				if ( isset( $value['activity_id'] ) ) {
					$activity_id      = $value['activity_id'];
					$activity_details = bp_activity_get_specific( $args = array( 'activity_ids' => $activity_id ) );
					if ( is_array( $activity_details ) ) {
						$poll_title = isset( $activity_details['activities'][0]->content ) ? $activity_details['activities'][0]->content : '';
					} else {
						$poll_title = '';
					}
					$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
					$poll_options  = isset( $activity_meta['poll_option'] ) ? $activity_meta['poll_option'] : '';

					if ( ! array_key_exists( $activity_id, $uptd_votes ) ) {
						if ( ! empty( $poll_options ) && is_array( $poll_options ) ) {
							foreach ( $poll_options as $key => $value ) {
								if ( isset( $activity_meta['poll_total_votes'] ) ) {
									$total_votes = $activity_meta['poll_total_votes'];
								} else {
									$total_votes = 0;
								}
								if ( isset( $activity_meta['poll_optn_votes'] ) && array_key_exists( $key, $activity_meta['poll_optn_votes'] ) ) {
									$this_optn_vote = $activity_meta['poll_optn_votes'][ $key ];
								} else {
									$this_optn_vote = 0;
								}

								if ( $total_votes != 0 ) {
									$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 );
								} else {
									$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
								}

								$bpolls_votes_txt = '(&nbsp;' . $this_optn_vote . '&nbsp;' . _x( 'of', 'Poll Activity', 'buddypress-polls' ) . '&nbsp;' . $total_votes . '&nbsp;)';

								 $uptd_votes[ $activity_id ][] = array(
									 'poll_title' => $poll_title,
									 'label'      => $value,
									 'y'          => $vote_percent,
									 'color'      => bpolls_color(),

								 );
							}
						}
					}
				}
			}
		}

		wp_enqueue_script( 'bpolls-poll-activity-graph-js' . $hook, BPOLLS_PLUGIN_URL . '/public/js/poll-activity-graph.js', array( 'jquery' ), BPOLLS_PLUGIN_VERSION );

		wp_localize_script(
			'bpolls-poll-activity-graph-js' . $hook,
			'bpolls_wiget_obj',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'bpolls_widget_security' ),
				'votes'      => json_encode( $uptd_votes ),
			)
		);

		wp_enqueue_script( 'bpolls-poll-activity-chart-js' . $hook, BPOLLS_PLUGIN_URL . '/public/js/Chart.min.js', array( 'jquery' ), BPOLLS_PLUGIN_VERSION );
	}

	/**
	 * Extends our front-end output method.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Array of arguments for the widget.
	 * @param array $instance Widget instance data.
	 */
	public function widget( $args, $instance ) {
		global $wpdb;

		if ( ! is_user_logged_in() ) {
			return;
		}

		extract( $args );

		if ( empty( $instance['title'] ) ) {
			$instance['title'] = __( 'Poll Activity', 'buddypress-polls' );
		}

		/**
		 * Filters the title of the Poll graph widget.
		 *
		 * @since 2.8.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $before_widget;

		echo $before_title . $title . $after_title;

		$activity = ! empty( $instance['activity'] ) ? (int) $instance['activity'] : '';

		global $activities_template;

		// Back up the global.
		$old_activities_template = $activities_template;
			$act_args            = array(
				'action'  => 'activity_poll',
				'type'    => 'activity_poll',
				'include' => $activity,
			);

			if ( bp_has_activities( $act_args ) ) {
				?>
			<div class="bpolls-activity-select" style="text-align: center">
						<?php
						while ( bp_activities() ) :
							bp_the_activity();
							?>
							<?php bp_activity_content_body(); ?>
						<?php endwhile; ?>
			</div>
			<canvas class="poll-activity-chart" data-id="<?php echo $activity; ?>" id="bpolls-activity-chart-<?php echo $activity; ?>" width="800" height="450"></canvas>
				<?php
			} else {
				?>
				<div class="bpolls-empty-messgae">
					<?php _e( 'No polls created.', 'buddypress-polls' ); ?>
				</div>
			<?php } ?>
		<?php
		echo $after_widget;
		// Restore the global.
		$activities_template = $old_activities_template;
	}

	/**
	 * Extends our update method.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New instance data.
	 * @param array $old_instance Original instance data.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['activity'] = strip_tags( $new_instance['activity'] );

		return $instance;
	}

	/**
	 * Extends our form method.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current instance.
	 * @return mixed
	 */
	public function form( $instance ) {
		 global $activities_template;

		 // Back up the global.
		$old_activities_template = $activities_template;

		$act_args = array(
			'action' => 'activity_poll',
			'type'   => 'activity_poll',
		);

		if ( bp_has_activities( $act_args ) ) {
			$act_default = $activities_template->activities[0]->id;
		} else {
			$act_default = '';
		}

		$defaults = array(
			'title'       => __( 'Poll Activity', 'buddypress-polls' ),
			'activity_id' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title    = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$activity = isset( $instance['activity'] ) ? strip_tags( $instance['activity'] ) : '';
		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'buddypress' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
		<p>
			<?php if ( bp_has_activities( $act_args ) ) { ?>
				<label for="<?php echo $this->get_field_id( 'activity' ); ?>"><?php _e( 'Select Poll activity to display:', 'buddypress' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'activity' ); ?>" id="<?php echo $this->get_field_id( 'activity' ); ?>">
					<?php
					while ( bp_activities() ) :
						bp_the_activity();
						?>
						<option value="<?php bp_activity_id(); ?>" <?php selected( $activity, bp_get_activity_id() ); ?>><?php bp_activity_content_body(); ?></option>
					<?php endwhile; ?>
				</select>
			<?php } else { ?>
				<label for="<?php echo $this->get_field_id( 'activity' ); ?>"><?php _e( 'No polls are created yet.', 'buddypress' ); ?></label>
			<?php	} ?>

		</p>
		<?php
		// Restore the global.
		$activities_template = $old_activities_template;
	}
}

/*
add_action(
	'widgets_init',
	function() {
		register_widget( 'BP_Poll_Activity_Widget' );
	}
);
*/
