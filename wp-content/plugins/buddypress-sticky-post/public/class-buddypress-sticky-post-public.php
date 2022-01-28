<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Sticky_Post
 * @subpackage Buddypress_Sticky_Post/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Sticky_Post
 * @subpackage Buddypress_Sticky_Post/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Sticky_Post_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_buddypress() ) {

			/**
			* This function is provided for demonstration purposes only.
			*
			* An instance of this class should be passed to the run() function
			* defined in Buddypress_Sticky_Post_Loader as all of the hooks are defined
			* in that particular class.
			*
			* The Buddypress_Sticky_Post_Loader will then create the relationship
			* between the defined hooks and the functions defined in this
			* class.
			*/

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-sticky-post-public.css', array(), $this->version, 'all' );

			if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
			}
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_buddypress() ) {

			/**
			* This function is provided for demonstration purposes only.
			*
			* An instance of this class should be passed to the run() function
			* defined in Buddypress_Sticky_Post_Loader as all of the hooks are defined
			* in that particular class.
			*
			* The Buddypress_Sticky_Post_Loader will then create the relationship
			* between the defined hooks and the functions defined in this
			* class.
			*/

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-sticky-post-public.js', array( 'jquery' ), $this->version, false );
			$active_template = get_option( '_bp_theme_package_id' );
			// Get Data
			$localize_data = array(
				'current_component' => bp_is_groups_component() ? 'groups' : 'activity',
				'security_nonce'    => wp_create_nonce( 'bpsp-ajax-security' ),
				'unpin_post'        => bpsp_get_unpin_post_label(),
				'pin_post'          => bpsp_get_pin_post_label(),
				'active_template'   => $active_template,
			);

			// Get Current Group
			if ( bp_is_active( 'groups' ) ) {
				$localize_data['current_group'] = bp_get_current_group_id();
			}

			// Localize Script.
			wp_localize_script( $this->plugin_name, 'BuddyPress_Sticky_Posts', $localize_data );
		}

	}

	public function bpsp_activity_pin_toggle_icon() {

		if ( ! bpsp_user_can_pin_posts() ) {
			return;
		}

		$activity_id = bp_get_activity_id();

		$tools = array();

		$tools           = apply_filters( 'bpsp_activity_action', $tools, $activity_id );
		$active_template = get_option( '_bp_theme_package_id' );

		?>
		<?php foreach ( $tools as $tool ) : ?>
			<?php if ( 'nouveau' == $active_template ) { ?>
				<div class="generic-button">
			<?php } ?>
					<a href="JavaScript:void(0)" class="button item-button bp-secondary-action bp-tooltip <?php echo esc_attr( bpsp_generate_class( $tool['class'] ) ); ?>" data-bp-tooltip="<?php echo esc_attr( $tool['title'] ); ?>"
																													 <?php
																														if ( isset( $tool['action'] ) ) {
																															echo 'data-action="' . $tool['action'] . '"'; }
																														?>
					 data-activity-id="<?php echo esc_attr( $activity_id ); ?>">
						<i class="<?php echo esc_attr( $tool['icon'] ); ?>"></i><span class="pin-count"><?php echo esc_attr( $tool['title'] ); ?></span>
						<?php
						if ( 'legacy' == $active_template ) {
							echo '<span class="bpsp-txt-title">&nbsp' . $tool['title'] . '</span>';
						}
						?>
					</a>
			<?php if ( 'nouveau' == $active_template ) { ?>
				</div>
			<?php } ?>
		<?php endforeach; ?>
		<?php
	}

	public function bpsp_handle_pin_unpin_action() {
		// Hook.
		do_action( 'bpsp_before_handle_sticky_posts' );

		// Check Ajax Referer.
		check_ajax_referer( 'bpsp-ajax-security', 'security' );

		// Get Data.
		$data = $_POST;

		// Allowed Actions
		$allowed_actions = array( 'pin', 'unpin' );

		// Get Data.
		$post_id   = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : null;
		$group_id  = isset( $_POST['group_id'] ) ? sanitize_text_field( $_POST['group_id'] ) : null;
		$action    = isset( $_POST['operation'] ) ? sanitize_text_field( $_POST['operation'] ) : null;
		$component = isset( $_POST['component'] ) ? sanitize_text_field( $_POST['component'] ) : null;

		// Check if The Post ID & The Component are Exist.
		if ( empty( $post_id ) || empty( $component ) ) {
			$data['error'] = __( "Sorry we didn't receive enough data to process this action.", 'buddypress-sticky-post' );
			die( json_encode( $data ) );
		}

		// Check Requested Action.
		if ( empty( $action ) || ! in_array( $action, $allowed_actions ) ) {
			$data['error'] = __( 'The action you have requested does not exist.', 'buddypress-sticky-post' );
			die( json_encode( $data ) );
		}

		if ( 'pin' == $action ) {
			// Pin Activity.
			bpsp_add_sticky_post( $component, $post_id, $group_id );
			$data['action'] = 'unpin';
			$data['msg']    = __( 'The activity was pinned successfully', 'buddypress-sticky-post' );
		} elseif ( 'unpin' == $action ) {
			// Unpin Activity.
			bpsp_delete_sticky_post( $component, $post_id, $group_id );
			$data['action'] = 'pin';
			$data['msg']    = __( 'The activity is unpinned successfully', 'buddypress-sticky-post' );
		}

		die( json_encode( $data ) );

	}

	/**
	 * Callback for bp_get_activity filter
	 * @param query $query Contains all queried activities
	 * @param args $args Conatins arguments for query
	 */
	public function bpsp_profile_wall_set_sticky_post( $query, $args ) {
		/*  Return query when activities empty and filter action blog ajde events*/
		if ( isset( $query['activities'] ) && empty( $query['activities'] ) && isset( $args['filter']['action'] ) && $args['filter']['action'] === 'new_blog_ajde_events' ) {
			return $query;
		}	
		/*  Return query when activities not empty and filter action Activity Poll */
		if ( isset( $args['action'] ) && $args['action'] === 'activity_poll' ) {
			return $query;
		}
		if ( isset( $query['activities'] ) && ! empty( $query['activities'] ) && isset( $args['filter']['action'] ) && $args['filter']['action'] === 'activity_poll' ) {
			return $query;
		}
		
		/*  Return query when activities empty and filter action blog post*/
		if ( isset( $query['activities'] ) && empty( $query['activities'] ) && isset( $args['filter']['action'] ) && $args['filter']['action'] === 'new_blog_post' ) {
			return $query;
		}
		
		if ( ! empty( $_REQUEST['page'] ) && 'bp-activity' == $_REQUEST['page'] ) {
			return $query;
		}
		
		if ( ! empty( $_REQUEST['search_terms'] ) && '' != $_REQUEST['search_terms'] ) {
			return $query;
		}
		
		if ( ! bp_is_group_activity() && ! bp_is_activity_directory() && ( isset( $args['scope'] ) && $args['scope'] != 'just-me' &&  $args['scope'] != 'just-me,mentions' ) && ( isset( $args['filter']['user_id'] ) && $args['filter']['user_id'] == 0 ) ) {
			return $query;
		}

		// Get Sticky Posts ID's.
		$posts_ids = bpsp_get_sticky_posts_ids();
		if ( empty( $posts_ids ) ) {
			return $query;
		}

		// Get Sticky Posts Number.
		$count = count( explode( ',', $posts_ids ) );

		// Get Sticky Activities.
		// $sticky_activities = BP_Activity_Activity::get(
		// array( 'in' => $posts_ids, 'page' => 1, 'per_page' => $count, 'show_hidden' => 1, 'display_comments' => 'threaded' )
		// );

		if ( bp_is_group() ) {
			$sticky_activities = BP_Activity_Activity::get(
				array(
					'in'               => $posts_ids,
					'show_hidden'      => 1,
					'display_comments' => 'threaded',
				)
			);

		} else {
			$activity_args = array(
					'in'               => $posts_ids,
					'display_comments' => 'threaded',
				);
			if ( bp_is_user_activity() ){
				$activity_args['filter'] = $args['filter'];				
			}
			$sticky_activities = BP_Activity_Activity::get( $activity_args );	
		}

		$query['sticky_activities'] = $sticky_id = array();		
		if ( empty( $posts_ids ) || isset( $args['page'] ) && $args['page'] > 1 ) {

			if ( isset( $sticky_activities['activities'] ) && ! empty( $sticky_activities['activities'] ) ) {
				foreach ( $sticky_activities['activities'] as $act ) {
					$sticky_id[] = $act->id;
				}
				/* Exclude sticky activity from actual activity  */
				foreach ( $query['activities'] as $key => $act ) {
					if ( ! in_array( $act->id, $sticky_id ) ) {
						$query['sticky_activities'][] = $act;
						$sticky_id[]                  = $act->id;
					}
				}
				$query['activities'] = $query['sticky_activities'];
				unset( $query['sticky_activities'] );
			}
			return $query;
		}

		if ( isset( $sticky_activities['activities'] ) && ! empty( $sticky_activities['activities'] ) ) {
			$action = ( isset( $args['filter']['action'] ) ) ? $args['filter']['action'] : '';
			// Is true in case new post of custom post type is created as activity.
			if ( strpos( $action, 'new_blog_' ) !== false ) {
				foreach ( $sticky_activities['activities'] as $key => $act ) {
					if ( ! in_array( $act->id, $sticky_id ) ) {
						$query['sticky_activities'][] = $act;
						$sticky_id[]                  = $act->id;
					}
				}
			} else {
				// Merge Sticky activity and actual acivity.
				$query['activities'] = array_merge( $sticky_activities['activities'], $query['activities'] );

				/* Exclude sticky activity from actual activity  */
				foreach ( $query['activities'] as $key => $act ) {
					if ( ! in_array( $act->id, $sticky_id ) ) {
						$query['sticky_activities'][] = $act;
						$sticky_id[]                  = $act->id;
					}
				}
				$query['activities'] = $query['sticky_activities'];
				unset( $query['sticky_activities'] );
			}
		}
		return $query;
	}

	public function bpsp_exclude_sticky_posts( $query ) {

		if ( ! bp_is_group_activity() && ! bp_is_activity_directory() ) {
			return $query;
		}
		// Get Posts Per Page Number.
		$sticky_posts = bpsp_get_sticky_posts();
		$sticky_posts = implode( ',', $sticky_posts );
		if ( ! empty( $query ) ) {
			$query .= '&';
		}

		// Convert Query into Args.
		$args = wp_parse_args( $query );

		// Exclude Activities.
		if ( ! empty( $args['exclude'] ) ) {
			$query .= 'exclude=' . $args['exclude'] . ',' . $sticky_posts;
		} else {
			$query .= 'exclude=' . $sticky_posts;
		}

		return $query;
	}

	public function bpsp_bp_get_activity_action( $action, $activity_template, $r ) {

		$activity_id = $activity_template->id;
		if ( ! bpsp_is_post_pinned( $activity_id ) ) {
			return $action;
		}
		
		$bpsp_general_settings = get_option( 'bpsp_general_settings' );

		$pin_rebbon_color = ( isset($bpsp_general_settings['pin_rebbon_color'])) ? 'style="background:'. $bpsp_general_settings['pin_rebbon_color'].'"' : '';
		// Get Tag.
		$pinned_tag = '<div class="bpsp-pinned-post-tag"><span ' . $pin_rebbon_color. '><i class="fa fa-thumb-tack fa-thumbtack"></i>' . __( 'Pinned', 'buddypress-sticky-post' ) . '</span></div>';

		// Filter Pinned Tag.
		$pinned_tag = apply_filters( 'bpsp_activity_pinned_tag', $pinned_tag );

		return $action . $pinned_tag;
	}

	public function bpsp_before_activity_entry_comments() {
		$activity_id = bp_get_activity_id();
		if ( ! bpsp_is_post_pinned( $activity_id ) ) {
			return;
		}
		
		$bpsp_general_settings = get_option( 'bpsp_general_settings' );

		if ( function_exists('buddypress') && isset(buddypress()->buddyboss )) {
			$bbplatform_pin = 'bbplatform_pin_sticky';
		}

		$pin_rebbon_color = ( isset($bpsp_general_settings['pin_rebbon_color'])) ? 'style="background:'. $bpsp_general_settings['pin_rebbon_color'].'"' : '';
		// Get Tag.
		$pinned_tag = '<div class="bpsp-pinned-post-tag ' . $bbplatform_pin . '"><span ' . $pin_rebbon_color. '><i class="fa fa-thumb-tack fa-thumbtack"></i>' . __( 'Pinned', 'buddypress-sticky-post' ) . '</span></div>';

		// Filter Pinned Tag.
		$pinned_tag = apply_filters( 'bpsp_activity_pinned_tag', $pinned_tag );

		echo $pinned_tag;
	}

	/**
	 * Function to add pinned activity class in activity stream.
	 */
	public function bpsp_bp_get_activity_css_class( $class ) {

		// Get Activity ID.
		$activity_id = bp_get_activity_id();

		// Check if activity is pinned.
		if ( ! bpsp_is_post_pinned( $activity_id ) ) {
			return $class;
		}

		// Add Pinned Class.
		$class .= ' bpsp-pinned-post';

		// Remove Data Class.
		// $class = str_replace( 'date-recorded-', 'date-', $class );

		return $class;

	}

	public function bpsp_add_body_class( $classes ) {
		$classes[] = 'buddypress-sticky-post';
		return $classes;
	}

	public function bpsp_bp_before_activity_entry() {

		$posts_ids = bpsp_get_sticky_posts_ids();

	}
}
