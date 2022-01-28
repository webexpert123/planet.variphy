<?php

/**
 *
 * This file is used for writing general functions to be used at front and back ends.
 *
 */

/**
 *
 * Function to insert entries from option to hashtag table.
 *
 */
add_action( 'init', 'bpht_insert_table_entry_from_option' );
function bpht_insert_table_entry_from_option()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'bpht_hashtags';
	$buddypress_hashtags = get_option( 'bpht_hashtags');

	/* for buddypress hashtags */
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		if( is_array( $buddypress_hashtags ) && !empty( $buddypress_hashtags ) ) {
			foreach ($buddypress_hashtags as $hashtag => $hashtag_data) {

				$check = $wpdb->get_results("SELECT * FROM $table_name WHERE ht_name IN ('$hashtag') AND ht_type IN ('buddypress')");
				if( !$check ) {
					$wpdb->insert( 
						$table_name, 
						array( 
							'ht_name' => $hashtag,
							'ht_type' => 'buddypress',
							'ht_count' => $hashtag_data['count'],
							'ht_last_count' => current_time( 'mysql' ),
						) 
					);
				}
			}
		}
	}

	/* for bbpress hashtags */
	$bbpress_hashtags = get_option( 'bpht_bbpress_hashtags');
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		if( is_array( $bbpress_hashtags ) && !empty( $bbpress_hashtags ) ) {
			foreach ($bbpress_hashtags as $hashtag => $hashtag_data) {

				$check = $wpdb->get_results("SELECT * FROM $table_name WHERE ht_name IN ('$hashtag') AND ht_type IN ('bbpress')");
				if( !$check ) {
					$wpdb->insert( 
						$table_name, 
						array( 
							'ht_name' => $hashtag,
							'ht_type' => 'bbpress',
							'ht_count' => $hashtag_data['count'],
							'ht_last_count' => current_time( 'mysql' ),
						) 
					);
				}
			}
		}
	}
}


function bpht_db_buddypress_hashtag_entry( $ht_name, $ht_type ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bpht_hashtags';

	/* for buddypress hashtags */
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		$check = $wpdb->get_results("SELECT * FROM $table_name WHERE ht_name IN ('$ht_name') AND ht_type IN ('$ht_type') ");
		if( !$check ) {
			$wpdb->insert( 
				$table_name, 
				array( 
					'ht_name' => $ht_name,
					'ht_type' => $ht_type,
					'ht_count' => 1,
					'ht_last_count' => current_time( 'mysql' ),
				) 
			);
		}else{
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET ht_count = ht_count + 1, ht_last_count = '%s' WHERE ht_name IN ('%s') AND ht_type IN ('%s')", current_time( 'mysql' ), $ht_name, $ht_type) );
		}
	}
}

function bpht_alpha_numeric_hashtags_enabled() {
	if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
		$bpht_general_settings = get_site_option( 'bpht_general_settings' );
	} else {
		$bpht_general_settings = get_option( 'bpht_general_settings' );
	}

	$allow_non_an_ht = ( isset($bpht_general_settings['allow_non_an_ht']) )?$bpht_general_settings['allow_non_an_ht']:false;

	return apply_filters( 'bpht_alpha_numeric_hashtags_enabled', $allow_non_an_ht );

}
