<?php
/**
 *
 * This template file is used for fetching desired options page file at admin settings end.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( isset( $_GET['tab'] ) ) {
	$bpht_tab = sanitize_text_field( $_GET['tab'] );
} else {
	$bpht_tab = 'welcome';
}

bpht_include_admin_setting_tabs( $bpht_tab );

/**
 * Include setting template.
 *
 * @param string $bpht_tab
 */
function bpht_include_admin_setting_tabs( $bpht_tab ) {
	switch ( $bpht_tab ) {
		case 'welcome':
			include 'bpht-welcome-page.php';
			break;
		case 'general':
			include 'bpht-setting-general-tab.php';
			break;
		case 'hashtag-logs':
			include 'bpht-hashtag-delete-tab.php';
			break;
		case 'shortcodes':
			include 'bpht-setting-shortcodes.php';
			break;
		default:
			include 'bpht-welcome-page.php';
			break;
	}
}
