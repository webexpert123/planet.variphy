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
            <h2 class="wbcom-welcome-title"><?php esc_html_e( 'BuddyPress Sticky Post', 'buddypress-sticky-post' ); ?></h2>
            <p class="wbcom-welcome-description"><?php esc_html_e( 'BuddyPress Sticky Post plugin comes with the feature that lets site administrators prioritize certain activities over others. As its name suggests, BuddyPress Sticky Post lets admin pin-up site-wide and groups activities to the top of BuddyPress activity list.', 'buddypress-sticky-post' ) ?></p>
        </div><!-- .wbcom-welcome-head -->

        <div class="wbcom-welcome-content">

            <div class="wbcom-video-link-wrapper">
              <iframe src="https://player.vimeo.com/video/555176360" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/555176360">BuddyPress Sticky Activity - Pin Activity</a> from <a href="https://vimeo.com/wbcom">Wbcom Designs</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
            </div>

            <div class="wbcom-welcome-support-info">
                <h3><?php esc_html_e( 'Help &amp; Support Resources', 'buddypress-sticky-post' ); ?></h3>
                <p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-sticky-post' ); ?></p>
                <hr>

                <div class="three-col">

                    <div class="col">
                        <h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'buddypress-sticky-post' ); ?></h3>
                        <p><?php esc_html_e( 'We have prepared an extensive guide on BuddyPress Sticky Post to learn all aspects of the plugin. You will find most of your answers here.', 'buddypress-sticky-post' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/docs/buddypress-sticky-post/introduction-buddypress-sticky-post/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'buddypress-sticky-post' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'buddypress-sticky-post' ); ?></h3>
                        <p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'buddypress-sticky-post' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'buddypress-sticky-post' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'buddypress-sticky-post' ); ?></h3>
                        <p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'buddypress-sticky-post' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'buddypress-sticky-post' ); ?></a>
                    </div>

                </div>

            </div>
        </div>

    </div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->
