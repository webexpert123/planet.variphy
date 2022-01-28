<?php
/**
 * Functions
 *
 * @package     GamiPress\Leaderboards\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return leaderboard metrics options
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_leaderboards_get_metrics_options() {

    $metrics_options = array();

    // Achievement types
    $metrics_options['achievement_types'] = '<strong>' . __( 'Achievement Types', 'gamipress-leaderboards' ) . '</strong>';

    foreach( gamipress_get_achievement_types() as $achievement_type_slug => $achievement_type ) {
        if( in_array( $achievement_type_slug, array( 'step', 'points-award' ) ) ) {
            continue;
        }

        $metrics_options[$achievement_type_slug] = $achievement_type['plural_name'];
    }

    // Points types

    $metrics_options['points_types'] = '<strong>' . __( 'Points Types', 'gamipress-leaderboards' ) . '</strong>';

    foreach( gamipress_get_points_types() as $points_type_slug => $points_type ) {

        $metrics_options[$points_type_slug] = $points_type['plural_name'];
    }

    // Rank types
    $metrics_options['rank_types'] = '<strong>' . __( 'Rank Types', 'gamipress-leaderboards' ) . '</strong>';

    foreach( gamipress_get_rank_types() as $rank_type_slug => $rank_type ) {

        $metrics_options[$rank_type_slug] = $rank_type['singular_name'];
    }

    return apply_filters( 'gamipress_leaderboards_metric_options', $metrics_options );

}

/**
 * Return leaderboard columns options
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_leaderboards_get_columns_options() {

    $metrics_options = gamipress_leaderboards_get_metrics_options();

    unset( $metrics_options['achievement_types'] );
    unset( $metrics_options['points_types'] );
    unset( $metrics_options['rank_types'] );

    $columns_options = array(
        'avatar' => is_admin() ? __( 'User Avatar', 'gamipress-leaderboards' ) : __( 'Avatar', 'gamipress-leaderboards' ),
        'display_name' => is_admin() ? __( 'User Display Name', 'gamipress-leaderboards' ) : __( 'Name', 'gamipress-leaderboards' )
    );

    $columns_options = $columns_options + $metrics_options;

    return apply_filters( 'gamipress_leaderboards_columns_options', $columns_options );

}

/**
 * Return the user position in a leaderboard
 *
 * @since  1.0.8
 *
 * @param int $leaderboard_id   The leaderboard's ID
 * @param int $user_id          The user's ID
 *
 * @return int|false            Return the user position or false if is not ranked on this leaderboard
 */
function gamipress_leaderboards_get_user_position( $leaderboard_id, $user_id = null ) {

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    if( absint( $user_id ) === 0 ) {
        return false;
    }

    // Setup the leaderboard table
    $leaderboard_table = new GamiPress_Leaderboard_Table( $leaderboard_id );

    $position = false;

    if( $leaderboard_table->is_pagination_enabled() ) {
        // Paginated leaderboards

        // If cache is enabled, try to find the user in the cache
        if( (bool) $leaderboard_table->get_post_meta( 'cache' ) ) {

            if( false !== ( $results = gamipress_get_transient( 'gamipress_leaderboard_' . $leaderboard_id ) ) ) {

                foreach( $results as $page => $items ) {

                    // Skip not numeric pages
                    if( ! is_numeric( $page ) ) {
                        continue;
                    }

                    if( array( $items ) ) {

                        $index = array_search( $user_id . '', array_column( $items, 'user_id' ) );

                        if( $index !== false ) {
                            $items_per_page = $leaderboard_table->query_vars['items_per_page'];
                            $page -= 1; // Since page starts on 1, decrement by 1
                            $index += 1; // Since index starts on 0, increment by 1

                            $position = ( $items_per_page * $page ) + $index;
                            break;
                        }

                    }

                }

            }

        }

    }

    if( $position === false ) {
        // No paginated leaderboards and fallback for paginated leaderboards without cache

        $leaderboard_items = $leaderboard_table->get_items();

        // Make a search by on user ID column to get the index on leaderboard items array
        $index = array_search( $user_id . '', array_column( $leaderboard_items, 'user_id' ) );

        // Position is formed by index + 1
        $position = ( $index !== false ? $index + 1 : false );
    }

    // return the user position
    return $position;

}

/**
 * Gets registered time periods
 *
 * @since 1.1.5
 *
 * @return array
 */
function gamipress_leaderboards_get_time_periods() {

    return apply_filters( 'gamipress_leaderboards_get_time_periods', array(
        ''              => __( 'None', 'gamipress-leaderboards' ),
        'today'         => __( 'Today', 'gamipress-leaderboards' ),
        'yesterday'     => __( 'Yesterday', 'gamipress-leaderboards' ),
        'current-week'  => __( 'Current Week', 'gamipress-leaderboards' ),
        'past-week'     => __( 'Past Week', 'gamipress-leaderboards' ),
        'current-month' => __( 'Current Month', 'gamipress-leaderboards' ),
        'past-month'    => __( 'Past Month', 'gamipress-leaderboards' ),
        'current-year'  => __( 'Current Year', 'gamipress-leaderboards' ),
        'past-year'     => __( 'Past Year', 'gamipress-leaderboards' ),
        'custom'        => __( 'Custom', 'gamipress-leaderboards' ),
    ) );

}

/**
 * Clear a leaderboard cache
 *
 * @since 1.2.6
 *
 * @param int $leaderboard_id
 */
function gamipress_leaderboards_clear_leaderboard_cache( $leaderboard_id ) {

    $leaderboard_id = absint( $leaderboard_id );

    if( $leaderboard_id === 0 ) {
        return;
    }

    gamipress_delete_transient(  'gamipress_leaderboard_' . $leaderboard_id );
    gamipress_delete_transient(  'gamipress_leaderboard_' . $leaderboard_id . '_info' );

}

/**
 * Extensible function to disable datatables
 *
 * @since 1.2.9
 *
 * @return bool
 */
function gamipress_leaderboards_disable_datatables() {

    return apply_filters( 'gamipress_leaderboards_disable_datatables', false );

}