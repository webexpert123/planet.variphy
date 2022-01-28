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
            <h2 class="wbcom-welcome-title"><?php esc_html_e( 'BuddyPress Reactions and Status', 'buddypress-status' ); ?></h2>
            <p class="wbcom-welcome-description"><?php esc_html_e( 'Now, allow your members to set a status on their BuddyPress Profile with BuddyPress Status add-on for BuddyPress.', 'buddypress-status' ) ?></p>
            <p class="wbcom-welcome-description"><?php esc_html_e( 'This plugin allows your community members to update their status with what they are doing at that moment.', 'buddypress-status' ) ?></p>
        </div><!-- .wbcom-welcome-head -->

        <div class="wbcom-welcome-content">
            
            <div class="wbcom-video-link-wrapper">
                <iframe src="https://player.vimeo.com/video/566486649" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
<p><a href="https://vimeo.com/566486649">BuddyPress Status &amp; Reactions</a> from <a href="https://vimeo.com/wbcom">Wbcom Designs</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
            </div>

            <div class="wbcom-welcome-support-info">
                <h3><?php esc_html_e( 'Help &amp; Support Resources', 'buddypress-status' ); ?></h3>
                <p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-status' ); ?></p>
                <hr>

                <div class="three-col">

                    <div class="col">
                        <h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'buddypress-status' ); ?></h3>
                        <p><?php esc_html_e( 'We have prepared an extensive guide on BuddyPress Giphy to learn all aspects of the plugin. You will find most of your answers here.', 'buddypress-status' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/docs/buddypress-paid-addons/buddypress-status/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'buddypress-status' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'buddypress-status' ); ?></h3>
                        <p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'buddypress-status' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'buddypress-status' ); ?></a>
                    </div>

                    <div class="col">
                        <h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'buddypress-status' ); ?></h3>
                        <p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'buddypress-status' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wbcomdesigns.com/contact/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'buddypress-status' ); ?></a>
                    </div>

                </div>

            </div>
        </div>

    </div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->