<?php
/**
 * Plugin Name:             GamiPress - Leaderboards Include/Exclude Users
 * Plugin URI:              https://wordpress.org/plugins/gamipress-leaderboards-include-exclude-users
 * Description:             Include or exclude specific users or roles on any leaderboard.
 * Version:                 1.0.4
 * Author:                  GamiPress
 * Author URI:              https://gamipress.com/
 * Text Domain:             gamipress-leaderboards-include-exclude-users
 * Domain Path:             /languages/
 * Requires at least:       4.4
 * Tested up to:            5.3
 * License:                 GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package                 GamiPress\Leaderboards\Include_Exclude_Users
 * @author                  GamiPress
 * @copyright               Copyright (c) GamiPress
 */

final class GamiPress_Leaderboards_Include_Exclude_Users {

    /**
     * @var         GamiPress_Leaderboards_Include_Exclude_Users $instance The one true GamiPress_Leaderboards_Include_Exclude_Users
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Leaderboards_Include_Exclude_Users self::$instance The one true GamiPress_Leaderboards_Include_Exclude_Users
     */
    public static function instance() {

        if( !self::$instance ) {

            self::$instance = new GamiPress_Leaderboards_Include_Exclude_Users();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();

        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {

        // Plugin version
        define( 'GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_VER', '1.0.4' );

        // Plugin file
        define( 'GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_DIR . 'includes/content-filters.php';
            require_once GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_DIR . 'includes/scripts.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'GAMIPRESS_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'GamiPress - Leaderboards Include/Exclude Users requires %s and %s in order to work. Please install and activate them.', 'gamipress-leaderboards-include-exclude-users' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>',
                        '<a href="https://gamipress.com/add-ons/gamipress-leaderboards/" target="_blank">GamiPress - Leaderboards</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'GAMIPRESS_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'GamiPress' ) ) {
            return false;
        }

        if ( ! class_exists( 'GamiPress_Leaderboards' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = GAMIPRESS_LEADERBOARDS_INCLUDE_EXCLUDE_USERS_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_leaderboards_include_exclude_users_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-leaderboards-include-exclude-users' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-leaderboards-include-exclude-users', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-leaderboards-include-exclude-users/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-leaderboards-include-exclude-users', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-leaderboards-include-exclude-users', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-leaderboards-include-exclude-users', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Leaderboards_Include_Exclude_Users instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Leaderboards_Include_Exclude_Users The one true GamiPress_Leaderboards_Include_Exclude_Users
 */
function GamiPress_Leaderboards_Include_Exclude_Users() {
    return GamiPress_Leaderboards_Include_Exclude_Users::instance();
}
add_action( 'plugins_loaded', 'GamiPress_Leaderboards_Include_Exclude_Users' );
