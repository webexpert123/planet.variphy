<?php
/**
 * Admin
 *
 * @package     GamiPress\Leaderboards\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function gamipress_leaderboards_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_leaderboards_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * Add GamiPress Leaderboards admin bar menu
 *
 * @since 1.1.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_leaderboards_admin_bar_menu( $wp_admin_bar ) {

    // - Leaderboards
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-leaderboards',
        'title'  => __( 'Leaderboards', 'gamipress-leaderboards' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'edit.php?post_type=leaderboard' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_leaderboards_admin_bar_menu', 100 );

/**
 * GamiPress Leaderboards Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_leaderboards_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_leaderboards_';

    $meta_boxes['gamipress-leaderboards-settings'] = array(
        'title' => gamipress_dashicon( 'leaderboard' ) . __( 'Leaderboards', 'gamipress-leaderboards' ),
        'fields' => apply_filters( 'gamipress_leaderboards_settings_fields', array(
            $prefix . 'post_type_title' => array(
                'name' => __( 'Leaderboard Post Type', 'gamipress-leaderboards' ),
                'desc' => __( 'From this settings you can modify the default configuration of the leaderboard post type.', 'gamipress-leaderboards' ),
                'type' => 'title',
            ),
            $prefix . 'slug' => array(
                'name' => __( 'Slug', 'gamipress-leaderboards' ),
                'desc' => '<span class="gamipress-leaderboards-full-slug hide-if-no-js">' . site_url() . '/<strong class="gamipress-leaderboards-slug"></strong>/</span>',
                'type' => 'text',
                'default' => 'leaderboards',
            ),
            $prefix . 'public' => array(
                'name' => __( 'Public', 'gamipress-leaderboards' ),
                'desc' => __( 'Check this option if you want to allow to your visitors access to a leaderboard as a page. Not checking this option will make leaderboard just visible through shortcodes or widgets.', 'gamipress-leaderboards' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'supports' => array(
                'name' => __( 'Supports', 'gamipress-leaderboards' ),
                'desc' => __( 'Check the features you want to add to the leaderboard post type.', 'gamipress-leaderboards' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options' => array(
                    'title'             => __( 'Title' ),
                    'editor'            => __( 'Editor' ),
                    'author'            => __( 'Author' ),
                    'thumbnail'         => __( 'Thumbnail' ) . ' (' . __( 'Featured Image' ) . ')',
                    'excerpt'           => __( 'Excerpt' ),
                    'trackbacks'        => __( 'Trackbacks' ),
                    'custom-fields'     => __( 'Custom Fields' ),
                    'comments'          => __( 'Comments' ),
                    'revisions'         => __( 'Revisions' ),
                    'page-attributes'   => __( 'Page Attributes' ),
                    'post-formats'      => __( 'Post Formats' ),
                ),
                'default' => array( 'title', 'editor', 'excerpt' )
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_leaderboards_settings_meta_boxes' );

/**
 * GamiPress Leaderboards Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_leaderboards_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-leaderboards-license'] = array(
        'title' => __( 'GamiPress Leaderboards', 'gamipress-leaderboards' ),
        'fields' => array(
            'gamipress_leaderboards_license' => array(
                'name' => __( 'License', 'gamipress-leaderboards' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_LEADERBOARDS_FILE,
                'item_name' => 'Leaderboards',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_leaderboards_licenses_meta_boxes' );

/**
 * GamiPress Leaderboards automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_leaderboards_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-leaderboards'] = __( 'Leaderboards', 'gamipress-leaderboards' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_leaderboards_automatic_updates' );

/**
 * Register custom meta boxes used throughout GamiPress
 *
 * @since  1.0.0
 */
