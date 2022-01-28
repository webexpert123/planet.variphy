<?php
/**
 *
 * This file is called for general settings section at admin settings.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bpsts_gnrl_settings = get_option( 'bpsts_gnrl_settings' );
?>
<div class="wbcom-tab-content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpsts_gnrl_settings_section' );
		do_settings_sections( 'bpsts_gnrl_settings_section' );
		?>
		<div class="container">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="blogname"><?php esc_html_e( 'Enable status icon in activity loop', 'buddypress-status' ); ?></label>
					</th>
					<td>
						<label class="wb-switch">
							<input name='bpsts_gnrl_settings[act_loop_dis_icon]' type='checkbox' class="regular-text blpro-disp-resp-tr" value='yes' <?php (isset($bpsts_gnrl_settings['act_loop_dis_icon']))?checked($bpsts_gnrl_settings['act_loop_dis_icon'],'yes'):''; ?>/>
							<div class="wb-slider wb-round"></div>
						</label>
						<p class="description" id="tagline-description"><?php esc_html_e( 'This setting enables the status icon beside username in activity loop.', 'buddypress-status' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="blogname"><?php esc_html_e( 'Enable status icon at user profile page', 'buddypress-status' ); ?></label>
					</th>
					<td>
						<label class="wb-switch">
							<input name='bpsts_gnrl_settings[prof_pg_dis_icon]' type='checkbox' class="regular-text blpro-disp-resp-tr" value='yes' <?php (isset($bpsts_gnrl_settings['prof_pg_dis_icon']))?checked($bpsts_gnrl_settings['prof_pg_dis_icon'],'yes'):''; ?>/>
							<div class="wb-slider wb-round"></div>
						</label>
						<p class="description" id="tagline-description"><?php esc_html_e( 'This setting enables the status icon beside username in at user profile page(header section).', 'buddypress-status' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="blogname"><?php esc_html_e( 'Enable reaction icon in bp activity actions', 'buddypress-status' ); ?></label>
					</th>
					<td>
						<label class="wb-switch">
							<input name='bpsts_gnrl_settings[reaction_dis_icon]' type='checkbox' class="regular-text blpro-disp-resp-tr" value='yes' <?php (isset($bpsts_gnrl_settings['reaction_dis_icon']))?checked($bpsts_gnrl_settings['reaction_dis_icon'],'yes'):''; ?>/>
							<div class="wb-slider wb-round"></div>
						</label>
						<p class="description" id="tagline-description"><?php esc_html_e( 'This setting enables the reaction icon in buddypress activity actions.', 'buddypress-status' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
		<?php submit_button(); ?>
	</form>
</div>