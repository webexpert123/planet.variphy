<?php

function render_user_status_row( $user_id, $bpsts_status, $status_id ) {
	?>
	<tr>
		<td class="bpsts-user-status"><?php echo $bpsts_status; ?></td>
		<td class="bpsts-status-actions">
			<a class="bpsts-current-status" href="JavaScript:void(0)" data-userid="<?php echo $user_id; ?>" data-statusid="<?php echo $status_id; ?>"><i class="fa fa-check" aria-hidden="true"></i></a>
			<a class="bpsts-edit-status" href="JavaScript:void(0)" data-userid="<?php echo $user_id; ?>" data-statusid="<?php echo $status_id; ?>"><i class="fa fa-edit"></i></a>
			<a class="bpsts-del-status" href="JavaScript:void(0)" data-userid="<?php echo $user_id; ?>" data-statusid="<?php echo $status_id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
		</td>
	</tr>
	<?php
}

function render_user_status_icon_row( $user_id, $bpsts_status_icon ) {
	$icon_html = '';
	if ( empty( $bpsts_status_icon ) ) {
		return;
	}
	if ( isset( $bpsts_status_icon['folder'] ) ) {		
		$url          = BPSTS_PLUGIN_URL . 'icons/' . $bpsts_status_icon['folder'] . '/64/' . str_replace( '.png', '.svg', $bpsts_status_icon['imgname'] ) ;
		if ( $bpsts_status_icon['folder'] == 'custom') {
			$wp_upload_dir = wp_upload_dir();
			$url = $wp_upload_dir['baseurl'] . '/buddypress-status/' . $bpsts_status_icon['imgname'];
		}
		$file_headers = @get_headers( $url );
		if ( isset( $file_headers ) && $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
			$icon_html = '';
		} else {
			$icon_html = '<img class="bpsts-name-icon" src="' . $url . '">';
		}
	}
	?>
	<tr>
		<td class="bpsts-user-status-icon"><?php echo $icon_html; ?></td>
		<td class="bpsts-status-actions">
			<!-- <a class="bpsts-current-status" href="JavaScript:void(0)" data-userid="<?php // echo $user_id; ?>" data-statusid="<?php // echo $status_id; ?>"><i class="fa fa-check" aria-hidden="true"></i></a> -->
			<!-- <a class="bpsts-edit-status" href="JavaScript:void(0)" data-userid="<?php // echo $user_id; ?>" data-statusid="<?php // echo $status_id; ?>"><i class="fa fa-edit"></i></a> -->
			<a class="bpsts-del-status" href="JavaScript:void(0)" data-userid="<?php echo $user_id; ?>" data-statusid="<?php // echo $status_id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
		</td>
	</tr>
	<?php
}

function bpsts_icons_folder_set() {
	$icons_folder_set = array(
		'set_mood'      => array(
			'set_name'   => __( 'Mood', 'buddypress-status' ),
			'set_folder' => 'mood',
		),
		'set_activity'  => array(
			'set_name'   => __( 'Activity', 'buddypress-status' ),
			'set_folder' => 'activity',
		),
		'set_food'      => array(
			'set_name'   => __( 'Food', 'buddypress-status' ),
			'set_folder' => 'food',
		),
		'set_diversity' => array(
			'set_name'   => __( 'Diversity', 'buddypress-status' ),
			'set_folder' => 'diversity',
		),
		'set_custom' => array(
			'set_name'   => __( 'Custom Icon', 'buddypress-status' ),
			'set_folder' => '',
		),
	);

	return apply_filters( 'bpsts_add_icons_folder', $icons_folder_set );
}


