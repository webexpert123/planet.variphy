<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Sticky_Post
 * @subpackage Buddypress_Sticky_Post/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Sticky_Post
 * @subpackage Buddypress_Sticky_Post/admin
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Sticky_Post_Admin {

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
		 * defined in Buddypress_Sticky_Post_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Sticky_Post_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset($_GET['page']) && $_GET['page'] == 'buddypress-sticky-post' ) {
			 wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-sticky-post-admin.css', array(), $this->version, 'all' );
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
		 * defined in Buddypress_Sticky_Post_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Sticky_Post_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if ( isset($_GET['page']) && $_GET['page'] == 'buddypress-sticky-post' ) {
			wp_enqueue_script( 'wp-color-picker');
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-sticky-post-admin.js', array( 'jquery' ), $this->version, false );
		}

	}

	public function bpsp_add_admin_menu() {
		if ( empty ( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-sticky-post' ), esc_html__( 'WB Plugins', 'buddypress-sticky-post' ), 'manage_options', 'wbcomplugins', array( $this, 'bpsp_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-sticky-post' ), esc_html__( 'General', 'buddypress-sticky-post' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Sticky Post Settings Page', 'buddypress-sticky-post' ), esc_html__( 'Sticky Post', 'buddypress-sticky-post' ), 'manage_options', 'buddypress-sticky-post', array( $this, 'bpsp_settings_page' ) );
	}

	public function bpsp_settings_page() {
		$current = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'welcome';
		?>

		<div class="wrap">
                    <hr class="wp-header-end">
                    <div class="wbcom-wrap">
			<div class="blpro-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'BuddyPress Sticky Post Settings', 'buddypress-sticky-post' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
				<?php
				$blpro_tabs = array(
					'welcome'        => __( 'Welcome', 'buddypress-sticky-post' ),
					'general'        => __( 'General', 'buddypress-sticky-post' ),
				);

				$tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
				foreach ( $blpro_tabs as $blpro_tab => $blpro_name ) {
					$class     = ( $blpro_tab == $current ) ? 'nav-tab-active' : '';
					$tab_html .= '<li><a class="nav-tab ' . $class . '" href="admin.php?page=buddypress-sticky-post&tab=' . $blpro_tab . '">' . $blpro_name . '</a></li>';
				}
				$tab_html .= '</div></ul></div>';
				echo $tab_html;
				include 'inc/bpsp-tabs-options.php';
				echo '</div>';
				echo '</div>';
				echo '</div>';
	}

	public function bpsp_add_admin_register_setting() {
		register_setting( 'bpsp_general_settings_section', 'bpsp_general_settings' );
	}

}
