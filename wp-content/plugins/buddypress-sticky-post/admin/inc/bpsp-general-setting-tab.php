<?php
/**
 *
 * This file is called for general settings section at admin settings.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bpsp_general_settings = get_option( 'bpsp_general_settings' );

$bpsp_general_settings['pin_rebbon_color'] = ( isset($bpsp_general_settings['pin_rebbon_color'])) ? $bpsp_general_settings['pin_rebbon_color'] : '#00b0ff';
?>
<div class="wbcom-tab-content">
<form method="post" action="options.php">
	<?php
	settings_fields( 'bpsp_general_settings_section' );
	do_settings_sections( 'bpsp_general_settings_section' );
	?>
	<div class="container">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Pin post label', 'buddypress-sticky-post' ); ?></label></th>
				<td>
					<input type="text" name="bpsp_general_settings[pin_post_lbl]" value="<?php echo (isset( $bpsp_general_settings['pin_post_lbl'] ))?$bpsp_general_settings['pin_post_lbl']:''; ?>" placeholder="<?php esc_html_e( 'Pin post', 'buddypress-sticky-post' );  ?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Unpin post label', 'buddypress-sticky-post' ); ?></label></th>
				<td>
					<input type="text" name="bpsp_general_settings[unpin_post_lbl]" value="<?php echo (isset( $bpsp_general_settings['unpin_post_lbl'] ))?$bpsp_general_settings['unpin_post_lbl']:''; ?>" placeholder="<?php esc_html_e( 'Unpin post', 'buddypress-sticky-post' );  ?>">
				</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="pin_rebbon_color"><?php esc_html_e( 'Pin ribbon color', 'buddypress-sticky-post' ); ?></label></th>
				<td>					
					<input type="text" id="pin_rebbon_color"  name="bpsp_general_settings[pin_rebbon_color]" class="pin_rebbon_color" value="<?php echo esc_attr($bpsp_general_settings['pin_rebbon_color']); ?>" />
				</td>
			</tr>
	    </table>
	</div>
	<?php submit_button(); ?>
</form>
</div> <!-- closing of div class wbcom-tab-content -->
