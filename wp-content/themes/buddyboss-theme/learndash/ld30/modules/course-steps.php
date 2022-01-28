<?php
/**
 * LearnDash LD30 Displays a Course Prev/Next navigation.
 *
 * Available Variables:
 *
 * $course_id        : (int) ID of Course
 * $course_step_post : (int) ID of the lesson/topic post
 * $user_id          : (int) ID of User
 * $course_settings  : (array) Settings specific to current course
 * $can_complete     : (bool) Can the user mark this lesson/topic complete?
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO @37designs this is a bit confusing still, as you can still navigate left / right on lessons even with topics
$parent_id                  = ( get_post_type() === 'sfwd-lessons' ? absint( $course_id ) : learndash_course_get_single_parent_step( $course_id, get_the_ID() ) );
$learndash_previous_step_id = learndash_previous_post_link( null, 'id', $course_step_post );
if ( ( empty( $learndash_previous_step_id ) ) && ( learndash_get_post_type_slug( 'topic' ) === $course_step_post->post_type ) ) {

	/**
	 * Filters whether to show parent previous link in the course navigation.
	 *
	 * @since 3.1.0
	 *
	 * @param boolean $show_previous_link Whether to show parent previous link.
	 * @param int     $course_step_post   ID of the lesson/topic post.
	 * @param int     $user_id            User ID.
	 * @param int     $course_id          Course ID.
	 */
	if ( apply_filters( 'learndash_show_parent_previous_link', true, $course_step_post, $user_id, $course_id ) ) {
		$learndash_previous_step_id = learndash_previous_post_link( null, 'id', get_post( $parent_id ) );
	}
}

$learndash_previous_nav = learndash_previous_post_link( null, true );
$learndash_next_nav     = '';

$learndash_next_step_id = '';
$button_class           = 'ld-button ' . ( $context == 'focus' ? 'ld-button-transparent' : '' );

/*
 * See details for filter 'learndash_show_next_link' https://bitbucket.org/snippets/learndash/5oAEX
 *
 * @since version 2.3
 */

$current_complete = false;

if ( ( empty( $course_settings ) ) && ( ! empty( $course_id ) ) ) {
	$course_settings = learndash_get_setting( $course_id );
}

if ( ( isset( $course_settings['course_disable_lesson_progression'] ) ) && ( 'on' === $course_settings['course_disable_lesson_progression'] ) ) {
	$current_complete = true;
} else {

	if ( 'sfwd-topic' === $course_step_post->post_type ) {
		$current_complete = learndash_is_topic_complete( $user_id, $course_step_post->ID, $course_id );
	} elseif ( 'sfwd-lessons' === $course_step_post->post_type ) {
		$current_complete = learndash_is_lesson_complete( $user_id, $course_step_post->ID, $course_id );
	}

	if ( $current_complete !== true ) {
		$bypass_course_limits_admin_users = learndash_can_user_bypass( $user_id, 'learndash_course_progression' );
		if ( true === $bypass_course_limits_admin_users ) {
			$current_complete = true;
		}
	}
}

/**
 * Filters whether to show the next link in the course navigation.
 *
 * @since 2.3.0
 *
 * @param boolean $show_previous_link Whether to show parent previous link.
 * @param int     $course_step_post   ID of the lesson/topic post.
 * @param int     $course_id          Course ID.
 * @param int     $user_id            User ID.
 */
