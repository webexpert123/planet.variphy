<?php
/**
 * @package WordPress
 * @subpackage BP Auto Group Join
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'BP_Auto_Group_Join_Admin_Page' ) ):

	/**
	 *
	 * BP_Auto_Group_Join_Admin_Page
	 * ********************
	 *
	 *
	 */
	class BP_Auto_Group_Join_Admin_Page {
		/* Options/Load
		 * ===================================================================
		 */

		/**
		 * Plugin options
		 *
		 * @var array
		 */
		public $options = array();
		private $plugin_settings_tabs = array();

		private $network_activated = false,
			$plugin_slug = 'bp-auto-group-join',
			$menu_hook = 'admin_menu',
			$settings_page = 'buddyboss-settings',
			$capability = 'manage_options',
			$form_action = 'options.php',
			$plugin_settings_url;

		/**
		 * Empty constructor function to ensure a single instance
		 */
		public function __construct() {
			// ... leave empty, see Singleton below
		}

		/* Singleton
		 * ===================================================================
		 */

		/**
		 * Admin singleton
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @param  array  $options [description]
		 *
		 * @uses BP_Auto_Group_Join_Admin_Page::setup() Init admin class
		 *
		 * @return object Admin class
		 */
		public static function instance() {
			static $instance = null;

			if ( null === $instance ) {
				$instance = new BP_Auto_Group_Join_Admin_Page;
				$instance->setup();
			}

			return $instance;
		}

		/* Utility functions
		 * ===================================================================
		 */

		/**
		 * Get option
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @param  string $key Option key
		 *
		 * @uses BP_Auto_Group_Join_Admin_Page::option() Get option
		 *
		 * @return mixed      Option value
		 */
		public function option( $key ) {
			$value = bp_auto_group_join()->option( $key );
			return $value;
		}

		/* Actions/Init
		 * ===================================================================
		 */

		/**
		 * Setup admin class
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses bp_auto_group_join() Get options from main BP_Auto_Group_Join_Admin_Page class
		 * @uses is_admin() Ensures we're in the admin area
		 * @uses curent_user_can() Checks for permissions
		 * @uses add_action() Add hooks
		 */
		public function setup() {
			if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$this->plugin_settings_url = admin_url( 'admin.php?page=' . $this->plugin_slug );

			$this->network_activated = $this->is_network_activated();

			//if the plugin is activated network wide in multisite, we need to override few variables
			if ( $this->network_activated ) {
				// Main settings page - menu hook
				$this->menu_hook = 'network_admin_menu';

				// Main settings page - parent page
				//$this->settings_page = 'settings.php';

				// Main settings page - Capability
				$this->capability = 'manage_network_options';

				// Settins page - form's action attribute
				$this->form_action = 'edit.php?action=' . $this->plugin_slug;

				// Plugin settings page url
				//$this->plugin_settings_url = network_admin_url('settings.php?page=' . $this->plugin_slug);
			}

			//if the plugin is activated network wide in multisite, we need to process settings form submit ourselves
			if ( $this->network_activated ) {
				add_action('network_admin_edit_' . $this->plugin_slug, array( $this, 'save_network_settings_page' ));
			}

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_init', array($this, 'register_support_settings' ) );
			add_action( $this->menu_hook, array( $this, 'register_buddyboss_menu_page' ) );
			add_action( $this->menu_hook, array( $this, 'admin_menu' ) );

			add_filter( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			add_filter( 'network_admin_plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
		}

		/**
		 * Check if the plugin is activated network wide(in multisite).
		 *
		 * @return boolean
		 */
		private function is_network_activated() {
			$network_activated = false;
			if ( is_multisite() ) {
				if ( !function_exists('is_plugin_active_for_network') )
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

				if ( is_plugin_active_for_network(basename( constant( 'BP_AUTO_GROUP_JOIN_PLUGIN_DIR' ) ).'/bp-auto-group-join.php') ) {
					$network_activated = true;
				}
			}
			return $network_activated;
		}


		/**
		 * Register admin settings
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses register_setting() Register plugin options
		 * @uses add_settings_section() Add settings page option sections
		 * @uses add_settings_field() Add settings page option
		 */
		public function admin_init() {
            $is_network_activated = bp_auto_group_join()->is_network_activated();
            if( $is_network_activated ){
                add_action('network_admin_edit_bp_auto_group_join', array($this, 'save_network_settings_page'), 10, 0);
            }

            $this->plugin_settings_tabs[ 'bp_auto_group_join_plugin_options' ] = 'General';

			register_setting( 'bp_auto_group_join_plugin_options', 'bp_auto_group_join_plugin_options' );
			add_settings_section( 'general_section', __( 'General Settings', 'bp-auto-group-join' ), array( $this, 'section_general' ), __FILE__ );

			add_settings_field( 'ajg_bmt_info', 'Auto-Join Users to Groups', array( $this, 'ajg_bmt_info_option' ), __FILE__, 'general_section' );
			add_settings_field( 'ajg_bmt_support', 'BuddyPress Member Types', array( $this, 'ajg_bmt_support_option' ), __FILE__, 'general_section' );
		}

		function register_support_settings() {
			$this->plugin_settings_tabs[ 'bp_auto_group_join_support_options' ] = 'Support';

			register_setting( 'bp_auto_group_join_support_options', 'bp_auto_group_join_support_options' );
			add_settings_section( 'section_support', ' ', array( &$this, 'section_support_desc' ), 'bp_auto_group_join_support_options' );
		}

		function section_support_desc() {
			if ( file_exists( dirname( __FILE__ ) . '/help-support.php' ) ) {
				require_once( dirname( __FILE__ ) . '/help-support.php' );
			}
		}

        function save_network_settings_page(){
            if( isset($_POST['bp_auto_group_join_plugin_options']) && !empty($_POST['bp_auto_group_join_plugin_options']) ){
                $prepare_data = $_POST['bp_auto_group_join_plugin_options'];
                $prepare_data = serialize($prepare_data);
                global $wpdb;
                $table_name = $wpdb->base_prefix.'options';
                $row_exists = $wpdb->get_results("SELECT * from {$table_name} WHERE option_name = 'bp_auto_group_join_plugin_options' ");
                if( isset($row_exists[0]) && !empty($row_exists[0]) ){
                    $wpdb->update(
                        $table_name,
                        array(
                            'option_value' => $prepare_data
                        ),
                        array( 'option_name' => 'bp_auto_group_join_plugin_options' ),
                        array(
                            '%s'
                        ),
                        array( '%s' )
                    );
                }else{
                    $wpdb->insert(
                        $table_name,
                        array(
                            'option_name' => 'bp_auto_group_join_plugin_options',
                            'option_value' => $prepare_data
                        ),
                        array(
                            '%s',
                            '%s'
                        )
                    );
                }

				// Where are we redirecting to?
				$base_url = trailingslashit( network_admin_url() ) . 'admin.php';
				$redirect_url = esc_url_raw(add_query_arg( array( 'page' => $this->plugin_slug, 'updated' => 'true' ), $base_url ));

				// Redirect
				// Redirect
				wp_redirect( $redirect_url );
				die();
            }
        }

		/**
		 * Add plugin settings page
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses add_options_page() Add plugin settings page
		 */
		public function admin_menu() {
//			add_submenu_page( 'buddyboss-settings', 'BP Auto Group Join', 'Auto Group Join', 'manage_options', __FILE__, array( $this, 'options_page' ) );

			add_submenu_page(
				$this->settings_page, 'BP Auto Group Join', 'Auto Group Join', $this->capability, $this->plugin_slug, array( $this, 'options_page' )
			);
		}

		/**
		 * Add plugin settings page
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses BP_Auto_Group_Join_Admin_Page::admin_menu() Add settings page option sections
		 */
		public function network_admin_menu() {
			return $this->admin_menu();
		}

		/**
		 * Resister BuddyBoss Menu Page
		 */
		public function register_buddyboss_menu_page() {

			if ( ! empty( $GLOBALS['admin_page_hooks']['buddyboss-settings'] ) ) return;

			// Set position with odd number to avoid confict with other plugin/theme.
			add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', bp_auto_group_join()->assets_url . '/images/logo.svg', 60 );

			// To remove empty parent menu item.
			add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
			remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
		}


		public function add_action_links( $links, $file ) {
			// Return normal links if not this plugin
			if ( plugin_basename( basename( constant( 'BP_AUTO_GROUP_JOIN_PLUGIN_DIR' ) ) . '/bp-auto-group-join.php' ) != $file ) {
				return $links;
			}

			$mylinks = array(
				'<a href="' . esc_url( $this->plugin_settings_url ) . '">' . __( "Settings", "bp-auto-group-join" ) . '</a>',
			);
			return array_merge( $links, $mylinks );
		}


		// Add settings link on plugin page
		function plugin_settings_link( $links ) {
            $links[] = '<a href="' . admin_url( "admin.php?page=" . __FILE__ ) . '">'.__("Settings","bp-auto-group-join").'</a>';
            return $links;
		}

		/* Settings Page + Sections
		 * ===================================================================
		 */

		/**
		 * Render settings page
		 *
		 * @since BP Auto Group Join (1.0.0)
		 *
		 * @uses do_settings_sections() Render settings sections
		 * @uses settings_fields() Render settings fields
		 * @uses esc_attr_e() Escape and localize text
		 */
		public function options_page() {
			$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : $this->plugin_slug;

			if ( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] == 'true' ) {
				?>
				<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated">
					<p><strong>Settings saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div><?php
			}

            $is_network_activated = bp_auto_group_join()->is_network_activated();
            $submit_url = $is_network_activated ? 'edit.php?action=bp_auto_group_join' : 'options.php';
            ?>
			<div class="wrap">
				<h2><?php _e( "BP Auto Group Join", "bp-auto-group-join" ); ?></h2>
				<?php $this->plugin_options_tabs(); ?>
				<form action="<?php echo $this->form_action; ?>" method="post" class="bb-inbox-settings-form">

					<?php
					if ( $this->network_activated && isset($_GET['updated']) ) {
						echo "<div class='updated'><p>" . __('Settings updated.', 'bp-auto-group-join') . "</p></div>";
					}
					?>

					<?php
					if ( 'bp_auto_group_join_plugin_options' == $tab || empty( $_GET[ 'tab' ] ) ) {
						settings_fields( 'bp_auto_group_join_plugin_options' );
						do_settings_sections( __FILE__ );
						?>
						<p class="submit">
							<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( __( "Save Changes", "bp-auto-group-join" ) ); ?>" />
						</p><?php
					} else {
						settings_fields( $tab );
						do_settings_sections( $tab );
					}
					?>

				</form>
			</div>

			<?php
		}

		function plugin_options_tabs() {
			$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'bp_auto_group_join_plugin_options';

			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}

		public function ajg_bmt_info_option() {
            $is_network_activated = bp_auto_group_join()->is_network_activated();
            $get_admin_url = $is_network_activated ? network_admin_url() : get_admin_url();
			echo '<p class="description">' . __( 'To automatically join users to a specific group, go to the <a href="'.$get_admin_url.'admin.php?page=bp-groups">Groups edit page</a> and select the group to edit.', 'bp-auto-group-join' ) . '</p>';
		}

		public function ajg_bmt_support_option() {

			$profile_field_visibility = $this->option( 'ajg_bmt_support' );
			if ( ! $profile_field_visibility ) {
				$profile_field_visibility = 'off';
			}

			$options = array(
				'on' => __( 'On', 'bp-auto-group-join' ),
				'off' => __( 'Off', 'bp-auto-group-join' )
			);
			foreach ( $options as $option => $label ) {
				$checked = $profile_field_visibility == $option ? ' checked' : '';
				echo '<label><input type="radio" name="bp_auto_group_join_plugin_options[ajg_bmt_support]" value="' . $option . '" ' . $checked . '>' . $label . '</label>&nbsp;&nbsp;';
			}

			echo '<p class="description">' . __( 'Add support for joining users to groups based on their member type. Requires <a target="_blank" href="https://www.buddyboss.com/product/buddypress-member-types/">BuddyPress Member Types</a>.', 'bp-auto-group-join' ) . '</p>';
		}

		/**
		 * General settings section
		 *
		 * @since BuddyBoss Wall (1.0.0)
		 */
		public function section_general() {

		}

	}



// End class BP_Auto_Group_Join_Admin_Page
endif;