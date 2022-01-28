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
 * @package    Buddypress_Sticky_Post
 * @subpackage Buddypress_Sticky_Post/includes
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
 * @package    Buddypress_Sticky_Post
 * @subpackage Buddypress_Sticky_Post/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Sticky_Post {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Sticky_Post_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'BPSP_NAME_VERSION' ) ) {
			$this->version = BPSP_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-sticky-post';

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
	 * - Buddypress_Sticky_Post_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Sticky_Post_i18n. Defines internationalization functionality.
	 * - Buddypress_Sticky_Post_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Sticky_Post_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-sticky-post-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-sticky-post-i18n.php';
		
		/**
		 * The class responsible for defining plugin needed functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bpsp-functions.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-sticky-post-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-sticky-post-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		$this->loader = new Buddypress_Sticky_Post_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Sticky_Post_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Sticky_Post_i18n();

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

		$plugin_admin = new Buddypress_Sticky_Post_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpsp_add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpsp_add_admin_register_setting' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Sticky_Post_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'bp_activity_entry_meta', $plugin_public, 'bpsp_activity_pin_toggle_icon' );
		$this->loader->add_action( 'wp_ajax_bpsp_handle_pin_unpin_action', $plugin_public, 'bpsp_handle_pin_unpin_action' );
		//$this->loader->add_filter( 'bp_ajax_querystring', $plugin_public,'bpsp_exclude_sticky_posts', 999, 1 );
		//$this->loader->add_filter( 'bp_legacy_theme_ajax_querystring', $plugin_public,'bpsp_exclude_sticky_posts', 999, 1 );
		$this->loader->add_filter( 'bp_activity_get', $plugin_public, 'bpsp_profile_wall_set_sticky_post', 999, 2 );
		//$this->loader->add_filter( 'bp_get_activity_action', $plugin_public, 'bpsp_bp_get_activity_action', 999, 3 );
		$this->loader->add_action( 'bp_before_activity_entry_comments', $plugin_public, 'bpsp_before_activity_entry_comments' );
		$this->loader->add_filter( 'bp_get_activity_css_class', $plugin_public, 'bpsp_bp_get_activity_css_class', 999, 1 );
		$this->loader->add_filter( 'body_class', $plugin_public, 'bpsp_add_body_class' );
		
		$this->loader->add_action( 'bp_before_activity_entry', $plugin_public, 'bpsp_bp_before_activity_entry' );
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
	 * @return    Buddypress_Sticky_Post_Loader    Orchestrates the hooks of the plugin.
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
