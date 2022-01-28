<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Status
 * @subpackage Buddypress_Status/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Buddypress_Status
 * @subpackage Buddypress_Status/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Status {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Status_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BPSTS_PLUGIN_VERSION' ) ) {
			$this->version = BPSTS_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-status';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Buddypress_Status_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Status_i18n. Defines internationalization functionality.
	 * - Buddypress_Status_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Status_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-status-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-status-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-status-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-status-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom license file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		/* General functions file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/buddypress-status-gen-funcions.php';

		$this->loader = new Buddypress_Status_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Status_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Status_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Buddypress_Status_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpsts_add_menu_buddypress_status' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpsts_add_admin_register_setting' );
		$this->loader->add_action( 'pre_update_option_bpsts_icon_settings', $plugin_admin, 'bpsts_update_option_bpsts_icon_settings', 10 ,3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Status_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp', $plugin_public, 'bpsts_add_profile_status_menu' );
		$this->loader->add_action( 'bp_before_member_header_meta', $plugin_public, 'bpsts_render_member_status' );
		$this->loader->add_action( 'wp_ajax_bpsts_add_status', $plugin_public, 'bpsts_add_status' );
		$this->loader->add_action( 'wp_ajax_bpsts_current_status', $plugin_public, 'bpsts_current_status' );
		$this->loader->add_action( 'wp_ajax_bpsts_delete_status', $plugin_public, 'bpsts_delete_status' );
		$this->loader->add_action( 'wp_ajax_bpsts_delete_status_icon', $plugin_public, 'bpsts_delete_status_icon' );
		$this->loader->add_action( 'wp_ajax_bpsts_update_status', $plugin_public, 'bpsts_update_status' );
		$this->loader->add_action( 'wp_ajax_bpsts_update_icon_status', $plugin_public, 'bpsts_update_icon_status' );
		$this->loader->add_filter( 'bp_get_displayed_user_mentionname', $plugin_public, 'bpsts_show_user_icon_after_name' );
		$this->loader->add_filter( 'bp_get_send_public_message_link', $plugin_public, 'bpsts_bp_get_send_public_message_link', 99 );
		$this->loader->add_filter( 'bp_core_get_userlink', $plugin_public, 'bpsts_alter_user_display_name', 10, 2 );
		// $this->loader->add_filter( 'bp_get_activity_action', $plugin_public, 'bpsts_activity_loop_user_icon', 10, 3 );

		$this->loader->add_filter( 'bp_activity_allowed_tags', $plugin_public, 'bpsts_alter_activity_allowed_tags', 10, 1 );
		$this->loader->add_action( 'bp_activity_entry_meta', $plugin_public, 'bpsts_add_reactions_html' );

		$this->loader->add_action( 'wp_ajax_bpst_activity_reaction', $plugin_public, 'bpst_activity_reaction' );

		$this->loader->add_action( 'bp_activity_entry_content', $plugin_public, 'bpst_bp_activity_entry_content' );

		$this->loader->add_action( 'after_setup_theme', $plugin_public, 'bpst_bp_after_theme_setup_hpok' );
		
		 $this->loader->add_filter( 'bp_notifications_get_registered_components',  $plugin_public, 'bpst_bp_get_registered_components' );
		 
		 $this->loader->add_filter( 'bp_notifications_get_notifications_for_user', $plugin_public, 'bpst_bp_get_activity_reaction_notifications', 11, 7 );
	}
	

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Buddypress_Status_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
