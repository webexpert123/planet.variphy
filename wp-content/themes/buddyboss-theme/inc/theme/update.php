<?php
/**
 * Theme Update Hooks.
 *
 * @package BuddyBoss_Theme
 */

// Clear transient after theme update.
if ( ! function_exists( 'buddyboss_theme_update' ) ) {

	/**
	 * Function is called when theme is updated.
	 *
	 * @since 1.7.3
	 */
	function buddyboss_theme_update() {
		$current_version = wp_get_theme()->get( 'Version' );
		$old_version     = get_option( 'buddyboss_theme_version', '1.7.2' );

		if ( $old_version !== $current_version ) {

			// Call clear learndash group users transient.
			if ( version_compare( $current_version, '1.7.2', '>' ) && function_exists( 'bb_theme_update_1_7_3' ) ) {
				bb_theme_update_1_7_3();
			}

			// update not to run twice.
			update_option( 'buddyboss_theme_version', $current_version );
		}
	}

	add_action( 'after_setup_theme', 'buddyboss_theme_update' );
}

/**
 * Clear the learndash course enrolled user count transient.
 *
 * @since 1.7.3
 */
function bb_theme_update_1_7_3() {
	global $wpdb;
	$sql       = 'select option_name from ' . $wpdb->options . ' where option_name like "%_transient_buddyboss_theme_ld_course_enrolled_users_count_%"';
	$all_cache = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

	if ( ! empty( $all_cache ) ) {
		foreach ( $all_cache as $cache_name ) {
			$cache_name = str_replace( '_site_transient_', '', $cache_name );
			$cache_name = str_replace( '_transient_', '', $cache_name );
			delete_transient( $cache_name );
			delete_site_transient( $cache_name );
		}
	}
}
