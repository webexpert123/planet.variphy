<?php
namespace Restore_Classic_More {
   if (!defined('ABSPATH'))
      exit; // Exit if accessed directly
   if (is_multisite())
      return;
   define(__NAMESPACE__ . '\PRODCLASS', "restore-classic-more");
   define(__NAMESPACE__ . '\URL', RESTORECLASSICURL);
   define(__NAMESPACE__ . '\VERSION', RESTORECLASSICVERSION);
   class Bill_Config
   {
      protected static $namespace = __NAMESPACE__;
      protected static $bill_class = PRODCLASS;
      function __construct()
      {
         add_action('load-plugins.php', array(__CLASS__, 'init'));
         add_action('wp_ajax_install_wptools',  array(__CLASS__, 'installwptools'));
      }
      public static function init()
      {
         add_action('in_admin_footer', array(__CLASS__, 'message'));
         add_action('admin_head',      array(__CLASS__, 'register'));
         add_action('admin_footer',    array(__CLASS__, 'enqueue'));
      }
      public static function register()
      {
         wp_enqueue_style(PRODCLASS, URL . 'includes/more/more.css');
         wp_register_script(PRODCLASS, URL . 'includes/more/more.js', array('jquery'), VERSION, true);
      }
      public static function enqueue()
      {
         wp_enqueue_style(PRODCLASS);
         wp_enqueue_script(PRODCLASS);
      }
      public static function message()
      {
?>
         <div class="<?php echo PRODCLASS; ?>-wrap-deactivate" style="display:none">
            <div class="bill-vote-gravatar"><a href="http://profiles.wordpress.org/sminozzi" target="_blank"><img src="https://en.gravatar.com/userimage/94727241/31b8438335a13018a1f52661de469b60.jpg?size=100" alt="Bill Minozzi" width="70" height="70"></a></div>
            <div class="bill-vote-message">
               <h3><?php _e("WP TOOLS FREE PLUGIN", 'restoreclassic'); ?></h3>
               <big>
                  <h4><?php _e("Amazing Plugin from Same Author, Bill Minozzi, with more than 35 useful tools!", 'restoreclassic'); ?></h4>
                  <?php _e("WP Tools FREE Plugin is a swiss army knife, ", 'restoreclassic');
                  echo 'to take your site to the next level.';
                  echo '<br>';
                  echo '<br>';
                  echo 'Just Click Install (one click install) to get it from WordPress repository.';
                  echo '</big>';
                  ?>
                  <br />
                  <br /><br />
                  <a href="#" class="button button-primary <?php echo PRODCLASS; ?>-close-submit"><?php _e("Install", 'restoreclassic'); ?></a>
                  <img src="/wp-admin/images/wpspin_light-2x.gif" id="rcwimagewaitfbl" style="display:none;margin-right:20px;margin-top:5px;" />
                  <a href="https://wordpress.org/plugins/wptools/" class="button <?php echo PRODCLASS; ?>-close-dialog"><?php _e("More Details", 'restoreclassic'); ?></a>
                  <a href="#" class="button <?php echo PRODCLASS; ?>-close-dialog"><?php _e("Close", 'restoreclassic'); ?></a>
                  <br /><br />
            </div>
         </div>
<?php
      }
   }
   new Bill_Config;
} // End Namespace ...
?>