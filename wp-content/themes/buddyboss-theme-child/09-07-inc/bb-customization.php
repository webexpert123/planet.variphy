<?php
add_filter( 'bp_activity_get_visibility_levels', 'cstm_bp_activity_get_visibility_levels',99 );
function cstm_bp_activity_get_visibility_levels($visibility_levels){
	if(isset($visibility_levels['friends'])){
		unset($visibility_levels['friends']);
	}
	if(isset($visibility_levels['onlyme'])){
		unset($visibility_levels['onlyme']);
	}
	return $visibility_levels;

}

add_shortcode('bb-group-suggestion','cstm_get_invited_groups_list');
function cstm_get_invited_groups_list(){
	ob_start();
	/*if(is_user_logged_in()){
		$user_id = get_current_user_id();
		$invite_query = groups_get_invites_for_user( $user_id );
		$invites      = $invite_query['groups'];
		if(!empty($invites)){
			foreach ($invites as $invite) {
				$name = $invite->name;
				$slug = $invite->slug;
				$status = $invite->status;
				$id = $invite->id;
				$creator_id = $invite->creator_id;

				?>
				<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
					</div>
				<?php endif; ?>
				<?php 
			}
		}
	}*/
	?>
	<div class="create-group">
		<a href="<?php echo site_url();?>/groups/create/step/group-details/">Create Group</a> <br> <br>
	</div>
	<?php if ( bp_has_groups( 'type=invites&user_id=' . bp_loggedin_user_id() ) ) : ?>

		<ul id="groups-list" class="invites item-list bp-list item-list groups-list" data-bp-list="groups_invites">

			<?php while ( bp_groups() ) : bp_the_group();?>
				<?php 
				$bp_core_get_username = bp_core_get_username(bp_loggedin_user_id());
				$invite_url = site_url().'/members/'.$bp_core_get_username.'/groups/invites/?n=1';
				?>

				<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
					<div class="list-wrap">
						<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
							<div class="item-avatar">
								<a href="<?php echo $invite_url; ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
							</div>
						<?php endif; ?>
						<div class="item">
							<div class="item-block">
								<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>
								<p class="item-meta group-details">
		                            <?php $inviter = bp_groups_get_invited_by(); ?>
		                            <?php if ( ! empty( $inviter ) ) : ?>
		                                <?php
			                            printf(
				                            __( 'Invited by %1$s &middot; %2$s.', 'buddyboss-theme' ),
				                            sprintf(
					                            '<a href="%s">%s</a>',
					                            $invite_url,
					                            $inviter['name']
				                            ),
				                            sprintf(
					                            '<span class="last-activity">%s</span>',
					                            bp_core_time_since( $inviter['date_modified'] )
				                            )
			                            );
		                                ?>
		                               <p><a href="<?php echo $invite_url;?>">Click Here </a></p>
		                            <?php endif; ?>
								</p>
								<!-- <p class="desc item-meta invite-message">
									<?php // echo bp_groups_get_invite_messsage_for_user( bp_displayed_user_id(), bp_get_group_id() ); ?>
								</p> -->
							</div>
						</div>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
	<?php endif; ?>
	<?php 
	return ob_get_clean();
}