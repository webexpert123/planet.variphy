<?php
/**
 *
 * This file is used for rendering and saving plugin general settings.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bpolls_settings = get_site_option( 'bpolls_settings' );
} else {
	$bpolls_settings = get_site_option( 'bpolls_settings' );
}
if ( !isset($bpolls_settings['limit_poll_activity']) ) {
	$bpolls_settings['limit_poll_activity'] = 'no';
}

global $wp_roles;
?>
<div class="wbcom-tab-content">
<form method="post" action="admin.php?action=update_network_options">
	<input name='bpolls_settings[hidden]' type='hidden' value=""/>
	<?php
	settings_fields( 'buddypress_polls_general' );
	do_settings_sections( 'buddypress_polls_general' );
	?>
	<table class="form-table polls-general-options">
		<tr>
			<th scope="row"><label for="blogname"><?php esc_html_e( 'Multi select polls', 'buddypress-polls' ); ?></label></th>
			<td>
				<label class="wb-switch">
					<input name='bpolls_settings[multiselect]' type='checkbox' value='yes' <?php (isset($bpolls_settings['multiselect']))?checked($bpolls_settings['multiselect'],'yes'):''; ?>/>
					<div class="wb-slider wb-round"></div>
				</label>
				<p class="description" id="tagline-description"><?php esc_html_e( 'Enabled: users can vote for multiple options in each poll.', 'buddypress-polls' ); ?>
				</p>
				<p class="description" id="tagline-description"><?php esc_html_e( 'Disabled: users can only vote for one option in each poll.', 'buddypress-polls' ); ?>
				</p>
		    </td>
	    </tr>
	    <tr>
			<th scope="row"><label for="blogname"><?php esc_html_e( 'Hide results', 'buddypress-polls' ); ?></label></th>
			<td>
			<label class="wb-switch">
				<input name='bpolls_settings[hide_results]' type='checkbox' value='yes' <?php (isset($bpolls_settings['hide_results']))?checked($bpolls_settings['hide_results'],'yes'):''; ?>/>
				<div class="wb-slider wb-round"></div>
			</label>
			<p class="description" id="tagline-description"><?php esc_html_e( 'Enabled: results are hidden from users who have not voted yet.', 'buddypress-polls' ); ?>
			</p>
			<p class="description" id="tagline-description"><?php esc_html_e( 'Disabled: users can see poll results before voting.', 'buddypress-polls' ); ?>
			</p>
		    </td>
	    </tr>
	    <tr>
			<th scope="row"><label for="blogname"><?php esc_html_e( 'Poll closing date & time', 'buddypress-polls' ); ?></label></th>
			<td>
			<label class="wb-switch">
				<input name='bpolls_settings[close_date]' type='checkbox' value='yes' <?php (isset($bpolls_settings['close_date']))?checked($bpolls_settings['close_date'],'yes'):''; ?>/>
				<div class="wb-slider wb-round"></div>
			</label>
			<p class="description" id="tagline-description"><?php esc_html_e( 'Enabled: users can set poll closing date and time.', 'buddypress-polls' ); ?>
			</p>
			<p class="description" id="tagline-description"><?php esc_html_e( 'Disabled: polls will always remain open for voting', 'buddypress-polls' ); ?>
			</p>
		    </td>
	    </tr>
	    <tr>
			<th scope="row"><label for="blogname"><?php esc_html_e( 'Enable image attachment', 'buddypress-polls' ); ?></label></th>
			<td>
			<label class="wb-switch">
				<input name='bpolls_settings[enable_image]' type='checkbox' value='yes' <?php (isset($bpolls_settings['enable_image']))?checked($bpolls_settings['enable_image'],'yes'):''; ?>/>
				<div class="wb-slider wb-round"></div>
			</label>
			<p class="description" id="tagline-description"><?php esc_html_e( 'Enabled: users can set a image attachment with the poll.', 'buddypress-polls' ); ?>
			</p>
		    </td>
	    </tr>
		<tr>
			<th scope="row"><label for="blogname"><?php esc_html_e( 'Enable after poll message', 'buddypress-polls' ); ?></label></th>
			<td>
			<label class="wb-switch">
				<input name='bpolls_settings[enable_thank_you_message]' type='checkbox' value='yes' <?php (isset($bpolls_settings['enable_thank_you_message']))?checked($bpolls_settings['enable_thank_you_message'],'yes'):''; ?>/>
				<div class="wb-slider wb-round"></div>
			</label>
			<p class="description" id="tagline-description"><?php esc_html_e( 'Enabled: users can add thank you message with the poll.', 'buddypress-polls' ); ?>
			</p>
		    </td>
	    </tr>

		<tr>
			<th scope="row"><label><?php esc_html_e( 'Limit Poll Activities', 'buddypress-polls' ); ?></label></th>
			<td>
				<label>
				<input name='bpolls_settings[limit_poll_activity]' type='radio' value='no' <?php (isset($bpolls_settings['limit_poll_activity']))?checked($bpolls_settings['limit_poll_activity'],'no'):''; ?>/>&nbsp; <?php esc_html_e( 'No Limit', 'buddypress-polls' ); ?>
				</label>
				<label>
				<input name='bpolls_settings[limit_poll_activity]' type='radio' value='user_role' <?php (isset($bpolls_settings['limit_poll_activity']))?checked($bpolls_settings['limit_poll_activity'],'user_role'):''; ?>/>&nbsp; <?php esc_html_e( 'Limit by User Role', 'buddypress-polls' ); ?>
				</label>
				<label>
				<input name='bpolls_settings[limit_poll_activity]' type='radio' value='member_type' <?php (isset($bpolls_settings['limit_poll_activity']))?checked($bpolls_settings['limit_poll_activity'],'member_type'):''; ?>/>&nbsp; <?php esc_html_e( 'Limit by Member Type', 'buddypress-polls' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Limit by user role or member type to publish poll type activities', 'buddypress-polls' ); ?>
			</p>
		    </td>
	    </tr>
		<tr id="bpolls_user_role" <?php if ( isset($bpolls_settings['limit_poll_activity']) && $bpolls_settings['limit_poll_activity'] != 'user_role'):?> style="display:none" <?php endif;?>>
			<th scope="row"><label><?php esc_html_e( 'Select User Roles', 'buddypress-polls' ); ?></label></th>

			<td>
				<select class="multi-selectize" name="bpolls_settings[poll_user_role][]" multiple>
					<?php
					$roles = $wp_roles->get_names();
					foreach( $roles as $role => $role_name ) {
						$selected = ( !empty( $bpolls_settings['poll_user_role'] ) && in_array( $role, $bpolls_settings['poll_user_role'] ) ) ? 'selected' : '';
					?>
					   <option value="<?php echo esc_attr( $role ); ?>" <?php echo $selected; ?>><?php echo esc_attr( $role_name ); ?></option>
					<?php } ?>
				</select>
				<p class="description"><?php esc_html_e( 'Select user role which are allowed to publish poll type activities.', 'buddypress-polls' ); ?>
			</td>
		</tr>

		<tr id="bpolls_member_type" <?php if ( isset($bpolls_settings['limit_poll_activity']) && $bpolls_settings['limit_poll_activity'] != 'member_type'):?> style="display:none" <?php endif;?>>
			<th scope="row"><label><?php esc_html_e( 'Select Member Type', 'buddypress-polls' ); ?></label></th>

			<td>
				<?php $types        = bp_get_member_types( array(), 'objects' );
					if ( $types ) { ?>
						<select class="multi-selectize" name="bpolls_settings[poll_member_type][]" multiple>

						<?php foreach ( $types as $type ) {
							$selected = ( !empty( $bpolls_settings['poll_member_type'] ) && in_array( $type->name, $bpolls_settings['poll_member_type'] ) ) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr( $type->name ); ?>" <?php echo $selected;?>><?php echo esc_html( $type->labels['singular_name'] ); ?></option>
						<?php } ?>
						</select>
					<?php }?>

				<p class="description"><?php esc_html_e( 'Select member type which are allowed to publish poll type activities.', 'buddypress-polls' ); ?>
			</td>
		</tr>

	</table>
	<?php submit_button(); ?>
</form>
</div>
