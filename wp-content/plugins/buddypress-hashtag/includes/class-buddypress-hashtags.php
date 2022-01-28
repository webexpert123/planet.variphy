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
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/includes
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
 * @package    Buddypress_Hashtags
 * @subpackage Buddypress_Hashtags/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Hashtags {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Hashtags_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'BPHT_PLUGIN_VERSION' ) ) {
			$this->version = BPHT_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-hashtags';

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
	 * - Buddypress_Hashtags_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Hashtags_i18n. Defines internationalization functionality.
	 * - Buddypress_Hashtags_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Hashtags_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-hashtags-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-hashtags-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-hashtags-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-hashtags-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom license file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		/* Enqueue hashtags widget. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-hastags-wdget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-bbpress-hastags-wdget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-post-hashtags-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bpht-page-hashtags-widget.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bpht-general-functions.php';

		$this->loader = new Buddypress_Hashtags_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Hashtags_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Hashtags_i18n();

		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Buddypress_Hashtags_Admin( $this->get_plugin_name(), $this->get_version() );
		
		$bpht_general_settings = get_option( 'bpht_general_settings' );		
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpht_add_menu_buddypress_hashtags' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpht_add_admin_register_setting' );

		//ajax action to clear buddypress hashtag table
		$this->loader->add_action( 'wp_ajax_bpht_clear_buddypress_hashtag_table', $plugin_admin, 'bpht_clear_buddypress_hashtag_table' );
		if( class_exists( 'bbPress' ) ){
			$this->loader->add_action( 'wp_ajax_bpht_clear_bbpress_hashtag_table', $plugin_admin, 'bpht_clear_bbpress_hashtag_table' );
		}
		$this->loader->add_action( 'wp_ajax_bpht_clear_post_hashtag_table', $plugin_admin, 'bpht_clear_post_hashtag_table' );
		$this->loader->add_action( 'wp_ajax_bpht_clear_page_hashtag_table', $plugin_admin, 'bpht_clear_page_hashtag_table' );
		
		if ( !isset($bpht_general_settings['disable_on_blog_posts']) ) {
			remove_filter( 'wp_insert_post_data', 'bbp_fix_post_author', 30, 2 );
			$this->loader->add_filter( 'wp_insert_post_data', $plugin_admin, 'bpht_update_hashtags_links_on_save_post', 99, 2 );
			$this->loader->add_filter( 'preprocess_comment', $plugin_admin, 'bpht_update_hashtags_links_on_comment_process', 99 );
		}
		
		$this->loader->add_action( 'wp_ajax_bpht_delete_hashtag', $plugin_admin, 'bpht_delete_hashtag' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Hashtags_Public( $this->get_plugin_name(), $this->get_version() );
		$bpht_general_settings = get_option( 'bpht_general_settings' );		
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'bp_activity_comment_content', $plugin_public, 'bpht_activity_comment_hashtags_filter', 20, 2 );
		$this->loader->add_filter( 'bea_get_activity_content', $plugin_public, 'bpht_bea_get_activity_content',999 );
		$this->loader->add_filter( 'bp_activity_new_update_content', $plugin_public, 'bpht_activity_hashtags_filter' );
		$this->loader->add_filter( 'bp_get_activity_content_body', $plugin_public, 'bpht_activity_hashtags_filter' );
		$this->loader->add_filter( 'groups_activity_new_update_content', $plugin_public, 'bpht_activity_hashtags_filter' );

		$this->loader->add_filter( 'bp_blogs_activity_new_post_content', $plugin_public, 'bpht_activity_hashtags_filter' );
		$this->loader->add_filter( 'bp_blogs_activity_new_comment_content', $plugin_public, 'bpht_activity_hashtags_filter' );

		//support edit activity stream plugin
		$this->loader->add_filter( 'bp_edit_activity_action_edit_content', $plugin_public, 'bpht_activity_hashtags_filter' );
		
		
		if ( !isset($bpht_general_settings['disable_on_bbpress']) ) {
			
			$this->loader->add_filter( 'bbp_new_topic_pre_content', $plugin_public,'bpht_bbpress_hashtags_filter' );
			$this->loader->add_filter( 'bbp_edit_topic_pre_content', $plugin_public,'bpht_bbpress_hashtags_filter' );
			$this->loader->add_filter( 'bbp_new_reply_pre_content', $plugin_public,'bpht_bbpress_hashtags_filter' );
			$this->loader->add_filter( 'bbp_edit_reply_pre_content', $plugin_public,'bpht_bbpress_hashtags_filter' );
		}

		//ajax query string for comment search
		$this->loader->add_filter( 'bp_ajax_querystring', $plugin_public,'bpht_activity_hashtags_querystring', 11, 2 );
		$this->loader->add_filter( 'bp_dtheme_ajax_querystring', $plugin_public,'bpht_activity_hashtags_querystring', 11, 2 );

		$this->loader->add_action( 'widgets_init',  $plugin_public, 'bpht_register_hashtag_widget' );
		$this->loader->add_shortcode( 'bpht_bp_hashtags', $plugin_public, 'bpht_render_buddypress_hashtags' );
		if( class_exists( 'bbPress' ) ){
			$this->loader->add_shortcode( 'bpht_bbpress_hashtags', $plugin_public, 'bpht_render_bbpress_hashtags' );
		}
		
		$this->loader->add_action( 'bp_before_activity_delete', $plugin_public, 'bpht_delete_buddypress_activity_hashtag_table' );
		$this->loader->add_action( 'delete_post', $plugin_public, 'bpht_delete_buddypress_post_hashtag_table' );
		$this->loader->add_action( 'deleted_comment', $plugin_public, 'bpht_deleted_comment_hashtag_table', 20, 2 );

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
	 * @return    Buddypress_Hashtags_Loader    Orchestrates the hooks of the plugin.
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
