<?php

/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_activity_entry' ); ?>
<?php if ( bp_activity_has_content() ) :
	global $activities_template;
	//Get all photo ids of activity from buddyboss_media
	$act_id 				= bp_get_activity_id();
	$activity_media_ids 	= bbm_activity_media_ids( $act_id );
	$act_obj 				= $activities_template->activity;
	$is_super_admin 		= is_super_admin();
	$bp_displayed_user_id 	= bp_displayed_user_id();
	$bp_loggedin_user_id 	= bp_loggedin_user_id();

	if ( ! empty( $activity_media_ids ) ):


		/**
		 * After bulk upload feature was added, an array of attachment ids is saved in database.
		 * Before bulk upload feature, only one image was uploaded and thus, only one attachment id was saved in activity meta.
		 * In that case, $media_ids will be a int variable, lets convert it to array, for uniformity in following code.
		 */
		if ( ! is_array( $activity_media_ids ) ) {
			$activity_media_ids = array( $activity_media_ids );
		}

		$comment_count 			= bp_activity_recurse_comment_count( $act_obj );
		$favorite_count 		= bp_activity_get_meta( $act_id, 'favorite_count' );
		$favorite_count 		= max( $favorite_count, '0' );
		$owner 					= ( $act_obj->user_id == get_current_user_id() )?'1':'0';

		//Loop over all photos to generate masonry brick
		foreach ( $activity_media_ids as $key => $attachment_id ):

			$media_thumbnail_url 	= wp_get_attachment_thumb_url( $attachment_id );

			if ( wp_is_mobile() ) {
				$full = wp_get_attachment_image_src( $attachment_id, 'large' );
			} else {
				$full = wp_get_attachment_image_src( $attachment_id, 'buddyboss_media_photo_large' );
			}

			//Continue if media is not exist
			if ( empty( $media_thumbnail_url ) ) {
				continue;
			}

			if (  buddyboss_media()->types->photo->hooks->bbm_get_media_is_favorite() ) {
				$data_fav = 'bbm-unfav';
			} else {
				$data_fav = 'bbm-fav';
			}

			$caption_inner = '<div class="buddyboss_media_caption_action">' . $act_obj->action . '</div>';
			$caption_inner .= '<div class="buddyboss_media_caption_body">' . $act_obj->content . '</div>';
			$caption       = '<div class="buddyboss_media_caption" >' . $caption_inner . '</div>';
			$media_title   = get_the_title( $attachment_id );
			?>

			<div id="activity-<?php echo $act_id; ?>" class="photo-item-wrapper" data-activity-id="<?php echo $act_id; ?>">
				<div class='photo-item activity-inner'>
					<?php echo $caption; ?>
					<a class="buddyboss-media-photo-wrap" href="<?php echo $full[0]; ?>" data-caption="<?php echo esc_attr( $media_title ); ?>" data-activity-id="<?php echo $act_id; ?>" data-id="<?php echo $attachment_id; ?>" data-width="<?php echo ! empty( $full[1] ) ? esc_attr( $full[1] ) : 600; ?>" data-height="<?php echo ! empty( $full[2] ) ? esc_attr( $full[2] ) : 600; ?>">
						<img title="<?php echo esc_attr( $media_title ); ?>" src="<?php echo $media_thumbnail_url; ?>" class="buddyboss-media-photo fade-in" data-photo-caption="" data-owner="<?php echo $owner; ?>" data-media="<?php echo $attachment_id; ?>" data-permalink="<?php echo esc_url( bp_activity_get_permalink( $act_id, $act_obj ) ); ?>" data-photo-id="<?php echo $attachment_id ?>" data-bbmfav="<?php echo $data_fav ?>" data-comment-count="<?php echo $comment_count ?>" data-favorite-count='<?php echo $favorite_count ?>'/>
					</a>
				</div>
			</div>

		<?php endforeach;
	endif;
endif; ?>

<?php do_action( 'bp_after_activity_entry' ); ?>
