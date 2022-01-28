<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Status
 * @subpackage Buddypress_Status/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Status
 * @subpackage Buddypress_Status/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Status_Public {

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
		 * defined in Buddypress_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-status-public.css', array(), $this->version, 'all' );

		if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
			wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
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
		 * defined in Buddypress_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-status-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'bpsts_ajax_object',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'    => wp_create_nonce( 'bpsts_ajax_security' ),
				'char_left_txt' => __( 'characters left', 'buddypress-status' ),
				'cnf_del_txt'   => __(
					'Are you sure you want to delete this status?',
					'buddypress-status'
				),
				'cnf_del_icon'  => __(
					'Are you sure you want to delete this status icon?',
					'buddypress-status'
				),
			)
		);
	}

	/**
	 * Register the status profile menu.
	 *
	 * @since    1.0.0
	 */
	public function bpsts_add_profile_status_menu() {
		global $bp;
		if ( bp_is_my_profile() || current_user_can( 'administrator' ) ) {
			bp_core_new_subnav_item(
				array(
					'name'            => __( 'Profile Status', 'buddypress-status' ),
					'slug'            => 'status',
					'parent_url'      => trailingslashit( bp_displayed_user_domain() . 'profile' ),
					'parent_slug'     => 'profile',
					'screen_function' => array( $this, 'bpsts_show_add_profile_status_screen' ),
					'position'        => 50,
				)
			);
		}
	}

	function bpsts_show_add_profile_status_screen() {
		add_action( 'bp_template_content', array( $this, 'bpsts_add_profile_status_screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	function bpsts_add_profile_status_screen_content() {
		include 'inc/bpsts-add-status.php';
	}

	public function bpsts_render_member_status() {
		$user_id      = bp_displayed_user_id();
		$user_status  = get_user_meta( $user_id, 'bpsts_current_status', true );
		$saved_status = get_user_meta( $user_id, 'bpsts_saved_status', true );
		if ( $user_status && is_array( $saved_status ) && isset( $saved_status[ $user_status ] ) ) {
			$status_div  = "<div class='bpsts-status-div'>";
			$status_div .= $saved_status[ $user_status ];
			$status_div .= '</div>';
			echo $status_div;
		}
	}

	public function bpsts_add_status() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpsts_add_status' ) {

			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );

			$bpsts_status = $_POST['bpsts_status'];
			$user_id      = $_POST['user_id'];

			$user_saved_status = get_user_meta( $user_id, 'bpsts_saved_status', true );
			if ( ! is_array( $user_saved_status ) ) {
				$user_saved_status = array();
			}
			$status_count = count( $user_saved_status );
			$status_id    = 'status-' . time();
			if ( $status_count <= 9 ) {
				$user_saved_status[ $status_id ] = $bpsts_status;
			} else {
				array_shift( $user_saved_status );
				$user_saved_status[ $status_id ] = $bpsts_status;
			}
			update_user_meta( $user_id, 'bpsts_saved_status', $user_saved_status );

			$setcurrent = $_POST['setcurrent'];
			if ( $setcurrent ) {
				update_user_meta( $user_id, 'bpsts_current_status', $status_id );
			}

			render_user_status_row( $user_id, $bpsts_status, $status_id );

		}
		wp_die();
	}

	public function bpsts_delete_status() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpsts_delete_status' ) {

			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );

			$status_id = $_POST['status_id'];
			$user_id   = $_POST['user_id'];

			$user_saved_status = get_user_meta( $user_id, 'bpsts_saved_status', true );
			unset( $user_saved_status[ $status_id ] );
			update_user_meta( $user_id, 'bpsts_saved_status', $user_saved_status );

			$current_status = get_user_meta( $user_id, 'bpsts_current_status', true );
			if ( $current_status == $status_id ) {
				delete_user_meta( $user_id, 'bpsts_current_status' );
			}
		}
		wp_die();
	}

	public function bpsts_delete_status_icon() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpsts_delete_status_icon' ) {

			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );
			$user_id = $_POST['user_id'];
			if ( ! empty( $user_id ) ) {
				delete_user_meta( $user_id, 'bpsts_user_icon' );
			}
		}
		wp_die();
	}

	public function bpsts_current_status() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpsts_current_status' ) {
			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );

			$status_id = $_POST['status_id'];
			$user_id   = $_POST['user_id'];

			update_user_meta( $user_id, 'bpsts_current_status', $status_id );
		}
		wp_die();
	}

	public function bpsts_update_status() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpsts_update_status' ) {
			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );

			$status_id    = $_POST['status_id'];
			$user_id      = $_POST['user_id'];
			$bpsts_status = $_POST['bpsts_status'];

			$user_saved_status               = get_user_meta( $user_id, 'bpsts_saved_status', true );
			$user_saved_status[ $status_id ] = $bpsts_status;
			update_user_meta( $user_id, 'bpsts_saved_status', $user_saved_status );
			$setcurrent = $_POST['setcurrent'];
			if ( $setcurrent ) {
				update_user_meta( $user_id, 'bpsts_current_status', $status_id );
			}
		}
		wp_die();
	}

	public function bpsts_update_icon_status() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpsts_update_icon_status' ) {

			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );

			$user_id = $_POST['userid'];
			$imgname = $_POST['imgname'];
			$setnam  = $_POST['setnam'];
			$folder  = $_POST['folder'];

			$user_icon_status = array(
				'imgname' => $imgname,
				'folder'  => $folder,
				'setnam'  => $setnam,
			);
			update_user_meta( $user_id, 'bpsts_user_icon', $user_icon_status );
		}
		wp_die();
	}

	public function bpsts_show_user_icon_after_name( $username ) {

		$bpsts_gnrl_settings = get_option( 'bpsts_gnrl_settings', true );
		if ( !isset( $bpsts_gnrl_settings['prof_pg_dis_icon'] ) ) {
			return $username;
		}

		$user_id   = bp_displayed_user_id();
		$user_icon = get_user_meta( $user_id, 'bpsts_user_icon', true );
		$icon_html = '';
		if ( isset( $user_icon['folder'] ) ) {
			$url          = BPSTS_PLUGIN_URL . 'icons/' . $user_icon['folder'] . '/64/' . str_replace( '.png', '.svg', $user_icon['imgname'] );
			if ( $user_icon['folder'] == 'custom') {
				$wp_upload_dir = wp_upload_dir();
				$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $user_icon['imgname'];
			}
			$file_headers = @get_headers( $url );
			if ( isset( $file_headers ) && $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
				$icon_html = '';
			} else {
				$icon_html = '<img class="bpsts-name-icon" src="' . $url . '">';
			}
		}
		return $username . $icon_html;
	}
	
	/*
	 * Only Add Username and remove Status icon on Public Message link
	 */
	public function bpsts_bp_get_send_public_message_link( $retval){
		$user_id   = bp_displayed_user_id();
		$user_icon = get_user_meta( $user_id, 'bpsts_user_icon', true );
		$icon_html = '';
		if ( isset( $user_icon['folder'] ) && isset($user_icon['imgname']) && $user_icon['imgname'] != '') {
			if ( ! is_user_logged_in() || ! bp_is_user() || bp_is_my_profile() ) {
				$retval = '';
			} else {
				$args   = array( 'r' => bp_activity_get_user_mentionname( bp_displayed_user_id() ) );
				$url    = add_query_arg( $args, bp_get_activity_directory_permalink() );
				$retval = wp_nonce_url( $url );
			}
		}
		
		return $retval;
	}

	public function bpsts_alter_user_display_name( $username, $user_id ) {
		$bpsts_gnrl_settings = get_option( 'bpsts_gnrl_settings', true );
		if ( !isset( $bpsts_gnrl_settings['act_loop_dis_icon'] ) ) {
			return $username;
		}
		$user_icon = get_user_meta( $user_id, 'bpsts_user_icon', true );
		$icon_html = '';
		
		if ( isset( $user_icon['folder'] ) ) {
			$url          = BPSTS_PLUGIN_URL . 'icons/' . $user_icon['folder'] . '/64/' . str_replace( '.png', '.svg', $user_icon['imgname'] );
			$basedir      = BPSTS_PLUGIN_PATH . 'icons/' . $user_icon['folder'] . '/64/' . str_replace( '.png', '.svg', $user_icon['imgname'] );
			if ( $user_icon['folder'] == 'custom') {
				$wp_upload_dir = wp_upload_dir();
				$basedir = $wp_upload_dir['basedir'] . '/buddypress-status/' . $user_icon['imgname'];
				$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $user_icon['imgname'];
			}
			if ( file_exists($basedir)) {
				$icon_html = '<img class="bpsts-name-icon" src="' . $url . '">';
				
			} else {				
				$file_headers = @get_headers( $url );				
				if ( isset( $file_headers ) && $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
					$icon_html = '';
				} else {
					$icon_html = '<img class="bpsts-name-icon" src="' . $url . '">';
				}
			}
		}
		return $username . $icon_html;
	}

	public function bpsts_activity_loop_user_icon( $action, $activity, $r ) {
		$bpsts_gnrl_settings = get_option( 'bpsts_gnrl_settings', true );
		if ( !isset( $bpsts_gnrl_settings['act_loop_dis_icon'] ) ) {
			return $username;
		}
		$user_icon = get_user_meta( $activity->user_id, 'bpsts_user_icon', true );

		$icon_html = '';
		if ( isset( $user_icon['folder'] ) ) {
			$url          = BPSTS_PLUGIN_URL . 'icons/' . $user_icon['folder'] . '/64/' . str_replace( '.png', '.svg', $user_icon['imgname'] );
			if ( $user_icon['folder'] == 'custom') {
				$wp_upload_dir = wp_upload_dir();
				$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $user_icon['imgname'];
			}
			$file_headers = @get_headers( $url );
			if ( isset( $file_headers ) && $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
			} else {
				$pattern = "'</a>'";
				$action  = preg_replace( $pattern, '</a><img class="bpsts-name-icon" src="' . $url . '">', $action, 1 );
			}
		}
		return $action;
	}

	public function bpsts_alter_activity_allowed_tags( $allowed_tags ) {
		$allowed_tags['img']['class'] = array();
		return $allowed_tags;

	}

	public function bpsts_add_reactions_html() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$bpsts_gnrl_settings = get_option( 'bpsts_gnrl_settings', true );
		if ( !isset( $bpsts_gnrl_settings['reaction_dis_icon'] ) ) {
			return;
		}

		$bpsts_icon_settings = get_option( 'bpsts_icon_settings' );

		$activity_id = bp_get_activity_id();

		$user_id = get_current_user_id();

		$user_reactions = bp_get_user_meta( $user_id, 'bpsts_user_reactions', true );

		$element = array();
		if ( is_array( $user_reactions ) && array_key_exists( $activity_id, $user_reactions ) ) {
			$old_index = $user_reactions[ $activity_id ]['imgindex'];
			$element   = ( isset( $bpsts_icon_settings['reactions'][ $old_index ] ) ) ? $bpsts_icon_settings['reactions'][ $old_index ] : array();
			$first_key = $old_index;
			unset( $bpsts_icon_settings['reactions'][ $old_index ] );
		}
		$first_reaction = '';
		if ( empty( $element ) && isset( $bpsts_icon_settings['reactions'] ) ) {
			$element        = reset( $bpsts_icon_settings['reactions'] );
			$first_key      = key( $bpsts_icon_settings['reactions'] );
			$first_reaction = 'first-reaction';
		}

		if ( isset( $bpsts_icon_settings['reactions'] ) ) {
			$url = BPSTS_PLUGIN_URL . 'icons/' . $element['folder'] . '/64/' . str_replace( '.png', '.svg', $element['imgname'] );
			if ( $element['folder'] == 'custom') {
				$wp_upload_dir = wp_upload_dir();
				$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $element['imgname'];
			}
			echo '<div class="generic-button bpsts-reaction-parent">';
			echo '<div class="bpsts-open-reaction-div ' . $first_reaction . '">';
			echo '<img class="bpsts-icon-img bpsts-open-reaction" src="' . $url . '" data-index="' . $first_key . '" data-imgname="' . $element['imgname'] . '" data-folder="' . $element['folder'] . '" data-activityid="' . $activity_id . '">';
			echo '</div>';
			echo '<div class="bpsts-reaction-box">';
			foreach ( $bpsts_icon_settings['reactions'] as $key => $image ) {
				if ( isset( $image['folder'] ) ) {
					$url = BPSTS_PLUGIN_URL . 'icons/' . $image['folder'] . '/64/' . str_replace( '.png', '.svg', $image['imgname'] );
					
					if ( $image['folder'] == 'custom') {
						$wp_upload_dir = wp_upload_dir();
						$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $image['imgname'];
					}
					if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
						echo '<a href="javascript:void(0)" class="bpsts-mark-reaction" data-index="' . $key . '" data-imgname="' . $image['imgname'] . '" data-folder="' . $image['folder'] . '" data-activityid="' . $activity_id . '" data-src="' . $url . '"><img class="bpsts-icon-img" src="' . $url . '"></a>';
					}
				}
			}
			echo '</div>';
			echo '</div>';
		}
	}

	public function bpst_activity_reaction() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpst_activity_reaction' ) {

			check_ajax_referer( 'bpsts_ajax_security', 'ajax_nonce' );

			$user_id     = get_current_user_id();
			$activity_id = $_POST['activityid'];
			$imgname     = $_POST['imgname'];
			$folder      = $_POST['folder'];
			$imgurl      = $_POST['imgurl'];
			$imgindex    = $_POST['imgindex'];

			$user_reactions = bp_get_user_meta( $user_id, 'bpsts_user_reactions', true );

			$reactions_count = bp_activity_get_meta( $activity_id, 'bpsts_reaction_count', true );

			$bpsts_reactions = bp_activity_get_meta( $activity_id, 'bpsts_reactions', true );

			if ( is_array( $user_reactions ) && array_key_exists( $activity_id, $user_reactions ) ) {

				// get old reaction index of user.
				$old_index = $user_reactions[ $activity_id ]['imgindex'];

				$user_reactions[ $activity_id ] = array(
					'imgname'  => $imgname,
					'folder'   => $folder,
					'imgurl'   => $imgurl,
					'imgindex' => $imgindex,
				);
				// print_r( $user_reactions);die;
				if ( isset( $bpsts_reactions[ $old_index ]['users'] ) ) {
					$user_key = array_search( $user_id, $bpsts_reactions[ $old_index ]['users'] );
					unset( $bpsts_reactions[ $old_index ]['users'][ $user_key ] );
					if ( empty( $bpsts_reactions[ $old_index ]['users'] ) ) {
						unset( $bpsts_reactions[ $old_index ] );
					}
				}
				$bpsts_reactions[ $imgindex ]['imgdata'] = array(
					'imgname'  => $imgname,
					'folder'   => $folder,
					'imgurl'   => $imgurl,
					'imgindex' => $imgindex,
				);
				$bpsts_reactions[ $imgindex ]['users'][] = $user_id;
			} else {
				if ( ! is_array( $user_reactions ) ) {
					$user_reactions = array();
				}
				if ( ! is_array( $bpsts_reactions ) ) {
					$bpsts_reactions = array();
				}
				if ( ! is_array( $reactions_count ) ) {
					$reactions_count = array();
				}
				$user_reactions[ $activity_id ] = array(
					'imgname'  => $imgname,
					'folder'   => $folder,
					'imgurl'   => $imgurl,
					'imgindex' => $imgindex,
				);

				$bpsts_reactions[ $imgindex ]['imgdata'] = array(
					'imgname'  => $imgname,
					'folder'   => $folder,
					'imgurl'   => $imgurl,
					'imgindex' => $imgindex,
				);
				$bpsts_reactions[ $imgindex ]['users'][] = $user_id;

				$reactions_count[] = $user_id;
			}

			bp_update_user_meta( $user_id, 'bpsts_user_reactions', $user_reactions );
			bp_activity_update_meta( $activity_id, 'bpsts_reaction_count', $reactions_count );
			bp_activity_update_meta( $activity_id, 'bpsts_reactions', $bpsts_reactions );
			
			$original_activity = new BP_Activity_Activity( $activity_id );
			$arg = array(
				'user_id'           => $original_activity->user_id,
				'item_id'           => $activity_id,
				'secondary_item_id' => $user_id,
				'component_name'    => 'user_activity_reaction',
				'component_action'  => 'user_activity_reaction_' . $activity_id,
				'date_notified'     => bp_core_current_time(),
				'is_new'            => 1,
			);
			bp_notifications_add_notification( $arg );
		}

		$response['bpsts_open_reaction_div'] = '<img class="bpsts-icon-img bpsts-open-reaction" src="' . $imgurl . '" data-index="' . $imgindex . '" data-imgname="' . $imgname . '" data-folder="' . $folder . '">';

		$response['bpsts_activity_entry_reactions'] = $this->bpst_ajax_activity_entry_content( $activity_id );
		echo json_encode( $response );
		wp_die();
	}

	public function bpst_ajax_activity_entry_content( $activity_id ) {
		$reactions_count  = bp_activity_get_meta( $activity_id, 'bpsts_reaction_count', true );
		$_reactions_count = '';
		if ( is_array( $reactions_count ) ) {
			$_reactions_count = count( $reactions_count );
		}
		$svgfiles  = bpsts_icons_svgfiles();
		$logged_in = get_current_user_id();

		if ( is_array( $reactions_count ) && in_array( $logged_in, $reactions_count ) ) {
			$real_count = $_reactions_count - 1;
			if ( $real_count == 0 ) {
				$count_text = esc_html__( 'You', 'buddypress-status' );
			} elseif ( $real_count == 1 ) {
				$count_text = sprintf(
					__( 'You and %s other.', 'buddypress-status' ),
					$_reactions_count - 1
				);
			} else {
				$count_text = sprintf(
					__( 'You and %s others.', 'buddypress-status' ),
					$_reactions_count - 1
				);
			}
		} else {
			$count_text = $_reactions_count;
		}

		$bpsts_reactions = bp_activity_get_meta( $activity_id, 'bpsts_reactions', true );
		$reign_like_html = $this->bpst_get_reign_like_html( $_reactions_count, $activity_id );

		$reaction_html = '<div class="bpsts-reactions-list">';
		if ( ! $_reactions_count ) {
			$reaction_html .= $reign_like_html;
		}
		if ( $_reactions_count ) {
			$reaction_html .= '<div class="reactions">';
			$reaction_html .= $reign_like_html;
			foreach ( $bpsts_reactions as $key => $value ) {
				$image_name = basename( str_replace( '.png', '',$value['imgdata']['imgurl'] ) );
				if ( in_array( $image_name, $svgfiles)) {
					$value['imgdata']['imgurl'] = str_replace('.png', '.svg', $value['imgdata']['imgurl']);
				}
				$reaction_html .= '<div class="single-reaction">';
				$reaction_html .= '<img src="' . $value['imgdata']['imgurl'] . '">';
				$reaction_html .= '<div class="reacted-users">';
				foreach ( $value['users'] as $_key => $user_id ) {
					$reaction_html .= '<p>' . bp_core_get_username( $user_id ) . '</p>';
				}
				$reaction_html .= '</div>';
				$reaction_html .= '</div>';
			}
			$reaction_html .= '<div class="reactions-count">' . $count_text . '</div>';
			$reaction_html .= '</div>';

		}
		$reaction_html .= '</div>';
		return $reaction_html;
	}

	public function bpst_bp_activity_entry_content() {

		$bpsts_gnrl_settings = get_option( 'bpsts_gnrl_settings', true );
		if ( !isset( $bpsts_gnrl_settings['reaction_dis_icon'] ) ) {
			return;
		}
		$svgfiles = bpsts_icons_svgfiles();		
		$activity_id = bp_get_activity_id();

		$reactions_count  = bp_activity_get_meta( $activity_id, 'bpsts_reaction_count', true );
		$_reactions_count = '';
		if ( is_array( $reactions_count ) ) {
			$_reactions_count = count( $reactions_count );
		}

		$logged_in = get_current_user_id();

		if ( is_array( $reactions_count ) && in_array( $logged_in, $reactions_count ) ) {
			$real_count = $_reactions_count - 1;
			if ( $real_count == 0 ) {
				$count_text = esc_html__( 'You', 'buddypress-status' );
			} elseif ( $real_count == 1 ) {
				$count_text = sprintf(
					__( 'You and %s other.', 'buddypress-status' ),
					$_reactions_count - 1
				);
			} else {
				$count_text = sprintf(
					__( 'You and %s others.', 'buddypress-status' ),
					$_reactions_count - 1
				);
			}
		} else {
			$count_text = $_reactions_count;
		}

		$bpsts_reactions = bp_activity_get_meta( $activity_id, 'bpsts_reactions', true );
		$reign_like_html = $this->bpst_get_reign_like_html( $_reactions_count, $activity_id );
		if ( $reactions_count != '' || $reign_like_html != '' ) {
			?>
		<div class="bpsts-reactions-list">
			<?php
			if ( ! $_reactions_count ) {
				echo $reign_like_html;
			}
			if ( $_reactions_count ) {
				?>
			<div class="reactions">
				<?php
				echo $reign_like_html;
				foreach ( $bpsts_reactions as $key => $value ) {
					$image_name = basename( str_replace( '.png', '',$value['imgdata']['imgurl'] ) );
					if ( in_array( $image_name, $svgfiles)) {
						$value['imgdata']['imgurl'] = str_replace('.png', '.svg', $value['imgdata']['imgurl']);
					}
					echo '<div class="single-reaction">';
					echo '<img src="' . $value['imgdata']['imgurl'] . '" alt="reaction-image">';
					echo '<div class="reacted-users">';
					foreach ( $value['users'] as $_key => $user_id ) {
						echo '<p>' . bp_core_get_username( $user_id ) . '</p>';
					}
					echo '</div>';
					echo '</div>';
				}
					echo '<div class="reactions-count">' . $count_text . '</div>';
				?>
			</div>
			<?php } ?>
		</div>
			<?php
		}
	}

	public function bpst_bp_after_theme_setup_hpok() {
		if ( has_action( 'bp_activity_entry_content', 'wbcom_show_activity_like_avatars' ) ) {
			remove_action( 'bp_activity_entry_content', 'wbcom_show_activity_like_avatars' );
		}
	}

	public function bpst_get_reign_like_html( $_reactions_count, $activity_id ) {
		if ( ! class_exists( 'REIGN_Theme_Class' ) ) {
			return $html = '';
		}
		// $activity_id = bp_get_activity_id();
		global $wpdb;
		$html  = '';
		$query = 'SELECT user_id FROM ' . $wpdb->base_prefix . "usermeta WHERE meta_key = 'bp_favorite_activities' AND (meta_value LIKE '%:$activity_id;%' OR meta_value LIKE '%:\"$activity_id\";%') ";
		$users = $wpdb->get_results( $query, ARRAY_A );
		if ( ! $_reactions_count ) {
			if ( ! empty( $users ) && is_array( $users ) ) {
				$num_of_avatar_count  = apply_filters( 'wbcom_show_activity_like_avatars_count', 3 );
				$num_of_listing_count = apply_filters( 'wbcom_show_activity_like_listing_count', 5 );
				$html                .= '<div class="wbtm_fav_avatar_listing">';

				foreach ( $users as $counter => $user ) {
					$user_id = $user['user_id'];
					$avatar  = bp_core_fetch_avatar(
						array(
							'item_id' => $user_id,
							'object'  => 'user',
							'type'    => 'thumb',
						)
					);
					if ( ( $counter + 1 ) <= $num_of_avatar_count ) {

						$html     .= '<div class="rtm-tooltip">';
							$html .= $avatar;
							$html .= '<span class="rtm-tooltiptext">' . bp_core_get_userlink( $user_id ) . '</span>';
						$html     .= '</div>';

					} elseif ( ( $counter + 1 ) <= ( $num_of_avatar_count + $num_of_listing_count ) ) {
						if ( $counter == $num_of_avatar_count ) {
							$html         .= '<div class="rtm-tooltip">';
								$html     .= '<span class="round-fav-counter">+' . ( count( $users ) - $num_of_avatar_count ) . '</span>';
								$html     .= '<span class="rtm-tooltiptext">';
									$html .= '<ul class="wbtm-rest-member-list">';
						}
									$user_link = bp_core_get_userlink( $user_id );
									$html     .= '<li>' . $user_link . '</li>';
						if ( ( ( $counter + 1 ) == ( $num_of_avatar_count + $num_of_listing_count ) ) ) {
							$html .= '<li>+' . ( count( $users ) - ( $counter + 1 ) ) . __( 'others', 'buddypress-status' ) . '</li>';
							$html .= '</ul>';
							$html .= '</span>';
							$html .= '</div>';
						}
					}
				}
				$html .= '<span class="wbtm-likes-this">' . __( 'likes this', 'buddypress-status' ) . '</span>';
				$html .= '</div>';
			}
		} else {
			if ( ! empty( $users ) && is_array( $users ) ) {
				$html .= '<div class="single-reaction">';
				$html .= '<i class="fa fa-star" aria-hidden="true"></i>';
				$html .= '<div class="reacted-users">';
				foreach ( $users as $counter => $user ) {
					$user_id = $user['user_id'];
					$html   .= '<p>' . bp_core_get_username( $user_id ) . '</p>';
				}
				$html .= '</div>';
				$html .= '</div>';
			}
		}
		return apply_filters( 'bpst_get_reign_like_html', $html );
	}	
	
	
	public function bpst_bp_get_registered_components( $component_names = array() ) {
		// Force $component_names to be an array.
		if ( ! is_array( $component_names ) ) {
			$component_names = array();
		}

		array_push( $component_names, 'user_activity_reaction' );

		return $component_names;
	}
	
	public function bpst_bp_get_activity_reaction_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $component_action_name, $component_name ) {
		
		if ( 'user_activity_reaction' === $component_name ) {
			$link      = bp_activity_get_permalink( $item_id );
			$reporter_info = get_userdata( $secondary_item_id );
			$reporter_name = $reporter_info->user_login;
			$user_fullname = bp_core_get_user_displayname( $secondary_item_id );
			$text  = apply_filters( 'bpst_activity_reaction_notification_text',sprintf( esc_html__( '%s reacted on your activity.', 'buddypress-status' ) , $user_fullname));

			// WordPress Toolbar
			if ( 'string' === $format ||  'object' === $format) {
				//$result = sprintf( '%s', $text );
				
				return '<a href="' . $link . '" title="' . __( 'Activity reaction', 'buddypress-status' ) . '">' . $text . '</a>';
				
			}
		}
		
		return $action;
		
	}

}
