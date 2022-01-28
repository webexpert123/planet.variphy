<?php
/**
 *
 * This file is called for icon settings section at admin settings.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bpsts_icon_settings = get_option( 'bpsts_icon_settings' );
$icon_folders = bpsts_icons_folder_set();

$update_default_rctn = get_option( 'bpsts_default_reaction' );

if( $update_default_rctn != 'yes' && !isset( $bpsts_icon_settings['reactions'] ) ) {
	$bpsts_icon_settings['reactions'] = array(
		'mood-54' => array(
			'imgname' => 'smiling-face-with-heart-eyes.png',
			'folder'  => 'mood'
		),
		'diversity-24' => array(
			'imgname' => 'selfie-light-skin-tone.png',
			'folder'  => 'diversity'
		),
		'diversity-8' => array(
			'imgname' => 'crossed-fingers-light-skin-tone.png',
			'folder'  => 'diversity'
		),
		'activity-21' => array(
			'imgname' => 'sports-medal.png',
			'folder'  => 'activity'
		),
		'diversity-10' => array(
			'imgname' => 'folded-hands-light-skin-tone.png',
			'folder'  => 'diversity'
		),
		'mood-61' => array(
			'imgname' => 'star-struck.png',
			'folder'  => 'mood'
		),
	);
	update_option( 'bpsts_icon_settings', $bpsts_icon_settings );
	update_option( 'bpsts_default_reaction', 'yes' );
}
?>
<div class="wbcom-tab-content">
<form id="bpsts-icons-form" method="post" action="options.php" enctype="multipart/form-data">
	<?php
	settings_fields( 'bpsts_icon_settings_section' );
	do_settings_sections( 'bpsts_icon_settings_section' );
	?>
	<div class="container">
		<table class="form-table">
			<tr>
				<th>
					<label for="status-icon"><?php esc_html_e( 'Upload Custom Icons', 'buddypress-status' ); ?></label>
				</th>
				<td>
					<input type="file" name="bpsts_icon_settings[custom_icon][]"  multiple="multiple"  />
					<p class="description"><?php esc_html_e( 'Upload icons here. Icon sizes should be 64px*64px. Upload PNG and SVG format with transparent background for better results.', 'buddypress-status' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Select icons set to be available', 'buddypress-status' ); ?></label><p class="description"><?php esc_html_e( '( User selected icon will appear beside username at profile page. )', 'buddypress-status' ); ?></p>
				</th>
				<td>
				<div class="bpsts-chck-optn-container">
				<?php
					foreach ($icon_folders as $set_key => $set_data) {

						$checked = ( isset( $bpsts_icon_settings['iconsets'] ) && in_array( $set_key, $bpsts_icon_settings['iconsets'] ) )?'checked':'';
						?>
						<div class="bpsts-image-holder-container-wrapper">
							<div class="bpsts-image-holder-container">
								<div class="bpsts-image-holder">
									<?php
									$dir	 = '';
									$dir	 = BPSTS_PLUGIN_PATH . "icons/" . $set_data[ 'set_folder' ] . "/64/*.{svg}";
									if ( $set_key == 'set_custom' ) {
										$wp_upload_dir = wp_upload_dir();
										$dir = $wp_upload_dir['basedir'] . '/buddypress-status/*.{jpg,png,gif,svg}';
										$set_data[ 'set_folder' ] = 'custom';
									}

									$images	 = glob( $dir, GLOB_BRACE );
									if ( is_array( $images ) ) {
										foreach ( $images as $key => $image ) {
											$image_name = basename( $image );
											echo "<div class='bpsts-icon-div' data-index='".$set_data[ 'set_folder' ].'-'.$key."' data-imgname='".$image_name."' data-folder='".$set_data[ 'set_folder' ]."' >";

											$bpsts_icon_img = BPSTS_PLUGIN_URL . "icons/" . $set_data['set_folder' ] . "/64/" . $image_name;
											if ( $set_key == 'set_custom' ) {
												$bpsts_icon_img = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $image_name;
											}
											echo "<img class='bpsts-icon-img' src='" . $bpsts_icon_img . "'/>";
											echo "</div>";
										}
									}
									?>
								</div>
							</div>
							<footer class="bpsts-image-folder-name">
								<div class="row">
									<div class="col folder-name">
										<span class="name"><?php echo $set_data[ 'set_name' ]; ?></span>
									</div>
									<div class="col folder-checkbox">
										<?php
										echo "<div class='bpsts-chck-optn'><input type='checkbox' name='bpsts_icon_settings[iconsets][]' value='".$set_key."' ".$checked." >";
										?>
									</div>
								</div>
							</footer>
						</div>
						<?php
					}
				?>
				</div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="blogname"><?php esc_html_e( 'Activity reaction icons', 'buddypress-status' ); ?></label><p class="description"><?php esc_html_e( '( Selected icon will be available to give reaction on activity updates. Drag and drop here from above icon sets. )', 'buddypress-status' ); ?></p>
				</th>
				<td class="bpsts-reaction-fields">
					<div class="bpsts-drop-reaction-icons">
						<?php
							if( isset( $bpsts_icon_settings['reactions'] ) ) {
								foreach ( $bpsts_icon_settings['reactions'] as $key => $image) {
									if( isset( $image['folder'] ) ) {

										$url = BPSTS_PLUGIN_URL."icons/".$image['folder']."/64/".str_replace( '.png', '.svg', $image['imgname'] );
										$file_path = BPSTS_PLUGIN_PATH."icons/".$image['folder']."/64/".str_replace( '.png', '.svg', $image['imgname'] );
										if ( $image['folder'] == 'custom') {
											$wp_upload_dir = wp_upload_dir();
											$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $image['imgname'];
											$file_path = $wp_upload_dir['basedir'] . '/buddypress-status/' . $image['imgname'];
										}
																				
										if( file_exists( $file_path )) {
											echo '<div class="rc-div bpsts-icon-div  ui-draggable ui-draggable-handle">';
											echo '<img class="bpsts-icon-img" src="'.$url.'" data-index="'.$key.'" data-imgname="'.$image['imgname'].'" data-folder="'.$image['folder'].'">';
											echo '<div class="'.$key.'">';
											echo '<span class="remove-reaction" data-close="'.$key.'"><i class="fa fa-times" aria-hidden="true"></i></span>';
											echo '<input type="hidden" name="bpsts_icon_settings[reactions]['.$key.'][imgname]" value="'.$image['imgname'].'">';
											echo '<input type="hidden" name="bpsts_icon_settings[reactions]['.$key.'][folder]" value="'.$image['folder'].'">';
											echo '</div>';
											echo '</div>';
										}
									}
								}
							}
						?>
					</div>
				</td>
			</tr>
	    </table>
	</div>
	<?php submit_button(); ?>
</form>
</div> <!-- closing of div class wbcom-tab-content -->