function gamipress_leaderboards_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_leaderboards_';

    // Leaderboard Data
    gamipress_add_meta_box(
        'leaderboard-data',
        __( 'Leaderboard Data', 'gamipress-leaderboards' ),
        'leaderboard',
        array(
            $prefix . 'users' => array(
                'name' 	=> __( 'Number of Users', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Number of users to rank (0 to all users).', 'gamipress-leaderboards' ),
                'type' 	=> 'text_small',
                'attributes' => array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
                'default' => '10'
            ),
            $prefix . 'users_per_page' => array(
                'name' 	=> __( 'Users Per Page', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Number of users per page (0 to disable pagination).', 'gamipress-leaderboards' ),
                'type' 	=> 'text_small',
                'attributes' => array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
                'default' => '10'
            ),
            $prefix . 'metrics' => array(
                'name' 	=> __( 'Metrics to track', 'gamipress-leaderboards' ),
                'desc' 	=> __( 'Choose the metrics with which users will be ranked.', 'gamipress-leaderboards' ),
                'type' 	=> 'multicheck',
                'options' 	=> 'gamipress_leaderboards_metrics_options_cb',
                'classes' 	=> 'gamipress-switch',
            ),
        ),
        array( 'priority' => 'high', )
    );

    // Leaderboard Time Period
    gamipress_add_meta_box(
        'leaderboard-period',
        __( 'Leaderboard Time Period', 'gamipress-leaderboards' ),
        'leaderboard',
        array(
            $prefix . 'period' => array(
                'name' 	        => __( 'Period', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Filter metrics to track based on a specific period selected. By default "None", that will rank users based on their current earnings.', 'gamipress-leaderboards' ),
                'type' 	        => 'select',
                'options_cb' 	=> 'gamipress_leaderboards_get_time_periods',
            ),
            $prefix . 'period_start_date' => array(
                'name' 	        => __( 'Start Date', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Period start date. Leave blank to no filter by a start date (metrics will be filtered only to the end date).', 'gamipress-leaderboards' )
                                . '<br>' . __( 'Accepts any valid PHP date format.', 'gamipress-leaderboards' ) . ' (<a href="https://gamipress.com/docs/advanced/date-fields" target="_blank">' .  __( 'More information', 'gamipress-leaderboards' ) .  '</a>)',
                'type'          => 'text',
            ),
            $prefix . 'period_end_date' => array(
                'name' 	        => __( 'End Date', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Period end date. Leave blank to no filter by an end date (metrics will be filtered from the start date to today).', 'gamipress-leaderboards' )
                                . '<br>' . __( 'Accepts any valid PHP date format.', 'gamipress-leaderboards' ) . ' (<a href="https://gamipress.com/docs/advanced/date-fields" target="_blank">' .  __( 'More information', 'gamipress-leaderboards' ) .  '</a>)',
                'type'          => 'text',
            ),
        ),
        array( 'priority' => 'high', )
    );

    // Leaderboard Display Options
    gamipress_add_meta_box(
        'leaderboard-display-options',
        __( 'Leaderboard Display Options', 'gamipress-leaderboards' ),
        'leaderboard',
        array(
            $prefix . 'columns' => array(
                'name' 	        => __( 'Columns', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Choose the columns to show. Drag and drop any option to reorder them.', 'gamipress-leaderboards' ),
                'type' 	        => 'multicheck',
                'options_cb' 	=> 'gamipress_leaderboards_columns_options_cb',
                'classes' 	    => 'gamipress-switch',
            ),
            $prefix . 'avatar_size' => array(
                'name' 	        => __( 'Avatar Size', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Size of the users avatars.', 'gamipress-leaderboards' ),
                'type' 	        => 'text',
                'attributes' 	=> array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
                'default'    => 96,
            ),
            $prefix . 'merge_avatar_and_name' => array(
                'name' 	        => __( 'Merge Avatar & Name Columns', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Merge the avatar and name columns into one column.', 'gamipress-leaderboards' ),
                'type' 	        => 'checkbox',
                'classes' 	    => 'gamipress-switch',
            ),
            $prefix . 'search' => array(
                'name' 	        => __( 'Show Search', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Display a search input.', 'gamipress-leaderboards' ),
                'type' 	        => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default_cb'    => 'gamipress_cmb2_checkbox_enabled_by_default',
            ),
            $prefix . 'sort' => array(
                'name' 	        => __( 'Enable Sort', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Enable live column sorting.', 'gamipress-leaderboards' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
                'default_cb'    => 'gamipress_cmb2_checkbox_enabled_by_default',
            ),
            $prefix . 'hide_admins' => array(
                'name' 	        => __( 'Hide Administrators', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Hide website administrators.', 'gamipress-leaderboards' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
                'default_cb'    => 'gamipress_cmb2_checkbox_enabled_by_default',
            ),
        ),
        array( 'priority' => 'high', )
    );

    // Leaderboard Cache Options
    gamipress_add_meta_box(
        'leaderboard-cache-options',
        __( 'Leaderboard Cache Options', 'gamipress-leaderboards' ),
        'leaderboard',
        array(
            $prefix . 'cache' => array(
                'name' 	        => __( 'Enable Cache', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'Check this option to store leaderboard results by a limited time to improve loading time speed.', 'gamipress-leaderboards' ),
                'type' 	        => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default_cb'    => 'gamipress_cmb2_checkbox_enabled_by_default',
            ),
            $prefix . 'cache_duration' => array(
                'name' 	        => __( 'Cache Duration', 'gamipress-leaderboards' ),
                'desc' 	        => __( 'How much time should cache be stored (in hours).', 'gamipress-leaderboards' ),
                'type' 	        => 'text',
                'attributes' 	=> array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
                'default'    => 12,
            ),
            $prefix . 'cache_information' => array(
                'name' 	        => __( 'Cache Information', 'gamipress-leaderboards' ),
                'type' 	        => 'multi_buttons',
                'classes_cb' 	=> 'gamipress_leaderboards_cache_information_classes_cb',
                'before' 	    => 'gamipress_leaderboards_cache_information_before_cb',
                'buttons'       => array(
                    $prefix . 'clear_cache' => array(
                        'label' => __( 'Clear Cache', 'gamipress-leaderboards' )
                    )
                )
            ),
        ),
        array( 'priority' => 'high', )
    );

    // Leaderboard Shortcode
    gamipress_add_meta_box(
        'leaderboard-shortcode',
        __( 'Leaderboard Shortcode', 'gamipress-leaderboards' ),
        'leaderboard',
        array(
            $prefix . 'shortcode' => array(
                'desc' 	        => __( 'Place this shortcode anywhere to display this leaderboard.', 'gamipress-leaderboards' ),
                'type' 	        => 'text',
                'attributes'    => array(
                    'readonly'  => 'readonly',
                    'onclick'   => 'this.focus(); this.select();'
                ),
                'default_cb'    => 'gamipress_leaderboards_shortcode_field_default_cb'
            ),
        ),
        array(
            'context'  => 'side',
            'priority' => 'default'
        )
    );

}
add_action( 'gamipress_init_leaderboard_meta_boxes', 'gamipress_leaderboards_meta_boxes' );

// Metrics options cb
function gamipress_leaderboards_metrics_options_cb( $field ) {

    return gamipress_leaderboards_get_metrics_options();

}

// Columns options cb
function gamipress_leaderboards_columns_options_cb( $field ) {

    $columns_order = gamipress_get_post_meta( $field->object_id, '_gamipress_leaderboards_columns_order', true );

    if( ! $columns_order ) {
        $columns_order = array();
    }

    $columns_options = gamipress_leaderboards_get_columns_options();

    $final_options = array();

    foreach( $columns_order as $column_option ) {
        if( isset( $columns_options[$column_option] ) ) {
            $final_options[$column_option] = '<input type="hidden" name="_gamipress_leaderboards_columns_order[]" value="' . $column_option . '" />' .  $columns_options[$column_option];
        }
    }

    $columns_options_keys = array_keys( $columns_options );
    $unordered_column_options = array_diff( $columns_options_keys, $columns_order );

    // Append unordered column options
    foreach( $unordered_column_options as $column_option ) {
        if( isset( $columns_options[$column_option] ) ) {
            $final_options[$column_option] = '<input type="hidden" name="_gamipress_leaderboards_columns_order[]" value="' . $column_option . '" />' .  $columns_options[$column_option];
        }
    }

    return $final_options;

}

// Handle leaderboard fields save to update our hidden order field
function gamipress_leaderboards_save_leaderboard_fields( $object_id, $updated, $cmb ) {

    if( isset( $_POST['_gamipress_leaderboards_columns_order'] ) && ! empty( $_POST['_gamipress_leaderboards_columns_order'] ) ) {
        update_post_meta( $object_id, '_gamipress_leaderboards_columns_order', $_POST['_gamipress_leaderboards_columns_order'] );
    }

}
add_action( 'cmb2_save_post_fields_leaderboard-display-options', 'gamipress_leaderboards_save_leaderboard_fields', 10, 3 );

// Shortcode field default cb
function gamipress_leaderboards_shortcode_field_default_cb( $field_args, $field ) {
    return '[gamipress_leaderboard id="' . $field->object_id . '"]';
}

// Cache information classes cb
function gamipress_leaderboards_cache_information_classes_cb( $field_args, $field ) {

    if( false === $cache_info = gamipress_get_transient( 'gamipress_leaderboard_' . $field->object_id . '_info' ) ) {
        return array( 'gamipress-leaderboards-no-cache-info' );
    }

    return array();
}

// Cache information before cb
function gamipress_leaderboards_cache_information_before_cb( $field_args, $field ) {

    $cache_info_text = __( 'No cache stored.', 'gamipress-leaderboards' );

    if( false !== $cache_info = gamipress_get_transient( 'gamipress_leaderboard_' . $field->object_id . '_info' ) ) {
        $cache_info_text = __( 'Created:', 'gamipress-leaderboards' ) . ' ' . date_i18n( get_option( 'date_format' ), $cache_info['timestamp'] )
            . '<br>' . __( 'Users Ranked:', 'gamipress-leaderboards' ) . ' ' . $cache_info['results'];
    }

    echo '<p id="gamiress-leaderboards-cache-information">' . $cache_info_text . '</p>';

}

/**
 * Clear leaderboard cache on save
 *
 * @since 1.2.6
 *
 * @param int $post_id
 */
function gamipress_leaderboards_on_save_leaderboard( $post_id ) {

    $post = get_post( $post_id );

    if( ! $post ) {
        return;
    }

    if( $post->post_type !== 'leaderboard' ) {
        return;
    }

    gamipress_leaderboards_clear_leaderboard_cache( $post_id );

}
add_action( 'save_post', 'gamipress_leaderboards_on_save_leaderboard' );