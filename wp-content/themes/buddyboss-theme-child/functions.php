<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
include_once(get_stylesheet_directory().'/class.user.php');
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_theme_child_languages' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  // Styles
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css', '', '1.0.0' );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js', '', '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here
//require_once('filter-functions.php');
    // Load filter functions    
 //require_once( get_theme_file_path( '/filter-functions.php' ) ); 


/*add_filter( 'searchwp_posts_per_page', 'cstm_searchwp_posts_per_page', 30, 4 );
function cstm_searchwp_posts_per_page( $posts_per_page, $engine, $terms, $page ){
  return -1;
}


add_filter( 'rest_post_collection_params', 'big_json_change_post_per_page', 10, 1 );
function big_json_change_post_per_page( $params ) {
    if ( isset( $params['per_page'] ) ) {
        $params['per_page']['maximum'] = 200;
    }
    return $params;
}*/


require_once('inc/bb-customization.php');


add_filter( 'lostpassword_url',  'change_lostpassword_url', 10, 0 );
function change_lostpassword_url() {
    return 'https://www.variphy.com/support/forgot-password';
}





function wpd_custom_user_login( $user_login ) {
   
    $digits =  rand(pow(10, 5-1), pow(10, 5)-1);
    $user_login = 'Explorer-'.$digits;

    if(username_exists( $user_login  )){
        $user_login =   'Explorer-'.rand(pow(10, 5-1), pow(10, 5)-1);
    }


    return $user_login;
}
add_filter( 'pre_user_login' , 'wpd_custom_user_login' );


add_action( 'user_profile_update_errors', 'wpse5742_set_user_nicename_to_nickname', 10, 3 );
function wpse5742_set_user_nicename_to_nickname( &$errors, $update, &$user )
{

    // Return if not update
    if ( !$update ) return;


    if ( empty( $_POST['nickname'] ) ) {
        $errors->add(
            'empty_nicename',
            sprintf(
                '<strong>%1$s</strong>: %2$s',
                esc_html__( 'Error' ),
                esc_html__( 'Please enter a Nicename.' )
            ),
            array( 'form-field' => 'user_nicename' )
        );
    } else {
        // Set the nicename
         global  $wpdb;
        $wpdb->update($wpdb->users, array('user_nicename' => $user->user_nicename), array('ID' => $user->ID));

        if($_POST['nickname'] == $user->nickname ){
               $user->user_nicename = $user->nickname;
             }else{
               $user->user_nicename = $_POST['nickname'];
        }


    }

}





function register_my_session()
{
  if( !session_id() )
  {
    session_start();
  }
}

add_action('init', 'register_my_session');

function bdpwr_get_user( $user_id = false ) {
  return new BDPWR_User( $user_id );
}
add_action( 'rest_api_init', function () {  
  $route_namespace = apply_filters( 'bdpwr_route_namespace' , 'bdpwr/v1' );
  
  register_rest_route( $route_namespace , '/set-password' , array(

    'methods' => 'POST',

    'callback' => function( $data ) {

      if ( empty( $data['email'] ) || $data['email'] === '' ) {
        return new WP_Error( 'no_email' , __( 'You must provide an email address.' , 'bdvs-password-reset' ) , array( 'status' => 400 ));
      }

      

      if( empty( $data['password'] ) || $data['password'] === '' ) {
        return new WP_Error( 'no_code' , __( 'You must provide a new password.' , 'bdvs-password-reset' ) , array( 'status' => 400 ) );
      }

      $exists = email_exists( $data['email'] );

      if( ! $exists ) {
        return new WP_Error( 'bad_email' , __( 'No user found with this email address.' , 'bdvs-password-reset' ) , array( 'status' => 500 ));
      }
      
      try {
        $user = bdpwr_get_user( $exists );
        $user->set_new_password( $data['code'] , $data['password'] );
      }
      
      catch( Exception $e ) {
        return new WP_Error( 'bad_request' , $e->getMessage() , array( 'status' => 500 ));
      }

      return array(
        'data' => array(
          'status' => 200,
        ),
        'message' => __( 'Password reset successfully.' , 'bdvs-password-reset' ),
      );

    },

    'permission_callback' => function() {
      return true;
    },

  ));  
});