function bpsts_icons_svgfiles() {
	$svg_icons	= array(					
					'angry-face',
					'anguished-face',
					'anxious-face-with-sweat',
					'astonished-face',
					'confounded-face',
					'confused-face',
					'crying-face',
					'disappointed-face',
					'dizzy-face',
					'downcast-face-with-sweat',
					'drooling-face',
					'expressionless-face',
					'face-blowing-a-kiss',
					'face-savoring-food',
					'face-vomiting',
					'face-with-hand-over-mouth',
					'face-with-head-bandage',
					'face-with-medical-mask',
					'face-with-monocle',
					'face-with-raised-eyebrow',
					'face-with-rolling-eyes',
					'face-with-steam-from-nose',
					'face-with-tears-of-joy',
					'face-with-thermometer',
					'face-with-tongue',
					'fearful-face',
					'flushed-face',
					'frowning-face-with-open-mouth',
					'frowning-face',
					'grimacing-face',
					'grinning-face-with-big-eyes',
					'grinning-face-with-smiling-eyes',
					'grinning-face-with-sweat',
					'grinning-face',
					'grinning-squinting-face',
					'hugging-face',
					'hushed-face',
					'kissing-face-with-closed-eyes',
					'kissing-face-with-smiling-eyes',
					'kissing-face',
					'lying-face',
					'nerd-face',
					'neutral-face',
					'pensive-face',
					'persevering-face',
					'pleading-face',
					'pouting-face',
					'relieved-face',
					'rolling-on-the-floor-laughing',
					'sleeping-face',
					'sleepy-face',
					'slightly-frowning-face',
					'slightly-smiling-face',
					'smiling-face-with-3-hearts',
					'smiling-face-with-heart-eyes',
					'smiling-face-with-smiling-eyes',
					'smiling-face-with-sunglasses',
					'smiling-face',
					'smirking-face',
					'sneezing-face',
					'squinting-face-with-tongue',
					'star-struck',
					'thinking-face',
					'tired-face',
					'unamused-face',
					'upside-down-face',
					'weary-face',
					'winking-face-with-tongue',
					'winking-face',
					'woozy-face',
					'worried-face',
					'zany-face',
					'zipper-mouth-face',						
					'1st-place-medal',
					'admission-tickets',
					'artist-palette',
					'basketball',
					'bow-and-arrow',
					'bowling',
					'chess-pawn',
					'clapper-board',
					'direct-hit',
					'drum',
					'fishing-pole',
					'guitar',
					'horse-racing',
					'jigsaw',
					'man-biking',
					'man-cartwheeling',
					'man-climbing',
					'man-golfing',
					'man-in-lotus-position',
					'man-juggling',
					'performing-arts',
					'sports-medal',
					'trophy',
					'violin',
					'woman-biking',
					'woman-cartwheeling',
					'woman-climbing',
					'woman-golfing',
					'woman-in-lotus-position',
					'woman-juggling',						
					'avocado',
					'bagel',
					'beer-mug',
					'bento-box',
					'birthday-cake',
					'bottle-with-popping-cork',
					'cherries',
					'chocolate-bar',
					'doughnut',
					'egg',
					'fork-and-knife',
					'green-salad',
					'meat-on-bone',
					'oden',
					'pancakes',
					'peach',
					'pie',
					'pizza',
					'popcorn',
					'poultry-leg',
					'sandwich',
					'shallow-pan-of-food',
					'shortcake',
					'soft-ice-cream',
					'spaghetti',
					'steaming-bowl',
					'taco',
					'takeout-box',
					'teacup-without-handle',
					'tropical-drink',						
					'adult-light-skin-tone',
					'baby-angel-light-skin-tone',
					'backhand-index-pointing-down-light-skin-tone',
					'backhand-index-pointing-left-light-skin-tone',
					'backhand-index-pointing-up-light-skin-tone',
					'bearded-person-light-skin-tone',
					'blond-haired-man-light-skin-tone',
					'blond-haired-woman-light-skin-tone',
					'crossed-fingers-light-skin-tone',
					'fairy-light-skin-tone',
					'folded-hands-light-skin-tone',
					'left-facing-fist-light-skin-tone',
					'love-you-gesture-light-skin-tone',
					'man-artist-medium-light-skin-tone',
					'man-bowing-light-skin-tone',
					'man-facepalming-light-skin-tone',
					'man-health-worker-light-skin-tone',
					'man-shrugging-medium-light-skin-tone',
					'man-superhero-light-skin-tone',
					'man-walking-light-skin-tone',
					'man-wearing-turban-dark-skin-tone',
					'nail-polish-light-skin-tone',
					'prince-light-skin-tone',
					'raising-hands-light-skin-tone',
					'selfie-light-skin-tone',
					'woman-dancing-light-skin-tone',
					'woman-gesturing-ok-medium-light-skin-tone',
					'woman-playing-handball-light-skin-tone',
					'woman-shrugging-light-skin-tone',
					'woman-surfing-light-skin-tone'						
				);
		
	return $svg_icons;
}

