<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Polls_Public {

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
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Polls_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Polls_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $wp_styles, $post;

		$current_component = '';
		if ( isset( $post->ID ) && $post->ID != '' && $post->ID != '0' ) {
			$_elementor_controls_usage = get_post_meta( $post->ID, '_elementor_controls_usage', true );
			if ( ! empty( $_elementor_controls_usage ) ) {
				foreach ( $_elementor_controls_usage as $key => $value ) {
					if ( $key == 'buddypress_shortcode_activity_widget' || $key == 'bp_newsfeed_element_widget' || $key == 'bbp-activity' ) {
						$current_component = 'activity';
						break;
					}
				}
			}
		}
		$srcs = array_map( 'basename', (array) wp_list_pluck( $wp_styles->registered, 'src' ) );
		if ( is_buddypress()
			|| is_active_widget( false, false, 'bp_poll_activity_graph_widget', true )
			|| is_active_widget( false, false, 'bp_poll_create_poll_widget', true )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'activity-listing' ) ) )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bppfa_postform' ) ) )
			|| $current_component == 'activity'
			) {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-polls-public.css', array(), time(), 'all' );

			// if ( in_array( 'jquery.datetimepicker.css', $srcs ) || in_array( 'jquery.datetimepicker.min.css', $srcs ) ) {
			// * echo 'datetimepicker registered'; */
			// } else {
			// wp_enqueue_style( $this->plugin_name . '-time', plugin_dir_url( __FILE__ ) . 'css/jquery.datetimepicker.css', array(), time(), 'all' );
			// }

			wp_enqueue_style( $this->plugin_name . '-time', plugin_dir_url( __FILE__ ) . 'css/jquery.datetimepicker.css', array(), time(), 'all' );

			if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
			}

			add_filter( 'media_upload_tabs', array( $this, 'bpolls_remove_media_library_tab' ) );
			add_filter( 'media_view_strings', array( $this, 'bpolls_remove_medialibrary_tab' ) );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Polls_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Polls_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;
		$current_component = '';
		if ( isset( $post->ID ) && $post->ID != '' && $post->ID != '0' ) {
			$_elementor_controls_usage = get_post_meta( $post->ID, '_elementor_controls_usage', true );
			if ( ! empty( $_elementor_controls_usage ) ) {
				foreach ( $_elementor_controls_usage as $key => $value ) {
					if ( $key == 'buddypress_shortcode_activity_widget' || $key == 'bp_newsfeed_element_widget' || $key == 'bbp-activity' ) {
						$current_component = 'activity';
						break;
					}
				}
			}
		}

		if ( is_buddypress()
				|| is_active_widget( false, false, 'bp_poll_activity_graph_widget', true )
				|| is_active_widget( false, false, 'bp_poll_create_poll_widget', true )
				|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'activity-listing' ) ) )
				|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bppfa_postform' ) ) )
				|| $current_component == 'activity'
				) {
			if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			wp_enqueue_media();
			wp_enqueue_script( $this->plugin_name . '-timejs', plugin_dir_url( __FILE__ ) . 'js/jquery.datetimepicker.js', array( 'jquery' ), time(), false );
			wp_enqueue_script( $this->plugin_name . '-timefulljs', plugin_dir_url( __FILE__ ) . 'js/jquery.datetimepicker.full.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-polls-public.js', array( 'jquery' ), time(), false );

			if ( 'REIGN' == wp_get_theme() || 'REIGN Child' == wp_get_theme() ) {
				$body_polls_class = true;
			} else {
				$body_polls_class = false;
			}

			$rt_poll_fix = false;
			if ( class_exists( 'RTMedia' ) ) {
				$rt_poll_fix = true;
			}

			$active_template = get_option( '_bp_theme_package_id' );
			if ( 'legacy' == $active_template ) {
				$nouveau = false;
			} elseif ( 'nouveau' == $active_template ) {
				$nouveau = true;
			}

			wp_localize_script(
				$this->plugin_name,
				'bpolls_ajax_object',
				array(
					'ajax_url'        => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'      => wp_create_nonce( 'bpolls_ajax_security' ),
					'submit_text'     => __( 'Submitting vote', 'buddypress-polls' ),
					'optn_empty_text' => __( 'Please select your choice.', 'buddypress-polls' ),
					'reign_polls'     => $body_polls_class,
					'rt_poll_fix'     => $rt_poll_fix,
					'nouveau'         => $nouveau,
				)
			);
		}
	}

	/**
	 * Function to render polls html.
	 *
	 * @since    1.0.0
	 */
	public function bpolls_polls_update_html() {
		$bpolls_settings = get_site_option( 'bpolls_settings' );
		global $current_user;
		$multi_true = false;
		if ( isset( $bpolls_settings['multiselect'] ) ) {
			$multi_true = true;
		}

		$poll_cdate = false;
		if ( isset( $bpolls_settings['close_date'] ) ) {
			$poll_cdate = true;
		}

		$image_attachment = false;
		if ( isset( $bpolls_settings['enable_image'] ) ) {
			$image_attachment = true;
		}

		if ( isset( $bpolls_settings['limit_poll_activity'] ) && $bpolls_settings['limit_poll_activity'] == 'user_role' ) {
			$bpolls_settings['poll_user_role'] = ( isset( $bpolls_settings['poll_user_role'] ) ) ? $bpolls_settings['poll_user_role'] : array();
			$user_roles                        = array_intersect( $current_user->roles, $bpolls_settings['poll_user_role'] );
			if ( empty( $user_roles ) ) {
				return true;
			}
		}

		if ( isset( $bpolls_settings['limit_poll_activity'] ) && $bpolls_settings['limit_poll_activity'] == 'member_type' ) {
			$member_type = bp_get_member_type( $current_user->ID );

			if ( ! isset( $bpolls_settings['poll_member_type'] ) || ! in_array( $member_type, $bpolls_settings['poll_member_type'] ) ) {
				return true;
			}
		}

		?>
		<div class="bpolls-html-container">
			<span class="bpolls-icon"><i class="fa fa-bar-chart"></i></span>

		</div>
		<div class="bpolls-polls-option-html">
			<div class="bpolls-cancel-div">
				<a class="bpolls-cancel" href="JavaScript:void(0);"><?php esc_html_e( 'Cancel Poll', 'buddypress-polls' ); ?></a>
			</div>
			<div class="polls-option-image-div">
				<div class="bpolls-option-actions-wrap">
					<div class="bpolls-sortable">
						<div class="bpolls-option">
							<a class="bpolls-sortable-handle" title="Move" href="#"><i class="fa fa-arrows-alt"></i></a>
							<input name="bpolls_input_options" class="bpolls-input" placeholder="<?php esc_html_e( 'Option 1', 'buddypress-polls' ); ?>" type="text">
							<a class="bpolls-option-delete" title="Delete" href="JavaScript:void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
						</div>
						<div class="bpolls-option">
							<a class="bpolls-sortable-handle" title="Move" href="#"><i class="fa fa-arrows-alt"></i></a>
							<input name="bpolls_input_options" class="bpolls-input" placeholder="<?php esc_html_e( 'Option 2', 'buddypress-polls' ); ?>" type="text">
							<a class="bpolls-option-delete" title="Delete" href="JavaScript:void(0);"><i class="fa fa-trash" aria-hidden="true"></i></a>
						</div>
					</div>
					<div class="bpolls-option-action">
						<a href="JavaScript:void(0);" class="bpolls-add-option button"><?php esc_html_e( 'Add new option', 'buddypress-polls' ); ?></a>
						<?php if ( $poll_cdate ) { ?>
							<div class="bpolls-date-time">
								<input id="bpolls-datetimepicker" name="bpolls-close-date" type="textbox" value="" placeholder="<?php esc_html_e( 'Poll closing date & time', 'buddypress-polls' ); ?>">
							</div>
						<?php } ?>
					</div>
					<?php if ( $multi_true ) { ?>
						<div class="bpolls-checkbox">
							<input id="bpolls-alw-multi" name="bpolls_multiselect" class="bpolls-allow-multiple" type="checkbox" value="yes">
							<label class="lbl" for="bpolls-alw-multi"><?php esc_html_e( 'Allow multiple options selection', 'buddypress-polls' ); ?></label>
						</div>
					<?php } ?>
					<?php if ( isset( $bpolls_settings['enable_thank_you_message'] ) ) { ?>
						<div class="bpolls-checkbox bpolls-feedback">
							<span><?php esc_html_e( 'Follow-up', 'buddypress-polls' );?></span>
							<input type="text" id="bpolls-thankyou-feedback" name="bpolls_thankyou_feedback" class="bpolls-thankyou-feedback"  value="" placeholder="<?php esc_html_e('Enter Message', 'buddypress-polls' )?>">

						</div>
					<?php } ?>
					<?php if ( $image_attachment ) { ?>
						<button type='button' class="dashicons dashicons-admin-media" id="bpolls-attach-image"></button>
					<?php } ?>
				</div>
				<?php if ( $image_attachment ) { ?>
					<div class="bpolls-image-upload">
						<img id="bpolls-image-preview" />
						<input type="hidden" id="bpolls-attachment-url" name="bpolls-attachment-url">
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Filters the default activity types to add poll type activity.
	 *
	 * @since 1.0.0
	 *
	 * @param array $types Default activity types to moderate.
	 */
	public function bpolls_add_polls_type_activity( $types ) {
		$types[] = 'activity_poll';
		return $types;
	}

	/**
	 * Register the activity stream actions for poll updates.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_register_activity_actions() {
		$bp = buddypress();
		bp_activity_set_action(
			$bp->activity->id,
			'activity_poll',
			__( 'Polls Update', 'buddypress-polls' ),
			array( $this, 'bp_activity_format_activity_action_activity_poll' ),
			__( 'Poll', 'buddypress-polls' ),
			array( 'activity', 'group', 'member', 'member_groups' )
		);
	}

	public function bpolls_activity_action_wall_posts( $retval, $activity ) {
		if ( 'activity_poll' !== $activity->type ) {
			return $retval;
		}

		// $retval = sprintf( __( '%s created a poll', 'buddypress-polls' ), bp_core_get_userlink( $activity->user_id ) );
		return $retval;
	}

	/**
	 * Format 'activity_poll' activity actions.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action   Static activity action.
	 * @param object $activity Activity data object.
	 * @return string $action
	 */
	function bp_activity_format_activity_action_activity_poll( $action, $activity ) {
		$action = sprintf( __( '%s created a poll', 'buddypress-polls' ), bp_core_get_userlink( $activity->user_id ) );

		/**
		 * Filters the formatted activity action update string.
		 *
		 * @since 1.0.0
		 *
		 * @param string               $action   Activity action string value.
		 * @param BP_Activity_Activity $activity Activity item object.
		 */
		return apply_filters( 'bp_activity_new_poll_action', $action, $activity );
	}

	/**
	 * To set activity action for poll type activity in group.
	 *
	 * @param string $activity_action The group activity action.
	 * @since 1.0.0
	 */
	public function bpolls_groups_activity_new_update_action( $activity_action ) {
		global $bp;
		$user_id = bp_loggedin_user_id();

		// if (isset($_POST['bpolls_input_options']) && !empty($_POST['bpolls_input_options']) && is_array($_POST['bpolls_input_options']) ) {
		// $activity_action = sprintf(__('%1$s created a poll in the group %2$s', 'buddypress'), bp_core_get_userlink($user_id), '<a href="' . bp_get_group_permalink($bp->groups->current_group) . '">' . esc_attr($bp->groups->current_group->name) . '</a>');
		// }

		$_check_type = '';
		$_check_type = get_option( 'temp_poll_type' );

		if ( $_check_type && $_check_type == 'yes' ) {
			$activity_action = sprintf( __( '%1$s created a poll in the group %2$s', 'buddypress-polls' ), bp_core_get_userlink( $user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>' );
		}
		return $activity_action;
	}

	/**
	 * Function to set activity type activity_poll.
	 *
	 * @since 1.0.0
	 * @param array $activity Activity object.
	 */
	public function bpolls_update_poll_type_activity( $activity ) {
		// if (isset($_POST['bpolls_input_options']) && !empty($_POST['bpolls_input_options']) && is_array($_POST['bpolls_input_options']) ) {
		// $activity->type = 'activity_poll';
		// }

		$_check_type = '';
		$_check_type = get_option( 'temp_poll_type' );

		if ( $_check_type && $_check_type == 'yes' ) {
			$activity->type = 'activity_poll';
			delete_option( 'temp_poll_type' );
		}
	}

	/**
	 * Action performed to save the activity meta on poll update.
	 *
	 * @param string $content The actvity content.
	 * @param int    $user_id User id.
	 * @param int    $activity_id Activity id.
	 * @since 1.0.0
	 */
	public function bpolls_update_poll_activity_meta( $content, $user_id, $activity_id, $g_activity_id = null ) {
		if ( isset( $g_activity_id ) ) {
			$activity_id = $g_activity_id;
		}
		global $wpdb;
		$activity_tbl = $wpdb->base_prefix . 'bp_activity';

		if ( isset( $_POST['bpolls_input_options'] ) && ! empty( $_POST['bpolls_input_options'] ) ) {
			if ( isset( $_POST['bpolls_multiselect'] ) && $_POST['bpolls_multiselect'] == 'yes' ) {
				$multiselect = 'yes';
			} else {
				$multiselect = 'no';
			}

			if ( isset( $_POST['bpolls-close-date'] ) && ! empty( $_POST['bpolls-close-date'] ) ) {
				$close_date = $_POST['bpolls-close-date'];
			} else {
				$close_date = 0;
			}

			$poll_optn_arr = array();
			foreach ( (array) $_POST['bpolls_input_options'] as $key => $value ) {
				if ( $value != '' ) {
					$poll_key                   = str_replace( '%', '', sanitize_title( $value ) );
					$poll_optn_arr[ $poll_key ] = $value;
				}
			}

			$bpolls_thankyou_feedback = '';
			if ( isset( $_POST['bpolls_thankyou_feedback'] ) && ! empty( $_POST['bpolls_thankyou_feedback'] ) ) {
				$bpolls_thankyou_feedback = $_POST['bpolls_thankyou_feedback'];
			}
			$poll_meta = array(
				'poll_option' => $poll_optn_arr,
				'multiselect' => $multiselect,
				'close_date'  => $close_date,
				'bpolls_thankyou_feedback'  => $bpolls_thankyou_feedback,
			);
			bp_activity_update_meta( $activity_id, 'bpolls_meta', $poll_meta );

			$poll_image = get_option( 'temp_poll_image' );
			if ( $poll_image ) {
				bp_activity_update_meta( $activity_id, 'bpolls_image', $poll_image );
				delete_option( 'temp_poll_image' );
			}
		}
		delete_option( 'temp_poll_image' );
	}

	/**
	 * Filters the new poll activity content for current activity item.
	 *
	 * @since 1.0.0
	 *
	 * @param string $activity_content Activity content posted by user.
	 */
	public function bpolls_update_poll_activity_content( $act = null, $activity_obj = array() ) {
		global $current_user;
		$user_id     = get_current_user_id();
		$activity_id = bp_get_activity_id();
		if ( isset( $act ) && $act != null ) {
			$activity_id = $act;
		}
		$activity_poll_type = '';

		if ( !empty($activity_obj) && $activity_obj->type != '') {
			$activity_poll_type = $activity_obj->type;
		}

		$bpolls_settings = get_site_option( 'bpolls_settings' );

		$submit       = false;
		$hide_results = false;

		$poll_style      = 'style="display:none;"';
		$bpoll_user_vote = get_user_meta( $user_id, 'bpoll_user_vote', true );
		if ( $bpoll_user_vote ) {
			if ( ! array_key_exists( $activity_id, $bpoll_user_vote ) ) {
				$submit = true;
				if ( isset( $bpolls_settings['hide_results'] ) ) {
					$hide_results = true;
				}
				$poll_style = '';
			}
		} else {
			$submit = true;
			if ( isset( $bpolls_settings['hide_results'] ) ) {
				$hide_results = true;
			}
			$poll_style = '';
		}

		$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );

		$total_votes = bp_activity_get_meta( $activity_id, 'bpolls_total_votes', true );

		$poll_image = bp_activity_get_meta( $activity_id, 'bpolls_image', true );

		$poll_closing = false;
		if ( isset( $activity_meta['close_date'] ) && isset( $bpolls_settings['close_date'] ) && $activity_meta['close_date'] != 0 ) {
			$current_time    = new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) );
			$close_date      = $activity_meta['close_date'];
			$close_date_time = new DateTime( $close_date );
			if ( $close_date_time > $current_time ) {
				$poll_closing = true;
			}
		} else {
			$poll_closing = true;
		}

		$u_meta = array();
		if ( isset( $bpoll_user_vote[ $activity_id ] ) ) {
			$u_meta = $bpoll_user_vote[ $activity_id ];
		}
		if ( 'activity_poll' == $activity_poll_type || isset( $activity_meta['poll_option'] ) ) {
			$poll_options     = ( isset($activity_meta['poll_option'])) ? $activity_meta['poll_option'] : array();
			$activity_content = '';

			if ( 'yes' == $activity_meta['multiselect'] ) {
				$optn_typ = 'checkbox';
			} else {
				$optn_typ = 'radio';
			}

			if ( ! empty( $poll_options ) && is_array( $poll_options ) ) {
				$activity_content .= "<div class='bpolls-options-attach-container'>";

				if ( $poll_image ) {
					$activity_content .= "<div class='bpolls-image-container'>";
					$activity_content .= "<img src='" . $poll_image . "'>";
					$activity_content .= '</div>';
				}
				$activity_content .= "<div class='bpolls-options-attach-items'><form class='bpolls-vote-submit-form' method='post' action=''>";

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
						$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 ) . '%';
					} else {
						$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
					}

					$bpolls_votes_txt = '(&nbsp;' . $this_optn_vote . '&nbsp;' . _x( 'of', 'Poll Activity', 'buddypress-polls' ) . '&nbsp;' . $total_votes . '&nbsp;)';

					if ( $hide_results && ! in_array( 'administrator', (array) $current_user->roles ) ) {
						$vote_percent     = '';
						$bpolls_votes_txt = '';
					}

					if ( in_array( $key, $u_meta ) ) {
						$checked = 'checked';
					} else {
						$checked = '';
					}

					$activity_content .= "<div class='bpolls-item'>";
					$activity_content .= "<div class='bpolls-item-width' style='width:" . $vote_percent . "'></div>";
					$activity_content .= "<span class='bpolls-votes'>" . $bpolls_votes_txt . '</span>';
					$activity_content .= "<div class='bpolls-check-radio-div'>";
					$activity_content .= "<input id='" . $key . "' name='bpolls_vote_optn[]' value='" . $key . "' type='" . $optn_typ . "' " . $checked . ' ' . $poll_style . '>';
					$activity_content .= "<label for='" . $key . "' class='bpolls-option-lbl'>" . $value . '</label>';
					$activity_content .= "<span class='bpolls-percent'>" . $vote_percent . '</span>';
					$activity_content .= '</div>';
					$activity_content .= '</div>';
				}
				$activity_content .= "<input type='hidden' name='bpoll_activity_id' value='" . $activity_id . "'>";
				$activity_content .= "<input type='hidden' name='bpoll_multi' value='" . $activity_meta['multiselect'] . "'>";
				$activity_content .= "<input type='hidden' name='bpoll_user_id' value='" . $user_id . "'>";

				if ( $submit && $poll_closing && is_user_logged_in()  ) {
					$activity_content .= "<a class='bpolls-vote-submit' href='javascript:void(0)'>" . __( 'Submit', 'buddypress-polls' ) . '</a>';
				}
				$activity_content .= '</form></div></div>';

				if ( isset( $act ) && $act != null ) {
					return $activity_content;
				} else {
					echo $activity_content;
				}
			}
		}
	}

	/**
	 * Ajax request to save poll vote.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_save_poll_vote() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpolls_save_poll_vote' ) {
			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );
			$user_id = get_current_user_id();
			parse_str( $_POST['poll_data'], $poll_data ); // This will convert the string to array
			$poll_data = filter_var_array( $poll_data, FILTER_SANITIZE_STRING );

			$activity_id = $poll_data['bpoll_activity_id'];

			$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );
			$total_votes   = bp_activity_get_meta( $activity_id, 'bpolls_total_votes', true );
			if ( ! $total_votes ) {
				$total_votes = (int) 1;
			} else {
				$total_votes = (int) $total_votes + (int) 1;
			}
			if ( array_key_exists( 'poll_optn_votes', $activity_meta ) ) {
				foreach ( $activity_meta['poll_option'] as $key => $value ) {
					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
						$activity_meta['poll_optn_votes'][ $key ] = $activity_meta['poll_optn_votes'][ $key ] + 1;
					}
				}
			} else {
				foreach ( $activity_meta['poll_option'] as $key => $value ) {
					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
						$ed = 1;
					} else {
						$ed = 0;
					}
					$poll_optn_votes[ $key ] = $ed;
				}
				$activity_meta['poll_optn_votes'] = $poll_optn_votes;
			}

			if ( array_key_exists( 'poll_total_votes', $activity_meta ) ) {
				$activity_meta['poll_total_votes'] = $activity_meta['poll_total_votes'] + 1;
			} else {
				$activity_meta['poll_total_votes'] = 1;
			}

			/* Saved user id in poll option wise */
			if ( array_key_exists( 'poll_optn_user_votes', $activity_meta ) ) {
				foreach ( $activity_meta['poll_option'] as $key => $value ) {
					if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {

						$polls_existing_useid                          = isset( $activity_meta['poll_optn_user_votes'][ $key ] ) ? $activity_meta['poll_optn_user_votes'][ $key ] : array();
						$activity_meta['poll_optn_user_votes'][ $key ] = array_merge( $polls_existing_useid, array( $user_id ) );
					}
				}
			} else {
				$poll_optn_user_votes = array();
				foreach ( $activity_meta['poll_option'] as $key => $value ) {
					$poll_optn_user_votes[ $key ] = array();
					if ( in_array( $key, $poll_data['bpolls_vote_optn'], true ) ) {
						$poll_optn_user_votes[ $key ] = array( $user_id );
					}
				}
				$activity_meta['poll_optn_user_votes'] = $poll_optn_user_votes;
			}

			/* saved User id in activity meta */
			$existing_useid              = isset( $activity_meta['poll_users'] ) ? $activity_meta['poll_users'] : array();
			$activity_meta['poll_users'] = array_merge( $existing_useid, array( $user_id ) );

			bp_activity_update_meta( $activity_id, 'bpolls_meta', $activity_meta );

			bp_activity_update_meta( $activity_id, 'bpolls_total_votes', $total_votes );

			$bpoll_user_vote = get_user_meta( $user_id, 'bpoll_user_vote', true );

			$user_vote = array();
			foreach ( $activity_meta['poll_option'] as $key => $value ) {
				if ( in_array( $key, $poll_data['bpolls_vote_optn'] ) ) {
					$user_vote[] = $key;
				}
			}
			if ( $bpoll_user_vote ) {
				if ( ! array_key_exists( $activity_id, $bpoll_user_vote ) ) {
					$bpoll_user_vote[ $activity_id ] = $user_vote;
					update_user_meta( $user_id, 'bpoll_user_vote', $bpoll_user_vote );
				}
			} else {
				$vote[ $activity_id ] = $user_vote;
				update_user_meta( $user_id, 'bpoll_user_vote', $vote );
			}

			$updated_votes = $this->bpolls_ajax_calculate_votes( $activity_id );
			echo json_encode( $updated_votes );
			die;
		}
	}

	/**
	 * Calculate poll activity votes.
	 *
	 * @since 1.0.0
	 */
	function bpolls_ajax_calculate_votes( $activity_id ) {
		$user_id = get_current_user_id();

		$activity_meta = bp_activity_get_meta( $activity_id, 'bpolls_meta' );

		$poll_options = isset( $activity_meta['poll_option'] ) ? $activity_meta['poll_option'] : '';

		$uptd_votes = array();
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
					$vote_percent = round( $this_optn_vote / $total_votes * 100, 2 ) . '%';
				} else {
					$vote_percent = __( '(no votes yet)', 'buddypress-polls' );
				}

				$bpolls_votes_txt = '(&nbsp;' . $this_optn_vote . '&nbsp;' . _x( 'of', 'Poll Activity', 'buddypress-polls' ) . '&nbsp;' . $total_votes . '&nbsp;)';

				$uptd_votes[ $key ] = array(
					'vote_percent'     => $vote_percent,
					'bpolls_votes_txt' => $bpolls_votes_txt,
				);
			}
		}
		$uptd_votes['bpolls_thankyou_feedback'] = ( isset($activity_meta['bpolls_thankyou_feedback']) && $activity_meta['bpolls_thankyou_feedback'] != '' ) ? $activity_meta['bpolls_thankyou_feedback'] : '';

		return $uptd_votes;
	}

	/**
	 * Function to show poll activity entry content while embedding.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_bp_activity_get_embed_excerpt( $content, $global_activity_content ) {
		$activity_id = $GLOBALS['activities_template']->activity->id;
		return $content . $this->bpolls_update_poll_activity_content( $activity_id, '' );
	}

	/**
	 * Function to show poll activity entry content while embedding.
	 *
	 * @since 1.0.0
	 */
	public function bpquotes_update_pols_activity_content( $activity_content, $activity_obj ) {
		$activity_id = $activity_obj->id;
		return $activity_content . $this->bpolls_update_poll_activity_content( $activity_id, $activity_obj );
	}

	/**
	 * Function to add poll css for activity embedding.
	 *
	 * @since 1.0.0
	 */
	public function bpolls_activity_embed_add_inline_styles() {
		$css = file_get_contents( BPOLLS_PLUGIN_PATH . '/public/css/buddypress-polls-public.css' );
		$css = wp_kses( $css, array( "\'", '\"' ) );
		printf( '<style type="text/css">%s</style>', $css );
	}

	public function bpolls_set_poll_type_true() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpolls_set_poll_type_true' ) {

			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );

			$is_poll = $_POST['is_poll'];
			update_option( 'temp_poll_type', $is_poll );
		}
		wp_die();
	}

	public function bpolls_update_prev_polls_total_votes() {
		$args = array(
			'show_hidden' => true,
			'action'      => 'activity_poll',
			'count_total' => true,
		);

		if ( function_exists( 'bp_has_activities' ) && bp_has_activities( $args ) ) {
			global $activities_template;
			foreach ( $activities_template->activities as $key => $act_obj ) {
				$activity_meta = (array) bp_activity_get_meta( $act_obj->id, 'bpolls_meta' );
				$total_votes   = bp_activity_get_meta( $act_obj->id, 'bpolls_total_votes', true );
				if ( array_key_exists( 'poll_total_votes', $activity_meta ) && ! $total_votes ) {
					bp_activity_update_meta( $act_obj->id, 'bpolls_total_votes', (int) $activity_meta['poll_total_votes'] );
				}
			}
		}
	}

	public function bpolls_save_image() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpolls_save_image' ) {
			check_ajax_referer( 'bpolls_ajax_security', 'ajax_nonce' );
			$image_url = $_POST['image_url'];
			update_option( 'temp_poll_image', $image_url );
			exit();
		}
	}


	/**
	 * Function to unset media library when user upload image using wp-media on fronted
	 *
	 * @since 3.3.0
	 */
	public function bpolls_remove_media_library_tab( $tabs ) {

		if ( current_user_can( 'subscriber' ) || current_user_can( 'contributor' ) ) {
			unset( $tabs['library'] );

			$contributor = get_role( 'contributor' );
			$contributor->add_cap( 'upload_files' );

			$subscriber = get_role( 'subscriber' );
			$subscriber->add_cap( 'upload_files' );

		}
		return $tabs;
	}

	/**
	 * Function to unset media library title when user upload image using wp-media on fronted
	 *
	 * @since 3.3.0
	 */

	public function bpolls_remove_medialibrary_tab( $strings ) {
		if ( current_user_can( 'subscriber' ) || current_user_can( 'contributor' ) ) {
			unset( $strings['mediaLibraryTitle'] );
			return $strings;
		} else {
			return $strings;
		}
	}

}
