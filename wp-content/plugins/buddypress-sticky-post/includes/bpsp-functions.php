<?php

/**
 * Add New Activity Tool.
 */
function bpsp_get_activity_action( $action, $activity_id ) {
	
	if ( bpsp_is_post_pinned( $activity_id ) ) {
		// Get Unpin Button Data.
		$action = 'unpin';
		$class = 'bpsp-unpin-post';
		$title = bpsp_get_unpin_post_label();
		$icon  = 'fa fa-thumb-tack fa-thumbtack fa-flip-vertical';
	} else {
		// Get Pin Button Data.
		$action = 'pin';
		$icon  = 'fa fa-thumb-tack fa-thumbtack';
		$class = 'bpsp-pin-post';
		$title = bpsp_get_pin_post_label();
	}

	// Get Tool Data.
	$tools[] = array(
		'icon' => $icon,
		'title' => $title,
		'action' => $action,
		'class' => array( 'bpsp-pin-tool', $class ),
	);

	return $tools;
}

add_filter( 'bpsp_activity_action', 'bpsp_get_activity_action', 10, 2 );

/**
 * Check if Activity is Pinned
 */
function bpsp_is_post_pinned( $activity_id = null ) {

	// Check if Sticky Activities Are Enabled.
	// if ( ! yz_is_sticky_posts_active() ) {
	// 	return false;
	// }

	// Get Sticky Activities.
	$sticky_activities = bpsp_get_sticky_posts();

	if ( empty( $activity_id ) || empty( $sticky_activities ) ) {
		return false;
	}

	if ( ! in_array( $activity_id, $sticky_activities ) ) {
		return false;
	}

	return true;

}

/**
 * Get Sticky Posts.
 */
function bpsp_get_sticky_posts( $component = null, $group_id = null ) {

	// if ( ! yz_is_sticky_posts_active() ) {
	// 	return false;
	// }

	// Get Component.
	$component = bp_is_groups_component() ? 'groups' : 'activity';

	// Get Group ID.
	if ( bp_is_active( 'groups' ) ) {
		$group_id = ! empty( $group_id ) ? $group_id : bp_get_current_group_id();
	}

	// Get Sticky Posts ID's
	$posts_ids = bpsp_options( 'bpsp_' . $component . '_sticky_posts' );

	// Filter Sticky Posts ID's
	$posts_ids = apply_filters( 'bpsp_get_sticky_posts', $posts_ids, $component, $group_id );

	// Get Group Sticky Posts ID's.
	if ( 'groups' == $component ) {
		$posts_ids = isset( $posts_ids[ $group_id ] ) ? $posts_ids[ $group_id ] : array();
	}

	// Remove Duplicated Values.
	$posts_ids = is_array( $posts_ids ) ? array_unique( $posts_ids ) : $posts_ids;

	return $posts_ids;

}

/**
 * Youzer Options
 */
function bpsp_options( $option_id ) {

    // Get Option Value.
    $option_value = get_option( $option_id );

    // Filter Option Value.
    $option_value = apply_filters( 'bpsp_edit_options', $option_value, $option_id );

    return $option_value;
}

/**
 * # Class Generator.
 */
function bpsp_generate_class( $classes ) {
    // Convert Array to String.
    return implode( ' ' , array_filter( (array) $classes ) );
}

/**
 * Get Attributes
 */
function bpsp_get_item_attributes( $attributes = null ) {

	if ( empty( $attributes ) ) {
		return;
	}

	foreach ( $attributes as $attribute => $value ) {
		echo 'data-' . $attribute . '="' . $value . '"';
	}

}

/**
 * Add Sticky Posts
 */
function bpsp_add_sticky_post( $component, $post_id, $group_id = null ) {

		// Get All Sticky Posts.
	$sticky_posts = bpsp_options( 'bpsp_' . $component . '_sticky_posts' );

		// Add the new pinned post.
	if ( 'groups' == $component ) {
		$sticky_posts[ $group_id ][] = $post_id;
	} elseif ( 'activity' == $component ) {
		$sticky_posts[] = $post_id;
	}

		// Update Sticky Posts.
	update_option( 'bpsp_' . $component . '_sticky_posts', $sticky_posts );
}

/**
 * Delete Sticky Activities
 */
function bpsp_delete_sticky_post( $component, $post_id, $group_id = null ) {

	// Get All Sticky Posts.
	$sticky_posts = bpsp_options( 'bpsp_' . $component . '_sticky_posts' );

	if ( 'groups' == $component ) {

		// Get Removed Post Key.
		$post_key = array_search( $post_id, $sticky_posts[ $group_id ] );

		// Remove Post.
		if ( isset( $sticky_posts[ $group_id ][ $post_key ] ) ) {
			unset( $sticky_posts[ $group_id ][ $post_key ] ); 
		}

	} elseif ( 'activity' == $component ) {

		// Get Removed Post Key.
		$post_key = array_search( $post_id, $sticky_posts );

		// Remove Post.
		if ( isset( $sticky_posts[ $post_key ] ) ) {
			unset( $sticky_posts[ $post_key ] ); 
		}

	}

	// Update Sticky Posts.
	update_option( 'bpsp_' . $component . '_sticky_posts', $sticky_posts );
}

function bpsp_user_can_pin_posts() {

	if ( ( is_user_logged_in() && current_user_can('administrator') ) || ( bp_is_group() &&  groups_is_user_admin( get_current_user_id(), bp_get_current_group_id() ) ) ) {
		return true;
	}else{
		return false;
	}
	
}

/**
 * Get Sticky Posts ID's ( String )
 */
function bpsp_get_sticky_posts_ids( $component = null, $group_id = null ) {

	// Get Stikcy Posts Array
	$sticky_posts = bpsp_get_sticky_posts( $component, $group_id );

	// Convert Ids into a list seprarated with comas
	$posts_ids = implode( ',', (array) $sticky_posts );

	return $posts_ids;

}

function bpsp_get_pin_post_label() {
	$bpsp_general_settings = get_option( 'bpsp_general_settings' );
	$pin_post_label = ( isset( $bpsp_general_settings['pin_post_lbl'] ) && !empty( $bpsp_general_settings['pin_post_lbl'] ) )?$bpsp_general_settings['pin_post_lbl']:'Pin Post';

	return apply_filters( 'alter_bpsp_get_pin_post_label', $pin_post_label );
}

function bpsp_get_unpin_post_label() {
	$bpsp_general_settings = get_option( 'bpsp_general_settings' );
	$unpin_post_label = ( isset( $bpsp_general_settings['unpin_post_lbl'] ) && !empty( $bpsp_general_settings['unpin_post_lbl'] ) )?$bpsp_general_settings['unpin_post_lbl']:'Unpin Post';

	return apply_filters( 'alter_bpsp_get_unpin_post_label', $unpin_post_label );
}

function moveElement($array, $a, $b) {
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
    return $array;
}



function find_activity_with_position($activities, $id) {
    foreach($activities as $index => $act) {
        if($act->id == $id){
        	return $index;
        } 
    }
    return FALSE;
}