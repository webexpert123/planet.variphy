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
	$blpro_tab = sanitize_text_field( $_GET['tab'] );
} else {
	$blpro_tab = 'welcome';
}

bpsp_include_setting_tabs( $blpro_tab );

/**
 *
 * Function to select desired file for tab option.
 *
 * @param string $blpro_tab The current tab string.
 */
function bpsp_include_setting_tabs( $blpro_tab ) {

	switch ( $blpro_tab ) {
		case 'welcome':
			include 'bpsp-welcome-page.php';
			break;
		case 'general':
			include 'bpsp-general-setting-tab.php';
			break;
		default:
			include 'bpsp-welcome-page.php';
			break;
	}

}

