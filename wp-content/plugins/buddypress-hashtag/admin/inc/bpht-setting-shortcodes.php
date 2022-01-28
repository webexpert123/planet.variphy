<?php
/**
 *
 * This file is called for rendering shortcodes.
 */

?>
<div class="wbcom-tab-content">
	<div class="blpro-support-setting">
		<div class="blpro-tab-header">
			<h3><?php esc_html_e( 'Available shortcodes', 'buddypress-hashtags' ); ?></h3>
		</div>
		<div class="blpro-faqs-block-parent-contain">
			<div class="blpro-faqs-block-contain">
				<div class="blpro-faq-row border">
					<div class="blpro-admin-col-12">
						<button class="blpro-accordion">
							<?php esc_html_e( 'BuddyPress Hashtags', 'buddypress-hashtags' ); ?>
						</button>
						<div class="blpro-panel">
							<p>
								<code>[bpht_bp_hashtags]</code>
							</p>
							<p>
								<?php esc_html_e('For eg.','buddypress-hashtags') ?><code><?php esc_html_e( '[bpht_bp_hashtags displaystyle="cloud" sortby="name" sortorder="asc" limit="12"]', 'buddypress-hashtags' ); ?></code>
							</p>
							<p><?php esc_html_e('Values accepted by parameters','buddypress-hashtags'); ?></p>
							<p><?php esc_html_e('{displaystyle} - cloud/list','buddypress-hashtags') ?></p>
							<p><?php esc_html_e('{sortby} - name/size','buddypress-hashtags') ?></p>
							<p><?php esc_html_e('{sortorder} - asc/desc','buddypress-hashtags') ?></p>
							<p><?php esc_html_e('{limit} - any numeric value','buddypress-hashtags') ?></p>
						</div>
					</div>
					<div class="blpro-admin-col-12">
						<button class="blpro-accordion">
							<?php esc_html_e( 'Bbpress Hashtags', 'buddypress-hashtags' ); ?>
						</button>
						<div class="blpro-panel">
							<p>
								<code>[bpht_bbpress_hashtags]</code>
							</p>
							<p>
								<?php esc_html_e('For eg.','buddypress-hashtags') ?><code><?php esc_html_e( '[bpht_bbpress_hashtags displaystyle="cloud" sortby="name" sortorder="asc" limit="12"]', 'buddypress-hashtags' ); ?></code>
							</p>
							<p><?php esc_html_e('Values accepted by parameters','buddypress-hashtags'); ?></p>
							<p><?php esc_html_e('{displaystyle} - cloud/list','buddypress-hashtags') ?></p>
							<p><?php esc_html_e('{sortby} - name/size','buddypress-hashtags') ?></p>
							<p><?php esc_html_e('{sortorder} - asc/desc','buddypress-hashtags') ?></p>
							<p><?php esc_html_e('{limit} - any numeric value','buddypress-hashtags') ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
