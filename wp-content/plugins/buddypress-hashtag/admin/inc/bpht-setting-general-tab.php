<?php
/**
 *
 * This file is called for general settings section at admin settings.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bpht_general_settings = get_site_option( 'bpht_general_settings' );
} else {
	$bpht_general_settings = get_option( 'bpht_general_settings' );
}

$an_enabled = bpht_alpha_numeric_hashtags_enabled();
if( $an_enabled ){
	$lengths_display_class = 'display:none';
}else{
	$lengths_display_class = '';
}

?>
<div class="wbcom-tab-content">
<form method="post" action="options.php">
	<?php
	settings_fields( 'bpht_general_settings_section' );
	do_settings_sections( 'bpht_general_settings_section' );
	?>
	<div class="container">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Allow non alphanumeric hashtag', 'buddypress-hashtags' ); ?></th>
				<td>
					<label class="wb-switch">
						<input class="allow_non_an_ht" name="bpht_general_settings[allow_non_an_ht]" type="checkbox" value="yes" <?php (isset($bpht_general_settings['allow_non_an_ht']))?checked($bpht_general_settings['allow_non_an_ht'],'yes'):''; ?>>
						<div class="wb-slider wb-round"></div>
					</label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Minimum and maximum hashtag length boundation will not work with this setting on.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			<tr style="<?php echo $lengths_display_class;?>" class="bpht-lengths-row">
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Minimum hashtag length', 'buddypress-hashtags' ); ?></label></th>
				<td><input name='bpht_general_settings[min_length]' type='number' min='3' class="regular-text" value='<?php echo ( isset( $bpht_general_settings['min_length'] ) && $bpht_general_settings['min_length'] ) ? $bpht_general_settings['min_length'] : '3'; ?>' placeholder="<?php esc_html_e( 'set minimum hashtag length', 'buddypress-hashtags' ); ?>" />
					<p class="description" id="tagline-description"><?php esc_html_e( 'Default value is 3.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			<tr style="<?php echo $lengths_display_class; ?>" class="bpht-lengths-row">
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Maximum hashtag length', 'buddypress-hashtags' ); ?></label></th>
				<td><input name='bpht_general_settings[max_length]' type='number' min='5' class="regular-text" value='<?php echo ( isset( $bpht_general_settings['max_length'] ) && $bpht_general_settings['max_length'] ) ? $bpht_general_settings['max_length'] : '16'; ?>' placeholder="<?php esc_html_e( 'set maximum hashtag length', 'buddypress-hashtags' ); ?>" />
					<p class="description" id="tagline-description"><?php esc_html_e( 'Default value is 16.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Clear buddypress widgets hashtags', 'buddypress-hashtags' ); ?></label></th>
				<td>
					<a href="javascript:void(0)" class="bpht-clear-bp-hashtags button button-primary"><?php esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
					<p class="description" id="tagline-description"><?php esc_html_e( 'This will only clear old hashtags from buddypress community widget.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			<?php if( class_exists( 'bbPress' ) ){ ?>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Clear bbpress widgets hashtags', 'buddypress-hashtags' ); ?></label></th>
				<td>
					<a href="javascript:void(0)" class="bpht-clear-bbpress-hashtags button button-primary"><?php esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
					<p class="description" id="tagline-description"><?php esc_html_e( 'This will only clear old hashtags from bbpress forum widget.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Clear post widgets hashtags', 'buddypress-hashtags' ); ?></label></th>
				<td>
					<a href="javascript:void(0)" class="bpht-clear-post-hashtags button button-primary"><?php esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
					<p class="description" id="tagline-description"><?php esc_html_e( 'This will only clear old hashtags from wp post hashtags widget.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Clear page widgets hashtags', 'buddypress-hashtags' ); ?></label></th>
				<td>
					<a href="javascript:void(0)" class="bpht-clear-page-hashtags button button-primary"><?php esc_html_e( 'Clear', 'buddypress-hashtags' ); ?></a>
					<p class="description" id="tagline-description"><?php esc_html_e( 'This will only clear old hashtags from wp page hashtags widget.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Disable hashtags on bbPresss?', 'buddypress-hashtags' ); ?></label></th>
				<td>
					<label class="wb-switch">
						<input class="disable_on_bbpress" name="bpht_general_settings[disable_on_bbpress]" type="checkbox" value="yes" <?php (isset($bpht_general_settings['disable_on_bbpress']))?checked($bpht_general_settings['disable_on_bbpress'],'yes'):''; ?>>
						<div class="wb-slider wb-round"></div>
					</label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Disable to hashtags link on bbPress.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Disable hashtags on Blog Posts?', 'buddypress-hashtags' ); ?></label></th>
				<td>
					<label class="wb-switch">
						<input class="disable_on_blog_posts" name="bpht_general_settings[disable_on_blog_posts]" type="checkbox" value="yes" <?php (isset($bpht_general_settings['disable_on_blog_posts']))?checked($bpht_general_settings['disable_on_blog_posts'],'yes'):''; ?>>
						<div class="wb-slider wb-round"></div>
					</label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Disbale to hashtags link on Blog Posts.', 'buddypress-hashtags' ); ?>
					</p>
				</td>
			</tr>
	    </table>
	</div>
	<?php submit_button(); ?>
</form>
</div> <!-- closing of div class wbcom-tab-content -->
