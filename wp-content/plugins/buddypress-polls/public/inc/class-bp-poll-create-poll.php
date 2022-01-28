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
class BP_Poll_Create_Poll_Widget extends WP_Widget {

	/**
	 * Working as a poll activity, we get things done better.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		 $widget_ops = array(
			 'description'                 => __( 'A dynamic poll system. Users can give votes dynamically via widget.', 'buddypress-polls' ),
			 'classname'                   => 'widget_bp_poll_create_poll_widget buddypress widget',
			 'customize_selective_refresh' => true,
		 );
		 parent::__construct( false, _x( '(BuddyPress) Dynamic Poll Widget', 'widget name', 'buddypress-polls' ), $widget_ops );

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
			$instance['title'] = __( 'Create Poll', 'buddypress-polls' );
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

							<?php do_action( 'bp_polls_activity_entry_content' ); ?>
						<?php endwhile; ?>
			</div>
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

		$instance['title']    = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['activity'] = isset( $new_instance['title'] ) ? strip_tags( $new_instance['activity'] ) : '';

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
			'title'       => __( 'Create Poll', 'buddypress-polls' ),
			'activity_id' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title    = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$activity = isset( $instance['activity'] ) ? strip_tags( $instance['activity'] ) : '';
		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'buddypress-polls' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
		<p>			
			<?php if ( bp_has_activities( $act_args ) ) { ?>
				<label for="<?php echo $this->get_field_id( 'activity' ); ?>"><?php _e( 'Select Poll activity to display:', 'buddypress-polls' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'activity' ); ?>" id="<?php echo $this->get_field_id( 'activity' ); ?>">
					<?php
					while ( bp_activities() ) :
						bp_the_activity();
						?>
						<option value="<?php bp_activity_id(); ?>" <?php selected( $activity, bp_get_activity_id() ); ?>><?php bp_activity_content_body(); ?></option>
					<?php endwhile; ?>
				</select>
			<?php	} else { ?>
				<label for="<?php echo $this->get_field_id( 'activity' ); ?>"><?php _e( 'No polls are created yet.', 'buddypress-polls' ); ?></label>
			<?php }?>
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
		register_widget( 'BP_Poll_Create_Poll_Widget' );
	}
);
*/