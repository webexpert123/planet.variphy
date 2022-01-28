<?php
/**
 * Scripts
 *
 * @package     GamiPress\Leaderboards\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_leaderboards_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_script( 'gamipress-leaderboards-datatables-js', GAMIPRESS_LEADERBOARDS_URL . 'assets/libs/DataTables/js/jquery.dataTables.min.js', array( 'jquery' ), GAMIPRESS_LEADERBOARDS_VER, true );

    // Stylesheets
    wp_register_style( 'gamipress-leaderboards-css', GAMIPRESS_LEADERBOARDS_URL . 'assets/css/gamipress-leaderboards' . $suffix . '.css', array( ), GAMIPRESS_LEADERBOARDS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-leaderboards-js', GAMIPRESS_LEADERBOARDS_URL . 'assets/js/gamipress-leaderboards' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_LEADERBOARDS_VER, true );
}
add_action( 'init', 'gamipress_leaderboards_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_leaderboards_enqueue_scripts( $hook = null ) {

    $disable_datatables = gamipress_leaderboards_disable_datatables();

    // Enqueue libraries
    if( ! $disable_datatables ) {
        wp_enqueue_script( 'gamipress-leaderboards-datatables-js' );
    }

    // Localize scripts
    wp_localize_script( 'gamipress-leaderboards-js', 'gamipress_leaderboards', array(
        'ajaxurl' => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
        'disable_datatables' => $disable_datatables,
        'language' => array(
            'decimal' =>        '',
            'emptyTable' =>     __( 'No data available in table', 'gamipress-leaderboards' ),
            'info' =>           __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'gamipress-leaderboards' ),
            'infoEmpty' =>      __( 'Showing 0 to 0 of 0 entries', 'gamipress-leaderboards' ),
            'infoFiltered' =>   __( '(filtered from _MAX_ total entries)', 'gamipress-leaderboards' ),
            'infoPostFix' =>    '',
            'thousands' =>      ',',
            'lengthMenu' =>     __( 'Show _MENU_ entries', 'gamipress-leaderboards' ),
            'loadingRecords' => __( 'Loading...', 'gamipress-leaderboards' ),
            'processing' =>     __( 'Processing...', 'gamipress-leaderboards' ),
            'search' =>         __( 'Search:', 'gamipress-leaderboards' ),
            'zeroRecords' =>    __( 'No matching records found', 'gamipress-leaderboards' ),
            'paginate' => array(
                'first' =>      __( 'First', 'gamipress-leaderboards' ),
                'last' =>       __( 'Last', 'gamipress-leaderboards' ),
                'next' =>       __( 'Next', 'gamipress-leaderboards' ),
                'previous' =>   __( 'Previous', 'gamipress-leaderboards' )
            ),
            'aria' => array(
                'sortAscending' =>  __( ': activate to sort column ascending', 'gamipress-leaderboards' ),
                'sortDescending' => __( ': activate to sort column descending', 'gamipress-leaderboards' )
            )
        )
    ) );

    // Enqueue assets
    wp_enqueue_style( 'gamipress-leaderboards-css' );
    wp_enqueue_script( 'gamipress-leaderboards-js' );

}
add_action( 'wp_enqueue_scripts', 'gamipress_leaderboards_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_leaderboards_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-leaderboards-admin-css', GAMIPRESS_LEADERBOARDS_URL . 'assets/css/gamipress-leaderboards-admin' . $suffix . '.css', array( ), GAMIPRESS_LEADERBOARDS_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-leaderboards-admin-js', GAMIPRESS_LEADERBOARDS_URL . 'assets/js/gamipress-leaderboards-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_LEADERBOARDS_VER, true );
    wp_register_script( 'gamipress-leaderboards-shortcode-editor-js', GAMIPRESS_LEADERBOARDS_URL . 'assets/js/gamipress-leaderboards-shortcode-editor' . $suffix . '.js', array( 'jquery', 'gamipress-select2-js' ), GAMIPRESS_LEADERBOARDS_VER, true );

}
add_action( 'admin_init', 'gamipress_leaderboards_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_leaderboards_admin_enqueue_scripts( $hook ) {

    global $post_type;

    // Stylesheets
    wp_enqueue_style( 'gamipress-leaderboards-admin-css' );

    // Localize scripts
    wp_localize_script( 'gamipress-leaderboards-admin-js', 'gamipress_leaderboards_admin', array(
        'nonce' => gamipress_get_admin_nonce(),
    ) );

    // Scripts
    wp_enqueue_script( 'gamipress-leaderboards-admin-js' );

    // Just enqueue on add/edit views and on post types that supports editor feature
    if( ( $hook === 'post.php' || $hook === 'post-new.php' ) && post_type_supports( $post_type, 'editor' ) ) {
        wp_enqueue_script( 'gamipress-leaderboards-shortcode-editor-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_leaderboards_admin_enqueue_scripts', 100 );