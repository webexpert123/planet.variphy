(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 // Ajax action to clear buddypress hashtags.
	 $( document ).on( 'click', '.bpht-clear-bp-hashtags', function (e) {
	 	e.preventDefault();
	 	var clickd_obj = $(this);
	 	var clickd_txt = $('.bpht-clear-bp-hashtags').text();
	 	clickd_obj.text( bpht_ajax_obj.wait_text );
 		var data = {
 			'action': 'bpht_clear_buddypress_hashtag_table',
 			'ajax_nonce': bpht_ajax_obj.ajax_nonce
 		};
 		$.post( bpht_ajax_obj.ajax_url, data, function ( response ) {
 			clickd_obj.text( clickd_txt );
 		} );
	 });
	 
	 // Ajax request to clear bbpress hashtags.
	 $( document ).on( 'click', '.bpht-clear-bbpress-hashtags', function (e) {
	 	e.preventDefault();
	 	var clickd_obj = $(this);
	 	var clickd_txt = $('.bpht-clear-bbpress-hashtags').text();
	 	clickd_obj.text( bpht_ajax_obj.wait_text );
 		var data = {
 			'action': 'bpht_clear_bbpress_hashtag_table',
 			'ajax_nonce': bpht_ajax_obj.ajax_nonce
 		};
 		$.post( bpht_ajax_obj.ajax_url, data, function ( response ) {
 			clickd_obj.text( clickd_txt );
 		} );
	 });

	 // Ajax request to clear post hashtags.
	 $( document ).on( 'click', '.bpht-clear-post-hashtags', function (e) {
	 	e.preventDefault();
	 	var clickd_obj = $(this);
	 	var clickd_txt = $('.bpht-clear-post-hashtags').text();
	 	clickd_obj.text( bpht_ajax_obj.wait_text );
 		var data = {
 			'action': 'bpht_clear_post_hashtag_table',
 			'ajax_nonce': bpht_ajax_obj.ajax_nonce
 		};
 		$.post( bpht_ajax_obj.ajax_url, data, function ( response ) {
 			clickd_obj.text( clickd_txt );
 		} );
	 });

	 // Ajax request to clear page hashtags.
	 $( document ).on( 'click', '.bpht-clear-page-hashtags', function (e) {
	 	e.preventDefault();
	 	var clickd_obj = $(this);
	 	var clickd_txt = $('.bpht-clear-page-hashtags').text();
	 	clickd_obj.text( bpht_ajax_obj.wait_text );
 		var data = {
 			'action': 'bpht_clear_page_hashtag_table',
 			'ajax_nonce': bpht_ajax_obj.ajax_nonce
 		};
 		$.post( bpht_ajax_obj.ajax_url, data, function ( response ) {
 			clickd_obj.text( clickd_txt );
 		} );
	 });

	 $( document ).on(
	 	'click', '.allow_non_an_ht', function(){
 		$(".bpht-lengths-row").animate({
 			height: 'toggle'
 		});
	 });

	 $(function() {
	 	var blpro_elmt = document.getElementsByClassName( "blpro-accordion" );
	 	var k;
	 	var blpro_elmt_len = blpro_elmt.length;
	 	for (k = 0; k < blpro_elmt_len; k++) {
	 		blpro_elmt[k].onclick = function() {
	 			this.classList.toggle( "active" );
	 			var panel = this.nextElementSibling;
	 			if (panel.style.maxHeight) {
	 				panel.style.maxHeight = null;
	 			} else {
	 				panel.style.maxHeight = panel.scrollHeight + "px";
	 			}
	 		}
	 	}
	 });
	 
	$( document ).on( 'click', '.hashtag-delete', function (e) {
		e.preventDefault();
		var id = $( this ).data( 'id' );
		var name = $( this ).data( 'name' );
		var type = $( this ).data( 'type' );
		
		var data = {
 			'action' : 'bpht_delete_hashtag',
			'id' : id,
			'name' : name,
			'type' : type,
 			'ajax_nonce' : bpht_ajax_obj.ajax_nonce
 		};
 		$.post( bpht_ajax_obj.ajax_url, data, function ( response ) {
			$( '#buddypress-hashtags-' + id).remove();
 			location.reload(true);
 		} );
		
	});

})( jQuery );