$learndash_maybe_show_next_step_link = apply_filters( 'learndash_show_next_link', $current_complete, $user_id, $course_step_post->ID );
if ( true === (bool) $learndash_maybe_show_next_step_link ) {
	$learndash_next_step_id = learndash_next_post_link( null, 'id', $course_step_post );
	if ( ( empty( $learndash_next_step_id ) ) && ( learndash_get_post_type_slug( 'topic' ) === $course_step_post->post_type ) ) {
		if ( learndash_is_lesson_complete( $user_id, $parent_id ) ) {
			/**
			 * Filters whether to show parent next link in the course navigation.
			 *
			 * @since 3.1.0
			 *
			 * @param boolean $show_previous_link Whether to show parent next link.
			 * @param int     $course_step_post   ID of the lesson/topic post.
			 * @param int     $user_id            User ID.
			 * @param int     $course_id          Course ID.
			 */
			if ( apply_filters( 'learndash_show_parent_next_link', true, $course_step_post, $user_id, $course_id ) ) {
				$learndash_next_step_id = learndash_next_post_link( null, 'id', get_post( $parent_id ) );
			}
		}
	}
} elseif ( ( ! is_user_logged_in() ) && ( empty( $learndash_next_step_id ) ) ) {
	$learndash_next_step_id = learndash_next_post_link( null, 'id', $course_step_post );
	if ( ( empty( $learndash_next_step_id ) ) && ( learndash_get_post_type_slug( 'topic' ) === $course_step_post->post_type ) ) {
		$learndash_next_step_id = learndash_next_post_link( null, 'id', get_post( $parent_id ) );
	}
	if ( ! empty( $learndash_next_step_id ) ) {
		if ( ! learndash_is_sample( $learndash_next_step_id ) ) {
			if ( ( ! isset( $course_settings['course_price_type'] ) ) || ( 'open' !== $course_settings['course_price_type'] ) ) {
				$learndash_next_step_id = '';
			}
		}
	}
}

/**
 * Filters to override next step post ID.
 *
 * @since 3.1.2
 *
 * @param int $learndash_next_step_id The next step post ID.
 * @param int $course_step_post       The current step WP_Post ID.
 * @param int $course_id              The current Course ID.
 * @param int $user_id                The current User ID.
 *
 * @return int $learndash_next_step_id
 */
$learndash_next_step_id = apply_filters( 'learndash_next_step_id', $learndash_next_step_id, $course_step_post->ID, $course_id, $user_id );

$complete_button = learndash_mark_complete( $course_step_post );

?>

	<div class="ld-content-actions">

		<?php
		/**
		 * Fires before the course steps (all locations).
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-all-course-steps-before', get_post_type(), $course_id, $user_id );

		/**
		 * Fires before the course steps for any context.
		 *
		 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
		 * such as `course`, `lesson`, `topic`, `quiz`, etc.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-' . $context . '-course-steps-before', get_post_type(), $course_id, $user_id );

		$learndash_current_post_type = get_post_type();

		if ( $parent_id && 'focus' !== $context ) :
			if ( $learndash_maybe_show_next_step_link ) :
				?>
				<div class="ld-content-action">
					<?php
					if ( isset( $can_complete ) && $can_complete && ! empty( $complete_button ) ) :
						echo learndash_mark_complete( $course_step_post );

					endif;
					?>
				</div>

			<?php else : ?>
				<div class="ld-content-action
				<?php
				if ( ( ! $can_complete ) && ( ! $learndash_next_step_id ) ) :
					?>
					ld-empty<?php endif; ?>">
					<?php
					if ( isset( $can_complete ) && $can_complete && ! empty( $complete_button ) ) :
						echo learndash_mark_complete( $course_step_post );
					endif;
					?>
				</div>
			<?php endif; ?>
		<?php elseif ( $parent_id && 'focus' === $context ) : ?>
			<div class="ld-content-action
			<?php
			if ( ( ! $can_complete ) && ( ! $learndash_next_step_id ) ) :
				?>
				ld-empty<?php endif; ?>">
				<?php
				if ( isset( $can_complete ) && $can_complete && ! empty( $complete_button ) ) :
					echo learndash_mark_complete( $course_step_post );
				endif;
				?>
			</div>
			<?php
		endif;

		/**
		 * Fires after the course steps (all locations).
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-all-course-steps-after', get_post_type(), $course_id, $user_id );

		/**
		 * Fires after the course steps for any context.
		 *
		 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
		 * such as `course`, `lesson`, `topic`, `quiz`, etc.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-' . $context . '-course-steps-after', get_post_type(), $course_id, $user_id );
		?>

	</div> <!--/.ld-topic-actions-->
