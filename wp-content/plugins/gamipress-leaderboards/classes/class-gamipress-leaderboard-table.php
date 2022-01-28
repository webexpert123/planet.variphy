<?php
/**
 * GamiPress Leaderboard Table Class
 *
 * @package     GamiPress\Leaderboards\Leaderboard_Table
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Leaderboard_Table {

    public $leaderboard_id = 0;
    public $args = array();
    public $items = array();
    public $columns = array();
    public $metas = array();
    public $query_vars = array();
    public $query_args = array();
    public $results = array();
    public $found_results = 0;

    public function __construct( $leaderboard_id = null, $args = array() ) {

        if( $leaderboard_id === null ) {
            $this->leaderboard_id = get_the_ID();
        } else {
            $this->leaderboard_id = $leaderboard_id;
        }

        // For missing attributes, load the leaderboard setup
        if( ! isset( $args['search'] ) ) {
            $args['search'] = (bool) $this->get_post_meta( 'search' );
        }

        if( ! isset( $args['sort'] ) ) {
            $args['sort'] = (bool) $this->get_post_meta( 'sort' );
        }

        if( ! isset( $args['hide_admins'] ) ) {
            $args['hide_admins'] = (bool) $this->get_post_meta( 'hide_admins' );
        }

        // Setup default args
        $args = wp_parse_args( $args, array(
            'search'            => true,
            'sort'              => true,
            'hide_admins'       => true,
            'force_responsive'  => false,
        ) );

        /**
         * Filter the leaderboard args
         *
         * @since 1.0.8
         *
         * @param array                         $args                 An array with all query vars
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
         */
        $this->args = apply_filters( 'gamipress_leaderboards_leaderboard_args', $args, $this->leaderboard_id, $this );

        $this->parse_query_vars();
    }

    public function display() {

        if( gamipress_get_post_field( 'post_type', $this->leaderboard_id ) !== 'leaderboard' ) {
            return;
        }

        $this->prepare_items();

        if( $this->has_items() ) :

            // Setup table classes
            $classes = $this->get_table_classes(); ?>

            <?php
            /**
             * Action triggered before render the leaderboard table
             *
             * @since 1.2.1
             *
             * @param integer                       $leaderboard_id       The Leaderboard ID
             * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
             */
            do_action( 'gamipress_leaderboards_before_render_leaderboard_table', $this->leaderboard_id, $this ); ?>

            <table class="gamipress-leaderboard-table <?php echo implode( ' ', $classes ); ?>" data-leaderboard-id="<?php echo $this->leaderboard_id; ?>">

                <?php
                /**
                 * Action triggered at top of the leaderboard table
                 *
                 * @since 1.2.1
                 *
                 * @param integer                       $leaderboard_id       The Leaderboard ID
                 * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
                 */
                do_action( 'gamipress_leaderboards_leaderboard_table_top', $this->leaderboard_id, $this ); ?>

                <thead>
                    <tr>
                        <?php $this->print_column_headers(); ?>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    /**
                     * Action triggered before render the leaderboard table body
                     *
                     * @since 1.2.1
                     *
                     * @param integer                       $leaderboard_id       The Leaderboard ID
                     * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
                     */
                    do_action( 'gamipress_leaderboards_before_render_leaderboard_table_body', $this->leaderboard_id, $this ); ?>

                    <?php $this->display_rows(); ?>

                    <?php
                    /**
                     * Action triggered after render the leaderboard table body
                     *
                     * @since 1.2.1
                     *
                     * @param integer                       $leaderboard_id       The Leaderboard ID
                     * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
                     */
                    do_action( 'gamipress_leaderboards_after_render_leaderboard_table_body', $this->leaderboard_id, $this ); ?>

                </tbody>

                <?php
                /**
                 * Action triggered at bottom of the leaderboard table
                 *
                 * @since 1.2.1
                 *
                 * @param integer                       $leaderboard_id       The Leaderboard ID
                 * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
                 */
                do_action( 'gamipress_leaderboards_leaderboard_table_bottom', $this->leaderboard_id, $this ); ?>

            </table>

            <?php
            /**
             * Action triggered after render the leaderboard table
             *
             * @since 1.2.1
             *
             * @param integer                       $leaderboard_id       The Leaderboard ID
             * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
             */
            do_action( 'gamipress_leaderboards_before_after_leaderboard_table', $this->leaderboard_id, $this ); ?>

            <?php if( $this->is_pagination_enabled() ) : ?>

                <div class="gamipress-leaderboards-infinite-scrolling" data-leaderboard-id="<?php echo $this->leaderboard_id; ?>" data-page="1">
                    <div id="gamipress-leaderboards-spinner" class="gamipress-spinner" style="display: none;"></div>
                </div>

            <?php endif; ?>

        <?php else: ?>
            <div class="gamipress-leaderboards no-items">
                <?php echo $this->no_items(); ?>
            </div>
        <?php endif;
    }

    public function to_array( $column_html = true ) {

        $result = array(
            'items' => array(),
            'more_items' => false
        );

        if( gamipress_get_post_field( 'post_type', $this->leaderboard_id ) !== 'leaderboard' ) {
            return $result;
        }

        $this->prepare_items();

        if( $this->has_items() ) {

            foreach ( $this->items as $position => $item ) {

                $offset = ( $this->query_vars['page'] - 1 ) * $this->query_vars['items_per_page'];
                $position += $offset;

                // Initialize items with the position column
                $result['items'][$position] = array(
                    'position' => $position,
                );

                // Get each column output
                foreach( $this->get_column_info() as $column_name => $column_display_name ) {

                    $output = $this->get_column_output( $position, $item, $column_name );

                    if( $column_html ) {

                        ob_start(); ?>
                        <td
                            class="column-<?php echo $column_name; ?>"
                            data-label="<?php echo esc_attr( $column_display_name ); ?>"
                            <?php // If column is a rank type, then we need to order by priority and search by rank name
                            if( in_array( $column_name, gamipress_get_rank_types_slugs() ) ) : ?>
                                data-sort="<?php echo esc_attr( $item[$column_name] ); ?>"
                                data-search="<?php echo esc_attr( $output ); ?>"
                            <?php endif; ?>
                        >

                            <?php // Shows the final output
                            echo $output; ?>

                        </td>
                        <?php
                        $output = ob_get_clean();

                    }

                    $result['items'][$position][$column_name] = $output;
                }
            }

            // Check if there will be more items to display
            $items_displayed = $this->query_vars['page'] * $this->query_vars['items_per_page'];
            $result['more_items'] = ( $items_displayed < $this->found_results );
            $result['found_items'] = $this->found_results;

        } else {
            $result['no_items'] = $this->no_items();
        }

        return $result;

    }

    public function prepare_items() {
        $this->items = $this->get_items();
    }

    public function has_items() {
        return ( count( $this->items ) > 0 );
    }

    public function no_items() {
        return __( 'No users ranked on this leaderboard.', 'gamipress-leaderboards' );
    }

    public function parse_query_vars() {

        global $wpdb;

        // Setup vars
        $achievement_types = gamipress_get_achievement_types_slugs();
        $rank_types = gamipress_get_rank_types_slugs();

        // Setup stored vars
        $max_items      = absint( $this->get_users() );
        $items_per_page = absint( $this->get_users_per_page() );
        $page           = max( 1, get_query_var( 'paged' ) );
        $metrics        = $this->get_metrics();
        $date_range     = $this->get_date_range();

        // Ensure page in case global WP_Query is not setup
        if( isset( $_REQUEST['paged'] ) ) {
            $page = absint( $_REQUEST['paged'] );
        }

        if( isset( $_REQUEST['page'] ) ) {
            $page = absint( $_REQUEST['page'] );
        }

        // Remove titles used to separate options
        if( isset( $metrics['achievement_types'] ) ) {
            unset( $metrics['achievement_types'] );
        }

        if( isset( $metrics['points_types'] ) ) {
            unset($metrics['points_types']);
        }

        if( isset( $metrics['rank_types'] ) ) {
            unset($metrics['rank_types']);
        }

        // Setup as local vars for easy usage
        $start_date = $date_range['start'];
        $end_date = $date_range['end'];

        // Setup table names
        $posts 				= GamiPress()->db->posts;
        $users 				= GamiPress()->db->users;
        $usermeta 			= GamiPress()->db->usermeta;
        $user_earnings      = GamiPress()->db->user_earnings;
        $logs               = GamiPress()->db->logs;
        $logs_meta          = GamiPress()->db->logs_meta;

        // Initialize query vars
        $select     = array( 'u.ID AS user_id' );
        $from       = array( "{$users} AS u" );
        $join       = array();
        $where      = array( '1=1' );
        $order_by   = array();
        $query_args = array();

        foreach( $metrics as $metric ) {

            if( in_array( $metric, $achievement_types ) ) {

                // Achievement type
                $i = count( $select ) + 1;

                // User achievements are calculated from user earnings table
                $achievements_query = "
                        SELECT COUNT(*) 
                        FROM {$user_earnings} as ue{$i} 
                        WHERE ue{$i}.user_id = u.ID 
                          AND ue{$i}.post_type = '{$metric}'
                    ";

                // Apply start date filter
                if( ! empty( $start_date ) )
                    $achievements_query .= "AND ue{$i}.date >= '{$start_date}' ";

                // Apply end date filter
                if( ! empty($end_date ) )
                    $achievements_query .= "AND ue{$i}.date <= '{$end_date}' ";

                // Do a sub count with the number of this achievement type earned
                $select[] = "( {$achievements_query} ) AS `{$metric}`";

            } else if( in_array( $metric, $rank_types ) ) {

                // Rank type
                $i = count( $join ) + 1;

                // If filtered by an end date, then need to get higher user rank on date range
                if( ! empty( $end_date ) ) {

                    // Let's to get last user rank from user earnings table
                    $ranks_query = "
                            SELECT ue{$i}.post_id
                            FROM {$user_earnings} as ue{$i} 
                            WHERE ue{$i}.user_id = u.ID 
                              AND ue{$i}.post_type = '{$metric}'
                              AND ue{$i}.date <= '{$end_date}'
                            ORDER BY ue{$i}.date DESC
                            LIMIT 1
                        ";

                    $select[] = "IFNULL( ( SELECT r.menu_order FROM {$posts} AS r WHERE r.ID = ( {$ranks_query} ) ), 1 ) AS `{$metric}`";

                } else {

                    // Retrieve the current rank priority from rank ID stored on user meta '_gamipress_{$points_type}_rank'
                    $select[] = "IFNULL( ( SELECT r.menu_order FROM {$posts} AS r WHERE r.ID = um{$i}.meta_value ), 1 ) AS `{$metric}`";
                    $join[] = "LEFT JOIN {$usermeta} AS um{$i} ON ( um{$i}.user_id = u.ID AND um{$i}.meta_key = '_gamipress_{$metric}_rank' )";

                }

            } else {

                // Points type
                $i = count( $join ) + 1;

                // If filtered then need to get user points from logs
                if( ! empty( $start_date ) || ! empty( $end_date ) ) {

                    $points_query = "
                            SELECT GREATEST( IFNULL( SUM( pm{$i}.meta_value ), 0 ), 0 )
                            FROM {$logs} AS l{$i}
                            INNER JOIN {$logs_meta} AS pm{$i} ON ( pm{$i}.log_id = l{$i}.log_id AND pm{$i}.meta_key = '_gamipress_points' )
                            INNER JOIN {$logs_meta} AS ptm{$i} ON ( ptm{$i}.log_id = l{$i}.log_id )
                            WHERE l{$i}.user_id = u.ID
                              AND pm{$i}.meta_value != 0
                              AND ptm{$i}.meta_key = '_gamipress_points_type' AND ptm{$i}.meta_value = '{$metric}'
                        ";

                    // Apply start date filter
                    if( ! empty( $start_date ) )
                        $points_query .= "AND l{$i}.date >= '{$start_date}' ";

                    // Apply end date filter
                    if( ! empty($end_date ) )
                        $points_query .= "AND l{$i}.date <= '{$end_date}' ";

                    // Do a sub count with the sum of points movements (awards, deducts, expend, etc)
                    $select[] = "( {$points_query} ) AS `{$metric}`";

                } else {

                    // Retrieve the current amount of points from the user meta '_gamipress_{$points_type}_points'
                    $select[] = "IFNULL( um{$i}.meta_value, 0 ) AS `{$metric}`";
                    $join[] = "LEFT JOIN {$usermeta} AS um{$i} ON ( um{$i}.user_id = u.ID AND um{$i}.meta_key = '_gamipress_{$metric}_points' )";

                }

            }

            $order_by[] = "CAST( `{$metric}` AS UNSIGNED )";
        }

        // If hide admins is set to true, the filter users that has not this capability
        if( $this->args['hide_admins'] ) {

            $join[] = "LEFT JOIN {$usermeta} AS umcap ON ( umcap.user_id = u.ID AND umcap.meta_key = %s )";
            $where[] = "umcap.meta_value NOT LIKE %s";
            $query_args[] = $wpdb->get_blog_prefix() . "capabilities";
            $query_args[] = '%"administrator"%';

        }

        // Setup query vars
        $query_vars = array(
            'select'            => $select,
            'from'              => $from,
            'join'              => $join,
            'where'             => $where,
            'order_by'          => $order_by,
            'limit'             => $max_items,
            'max_items'         => $max_items,
            'items_per_page'    => $items_per_page,
            'page'              => $page,
        );

        /**
         * Filter the leaderboard query vars (before get processed)
         *
         * @since 1.0.6
         *
         * @param array                         $query_vars           An array with all query vars
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
         */
        $query_vars = apply_filters( "gamipress_leaderboards_leaderboard_pre_query_vars", $query_vars, $this->leaderboard_id, $this );

        // Sanitize vars
        $query_vars['limit']            = absint( $query_vars['limit'] );
        $query_vars['max_items']        = absint( $query_vars['max_items'] );
        $query_vars['items_per_page']   = absint( $query_vars['items_per_page'] );
        $query_vars['page']             = absint( $query_vars['page'] );

        // Process query vars
        $query_vars['select']   = ( ! empty( $query_vars['select'] )    ? implode( ', ', $query_vars['select'] )                                : '' );
        $query_vars['from']     = ( ! empty( $query_vars['from'] )      ? implode( ', ', $query_vars['from'] )                                  : '' );
        $query_vars['join']     = ( ! empty( $query_vars['join'] )      ? implode( ' ',  $query_vars['join'] )                                  : '' );
        $query_vars['where']    = ( ! empty( $query_vars['where'] )     ? 'WHERE ' . implode( ' AND ', $query_vars['where'] )                   : '' );
        $query_vars['order_by'] = ( ! empty( $query_vars['order_by'] )  ? 'ORDER BY ( ' . implode( ' + ', $query_vars['order_by'] ) . ' ) DESC' : '' );

        // Setup the LIMIT clause
        $query_vars['limit'] = '';

        if( $query_vars['items_per_page'] !== 0 && $query_vars['items_per_page'] < $query_vars['max_items'] ) {
            $offset = ( $query_vars['page'] - 1 ) * $query_vars['items_per_page'];

            $query_vars['limit'] = "LIMIT {$offset}, {$query_vars['items_per_page']}";
        } else if( $query_vars['max_items'] > 0 ) {
            $query_vars['limit'] = "LIMIT 0, {$query_vars['max_items']}";
        }

        /**
         * Filter the leaderboard query vars (after get processed)
         *
         * @since 1.0.6
         *
         * @param array                         $query_vars           An array with all query vars
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        $query_vars = apply_filters( "gamipress_leaderboards_leaderboard_query_vars", $query_vars, $this->leaderboard_id, $this );

        $this->query_vars = $query_vars;
        $this->query_args = $query_args;

    }

    public function get_items() {

        global $wpdb;

        $this->results = $this->get_results_cache();

        if( $this->results === false ) {

            // Setup the query
            $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT {$this->query_vars['select']}
                FROM {$this->query_vars['from']}
                {$this->query_vars['join']}
                {$this->query_vars['where']}
                {$this->query_vars['order_by']}
                {$this->query_vars['limit']}";

            // Prevent to call to wpdb::prepare() if there aren't any query args
            if( empty( $this->query_args ) ) {
                $query = $sql;
            } else {
                $query = $wpdb->prepare( $sql, $this->query_args );
            }

            /**
             * Filter the leaderboard SQL query
             *
             * @since 1.0.6
             *
             * @param string                        $query                The leaderboard query
             * @param array                         $query_vars           An array with all query vars
             * @param integer                       $leaderboard_id       The Leaderboard ID
             * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
             */
            $query = apply_filters( "gamipress_leaderboards_leaderboard_query", $query, $this->query_vars, $this->leaderboard_id, $this );

            // Execute our query
            $this->results = $wpdb->get_results( $query, ARRAY_A );

            // Set results found
            if( $this->query_vars['max_items'] > 0 ) {
                $this->found_results = $this->query_vars['max_items'];
            } else {
                $this->found_results = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
            }

            // Update the cache
            $this->update_results_cache( $this->results );

        }

        return $this->results;

    }

    public function get_results_cache() {

        // If caching is enabled and this leaderboard has been cached already, then use cached results
        if ( (bool) $this->get_post_meta( 'cache' ) && false !== ( $results = gamipress_get_transient( 'gamipress_leaderboard_' . $this->leaderboard_id ) ) ) {

            if( $this->is_pagination_enabled() ) {

                // Set results found from cache
                if( isset( $results['found_results'] ) ) {
                    $this->found_results = $results['found_results'];
                }

                // Return the page cached results
                if( isset( $results[$this->query_vars['page']] ) ) {
                    return $results[$this->query_vars['page']];
                }

            } else {
                // Return the cached results
                return $results;
            }

        }

        return false;

    }

    public function update_results_cache( $results ) {

        // If caching results is enabled, then store results by time desired in hours
        if ( (bool) $this->get_post_meta( 'cache' ) ) {
            $transient_duration = absint( $this->get_post_meta( 'cache_duration' ) );

            // Set default transient duration
            if( $transient_duration === 0 ) {
                $transient_duration = 12;
            }

            $transient_duration *= HOUR_IN_SECONDS;

            if( $this->is_pagination_enabled() ) {

                $stored_results = gamipress_get_transient( 'gamipress_leaderboard_' . $this->leaderboard_id );

                if( ! $stored_results ) {
                    $stored_results = array();
                }

                $stored_results[$this->query_vars['page']] = $results;
                $stored_results['found_results'] = $this->found_results;

                // Set the results transient
                gamipress_set_transient( 'gamipress_leaderboard_' . $this->leaderboard_id, $stored_results, $transient_duration );

                // Store a new transient with additional info
                gamipress_set_transient( 'gamipress_leaderboard_' . $this->leaderboard_id . '_info', array(
                    'timestamp' => current_time( 'timestamp' ),
                    'results' => $this->found_results,
                ), $transient_duration );

            } else {

                // Set the results transient
                gamipress_set_transient( 'gamipress_leaderboard_' . $this->leaderboard_id, $results, $transient_duration );

                // Store a new transient with additional info
                gamipress_set_transient( 'gamipress_leaderboard_' . $this->leaderboard_id . '_info', array(
                    'timestamp' => current_time( 'timestamp' ),
                    'results' => count( $results ),
                ), $transient_duration );

            }


        }

    }

    public function get_table_classes() {

        $classes = array(
            'gamipress-leaderboard-table-responsive', // Class to make the leaderboard responsive
            'gamipress-leaderboard-table-responsive-toggleable', // Class to make responsive leaderboards toggleables
        );

        if( $this->args['force_responsive'] ) {
            $classes[] = 'gamipress-leaderboard-table-force-responsive'; // Class to force the leaderboard responsive
            $classes[] = 'gamipress-leaderboard-table-force-responsive-toggleable'; // Class to force responsive leaderboards toggleables
        }

        if( $this->args['search'] ) {
            $classes[] = 'gamipress-leaderboard-search-enabled';
        }

        if( $this->args['sort'] ) {
            $classes[] = 'gamipress-leaderboard-sort-enabled';
        }

        if( $this->is_pagination_enabled() ) {
            $classes[] = 'gamipress-leaderboard-pagination-enabled';
        }

        /**
         * Filter the leaderboard classes
         *
         * @since 1.0.0
         *
         * @param array                         $classes              An array with leaderboard classes
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_table_classes', $classes, $this->leaderboard_id, $this );

    }

    public function print_column_headers() {

        foreach( $this->get_column_info() as $column => $column_display_name ) : ?>

            <th class="column-<?php echo $column; ?> column-header <?php echo ( $column === 'avatar' ) ? 'no-sortable' : ''; ?>">
                <?php echo $column_display_name; ?>
            </th>

        <?php endforeach;

    }

    public function get_column_info() {

        $columns = $this->get_columns();
        $metrics = $this->get_metrics();
        $merge_avatar_and_name = (bool) $this->get_post_meta( 'merge_avatar_and_name' );

        $columns_options = gamipress_leaderboards_get_columns_options();

        $final_columns = array(
            'position' => '#'   // Position is always served and as first element
        );

        foreach( $columns as $column ) {

            // Prevent to show columns that are not in metrics but keep display name and avatar
            if(
                ! in_array( $column, $metrics )
                && $column !== 'display_name'
                && $column !== 'avatar'
            ) {
                continue;
            }

            if( $column === 'avatar' && $merge_avatar_and_name ) {
                continue;
            }

            $final_columns[$column] = $columns_options[$column];

        }

        /**
         * Filter the leaderboard columns to show
         *
         * @since 1.0.9
         *
         * @param array                         $columns              An array with columns to be shown
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_columns_info', $final_columns, $this->leaderboard_id, $this );

    }

    public function get_users() {

        $users = $this->get_post_meta( 'users' );

        /**
         * Filter the leaderboard columns
         *
         * @since 1.0.0
         *
         * @param integer                       $users                Number of users
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_users', $users, $this->leaderboard_id, $this );

    }

    public function get_users_per_page() {

        $users_per_page = $this->get_post_meta( 'users_per_page' );

        /**
         * Filter the leaderboard columns
         *
         * @since 1.0.0
         *
         * @param integer                       $users_per_page       Number of users per page
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_users_per_page', $users_per_page, $this->leaderboard_id, $this );

    }

    public function get_columns() {

        $columns = $this->get_post_meta( 'columns' );

        if( ! is_array( $columns ) ) {
            $columns = array();
        }

        /**
         * Filter the leaderboard columns
         *
         * @since 1.0.0
         *
         * @param array                         $columns              An array with columns
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_columns', $columns, $this->leaderboard_id, $this );

    }

    public function get_metrics() {

        $metrics = $this->get_post_meta( 'metrics' );

        if( ! is_array( $metrics ) ) {
            $metrics = array();
        }

        /**
         * Filter the leaderboard metrics to track
         *
         * @since 1.0.0
         *
         * @param array                         $metrics              An array with metrics to track
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_metrics', $metrics, $this->leaderboard_id, $this );

    }

    public function get_period() {

        $period = $this->get_post_meta( 'period' );

        /**
         * Filter the leaderboard period
         *
         * @since 1.1.5
         *
         * @see gamipress_leaderboards_get_time_periods()
         *
         * @param string                        $period               The leaderboard period
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_period', $period, $this->leaderboard_id, $this );

    }

    public function get_date_range() {

        // Setup vars
        $period     = $this->get_period();
        $date_range = array(
            'start' => '',
            'end'   => '',
        );

        if( $period !== '' ) {

            // Note: For custom ranges use 'gamipress_leaderboards_leaderboard_get_date_range' filter

            if( $period === 'custom' ) {
                $date_range = array(
                    'start' => $this->get_period_start_date(),
                    'end' => $this->get_period_end_date(),
                );
            } else {
                $date_range = gamipress_get_period_range( $period );
            }

        }

        /**
         * Filter the leaderboard date range
         *
         * @since 1.1.5
         *
         * @param array                         $date_range           An array with leaderboard date range
         * @param string                        $period               Leaderboard configured period
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard object
         */
        return $date_range = apply_filters( "gamipress_leaderboards_leaderboard_get_date_range", $date_range, $period, $this->leaderboard_id, $this );
    }

    public function get_period_start_date() {

        $period_start_date = $this->get_post_meta( 'period_start_date' );

        if( strtotime( $period_start_date ) ) {
            // Ensure date given is correct
            $period_start_date = date( 'Y-m-d', strtotime( $period_start_date, current_time( 'timestamp' ) ) );
        } else if( absint( $period_start_date ) > 0 ) {
            // Support for timestamp value
            $period_start_date = date( 'Y-m-d', absint( $period_start_date ) );
        }

        /**
         * Filter the leaderboard period start date
         *
         * Just if period is setup as custom
         *
         * @since 1.1.5
         *
         * @see gamipress_leaderboards_get_time_periods()
         *
         * @param string                        $period_start_date    The leaderboard period start date
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_period_start_date', $period_start_date, $this->leaderboard_id, $this );

    }

    public function get_period_end_date() {

        $period_end_date = $this->get_post_meta( 'period_end_date' );

        if( strtotime( $period_end_date ) ) {
            // Ensure date given is correct
            $period_end_date = date( 'Y-m-d', strtotime( $period_end_date, current_time( 'timestamp' ) ) );
        } else if( absint( $period_end_date ) > 0 ) {
            // Support for timestamp value
            $period_end_date = date( 'Y-m-d', absint( $period_end_date ) );
        }

        /**
         * Filter the leaderboard period end date
         *
         * Just if period is setup as custom
         *
         * @since 1.1.5
         *
         * @see gamipress_leaderboards_get_time_periods()
         *
         * @param string                        $period_end_date      The leaderboard period end date
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The leaderboard table object
         */
        return apply_filters( 'gamipress_leaderboards_leaderboard_period_end_date', $period_end_date, $this->leaderboard_id, $this );

    }

    public function display_rows() {

        foreach ( $this->items as $position => $item ) {

            $offset = ( $this->query_vars['page'] - 1 ) * $this->query_vars['items_per_page'];
            $position += $offset;

            $this->single_row( $position, $item );
        }
    }

    public function single_row( $position, $item ) {

        $row_classes = array(
            'position-' . (  $position + 1 ),
            'user-' . $item['user_id'],
            ( is_user_logged_in() && absint( $item['user_id'] ) === get_current_user_id() ? 'is-current-user' : '' ),
        );

        /**
         * Filter the leaderboard row classes
         *
         * @since 1.0.0
         *
         * @param array                         $row_classes            An array with row classes
         * @param integer                       $position               Current row position
         * @param array                         $item                   Item array
         * @param integer                       $leaderboard_id         The Leaderboard ID
         * @param GamiPress_Leaderboard_Table   $leaderboard_table      The leaderboard table object
         */
        $row_classes = apply_filters( 'gamipress_leaderboards_leaderboard_row_classes', $row_classes, $position, $item, $this->leaderboard_id, $this );

        ?>
        <tr class="<?php echo implode( ' ', $row_classes ); ?>">
            <?php $this->single_row_columns( $position, $item ); ?>
        </tr>
        <?php
    }

    public function single_row_columns( $position, $item ) {

        foreach( $this->get_column_info() as $column_name => $column_display_name ) :

            $output = $this->get_column_output( $position, $item, $column_name ); ?>

            <td
                class="column-<?php echo $column_name; ?>"
                data-label="<?php echo esc_attr( $column_display_name ); ?>"
                <?php // If column is a rank type, then we need to order by priority and search by rank name
                if( in_array( $column_name, gamipress_get_rank_types_slugs() ) ) : ?>
                    data-sort="<?php echo esc_attr( $item[$column_name] ); ?>"
                    data-search="<?php echo esc_attr( $output ); ?>"
                <?php endif; ?>
            >

                <?php // Shows the final output
                echo $output; ?>

            </td>

        <?php endforeach;

    }

    public function get_column_output( $position, $item, $column_name ) {

        ob_start();
        if ( method_exists( $this, 'column_' . $column_name ) ) {
            call_user_func_array( array( $this, 'column_' . $column_name ), array( $position, $item ) );
        } else {
            $this->column_default( $position, $item, $column_name );
        }
        $output = ob_get_clean();

        /**
         * Filter the leaderboard column rendering
         *
         * @since 1.0.0
         *
         * @param string                        $output               The column output
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param integer                       $position             The result position
         * @param array                         $item                 The item to be rendered
         * @param string                        $column               The column key
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The Leaderboard table object
         */
        $output = apply_filters( "gamipress_leaderboards_leaderboard_column_$column_name", $output, $this->leaderboard_id, $position, $item, $column_name, $this );

        return $output;

    }

    /**
     * Handles the position column output.
     *
     * @since 1.0.0
     *
     * @param integer   $position   The current item position.
     * @param array     $item       The current item.
     */
    public function column_position( $position, $item ) {

        echo '<strong>' . ( $position + 1 ) . '</strong>';

    }

    /**
     * Handles the avatar column output.
     *
     * @since 1.0.0
     *
     * @param integer   $position   The current item position.
     * @param array     $item       The current item.
     */
    public function column_avatar( $position, $item ) {

        echo get_avatar( $item['user_id'], $this->get_post_meta( 'avatar_size' ) );

    }

    /**
     * Handles the display name column output.
     *
     * @since 1.0.0
     *
     * @param integer   $position   The current item position.
     * @param array     $item       The current item.
     */
    public function column_display_name( $position, $item ) {

        $columns = $this->get_columns();
        $merge_avatar_and_name = (bool) $this->get_post_meta( 'merge_avatar_and_name' );

        if( $merge_avatar_and_name && in_array( 'avatar', $columns ) ) {
            $this->column_avatar( $position, $item );
        }

        echo '<strong>' . get_the_author_meta( 'display_name', $item['user_id'] ) . '</strong>';

    }

    /**
     * Handles the default column output.
     *
     * @since 1.0.0
     *
     * @param integer   $position       The current item position.
     * @param array     $item           The current item.
     * @param string    $column_name    The column name.
     */
    public function column_default( $position, $item, $column_name ) {

        ob_start();
        /**
         * Render leaderboard column hook
         *
         * @since 1.0.0
         *
         * @param integer                       $leaderboard_id       The Leaderboard ID
         * @param integer                       $position             The result position
         * @param array                         $item                 The item to be rendered
         * @param string                        $column               The column key
         * @param GamiPress_Leaderboard_Table   $leaderboard_table    The Leaderboard table object
         */
        do_action( "gamipress_leaderboards_render_leaderboard_column_$column_name", $this->leaderboard_id, $position, $item, $column_name, $this );
        $output = ob_get_clean();

        if( empty( $output ) && isset( $item[$column_name] ) ) {

            if( in_array( $column_name, gamipress_get_rank_types_slugs() ) ) {

                // For ranks, the output is the rank name (post_title)
                $rank = gamipress_get_user_rank( $item['user_id'], $column_name );

                if( $rank ) {
                    $output = $rank->post_title;
                }

            } else  if( in_array( $column_name, gamipress_get_points_types_slugs() ) ) {

                // For points, the output need to be formatted
                if( function_exists('gamipress_format_amount') ) {
                    $output = gamipress_format_amount( $item[$column_name], $column_name );
                }

            } else {

                $output = $item[$column_name];

            }
        }

        echo $output;

    }

    /**
     * Helper function to meet if pagination should be enabled for this leaderboard
     *
     * @since 1.2.6
     *
     * @return bool
     */
    public function is_pagination_enabled() {
        return (bool) ( $this->query_vars['items_per_page'] !== 0 && $this->query_vars['items_per_page'] < $this->query_vars['max_items'] );
    }

    /**
     * Helper function to easily get leaderboard meta field
     *
     * @since 1.0.0
     *
     * @param string   $meta_key        The meta key to retrieve.
     * @param bool     $single          Optional. Whether to return a single value.
     *
     * @return mixed
     */
    public function get_post_meta( $meta_key, $single = true ) {

        if( isset( $this->metas[$meta_key] ) ) {
            return $this->metas[$meta_key];
        }

        $prefix = '_gamipress_leaderboards_';

        $this->metas[$meta_key] = gamipress_get_post_meta( $this->leaderboard_id, $prefix . $meta_key, $single );

        return $this->metas[$meta_key];

    }

}