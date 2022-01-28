(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

	 $( document ).ready( function() {
	 	$( document ).on( 'click',  ".bpsp-pin-tool", function ( e ) {
	 		e.preventDefault();

	 		// Disable Click On Processing Verification. 
    		if ( $( this ).hasClass( 'loading' ) ) {
    			return false;
    		}

    		// Init Vars
    		var bpsp_curent_pin_btn, bpsp_pin_btn_title;
    		bpsp_curent_pin_btn = $( this );

    		// Add Loading Class.
    		bpsp_curent_pin_btn.addClass( 'loading' );

	 		var data = {
				action: 'bpsp_handle_pin_unpin_action',
				group_id: BuddyPress_Sticky_Posts.current_group,
				security: BuddyPress_Sticky_Posts.security_nonce,
				operation: $( this ).attr( 'data-action' ),
				component: BuddyPress_Sticky_Posts.current_component,
				post_id: $( this ).attr( 'data-activity-id' ),
			};

			// Process Verification.
			$.post( ajaxurl, data, function( response ) {

            	// Get Response Data.
            	var res = $.parseJSON( response );

            	if ( res.error ) {

		    		// Remove Loading Class.
		    		bpsp_curent_pin_btn.removeClass( 'loading' );

	            	// Show Error Message
	            	//$.yz_DialogMsg( 'error', res.error );

	            	return false;

	            } else if ( res.action ) {

		    		// Remove Loading Class.
		    		bpsp_curent_pin_btn.removeClass( 'loading' );

		    		// Update Button Icon & Activity Class.
		    		if ( res.action == 'pin' ) {
		    			bpsp_curent_pin_btn.find( 'i' ).removeClass( 'fa-flip-vertical');
		    			bpsp_curent_pin_btn.closest( '.activity-item' ).removeClass( 'bpsp-pinned-post' );
		    		} else if ( res.action == 'unpin' ) {
		    			bpsp_curent_pin_btn.find( 'i' ).addClass( 'fa-flip-vertical');
		    			bpsp_curent_pin_btn.closest( '.activity-item' ).addClass( 'bpsp-pinned-post' );
		    		}

					// Get Button Title.
					bpsp_pin_btn_title = ( res.action == 'pin' ) ?
					BuddyPress_Sticky_Posts.pin_post : BuddyPress_Sticky_Posts.unpin_post;

					// Update Button title.
					bpsp_curent_pin_btn.attr("data-bp-tooltip", bpsp_pin_btn_title);
					if( BuddyPress_Sticky_Posts.active_template == 'legacy' ){
						bpsp_curent_pin_btn.find( '.bpsp-txt-title' ).text( bpsp_pin_btn_title);
					}
					// Update Button Action
					bpsp_curent_pin_btn.attr( 'data-action', res.action );
					location.reload(true);
	            	// Show Error Message
	            	//$.yz_DialogMsg( 'success', res.msg );

	            	return false;
	            }

	        });
	 	});
	 });
})( jQuery );
