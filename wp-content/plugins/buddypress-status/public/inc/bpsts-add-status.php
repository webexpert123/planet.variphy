<?php
/**
 * This template file is used for listing buddypress status messages.
 */
$user_id                = bp_displayed_user_id();
$user_saved_status      = get_user_meta( $user_id, 'bpsts_saved_status', true );
$user_saved_status_icon = get_user_meta( $user_id, 'bpsts_user_icon', true );

?>
<p>
	<strong><?php esc_html_e( 'Note: ', 'buddypress-status' ); ?></strong>
	<?php esc_html_e( 'You can store upto ten status, adding more than that will delete the oldest one.', 'buddypress-status' ); ?>
</p>
<form method="post" action="">
	<textarea id="bpsts-status" name="bpsts-status" maxlength="140"></textarea>
	<span id="bpsts-charleft"></span>
	<div class="bpsts-form-actions">
		<div class="bpsts-add-actions">
			<a href="javascript:void(0)" class="bpsts-add button" data-userid="<?php echo $user_id; ?>"><?php esc_html_e( 'Add', 'buddypress-status' ); ?></a>
			<a href="javascript:void(0)" class="bpsts-add button" data-userid="<?php echo $user_id; ?>" data-setcurrent="yes"><?php esc_html_e( 'Add & Set Current', 'buddypress-status' ); ?></a>
		</div>
		<div class="bpsts-update-actions">
			<a href="javascript:void(0)" class="bpsts-update button" data-userid="<?php echo $user_id; ?>"><?php esc_html_e( 'Update', 'buddypress-status' ); ?></a>
			<a href="javascript:void(0)" class="bpsts-update button" data-setcurrent="yes" data-userid="<?php echo $user_id; ?>"><?php esc_html_e( 'Update & Set Current', 'buddypress-status' ); ?></a>
		</div>
		<div class="bpsts-hidden-fields">
			<input id="bpsts-for-update" type="hidden" data-userid="" data-statusid="">
		</div>
	</div>
</form>
<table class="form-table bpsts-statuses-table">
	<thead>
		<tr>
			<th class="bpsts-th"><?php esc_html_e( 'Status', 'buddypress-status' ); ?></th>
			<th class="bpsts-th"><?php esc_html_e( 'Actions', 'buddypress-status' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php		
		if ( ! empty( $user_saved_status ) && is_array( $user_saved_status ) || ! empty( $user_saved_status_icon ) ) {
			if ( ! empty( $user_saved_status ) && is_array( $user_saved_status )) {
				foreach ( $user_saved_status as $status_id => $status_text ) {
					render_user_status_row( $user_id, $status_text, $status_id );
				}
			}
			render_user_status_icon_row( $user_id, $user_saved_status_icon );


		} else {
			?>
			<tr class="bpsts-placeholder-status-tr"><td class="bpsts-placeholder-status"><?php esc_html_e( 'Set a profile status "While there\'s life there\'s hope." ', 'buddypress-status' ); ?></td></tr>
																										   <?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<th class="bpsts-th"><?php esc_html_e( 'Status', 'buddypress-status' ); ?></th>
			<th class="bpsts-th"><?php esc_html_e( 'Actions', 'buddypress-status' ); ?></th>
		</tr>
	</tfoot>
</table>
<div class="bpsts-icon-dialog">
	<div class="bpsts-icon-dialog-container">
		<div class="bpsts-icon-dialog-header">
			<i class="fa fa-check"></i>
		</div>
		<div class="bpsts-icon-dialog-msg">
			<div class="bpsts-icon-dialog-desc">
				<div class="bpsts-icon-dialog-title">

				</div>
				<div class="bpsts-icon-dialog-content">
					<?php esc_html_e( 'Set this as status icon', 'buddypress-status' ); ?>
				</div>
			</div>
		</div>
		<ul class="bpsts-icon-dialog-buttons">
			<li>
				<a class="bpsts-icon-dialog-set">
					<?php esc_html_e( 'Okay', 'buddypress-status' ); ?>
				</a>
			</li>
			<li>
				<a class="bpsts-icon-dialog-cancel">
					<?php esc_html_e( 'Cancel', 'buddypress-status' ); ?>
				</a>
			</li>
		</ul>
	</div>
</div>
<?php
$icon_folders        = bpsts_icons_folder_set();
$bpsts_icon_settings = get_option( 'bpsts_icon_settings' );

if ( is_array( $icon_folders ) && is_array( $bpsts_icon_settings ) && isset( $bpsts_icon_settings['iconsets'] ) ) {
	?>
	<p>
		<strong><?php esc_html_e( 'Set a status icon: ', 'buddypress-status' ); ?></strong>
		<?php esc_html_e( 'Selected icon will appear beside username in member header.', 'buddypress-status' ); ?>		
	</p>
	<div class="bpsts-image-holder-container-parent">
		<div class="bpsts-image-holder-container-wrapper">
			<div class="bpsts-image-holder-container">
				<div class="bpsts-image-holder">
	<?php
	foreach ( $icon_folders as $set_key => $set_data ) {
		if ( in_array( $set_key, $bpsts_icon_settings['iconsets'] ) ) {			
			$dir    = '';
			$dir    = BPSTS_PLUGIN_PATH . 'icons/' . $set_data['set_folder'] . '/64/*.{svg}';
			if ( $set_key == 'set_custom' ) {
				$wp_upload_dir = wp_upload_dir();
				$dir = $wp_upload_dir['basedir'] . '/buddypress-status/*.{jpg,png,gif,svg}';
				$set_data[ 'set_folder' ] = 'custom';
			}
			
			$images = glob( $dir, GLOB_BRACE );
			if ( is_array( $images ) ) {
				foreach ( $images as $key => $image ) {
					$image_name = basename( $image );
					echo "<div id='" . $set_data['set_folder'] . $key . "' class='bpsts-icon-div'>";
					
					$bpsts_icon_img = BPSTS_PLUGIN_URL . 'icons/' . $set_data['set_folder'] . '/64/' . str_replace( '.png', '.svg', $image_name );
					if ( $set_key == 'set_custom' ) {
						$bpsts_icon_img = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $image_name;
					}
					
					echo "<img data-id='" . $set_data['set_folder'] . $key . "' data-imgname='" . $image_name . "' data-setnam='" . $set_data['set_name'] . "' data-folder='" . $set_data['set_folder'] . "' data-userid='" . $user_id . "' class='bpsts-icon-img' src='" . $bpsts_icon_img . "' />";
					echo '</div>';
				}
			}						
		}
	}
	?>	
				</div>
			</div>
		</div>
	</div>
	<?php
}
