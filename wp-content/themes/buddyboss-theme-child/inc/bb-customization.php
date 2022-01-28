<?php
add_filter( 'bp_activity_get_visibility_levels', 'cstm_bp_activity_get_visibility_levels',99 );
function cstm_bp_activity_get_visibility_levels($visibility_levels){
	if(isset($visibility_levels['friends'])){
		unset($visibility_levels['friends']);
	}
	if(isset($visibility_levels['onlyme'])){
		unset($visibility_levels['onlyme']);
	}
	return $visibility_levels;

}

add_shortcode('bb-group-suggestion','cstm_get_invited_groups_list');
function cstm_get_invited_groups_list(){
ob_start();

/*
if(is_user_logged_in()){ ?>
	<div class="create-group">
		<a href="<?php echo site_url();?>/groups/create/step/group-details/">Create Group</a> <br> <br>
	</div>
<?php 
}
*/
//  if ( bp_has_groups( 'type=invites&user_id=' . bp_loggedin_user_id() ) ) : ?>


  <style type="text/css">
    .bb_group_suggestion{
      display: none !important;
    }
  </style>
<?php 
global $wpdb;

$row = $wpdb->get_results( "SELECT * FROM wp_bp_groups  ");
  $i = 1;
   
   foreach ( $row as $row ) {
   $user_id = get_current_user_id();
   $group_id =  $row->id ; 

    if(  $i > 5 ||  groups_is_user_member( $user_id, $group_id )  ){
       continue;
    }
$group_link      = site_url().'/groups/'.$row->slug.'/';
$admin_link      = trailingslashit( $group_link . 'admin' );
 $group_avatar   = trailingslashit( $admin_link . 'group-avatar' );
 $avatarUrl =  bp_core_fetch_avatar(
    array(
      'type'    => 'full',
      'object'  => 'group',
      'item_id' => $group_id,
      'html'    => false,
    )
  );
?>

<?php
$titleShow = false;
 if($i == 1 && $group_id > 0 ){ 
  $titleShow = true;
  ?>
 <style type="text/css">
    .bb_group_suggestion{
      display: block !important;
    }
  </style>
  <?php //echo $user_id.'test';?>

    <h2 class="widget-title">   Suggested Groups </h2>
    <ul id="groups-list" class="invites item-list bp-list item-list groups-list" data-bp-list="groups_invites">
<?php } ?>

  <li   <?php echo $i; ?>     <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?=$group_id; ?>" data-bp-item-component="groups">
          <div class="list-wrap">
            <?php if( !bp_disable_group_cover_image_uploads($group_id ) ) : ?>
              <div class="item-avatar">
                <a href="<?=site_url().'/groups/'.$row->slug.'/'?>"><img src="<?=$avatarUrl;?>" class="avatar group-51-avatar avatar-300 photo" width="300" height="300" alt="<?= $row->name; ?>"></a> 
              </div>
            <?php endif; ?>
            <div class="item">
              <div class="item-block">
                <h2 class="list-title groups-title"><a href="<?=site_url().'/groups/'.$row->slug.'/'?>" ><?= $row->name; ?></a></h2>
               
                <!-- <p class="desc item-meta invite-message">
                  <?php // echo bp_groups_get_invite_messsage_for_user( bp_displayed_user_id(), bp_get_group_id() ); ?>
                </p> -->
              </div>
            </div>

          </div>

        </li>
  <?php

      $i++;
  }
  ?>
  <?php if($titleShow){ ?>
 </ul>
  <?php } ?>

	<?php // endif; ?>
	<?php 

	return ob_get_clean();
}



