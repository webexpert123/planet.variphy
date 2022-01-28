<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Status
 * @subpackage Buddypress_Status/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Status
 * @subpackage Buddypress_Status/admin
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Status_Admin {

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
		 * defined in Buddypress_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-status-admin.css', array(), $this->version, 'all' );

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
		 * defined in Buddypress_Status_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Status_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'jquery-ui-draggable' );
		//wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script('jquery-ui-sortable');

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-status-admin.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Register the admin menu for plugin.
	 *
	 * @since    1.0.0
	 */
	public function bpsts_add_menu_buddypress_status() {

		if ( empty ( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {

			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-status' ), esc_html__( 'WB Plugins', 'buddypress-status' ), 'manage_options', 'wbcomplugins', array( $this, 'bpsts_status_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-status' ), esc_html__( 'General', 'buddypress-status' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Status Setting Page', 'buddypress-status' ), esc_html__( 'BuddyPress Status', 'buddypress-status' ), 'manage_options', 'buddypress_status', array( $this, 'bpsts_status_settings_page' ) );

	}

	public function bpsts_status_settings_page() {
		$current = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'welcome';
		?>
		<div class="wrap">
                <hr class="wp-header-end">
                <div class="wbcom-wrap">
		<div class="blpro-header">
			<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
			<h1 class="wbcom-plugin-heading">
				<?php esc_html_e( 'BuddyPress Status Settings', 'buddypress-status' ); ?>
			</h1>
		</div>
		<div class="wbcom-admin-settings-page">
		<?php

		$bpsts_tabs = array(
			'welcome'        => __( 'Welcome', 'buddypress-status' ),
			'general'        => __( 'General', 'buddypress-status' ),
			'status-icon'    => __( 'Status Icon', 'buddypress-status' ),			
		);

	    $tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $bpsts_tabs as $bpsts_tab => $bpsts_name ) {
			$class     = ( $bpsts_tab == $current ) ? 'nav-tab-active' : '';
			$tab_html .= '<li><a class="nav-tab ' . $class . '" href="admin.php?page=buddypress_status&tab=' . $bpsts_tab . '">' . $bpsts_name . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo $tab_html;

		include 'inc/bpsts-options-page.php';
		echo '</div>'; /* closing of div class wbcom-admin-settings-page */
                echo '</div>'; /* closing div class wbcom-wrap */
		echo '</div>'; /* closing div class wrap */
	}

	public function bpsts_add_admin_register_setting() {
		register_setting( 'bpsts_icon_settings_section', 'bpsts_icon_settings' );
		register_setting( 'bpsts_gnrl_settings_section', 'bpsts_gnrl_settings' );
	}
	
	public function bpsts_update_option_bpsts_icon_settings( $value, $old_value, $option) {
		
		if ( isset($_FILES['bpsts_icon_settings']['name']['custom_icon']) && !empty($_FILES['bpsts_icon_settings']['name']['custom_icon'])) {
			$wp_upload_dir = wp_upload_dir();
			$path = $wp_upload_dir['basedir'] . '/buddypress-status';
			if ( ! is_dir($path)) {
				mkdir($path);
			}
			
			foreach($_FILES['bpsts_icon_settings']['name']['custom_icon'] as $key=>$file_value) {				
				
				$upload_path = $path . '/' . basename($_FILES['bpsts_icon_settings']['name']['custom_icon'][$key]);
				if( move_uploaded_file( $_FILES['bpsts_icon_settings']['tmp_name']['custom_icon'][$key], $upload_path ) ){					
					
				}
			}
		}		
		return $value;
	}

}
