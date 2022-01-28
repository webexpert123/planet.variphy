<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/admin
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Hashtags_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Hashtags_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Hashtags_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if ( isset($_GET['page']) && $_GET['page'] == 'buddypress_hashtags' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-hashtags-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Hashtags_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Hashtags_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset($_GET['page']) && $_GET['page'] == 'buddypress_hashtags' ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-hashtags-admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script($this->plugin_name, 'bpht_ajax_obj', array( 
				'ajax_url' => admin_url('admin-ajax.php'), 
				'ajax_nonce' => wp_create_nonce('bpht_ajax_security'),
				'wait_text' => __('Please wait', 'buddypress-hashtags'),
				)
			);
		}

	}

	/**
	 * Register the plugins's admin menu.
	 *
	 * @since    1.0.0
	 */
	public function bpht_add_menu_buddypress_hashtags() {
		
		if ( empty ( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {

			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-hashtags' ), esc_html__( 'WB Plugins', 'buddypress-hashtags' ), 'manage_options', 'wbcomplugins', array( $this, 'bpht_hashtags_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-hashtags' ), esc_html__( 'General', 'buddypress-hashtags' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Hashtags Setting Page', 'buddypress-hashtags' ), esc_html__( 'Hashtags', 'buddypress-hashtags' ), 'manage_options', 'buddypress_hashtags', array( $this, 'bpht_hashtags_settings_page' ) );
	}

	public function bpht_hashtags_settings_page() {
		$current = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'welcome';
		?>
		<div class="wrap">
                <hr class="wp-header-end">
                <div class="wbcom-wrap">
		<div class="blpro-header">
			<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
			<h1 class="wbcom-plugin-heading">
				<?php esc_html_e( 'BuddyPress Hashtags Settings', 'buddypress-hashtags' ); ?>
			</h1>
		</div>
		<div class="wbcom-admin-settings-page">
		<?php

		$bpht_tabs = array(
			'welcome'        => __( 'Welcome', 'buddypress-hashtags' ),
			'general'        => __( 'General', 'buddypress-hashtags' ),
			'hashtag-logs' => __( 'Hashtags logs', 'buddypress-hashtags' ),
			'shortcodes'	 => __( 'Shortcodes', 'buddypress-hashtags' )
		);

	    $tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $bpht_tabs as $bpht_tab => $bpht_name ) {
			$class     = ( $bpht_tab == $current ) ? 'nav-tab-active' : '';
			$tab_html .= '<li><a class="nav-tab ' . $class . '" href="admin.php?page=buddypress_hashtags&tab=' . $bpht_tab . '">' . $bpht_name . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo $tab_html;

		include 'inc/bpht-options-page.php';
		echo '</div>'; /* closing of div class wbcom-admin-settings-page */
		echo '</div>'; /* closing div class wbcom-wrap */
		echo '</div>'; /* closing div class wrap */
	}

	public function bpht_add_admin_register_setting() {
		register_setting( 'bpht_general_settings_section', 'bpht_general_settings' );
	}

	public function bpht_clear_buddypress_hashtag_table() {
		global $wpdb;
		if (isset($_POST[ 'action' ]) && $_POST[ 'action' ] == 'bpht_clear_buddypress_hashtag_table') {
			check_ajax_referer( 'bpht_ajax_security', 'ajax_nonce' );	
			$table_name = $wpdb->prefix . 'bpht_hashtags';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$delete = $wpdb->query("DELETE FROM $table_name WHERE ht_type = 'buddypress'");
				delete_option('bpht_hashtags');
			}
			exit();
		}
	}

	public function bpht_clear_bbpress_hashtag_table() {
		global $wpdb;
		if( isset($_POST['action']) && $_POST['action'] == 'bpht_clear_bbpress_hashtag_table' ) {
			check_ajax_referer( 'bpht_ajax_security', 'ajax_nonce' );

			$table_name = $wpdb->prefix . 'bpht_hashtags';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$delete = $wpdb->query("DELETE FROM $table_name WHERE ht_type = 'bbpress'");
				delete_option('bpht_bbpress_hashtags');
			}
			exit();
		}
	}

	public function bpht_clear_post_hashtag_table() {
		global $wpdb;
		if( isset($_POST['action']) && $_POST['action'] == 'bpht_clear_post_hashtag_table' ) {
			check_ajax_referer( 'bpht_ajax_security', 'ajax_nonce' );

			$table_name = $wpdb->prefix . 'bpht_hashtags';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$delete = $wpdb->query("DELETE FROM $table_name WHERE ht_type = 'post'");
			}
			exit();
		}
	}

	public function bpht_clear_page_hashtag_table() {
		global $wpdb;
		if( isset($_POST['action']) && $_POST['action'] == 'bpht_clear_page_hashtag_table' ) {
			check_ajax_referer( 'bpht_ajax_security', 'ajax_nonce' );

			$table_name = $wpdb->prefix . 'bpht_hashtags';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$delete = $wpdb->query("DELETE FROM $table_name WHERE ht_type = 'page'");
			}
			exit();
		}
	}

	public function bpht_update_hashtags_links_on_save_post( $data , $postarr ) {
		
		if( !is_admin() ) {
			return $data;
		}
		
		if ( isset($_REQUEST['wp_customize']) && $_REQUEST['wp_customize'] == 'on' ) {
			return $data;
		}				
		
		if( !empty( $data['post_content']) && $data['post_status'] == 'publish' ) {
			$content = $data['post_content'];
			$data['post_content'] = str_replace( array('<p>', '</p>', '<strong>', '</strong>', '<em>', '</em>'), array('<p> ', ' </p>', '<strong> ', ' </strong>','<em> ', ' </em>'), $data['post_content']);
			$bpht_general_settings = get_option( 'bpht_general_settings' );
			$minlen = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] )?$bpht_general_settings['min_length']:3;
			$maxlen = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] )?$bpht_general_settings['max_length']:16;

			$pattern = '/[#]([\p{L}_0-9a-zA-Z-]{'.$minlen.','.$maxlen.'})/iu';
			$an_enabled = bpht_alpha_numeric_hashtags_enabled();

			if( $an_enabled ) {
				//$pattern = " /#(\S{1,})/u";
				$pattern = " /(?<!\S)#(\S{1,})/u";
			}

			$hashtags_option = get_option( 'bpht_bbpress_hashtags' );

			$site_url = trailingslashit( get_bloginfo('url') );
			$hashtags = array();			
			preg_match_all( $pattern, $data['post_content'], $hashtags );
			
			if ( $hashtags ) {
				if ( !$hashtags = array_unique( $hashtags[1] ) )
					return $data;

				foreach( (array)$hashtags as $hashtag ) {
					$pattern = "/(^|\s|\b)#". $hashtag ."($|\b)/";
					if( $an_enabled ) {
						$hashtag = str_replace( array('<p>','</p>') , array('',''), $hashtag );
						$pattern = "/#". $hashtag ."/u";
					}
					$data['post_content'] = preg_replace( $pattern, ' <a href="'. $site_url . '?s=%23' . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $hashtag ) . '">#'. htmlspecialchars( $hashtag ) .'</a>', $data['post_content'] );
					
					$post_type = (isset($_POST['post_type']))?$_POST['post_type']:'post';
					if ( $post_type == 'forum' || $post_type == 'topic' || $post_type =='reply' ) {
						$post_type = 'bbpress';
					}
					
					bpht_db_buddypress_hashtag_entry( $hashtag, $post_type );
				}
			}
		}
		return $data;
	}
	
	/*
	 * Add Comment Hashtags when new comment add from fronted
	 *
	 */
	public function bpht_update_hashtags_links_on_comment_process( $commentdata ) {
		
				
		if( is_admin() ) {
			return $commentdata;
		}
		
		if( !empty( $commentdata['comment_content']) ) {
			$content = $commentdata['comment_content'];

			$bpht_general_settings = get_option( 'bpht_general_settings' );
			$minlen = ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] )?$bpht_general_settings['min_length']:3;
			$maxlen = ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] )?$bpht_general_settings['max_length']:16;

			$pattern = '/[#]([\p{L}_0-9a-zA-Z-]{'.$minlen.','.$maxlen.'})/iu';
			$an_enabled = bpht_alpha_numeric_hashtags_enabled();

			if( $an_enabled ) {
				//$pattern = " /#(\S{1,})/u";
				$pattern = " /(?<!\S)#(\S{1,})/u";
			}

			$hashtags_option = get_option( 'bpht_bbpress_hashtags' );

			$site_url = trailingslashit( get_bloginfo('url') );
			$hashtags = array();
			preg_match_all( $pattern, $commentdata['comment_content'], $hashtags );			
			if ( $hashtags ) {
				if ( !$hashtags = array_unique( $hashtags[1] ) )
					return $commentdata;

				foreach( (array)$hashtags as $hashtag ) {
					$pattern = "/(^|\s|\b)#". $hashtag ."($|\b)/";
					if( $an_enabled ) {
						$pattern = "/#". $hashtag ."/u";
					}
					$commentdata['comment_content'] = preg_replace( $pattern, '<a href="'. $site_url . '?s=%23' . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag" id="' . htmlspecialchars( $hashtag ) . '">#'. htmlspecialchars( $hashtag ) .'</a>', $commentdata['comment_content'] );
					$comment_post_type = get_post_type($commentdata['comment_post_ID']);
					$post_type = (  $comment_post_type != '' ) ? $comment_post_type : 'post';
				
					bpht_db_buddypress_hashtag_entry( $hashtag, $post_type );
				}
			}
		}				
		return $commentdata;
	}
	
	
	public function bpht_delete_hashtag() {
		if( isset($_POST['action']) && $_POST['action'] == 'bpht_delete_hashtag' ) {
			check_ajax_referer( 'bpht_ajax_security', 'ajax_nonce' );			
			global $wpdb;
			$r = array(					
					'per_page'         	=> 0,
					'page'          	=> 0,
					'search_terms'      => '#'. trim($_POST['name']),
					'update_meta_cache' => false,
				);
	
			if ( bp_has_activities( $r ) ) {				
				while ( bp_activities() ) { bp_the_activity();					
					bp_activity_delete( array( 'id' => bp_get_activity_id() ) );
				}
				
			}
		}
		
		wp_die();
	}

}