//remove_filter('authenticate', 'wp_authenticate_username_password', 20);
add_filter('authenticate', function($user, $email, $password){

    //Check for empty fields
    if(empty($email) || empty ($password)){
        //create new error object and add errors to it.
        $error = new WP_Error();

        if(empty($email)){ //No email
            $error->add('empty_username', __('<strong>ERROR</strong>: Email field is empty.'));
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //Invalid Email
            $error->add('invalid_username', __('<strong>ERROR</strong>: Email is invalid.'));
        }

        if(empty($password)){ //No password
            $error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
        }

        return $error;
    }

    //Check if user exists in WordPress database
    $user = get_user_by('email', $email);

    //bad email
    if(!$user){
        $error = new WP_Error();
        $error->add('invalid', __('<strong>ERROR</strong>: Either the email or password you entered is invalid.'));
        return $error;
    }
    else{ //check password
        if(!wp_check_password($password, $user->user_pass, $user->ID)){ //bad password
            $error = new WP_Error();
            $error->add('invalid', __('<strong>ERROR</strong>: Either the email or password you entered is invalid.'));
            return $error;
        }else{
            return $user; //passed
        }
    }
}, 20, 3);

add_filter('gettext', function($text){
    if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
        if('Username' == $text){
            return 'Email';
        }
    }
    return $text;
}, 20);


// Remove Lost Password Link
//add_filter ( 'allow_password_reset', 'disable_password_reset' );
function disable_password_reset() { 
    return false;
}
//==== Update Lost Url ========//
add_filter( 'lostpassword_url',  'wdm_lostpassword_url', 10, 0 );
function wdm_lostpassword_url() {
    return 'https://variphy.com/support/my-account';
}

add_filter( 'avatar_defaults', 'custom_avatar_defaults_url',20,3 );
function custom_avatar_defaults_url ($avatar_defaults) {
	$myavatar = 'http://example.com/wp-content/uploavatar/2017/01/wpb-default-gravatar.png';
	$avatar_defaults[$myavatar] = "Default Gravatar";
	return $avatar_defaults;
}

// default avtar 
/*add_filter( 'bp_get_signup_avatar_mystery', 'default_avtar_image_url',50,3);
function default_avtar_image_url($url){
	$url = get_site_url().'/wp-content/uploavatar/rtMedia/groups/53/2021/03/uccx-avatar.png';
	return $url;
}*/

//define ( 'BP_AVATAR_DEFAULT', 'https://ghaslate.com/variphy/wp-content/uploavatar/rtMedia/groups/53/2021/03/uccx-avatar.png' );


add_action( 'admin_init', 'avatar_register_setting' );
function avatar_register_setting(){
    add_settings_section(
        'avatar_option_id',
        'Extra Settings',
        'avatar_description',
        'discussion'
    );

    // Register a callback
    register_setting(
        'discussion',
        'avatar_option_url',
        'trim'
    );
    // Register the field for the "avatars" section.
    add_settings_field(
        'avatar_option_url',
        'Default Avatar Url',
        'avatar_show_settings',
        'discussion',
        'avatar_option_id',
        array ( 'label_for' => 'avatar_option_id' )
    );
}

/**
 * Print the text before our field.
 */
function avatar_description(){
    // nothing
}

/**
 * Show our field.
 *
 * @param array $args
 */
function avatar_show_settings( $args )
{
    $get_avatar_option_url = get_option( 'avatar_option_url');

    //echo $get_avatar_option_url;
    
    
    printf(
        '<p><input class="large-text code" type="url" name="avatar_option_url" value="%1$s" id="%2$s"/></p>',
        $get_avatar_option_url,
        $args['label_for']
    );
}

$get_avatar_option_url = get_option( 'avatar_option_url');
define ( 'BP_AVATAR_DEFAULT', $get_avatar_option_url );


/**
 * Set default activity directory tab for BuddyPress
 */
function buddydev_set_default_activity_directory_tab() {

  $tab = 'forum'; // 'all', 'friends', 'groups', 'mentions'
  // Set scope to our needed tab,
  // In this case, I am setting to the 'friends' tab
  setcookie( 'bp-activity-scope', $tab,null, '/' );
  $_COOKIE['bp-activity-scope'] = $tab;
}
add_action( 'bp_template_redirect', 'buddydev_set_default_activity_directory_tab' );




