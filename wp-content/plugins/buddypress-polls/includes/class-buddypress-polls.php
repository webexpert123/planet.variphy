<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/includes
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
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
if ( !class_exists('Buddypress_Polls') ) {
	class Buddypress_Polls {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Buddypress_Polls_Loader    $loader    Maintains and registers all hooks for the plugin.
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
			if ( defined( 'BPOLLS_PLUGIN_VERSION' ) ) {
				$this->version = BPOLLS_PLUGIN_VERSION;
			} else {
				$this->version = '1.0.0';
			}
			$this->plugin_name = 'buddypress-polls';

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
		 * - Buddypress_Polls_Loader. Orchestrates the hooks of the plugin.
		 * - Buddypress_Polls_i18n. Defines internationalization functionality.
		 * - Buddypress_Polls_Admin. Defines all hooks for the admin area.
		 * - Buddypress_Polls_Public. Defines all hooks for the public side of the site.
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
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-polls-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-polls-i18n.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-polls-admin.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-polls-public.php';

			/**
			 * The class responsible for initiating bp poll activity graph widget.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/inc/bp-poll-activity-graph.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/inc/class-bp-poll-activity-graph.php';		
			

			/* Enqueue wbcom plugin folder file. */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

			/* Enqueue wbcom plugin folder file. */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

			$this->loader = new Buddypress_Polls_Loader();

		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Buddypress_Polls_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new Buddypress_Polls_i18n();

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

			$plugin_admin = new Buddypress_Polls_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			$this->loader->add_action( bp_core_admin_hook(), $plugin_admin, 'bpolls_add_menu_buddypress_polls' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'bpolls_admin_register_settings' );
			$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'bpolls_add_dashboard_widgets' );
			$this->loader->add_action( 'init', $plugin_admin, 'bpolls_activity_polls_data_export' );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {

			$plugin_public = new Buddypress_Polls_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			$this->loader->add_action( 'wp_ajax_bpolls_set_poll_type_true', $plugin_public, 'bpolls_set_poll_type_true' );

			/* adds polls html in whats new area */
			$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'bpolls_polls_update_html' );
			// $this->loader->add_action( 'bp_after_activity_post_form', $plugin_public, 'bpolls_polls_update_html' );

			/* adds new activity type poll */
			$this->loader->add_filter( 'bp_activity_check_activity_types', $plugin_public, 'bpolls_add_polls_type_activity', 10, 1 );

			/* register poll type activity action */
			$this->loader->add_action( 'bp_register_activity_actions', $plugin_public, 'bpolls_register_activity_actions' );

			$this->loader->add_filter( 'bp_get_activity_action_pre_meta', $plugin_public, 'bpolls_activity_action_wall_posts', 9999, 2 );

			/* update poll type activity on post update */
			$this->loader->add_action( 'bp_activity_before_save', $plugin_public, 'bpolls_update_poll_type_activity', 10, 1 );

			/* update poll activity meta */
			$this->loader->add_action( 'bp_activity_posted_update', $plugin_public, 'bpolls_update_poll_activity_meta', 10, 4 );

			/* update group poll activity meta */
			$this->loader->add_action( 'bp_groups_posted_update', $plugin_public, 'bpolls_update_poll_activity_meta', 10, 4 );

			/* ypuzer update activity meta */
			$this->loader->add_action( 'yz_activity_posted_update', $plugin_public, 'bpolls_update_poll_activity_meta', 10, 4 );
			$this->loader->add_action( 'yz_groups_posted_update', $plugin_public, 'bpolls_update_poll_activity_meta', 10, 4 );

			/* update poll activity content */
			//$this->loader->add_action( 'bp_activity_entry_content', $plugin_public, 'bpolls_update_poll_activity_content', 10, 1 );
			
			$this->loader->add_filter( 'bp_get_activity_content_body', $plugin_public, 'bpquotes_update_pols_activity_content', 10, 2 );
			/* update widget poll activity content */
			$this->loader->add_action( 'bp_polls_activity_entry_content', $plugin_public, 'bpolls_update_poll_activity_content', 10, 1 );


			/* ajax request to save note */
			$this->loader->add_action( 'wp_ajax_bpolls_save_poll_vote', $plugin_public, 'bpolls_save_poll_vote' );

			/* set poll type activity action in groups */
			if ( defined( 'BP_VERSION' ) ) {
				if ( version_compare( BP_VERSION, '5.0.0', '>=' ) ) {
					$this->loader->add_filter( 'bp_groups_format_activity_action_group_activity_update', $plugin_public, 'bpolls_groups_activity_new_update_action', 10, 1 );
				} else {
					$this->loader->add_filter( 'groups_activity_new_update_action', $plugin_public, 'bpolls_groups_activity_new_update_action', 10, 1 );
				}
			}
			/* set poll activity content in embed */
			$this->loader->add_filter( 'bp_activity_get_embed_excerpt', $plugin_public, 'bpolls_bp_activity_get_embed_excerpt', 10, 2 );
			/* embed poll activity css */
			$this->loader->add_action( 'embed_head', $plugin_public, 'bpolls_activity_embed_add_inline_styles', 20 );

			/* update total poll votes */
			$this->loader->add_action( 'bp_init', $plugin_public, 'bpolls_update_prev_polls_total_votes', 20 );

			$this->loader->add_action( 'wp_ajax_bpolls_save_image', $plugin_public, 'bpolls_save_image' );
			
			

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
		 * @return    Buddypress_Polls_Loader    Orchestrates the hooks of the plugin.
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
}