<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 */
if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
?>

<div class="wbcom-tab-content">
    <div class="wbcom-welcome-main-wrapper">
        <div class="wbcom-welcome-head">
            <h2 class="wbcom-welcome-title"><?php esc_html_e( 'BuddyPress Polls', 'buddypress-polls' ); ?></h2>
            <p class="wbcom-welcome-description"><?php esc_html_e( 'Use BuddyPress Polls plugin to create polls inside the activity, let your user response to your polls. Members can create polls inside activities, easily votes on them.', 'buddypress-polls' ) ?></p>
        </div><!-- .wbcom-welcome-head -->

        <div class="wbcom-welcome-content">

            <div class="wbcom-video-link-wrapper">
              <iframe src="https://player.vimeo.com/video/562748687" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/562748687">BuddyPress Polls for Member Activities</a> from <a href="https://vimeo.com/wbcom">Wbcom Designs</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
            </div>

            <div class="wbcom-welcome-support-info">
                <h3><?php esc_html_e( 'Help &amp; Support Resources', 'buddypress-polls' ); ?></h3>
                <p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-polls' ); ?></p>
                <hr>

                <div class="three-col">

                    <div class="col">
                        <h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'buddypress-polls' ); ?></h3>
                        <p><?php esc_html_e( 'We have prepared an extensive guide on BuddyPress Polls to learn all aspects of the plugin. You will find most of your answers here.', 'buddypress-polls' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/docs/buddypress-paid-addons/buddypress-polls/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'buddypress-polls' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'buddypress-polls' ); ?></h3>
                        <p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'buddypress-polls' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'buddypress-polls' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'buddypress-polls' ); ?></h3>
                        <p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'buddypress-polls' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'buddypress-polls' ); ?></a>
                    </div>

                </div>

            </div>
        </div>

    </div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->
