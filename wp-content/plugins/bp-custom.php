<?php
add_filter( 'bbp_is_site_public', '__return_true' );
add_post_type_support( 'topic', 'buddypress-activity' );
add_filter( 'bbp_is_site_public', 'yourownprefix_enable_bbp_activity', 10, 2);

function yourownprefix_enable_bbp_activity( $public, $site_id ) {
	return true;
}
function redirect_group_home() {
  global $bp;
  $path = clean_url( $_SERVER['REQUEST_URI'] );
  $path = apply_filters( 'bp_uri', $path );
  if (bp_is_group_home() && strpos( $path, $bp->bp_options_nav[$bp->groups->current_group->slug]['home']['slug'] ) === false ) {
    if ($bp->groups->current_group->is_user_member || $bp->groups->current_group->status == 'public') {
      bp_core_redirect( $path . 'forum/' );
    }
  }
}
function move_group_activity_tab() {
  global $bp;
  if (isset($bp->groups->current_group->slug) && $bp->groups->current_group->slug == $bp->current_item) {
  	unset($bp->bp_options_nav[$bp->groups->current_group->slug]['home']);
  }
}
add_action('bp_init', 'redirect_group_home' );
add_action('bp_init', 'move_group_activity_tab');
?>