function bb_user_status( $user_id ) {


    if ( bb_is_user_online( $user_id ) ) {
      echo '<span class="member-status online"></span>';
    }else{

      echo '<span class="member-status offline"></span>';
    }
  }



add_filter( 'bp_core_get_js_strings', 'new_bp_nouveau_activity_localize_scripts', 11, 1 );

function new_bp_nouveau_activity_localize_scripts( $params = array() ) {
  if ( ! bp_is_activity_component() && ! bp_is_group_activity() && ! bp_is_media_component() && ! bp_is_document_component() && ! bp_is_media_directory() && ! bp_is_document_directory() && ! bp_is_group_media() && ! bp_is_group_document() && ! bp_is_group_albums() && ! bp_is_group_folders() && ( ! isset( $_REQUEST ) && ! isset( $_REQUEST['bp_search'] ) ) ) {
    // media popup overlay needs activity scripts.
    return $params;
  }

  $activity_params = array(
    'user_id'          => bp_loggedin_user_id(),
    'object'           => 'user',
    'backcompat'       => (bool) has_action( 'bp_activity_post_form_options' ),
    'post_nonce'       => wp_create_nonce( 'post_update', '_wpnonce_post_update' ),
    'excluded_hosts'   => array(),
    'user_can_post'    => ( is_user_logged_in() && bb_user_can_create_activity() ),
    'is_activity_edit' => bp_is_activity_edit() ? (int) bp_current_action() : false,
    'errors'           => array(
      'empty_post_update' => __( 'Sorry, Your update cannot be empty.', 'buddyboss' )
    ),
  );

  $user_displayname = bp_get_loggedin_user_fullname();

  if ( buddypress()->avatar->show_avatars ) {
    $width  = bp_core_avatar_thumb_width();
    $height = bp_core_avatar_thumb_height();
    $activity_params = array_merge( $activity_params, array(
      'avatar_url'        => bp_get_loggedin_user_avatar( array(
        'width'  => $width,
        'height' => $height,
        'html'   => false,
      ) ),
      'avatar_width'      => $width,
      'avatar_height'     => $height,
      'user_display_name' => bp_core_get_user_displayname( bp_loggedin_user_id() ),
      'user_domain'       => bp_loggedin_user_domain(),
      'avatar_alt'        => sprintf(
      /* translators: %s = member name */
        __( 'Profile photo of %s', 'buddyboss' ),
        $user_displayname
      ),
    ) );
  }

  if ( bp_is_activity_autoload_active() ) {
    $activity_params['autoload'] = true;
  }

  if ( bp_is_activity_link_preview_active() ) {
    $activity_params['link_preview'] = true;
  }

  /**
   * Filters the included, specific, Action buttons.
   *
   * @since BuddyPress 3.0.0
   *
   * @param array $value The array containing the button params. Must look like:
   * array( 'buttonid' => array(
   *  'id'      => 'buttonid',                            // Id for your action
   *  'caption' => __( 'Button caption', 'text-domain' ),
   *  'icon'    => 'dashicons-*',                         // The dashicon to use
   *  'order'   => 0,
   *  'handle'  => 'button-script-handle',                // The handle of the registered script to enqueue
   * );
   */
  $activity_buttons = apply_filters( 'bp_nouveau_activity_buttons', array() );

  if ( ! empty( $activity_buttons ) ) {
    $activity_params['buttons'] = bp_sort_by_key( $activity_buttons, 'order', 'num' );

    // Enqueue Buttons scripts and styles
    foreach ( $activity_params['buttons'] as $key_button => $buttons ) {
      if ( empty( $buttons['handle'] ) ) {
        continue;
      }

      if ( wp_style_is( $buttons['handle'], 'registered' ) ) {
        wp_enqueue_style( $buttons['handle'] );
      }

      if ( wp_script_is( $buttons['handle'], 'registered' ) ) {
        wp_enqueue_script( $buttons['handle'] );
      }

      unset( $activity_params['buttons'][ $key_button ]['handle'] );
    }
  }

  // Activity Objects
  if ( ! bp_is_single_item() && ! bp_is_user() ) {
    $activity_objects = array(
      'profile' => array(
        'text'                     => __( 'Post in: Profile', 'buddyboss' ),
        'autocomplete_placeholder' => '',
        'priority'                 => 5,
      ),
    );

    // the groups component is active & the current user is at least a member of 1 group

    /*
    if ( bp_is_active( 'groups' ) && bp_has_groups( array( 'user_id' => bp_loggedin_user_id(), 'max' => 1 ) ) ) {
      $activity_objects['group'] = array(
        'text'                     => __( 'Post in: Group', 'buddyboss' ),
        'autocomplete_placeholder' => __( 'Start typing the group name...', 'buddyboss' ),
        'priority'                 => 10,
      );
    }
    */


    /**
     * Filters the activity objects to apply for localized javascript data.
     *
     * @since BuddyPress 3.0.0
     *
     * @param array $activity_objects Array of activity objects.
     */
    $activity_params['objects'] = apply_filters( 'bp_nouveau_activity_objects', $activity_objects );
  }

  $activity_strings = array(
    'whatsnewPlaceholder' => sprintf( __( "Start a post...", 'buddyboss' ), bp_get_user_firstname( $user_displayname ) ),
    'whatsnewLabel'       => __( 'Post what\'s new', 'buddyboss' ),
    'whatsnewpostinLabel' => __( 'Post in', 'buddyboss' ),
    'postUpdateButton'    => __( 'Post Update', 'buddyboss' ),
    'updatePostButton'    => __( 'Update Post', 'buddyboss' ),
    'cancelButton'        => __( 'Cancel', 'buddyboss' ),
    'commentLabel'        => __( '%d Comment', 'buddyboss' ),
    'commentsLabel'       => __( '%d Comments', 'buddyboss' ),
    'loadingMore'         => __( 'Loading...', 'buddyboss' ),
  );

    if ( bp_get_displayed_user() && ! bp_is_my_profile() ) {
        $activity_strings['whatsnewPlaceholder'] = sprintf( __( "Write something to %s...", 'buddyboss' ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) );
    }

  if ( bp_is_group() ) {
    $activity_strings['whatsnewPlaceholder'] = __( 'Share something with your group...', 'buddyboss' );
    $activity_params = array_merge(
      $activity_params,
      array(
        'object'  => 'group',
        'item_id' => bp_get_current_group_id(),
      )
    );
  }

  $activity_params['access_control_settings'] = array(
    'can_create_activity'          => bb_user_can_create_activity(),
    'can_create_activity_media'    => bb_user_can_create_media(),
    'can_create_activity_document' => bb_user_can_create_document(),
  );

  $params['activity'] = array(
    'params'  => $activity_params,
    'strings' => $activity_strings,
  );

  return $params;
}




$currntUrl =  home_url($_SERVER['REQUEST_URI']);
$currntUrlData = explode('/', $currntUrl);
$count =  count($currntUrlData);

if($count === 6 && $currntUrlData[3]=='groups'  ){
wp_redirect(get_permalink($post->ID).'forum/');
exit;
}


// disable buddy boss plateform update

add_filter('use_block_editor_for_post', '__return_false', 10);


function disable_plugin_updates( $value ) {
  
        $pluginsToDisable = [
        
        'buddyboss-platform/bp-loader.php'
        
        ];
       
        if ( isset($value) && is_object($value) ) {
            
            foreach ($pluginsToDisable as $plugin) {
                
                if ( isset( $value->response[$plugin] ) ) {
                    
                    unset( $value->response[$plugin] );
                    
                }
                
            }
            
        }
        
        return $value;
        
  }
    
  add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );