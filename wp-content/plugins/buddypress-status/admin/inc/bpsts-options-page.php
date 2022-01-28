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
	$bpsts_tab = sanitize_text_field( $_GET['tab'] );
} else {
	$bpsts_tab = 'welcome';
}

bpsts_include_admin_setting_tabs( $bpsts_tab );

/**
 * Include setting template.
 *
 * @param string $bpsts_tab
 */
function bpsts_include_admin_setting_tabs( $bpsts_tab ) {
	switch ( $bpsts_tab ) {
		case 'welcome':
			include 'bpsts-welcome-page.php';
			break;
		case 'status-icon':
			include 'bpsts-setting-status-icon-tab.php';
			break;
		case 'general':
			include 'bpsts-setting-general-tab.php';
			break;
		default:
			include 'bpsts-welcome-page.php';
			break;
	}
}
