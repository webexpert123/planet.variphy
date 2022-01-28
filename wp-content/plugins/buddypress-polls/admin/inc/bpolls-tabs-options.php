<?php
/**
 *
 * This template file is used for fetching desired options page file at admin settings.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_GET['tab'] ) ) {
	$bpolls_tab = sanitize_text_field( $_GET['tab'] );
} else {
	$bpolls_tab = 'welcome';
}

bpolls_include_setting_tabs( $bpolls_tab );

/**
 *
 * Function to select desired file for tab option.
 *
 * @param string $bpolls_tab The current tab string.
 */
function bpolls_include_setting_tabs( $bpolls_tab ) {

	switch ( $bpolls_tab ) {
		case 'welcome':
			include 'bpolls-welcome-page.php';
			break;
		case 'general':
			include 'bpolls-general-setting-tab.php';
			break;
		case 'support':
			include 'bpolls-support-setting-tab.php';
			break;
		default:
			include 'bpolls-welcome-page.php';
			break;
	}

}

