/**
 * BuddyBoss Media > Pics JavaScript functionality
 *
 * A BuddyPress plugin combining user activity feeds with media management.
 *
 * This file should load in the footer
 *
 * @author      BuddyBoss
 * @since       BuddyBoss Media 1.0, BuddyBoss Media 1.0, BuddyBoss Media Pics 1.0
 * @package     buddyboss-media
 *
 * ====================================================================
 *
 * 1. jQuery + Globals
 * 2. BuddyBoss Media Picture Grid + PhotoSwipe
 * 3. BuddyBoss Media Uploader
 */


/**
 * 1. jQuery + Globals
 * ====================================================================
 */
var jq = $ = jQuery.noConflict();

// Window.Code fallback
window.Code = window.Code || { Util: false, PhotoSwipe: false };

// Util
window.BuddyBoss_Media_Util = ( function ( window, jq, opt, undefined ) {

	var $window = jq( window );

	var Util = {
		state: opt,
		lang: function ( key ) {
			var key = key || 'undefined key!';
			return opt[key] || 'Language key missing for: ' + key;
		}
	}

	var resizeThrottle;

	// Check for mobile resolution
	function checkMobile() {
		// Set to true if not set and is mobile
		if ( ! Util.state.isMobile && $window.width() <= 800 ) {
			Util.state.isMobile = true;
		}
		// Set to false if not set
		else if ( ! Util.state.isMobile ) {
			Util.state.isMobile = false;
		}
	}
	checkMobile();

	// Check for mobile resolution on resize
	$window.on( 'resize orientationchange', function () {
		clearTimeout( resizeThrottle );
		resizeThrottle = setTimeout( checkMobile, 75 );
	} );

	return Util;

}
(
		window,
		window.jQuery,
		window.BuddyBoss_Media_Appstate || { }
) );

var initPhotoSwipeFromDOM = function(gallerySelector) {

	// parse slide data (url, title, size ...) from DOM elements
	// (children of gallerySelector)
	var parseThumbnailElements = function(el) {
		var thumbElements = jQuery(el).find('.photo-item'),
			numNodes = thumbElements.length,
			items = [],
			figureEl,
			linkEl,
			size,
			item;

		for(var i = 0; i < numNodes; i++) {

			figureEl = thumbElements[i]; // <figure> element

			// include only element nodes
			if(figureEl.nodeType !== 1) {
				continue;
			}

			linkEl = jQuery(figureEl).find('.buddyboss-media-photo-wrap'); // <a> element

			//size = linkEl.getAttribute('data-size').split('x');

			// create slide object
			item = {
				src: jQuery(linkEl).attr('href'),
				attachment_id: jQuery(linkEl).data('id'),
				act_id: jQuery(linkEl).data('activity-id'),
				w: parseInt(jQuery(linkEl).data('width'), 10),
				h: parseInt(jQuery(linkEl).data('height'), 10),
				fav_count: jQuery(linkEl).find('.buddyboss-media-photo').data('favorite-count'),
				comment_count: jQuery(linkEl).find('.buddyboss-media-photo').data('comment-count'),
				is_fav: jQuery(linkEl).find('.buddyboss-media-photo').data('bbmfav'),
				permalink: jQuery(linkEl).find('.buddyboss-media-photo').data('permalink'),
				is_owner: jQuery(linkEl).find('.buddyboss-media-photo').data('owner'),
				title: jQuery(linkEl).data('caption'),
				caption_el: jQuery(linkEl).closest('.photo-item').find('.buddyboss_media_caption'),
			};


			// var figCaptionEl = jQuery(figureEl).closest('.photo-item-wrapper').find('.buddyboss_media_caption');
			// if(figCaptionEl.length > 0) {
			// 	// <figcaption> content
			// 	if ( figCaptionEl.find('.buddyboss_media_caption_body').length ) {
			// 		item.title = figCaptionEl.find('.buddyboss_media_caption_body').html();
			// 	} else if ( figCaptionEl.find('.buddyboss_media_caption_action').length ) {
			// 		item.title = figCaptionEl.find('.buddyboss_media_caption_action').html();
			// 	}
			// }

			// if(linkEl.children.length > 0) {
			// 	// <img> thumbnail element, retrieving thumbnail url
			// 	item.msrc = linkEl.children[0].getAttribute('src');
			// }

			item.el = figureEl; // save link to element for getThumbBoundsFn
			items.push(item);
		}

		return items;
	};

	// triggers when user clicks on thumbnail
	var onThumbnailsClick = function(e) {
		e = e || window.event;
		e.preventDefault ? e.preventDefault() : e.returnValue = false;

		var eTarget = e.target || e.srcElement;

		if(!jQuery(eTarget).closest('.buddyboss-media-photo-wrap').length) {
			return;
		}

		// find root element of slide
		var clickedListItem = jQuery(eTarget).closest('.buddyboss-media-photo-wrap');

		// find index of clicked item by looping through all child nodes
		// alternatively, you may define index via data- attribute
		var clickedGallery = clickedListItem.closest(gallerySelector),
			childNodes = clickedListItem.closest(gallerySelector).find('.buddyboss-media-photo-wrap'),
			numChildNodes = childNodes.length,
			nodeIndex = 0,
			index;

		for (var i = 0; i < numChildNodes; i++) {
			if(childNodes[i].nodeType !== 1) {
				continue;
			}

			if(jQuery(childNodes[i]).data('id') == clickedListItem.data('id')) {
				index = nodeIndex;
				break;
			}
			nodeIndex++;
		}



		if(index >= 0) {
			// open PhotoSwipe if valid index found
			openPhotoSwipe( index, clickedGallery );
		}
		return false;
	};

	var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
		var pswpElement = document.querySelectorAll('.pswp')[0],
			gallery,
			options,
			items;

		items = parseThumbnailElements(galleryElement);

		// define options (if needed)
		options = {

			// define gallery index (for URL)
			galleryUID: jQuery(galleryElement).data('pswp-uid'),

			shareButtons: [
				{id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
			],

			// Function builds caption markup
			addCaptionHTMLFn: function(item, captionEl, isFake) {
				// item      - slide object
				// captionEl - caption DOM element
				// isFake    - true when content is added to fake caption container
				//             (used to get size of next or previous caption)

				if(!item.caption_el) {
					captionEl.children[0].innerHTML = '';
					return false;
				}

				$caption = item.caption_el;
				$comment = $caption.find( '.buddyboss_media_caption_body' );

				$comment.find( 'a' ).remove();

				current_photo_activity_text = $comment.text();

				// Replace all whitespace and check for contents, if empty fallback to upload date
				if ( current_photo_activity_text.replace( /\s/g, '' ) === '' ) {
					current_photo_activity_text = $caption.find( '.buddyboss_media_caption_action' ).text();
				}

				captionEl.children[0].innerHTML = '<a href="'+item.permalink+'">' + current_photo_activity_text + '</a>';
				return true;
			},

			// getThumbBoundsFn: function(index) {
			// 	// See Options -> getThumbBoundsFn section of documentation for more info
			// 	var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
			// 		pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
			// 		rect = thumbnail.getBoundingClientRect();
			//
			// 	return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
			// }

		};

		// PhotoSwipe opened from URL
		if(fromURL) {
			if(options.galleryPIDs) {
				// parse real index when custom PIDs are used
				// http://photoswipe.com/documentation/faq.html#custom-pid-in-url
				for(var j = 0; j < items.length; j++) {
					if(items[j].pid == index) {
						options.index = j;
						break;
					}
				}
			} else {
				// in URL indexes start from 1
				options.index = parseInt(index, 10) - 1;
			}
		} else {
			options.index = parseInt(index, 10);
		}

		// exit if index not found
		if( isNaN(options.index) ) {
			return;
		}

		if(disableAnimation) {
			options.showAnimationDuration = 0;
		}

		// Pass data to PhotoSwipe and initialize it
		gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
		gallery.init();

		// Image loaded
		gallery.listen('imageLoadComplete', function(index, item) {
			if ( typeof item.fav_count !== 'undefined' ) {
				jq(pswpElement).find('.ps-favorite-count').text(item.fav_count);
			}
			if( typeof item.comment_count !== 'undefined' ) {
				jq(pswpElement).find('.ps-comment-count').text(item.comment_count);
			}
			if( typeof item.permalink !== 'undefined' ) {
				jq(pswpElement).find('.pswp__button--comment').data('permalink',item.permalink);
			}
			if ( typeof item.is_fav !== 'undefined' ) {
				jq(pswpElement).find('.pswp__button--favorite').addClass(item.is_fav);

				if ( item.is_fav == 'unfav' ) {
					jq(pswpElement).find('.pswp__button--favorite').attr( 'title', BP_DTheme.mark_as_fav );
				} else {
					jq(pswpElement).find('.pswp__button--favorite').attr( 'title', BP_DTheme.remove_fav );
				}
			}
			if ( typeof item.is_owner !== 'undefined' && item.is_owner == '0' ) {
				jq(pswpElement).find('.pswp__button--delete').hide();
			} else if ( typeof item.is_owner !== 'undefined' && item.is_owner == '1' ) {
				jq(pswpElement).find('.pswp__button--delete').show();
			}
		});

		gallery.listen('destroy', function() {
			jq(pswpElement).find('.ps-favorite-count').text('');
			jq(pswpElement).find('.ps-comment-count').text('');
			jq(pswpElement).find('.pswp__button--comment').data('permalink','');
			jq(pswpElement).find('.pswp__button--favorite').removeClass('bbm-fav').removeClass('bbm-unfav');
			jq(pswpElement).find('.pswp__button--delete').show();
		});

		jq(pswpElement).on('click','.pswp__button--comment',function(e) {
			e.preventDefault();

			if ( typeof gallery.currItem.permalink !== 'undefined' && gallery.currItem.permalink != '' ) {
				window.location.href = gallery.currItem.permalink+'?mcomment='+gallery.currItem.act_id;
			}
		});

		jq(pswpElement).on('click','.pswp__button--delete',function(e) {
			e.preventDefault();

			if ( typeof gallery.currItem.is_owner === 'undefined') {
				return false;
			}

			if ( gallery.currItem.is_owner == '1' ) {

				if ( jq(pswpElement).find('.pswp__button--delete').hasClass('deleting') ) {
					return false;
				}

				if ( confirm( BuddyBoss_Media_Appstate.sure_delete_photo ) ) {
					jq(pswpElement).find('.pswp__button--delete').addClass('deleting');
					jq.ajax({
						type: "POST",
						url: ajaxurl,
						data: {
							'action': 'buddyboss_delete_media',
							'media': gallery.currItem.act_id,
							'photo-id': gallery.currItem.attachment_id,
						},
						success: function(response){
							window.location.reload();
							return false;
							// jq('.buddyboss-media-photo-wrap[data-id="'+gallery.currItem.attachment_id+'"]').closest('.photo-item-wrapper').remove();
							// gallery.next();
							// jq(pswpElement).find('.pswp__button--delete').removeClass('deleting');
						},
						error: function( error ) {
							jq(pswpElement).find('.pswp__button--delete').removeClass('deleting');
						}
					});
				}
			}
		});

		jq(pswpElement).on('click','.pswp__button--favorite',function(e){
			e.preventDefault();

			if ( jq(pswpElement).find('.pswp__button--favorite').hasClass('marking') ) {
				return false;
			}

			jq(pswpElement).find('.pswp__button--favorite').addClass('marking');

			var type = 'fav';

			if ( typeof gallery.currItem.is_fav !== 'undefined' && gallery.currItem.is_fav == 'bbm-unfav' ) {
				type = 'unfav';
			}

			jq.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'bbm_activity_mark_' + type,
					id: gallery.currItem.act_id,
					item_type: 'bbm_activity_mark'
				},
				dataType: 'json',
				success: function(response){
					jq(pswpElement).find('.pswp__button--favorite').removeClass('bbm-fav').removeClass('bbm-unfav');
					if ( type == 'unfav' ) {
						jq(pswpElement).find('.pswp__button--favorite').addClass('bbm-unfav');
						jq(pswpElement).find('.pswp__button--favorite').attr( 'title', BP_DTheme.mark_as_fav );
						gallery.currItem.is_fav = 'bbm-fav';
					} else {
						jq(pswpElement).find('.pswp__button--favorite').addClass('bbm-fav');
						jq(pswpElement).find('.pswp__button--favorite').attr( 'title', BP_DTheme.remove_fav );
						gallery.currItem.is_fav = 'bbm-unfav';
					}
					if ( typeof response.count !== 'undefined' ) {
						jq(pswpElement).find('.ps-favorite-count').text(response.count);
					}
					jq(pswpElement).find('.pswp__button--favorite').removeClass('marking');
				},
				error: function( error ) {
					jq(pswpElement).find('.pswp__button--favorite').removeClass('marking');
				}
			});
		});
	};

	// loop through all gallery elements and bind events
	var galleryElements = document.querySelectorAll( gallerySelector );

	for(var i = 0, l = galleryElements.length; i < l; i++) {
		galleryElements[i].setAttribute('data-pswp-uid', i+1);
		galleryElements[i].onclick = onThumbnailsClick;
	}

	if ( galleryElements.length ) {
		jq( document ).ajaxSuccess( photoSwipeAjaxSuccessHandler );
	}
};

if ( typeof PhotoSwipe !== 'undefined' ) {
	initPhotoSwipeFromDOM('.bbmedia-grid-wrapper');
	initPhotoSwipeFromDOM('#bbmedia-grid-wrapper');
}

function photoSwipeAjaxSuccessHandler( e, xhr, options ) {

	var action = bbmedia_getQueryVariable( options.data, 'action' );
	var resetCallback = function ( action ) {
		return function () {
			var reset_action = [ 'activity_get_older_updates', 'post_update', 'new_activity_comment', 'delete_activity_comment', 'activity_widget_filter', 'get_single_activity_content' ];

			if ( -1 !== reset_action.indexOf( action ) ) {

				initPhotoSwipeFromDOM('.bbmedia-grid-wrapper');
				initPhotoSwipeFromDOM('#bbmedia-grid-wrapper');
			}
		}
	}( action );

	// Most BuddyPress animations finish after 200ms
	window.setTimeout( resetCallback, 205 );

	// Perform again once after a longer delay just in case
	// @TODO: Get a dom observer
	window.setTimeout( resetCallback, 750 );
}

/**
 * 3. BuddyBoss Media Uploader
 * ====================================================================
 * @returns {object} BuddyBoss_Media_Uploader
 *
 * window.BuddyBoss_Media_Uploader = {
 *   /.../
 * }
 */

window.BuddyBoss_Media_Uploader = ( function ( window, jq, util, undefined ) {

	var uploader = false;

	var _l = { },
			filesAdded = 0;

	var state = util.state || { },
			lang = util.lang;

	var pics_uploaded = [ ];

	var APP = {
		/**
		 * Startup
		 *
		 * @return {void}
		 */
		init: function () {

			var self = this;

			this.inject_markup();

			if ( ! this.get_elements() ) {
				return false;
			}

			this.setup_modal();
			this.setup_textbox();

			setTimeout( function () {
				self.start_uploader();
			}, 10 );

			jq.ajaxPrefilter( APP.prefilter );
			jq( document ).ajaxSuccess( APP.ajaxSuccessHandler );
		},
		/**
		 * Would handle teardowns if AJAX was implemented for page
		 * navigations.
		 *
		 * @return {void}
		 */
		destroy: function () {
			// this.destroy_button();
		},

		/**
		 * Update Photo count after ajax activity post update
		 * @param e
		 * @param xhr
		 * @param options
		 */
		ajaxSuccessHandler: function( e, xhr, options ) {

			var action = bbmedia_getQueryVariable( options.data, 'action' );

			if( 'post_update' == action ) {
				var slug = BBOSS_MEDIA.photo_component_slug;

				var data = {
					action: 'bbm_photo_counts'
				};

				jq.ajax({
					type: 'GET',
					url: ajaxurl,
					data: data,
					success: function (response) {
						if ( response.success ) {
							var $photo_cnt_span = jq('#'+slug+'-personal-li #user-'+slug+' span');
							if( $photo_cnt_span.length > 0 ) {
								$photo_cnt_span.text(response.data);
							}
						}
					}
				});
			}
		},

		/**
		 * Dynamically inject markup, this avoids relying on BuddyPress
		 * templating and helps handle plugin conflicts
		 *
		 * @return {void}
		 */
		inject_markup: function () {
			// Activity greeting on user photo "What's new, %firstname%"
			var $activity_greeting = jq( '.my-gallery .activity-greeting' ),
					greeting = lang( 'user_add_photo' );

			if ( $activity_greeting.length && ! ! greeting ) {
				$activity_greeting.text( greeting ).show();
			}

			// For our add photo, progress and preview area we rely
			// on #what-new-content
			var $whats_new_content = jq( '#whats-new-content' );

			// Add photo button + progress area
			var $add_photo = jq( '#buddyboss-media-tpl-add-photo' );

			if ( $add_photo.length && $whats_new_content.length ) {
				$whats_new_content.before( $add_photo.html() );
			}

			// Add photo preview pane
			var $preview_pane = jq( '#buddyboss-media-tpl-preview' );

			if ( $preview_pane.length && $whats_new_content.length && 'undefined' == typeof bbpress_media ) {
				$whats_new_content.find( 'textarea' ).after( $preview_pane.html() );
			}
		},
		/**
		 * Get DOM elements we'll need
		 *
		 * @return {boolean} True if we have the required elements
		 */
		get_elements: function () {
			_l.$whats_new = jq( '#whats-new' );

			if ( _l.$whats_new.length === 0 && 'undefined' == typeof bbpress_media ) {
				return false;
			}

			_l.$add_photo = jq( '#buddyboss-media-bulk-uploader' );
			_l.$open_uploder_button = jq( '#buddyboss-media-open-uploader-button' );
			_l.$add_photo_button = jq( '#logo-file-browser-button' );
			_l.$post_button = jq( '#whats-new-submit' ).find( '[type=submit],button' );
			_l.$uploader = jq( '#buddyboss-media-bulk-uploader' );
			_l.$uploaded = jq( '#buddyboss-media-bulk-uploader-uploaded .images' );
			_l.$preview_pane = jq( '#buddyboss-media-preview-inner' );
            _l.$privacy_bp = jq('#bbwall-activity-privacy');
            _l.$privacy_modal = jq('#bbm-media-privacy');

			return true;
		},
		/**
		 * Magic. BuddyPress disables the post button when there aren't any
		 * characters in the post box. Since we want to allow users to upload
		 * photos as status updates, we get around disabling the post button
		 * with a timer.
		 *
		 * @return {void}
		 */
		setup_textbox: function () {
			_l.$whats_new.blur( function () {
				setTimeout( function () {
					if ( pics_uploaded && pics_uploaded.length > 0 ) {
						_l.$post_button.removeAttr( 'disabled' );
						_l.$post_button.prop( 'disabled', false );
					}
				}, 200 )
			} );
		},
		/**
		 * Setup fancybox
		 *
		 * @return {void}
		 */
		setup_modal: function () {
			jq("#buddyboss-media-add-photo [data-fancybox]").fancybox({
				minWidth: 500,
				beforeLoad: function () {
					jq( '#buddyboss-media-bulk-uploader-text' ).val( _l.$whats_new.val() );
					if( _l.$privacy_bp.length >0 ){
						_l.$privacy_modal.val( _l.$privacy_bp.val() );
					}
					_l.$preview_pane.hide();
				},
				beforeClose: function () {
					if ( jq( '#buddyboss-media-bulk-uploader-text' ).length > 0 ) {
						_l.$whats_new.val( jq( '#buddyboss-media-bulk-uploader-text' ).val() );
					}
					if( _l.$privacy_bp.length >0 ){
						_l.$privacy_bp.val( _l.$privacy_modal.val() );
					}
				},
				afterClose: function () {
					_l.$preview_pane.html(_l.$uploaded.html());
					_l.$preview_pane.show();
				}
			});

			jq( '#aw-whats-new-submit-bbmedia' ).click( function () {
				jq.fancybox.close();
				_l.$post_button.trigger( 'click' );
			} );
		},
		/**
		 * We use jQuery's Ajax.preFilter hook to add picture related
		 * uploads to new status update's when needed. Be wary of the
		 * dragons.
		 *
		 * @param  {object} options      jQuery ajax options that are sending
		 * @param  {object} origOptions  Original jQuery ajax options
		 * @param  {object} jqXHR        jQuery XHR object
		 * @return {void}
		 */
		prefilter: function ( options, origOptions, jqXHR ) {

			var action = bbmedia_getQueryVariable( options.data, 'action' );

			if ( typeof action == 'undefined' || action != 'post_update' )
				return;

			var new_data,
					pic_html = '';

			if ( pics_uploaded.length > 0 ) {
				for ( var i = 0; i < pics_uploaded.length; i ++ ) {
					var pic = jq( '<a/>' )
							.attr( 'href', pics_uploaded[i].url )
							.attr( 'target', '_blank' )
							.attr( 'title', pics_uploaded[i].name )
							.addClass( 'buddyboss-media-photo-link' )
							.html( pics_uploaded[i].name )[0].outerHTML;

					pic_html += pic;
				}

				new_data = jq.extend( { }, origOptions.data, {
					content: origOptions.data.content + ' ' + pic_html,
					pics_uploaded: pics_uploaded
				} );

				options.data = jq.param( new_data );

				options.success = ( function ( old_success ) {

					return function ( response, txt, xhr ) {
						if ( jq.isFunction( old_success ) ) {
							old_success( response, txt, xhr );
						}

						if ( response[0] + response[1] !== '-1' ) {
							APP.post_success( response, txt, xhr );
						}
					}
				} )( options.success );
			}
			else if ( origOptions.data && origOptions.data.action === 'get_single_activity_content' ) {
				options.success = ( function ( old_success ) {
					return function ( response, txt, xhr ) {
						if ( jq.isFunction( old_success ) ) {
							old_success( response, txt, xhr );
						}

						if ( response[0] + response[1] !== '-1' ) {
							APP.readmore_success( response, txt, xhr );
						}
					}
				} )( options.success );
			}
		},
		/**
		 * This callback fires after a photo was posted as part of
		 * an activity update, we'll animate the preview closed
		 * and reset.
		 *
		 * @param  {object} response Ajax response
		 * @return {void}
		 */
		post_success: function ( response ) {

			/* BuddyBoss: If we're using pics, we need to attach PhotoSwipe */
			var $new = jq( "li.new-update" ).find( '.buddyboss-media-photo-wrap' );
			if ( $new.length > 0 && typeof BuddyBossSwiper == 'object'
					&& BuddyBossSwiper.hasOwnProperty( 'reset' ) ) {
				BuddyBossSwiper.reset();
			}

			/* reset everything upload related */
			pics_uploaded = [ ];
			_l.$preview_pane.html( '' );
			_l.$uploaded.html( '' );
			uploader.splice( 0, uploader.files.length );
			filesAdded = 0;
		},
		/**
		 * Handles upload, upload progress and previewing pics
		 *
		 * @return {void}
		 */
		start_uploader: function () {
			var $progressBar, progressPercent = 0;

			//var uploader_state = 'closed';
			var ieMobile = navigator.userAgent.indexOf( 'IEMobile' ) !== - 1;

			// IE mobile
			if ( ieMobile ) {
				_l.$add_photo.addClass( 'legacy' );
			}

			uploader = new plupload.Uploader( {
				runtimes: state.uploader_runtimes || 'html5,flash,silverlight,html4',
				browse_button: _l.$add_photo_button[0],
				container: 'buddyboss-media-bulk-uploader-reception',
				drop_element: 'buddyboss-media-bulk-uploader-wrapper',
				multi_selection: state.uploader_multiselect || false,
				url: ajaxurl,
				multipart: true,
				multipart_params: {
					action: 'buddyboss_media_post_photo',
					'cookie': encodeURIComponent( document.cookie ),
					'_wpnonce_post_update': BBOSS_MEDIA.media_upload_nonce
				},
				flash_swf_url: state.uploader_swf_url || '',
				silverlight_xap_url: state.uploader_xap_url || '',
				filters: {
					max_file_size: state.uploader_filesize || '10mb',
					mime_types : state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					title: lang( 'file_browse_title' ),
					//extensions: state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					//prevent_duplicates: true
				},
				init: {
					Init: function () {
						if ( _l.$add_photo.find( '.moxie-shim' ).find( "input" ).length == '0' ) {
							_l.$add_photo.find( '.moxie-shim' ).first().css( "z-index", 10 );
							_l.$add_photo.find( '.moxie-shim' ).css( "cursor", 'pointer' );
						} else {
							clone = jq( _l.$add_photo_button[0] ).clone();
							jq( _l.$add_photo_button[0] ).after( clone ).remove();
							_l.$add_photo_button[0] = clone;
							jq( _l.$add_photo_button[0] ).on( "click", function () {
								_l.$add_photo.find( '.moxie-shim' ).find( "input" ).click();
							} );
						}
					},
					FilesAdded: function ( up, files ) {

						jq('#aw-whats-new-submit-bbmedia').prop('disabled', true);

						if ( up.files.length > state.uploader_max_files || files.length > state.uploader_max_files ) {
							uploader.splice( filesAdded, uploader.files.length );

							alert( lang( 'exceed_max_files_per_batch' ) );
							return false;
						}

						for ( var i = 0; i < files.length; i ++ ) {
							if ( jq( 'div[data-fileid="' + files[i].id + '"]' ).length === 0 ) {
								var newimg = "<div data-fileid='" + files[i].id + "' class='file uploading'><img src='" + state.uploader_temp_img + "'><progress class='buddyboss-media-progress-bar' value='0' max='100'></progress></div>";
								_l.$uploaded.append( newimg );
								_l.$preview_pane.append( newimg );
								filesAdded ++;
							}
						}


						//jq.fancybox.update();
						up.start();
					},
					UploadProgress: function ( up, file ) {

						if ( file && file.hasOwnProperty( 'percent' ) ) {
							$progressBar = jq( 'div[data-fileid="' + file.id + '"]' ).find( 'progress' );
							progressPercent = file.percent;
							$progressBar.val( progressPercent );
						}
					},
					FileUploaded: function ( up, file, info ) {

						jq('#aw-whats-new-submit-bbmedia').prop('disabled', false);

						var responseJSON = jq.parseJSON( info.response );
						//console.log('// ----- upload response ----- //');
						//console.log(up,file,info,responseJSON);
						$file = jq( 'div[data-fileid="' + file.id + '"]' );
						$file.removeClass( 'uploading' );

						if( 'undefined' != typeof bbpress_media ) {
							$file.append("<input type='hidden' value='"+ responseJSON.attachment_id +"' name='bbm_bbpress_attachments[]'>");
						}

						$file.data( 'attachment_id', responseJSON.attachment_id );
						$file.find( '>img' ).attr( 'src', responseJSON.url );

						$file.find( 'progress' ).replaceWith(
								"<a href='#' onclick='return window.BuddyBoss_Media_Uploader.removeUploaded(\"" + file.id + "\");' class='delete'>+</a>"
								);

						pics_uploaded.push( responseJSON );
						//jq.fancybox.update();
					},
					Error: function ( up, args ) {
						jq('#aw-whats-new-submit-bbmedia').prop('disabled', false);
						alert( lang( 'error_uploading_photo' ) );

						$progressWrap.removeClass( 'uploading' );
						$postButton.prop( "disabled", false ).removeClass( 'loading' );

						//uploader_state = 'closed';
					}
				}
			} );

			uploader.init();

			jq('.fancybox-close').on('click',function() {
                   jq('#aw-whats-new-submit-bbmedia').prop('disabled', false);
                   // images display in background when fancybox is closed
			});

			if ( jq( '#buddyboss-media-bulk-uploader-reception-fake' ).length > 0 ) {
				var additional_dropzone = document.getElementById( 'buddyboss-media-bulk-uploader-reception-fake' );
				$additional_dropzone = jq( additional_dropzone );

				var dropzone = new moxie.file.FileDrop( {
					drop_zone: additional_dropzone
				} );

				dropzone.ondrop = function ( event ) {
					_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( dropzone.files );
				};

				dropzone.init();

				// -- Configure FILEINPUT -- //

				var input = new moxie.file.FileInput( {
					browse_button: jq( "a.browse-file-button", $additional_dropzone )[ 0 ],
					container: additional_dropzone,
					multiple: true
				} );

				input.onchange = function ( event ) {
					_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( input.files );
				};

				input.init();

			}

			//Show upload box when clicking on Add Photo button INSTEAD of opening lightbox
			if ( jq( '#buddyboss-media-add-photo' ).length > 0 && 'false' === BBOSS_MEDIA.is_media_page && 'false' === BBOSS_MEDIA.is_photo_page ) {

				var additional_dropzone = document.getElementById( 'buddyboss-media-add-photo' );
				$additional_dropzone = jq( additional_dropzone );

				// -- Configure FILEINPUT -- //

				var input = new moxie.file.FileInput( {
					browse_button: jq( ".browse-file-button" )[ 0 ],
					container: additional_dropzone,
					multiple: true
				} );

				input.onchange = function ( event ) {
					//_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( input.files );
				};

				input.init();
			}

		}, // start_uploader();

		removeUploaded: function ( fileid ) {
			/* remove from upload files list */
			var $file = jq( 'div[data-fileid="' + fileid + '"]' );
			if ( pics_uploaded.length > 0 ) {
				var pics_uploaded_temp = [ ];
				for ( var i = 0; i < pics_uploaded.length; i ++ ) {
					if ( pics_uploaded[i].attachment_id !== $file.data( 'attachment_id' ) ) {
						pics_uploaded_temp.push( pics_uploaded[i] );
					}
				}

				pics_uploaded = pics_uploaded_temp;
			}

			var file_to_remove = false;
			/* remove from plupload queue */
			jq.each( uploader.files, function ( i, ufile ) {
				if ( ufile.hasOwnProperty( 'id' ) && ufile.id == fileid ) {
					file_to_remove = ufile;
				}
			} );

			if ( file_to_remove ) {
				uploader.removeFile( file_to_remove );

				// Permanently delete media attachment
				var media = new wp.api.models.Media( { id: $file.data('attachment_id') } );
				media.requireForceForDelete = true;
				media.destroy();

				filesAdded --;
			}

			/* delete html */
			$file.remove();
			return false;
		},


	} // APP


	var API = {
		setup: function () {
			APP.init();
		},
		teardown: function () {
			APP.destroy();
		},
		removeUploaded: function ( file ) {
			return APP.removeUploaded( file );
		},
	} // API

	jq( document ).ready( function () {
		APP.init();
	} );

	return API;
}
(
		window,
		window.jQuery,
		window.BuddyBoss_Media_Util
		) );

/**
 * 3. BuddyBoss Media Uploader
 * ====================================================================
 * @returns {object} BuddyBoss_Media_Uploader
 *
 * window.BuddyBoss_Media_Uploader = {
 *   /.../
 * }
 */

window.BuddyBoss_Edit_Media_Uploader = ( function ( window, jq, util, undefined ) {

    var uploader = false;

    var _el = { },
        filesAdded = 0;

    var state = util.state || { },
        lang = util.lang;

    var pics_uploaded = [ ];

    var APP = {
        /**
         * Startup
         *
         * @return {void}
         */
        init: function () {

            var self = this;

            if ( ! this.get_elements() ) {
                return false;
            }

            setTimeout( function () {
                self.start_uploader();
            }, 10 );

            jq.ajaxPrefilter( APP.prefilter );
            jq( document ).ajaxSuccess( APP.ajaxSuccessHandler );

            //Edit activity btn click callback
            jq( document ).on( 'click', 'a.buddyboss_edit_activity.action-edit, a.buddyboss_edit_activity_comment', APP.editActivityMediaPreview );
        },
        /**
         * Would handle teardowns if AJAX was implemented for page
         * navigations.
         *
         * @return {void}
         */
        destroy: function () {
            // this.destroy_button();
        },

        /**
         * We use jQuery's Ajax.preFilter hook to add picture related
         * uploads to new status update's when needed. Be wary of the
         * dragons.
         *
         * @param  {object} options      jQuery ajax options that are sending
         * @param  {object} origOptions  Original jQuery ajax options
         * @param  {object} jqXHR        jQuery XHR object
         * @return {void}
         */
        prefilter: function ( options, origOptions, jqXHR ) {

            var action = bbmedia_getQueryVariable( options.data, 'action' );

            if ( typeof action == 'undefined' || action != 'buddypress-edit-activity-save' )
                return;

            var media_ids = [];

            jq("#buddyboss-edit-media-preview .file").map(function (idx, ele) {
                media_ids[idx] = jq(ele).data('media-id');
            });

            var new_data = jq.extend( { }, origOptions.data, {
                'buddyboss_media_aid': media_ids,
            });

            options.data = jq.param( new_data );
        },



        /**
         * Get DOM elements we'll need
         *
         * @return {boolean} True if we have the required elements
         */
        get_elements: function () {

            _el.$add_photo = jq( '#buddyboss-edit-media-bulk-uploader' );
            _el.$open_uploder_button = jq( '#buddyboss-edit-media-open-uploader-button' );
            _el.$add_photo_button = jq( '#browse-file-button' );
            _el.$uploaded = jq( '#buddyboss-media-bulk-uploader-uploaded .images' );
            _el.$preview_pane = jq( '#buddyboss-edit-media-preview-inner' );

            return true;
        },

        //Conjure up media edit preview for activity edit and show in beneath the textarea
        // so user can remove it
        editActivityMediaPreview: function(e) {

        	// Cancel edit activity preview
        	if( window.keep_activity_changes ) {
        		return false;
			}

            $elm_edit_btn = jq(this);
            var activity_id = $elm_edit_btn.data('activity_id');

            var data = {
                'action': 'buddypress_edit_activity_media_content',
                'activity_id': $elm_edit_btn.data('activity_id'),
            };

            jq.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                success: function( response ) {
                    $media_edit_wrapper = jq('#buddyboss-edit-media-preview-inner');
                    $media_edit_wrapper.html( response );
                }
            });
        },

        /**
         * Handles upload, upload progress and previewing pics
         *
         * @return {void}
         */
        start_uploader: function () {
            var $progressBar, progressPercent = 0;

            //var uploader_state = 'closed';
            var ieMobile = navigator.userAgent.indexOf( 'IEMobile' ) !== - 1;

            // IE mobile
            if ( ieMobile ) {
                _el.$add_photo.addClass( 'legacy' );
            }

            uploader = new plupload.Uploader( {
				runtimes: state.uploader_runtimes || 'html5,flash,silverlight,html4',
				browse_button: _el.$add_photo_button[0],
				container: 'buddyboss-edit-media-bulk-uploader-reception',
				drop_element: 'buddyboss-edit-media-bulk-uploader-wrapper',
				multi_selection: state.uploader_multiselect || false,
				url: ajaxurl,
				multipart: true,
				multipart_params: {
					action: 'buddyboss_media_post_photo',
					'cookie': encodeURIComponent( document.cookie ),
					'_wpnonce_post_update': BBOSS_MEDIA.media_upload_nonce
				},
				flash_swf_url: state.uploader_swf_url || '',
				silverlight_xap_url: state.uploader_xap_url || '',
				filters: {
					max_file_size: state.uploader_filesize || '10mb',
					mime_types : state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					title: lang( 'file_browse_title' ),
					//extensions: state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					//prevent_duplicates: true
				},
                init: {
                    Init: function () {
                        if ( _el.$add_photo.find( '.moxie-shim' ).find( "input" ).length == '0' ) {
                            _el.$add_photo.find( '.moxie-shim' ).first().css( "z-index", 10 );
                            _el.$add_photo.find( '.moxie-shim' ).css( "cursor", 'pointer' );
                        } else {
                            clone = jq( _el.$add_photo_button[0] ).clone();
                            jq( _el.$add_photo_button[0] ).after( clone ).remove();
                            _el.$add_photo_button[0] = clone;
                            jq( _el.$add_photo_button[0] ).on( "click", function () {
                                _el.$add_photo.find( '.moxie-shim' ).find( "input" ).click();
                            } );
                        }
                    },
                    FilesAdded: function ( up, files ) {

                        jq('#aw-whats-new-submit-bbmedia').prop('disabled', true);

                        if ( up.files.length > state.uploader_max_files || files.length > state.uploader_max_files ) {
                            uploader.splice( filesAdded, uploader.files.length );

                            alert( lang( 'exceed_max_files_per_batch' ) );
                            return false;
                        }

                        for ( var i = 0; i < files.length; i ++ ) {
                            if ( jq( 'div[data-fileid="' + files[i].id + '"]' ).length === 0 ) {
                                var newimg = "<div data-fileid='" + files[i].id + "' class='file uploading'><img src='" + state.uploader_temp_img + "'><progress class='buddyboss-media-progress-bar' value='0' max='100'></progress></div>";
                                _el.$uploaded.append( newimg );
                                _el.$preview_pane.append( newimg );
                                filesAdded ++;
                            }
                        }


                        //jq.fancybox.update();
                        up.start();
                    },
                    UploadProgress: function ( up, file ) {

                        if ( file && file.hasOwnProperty( 'percent' ) ) {
                            $progressBar = jq( 'div[data-fileid="' + file.id + '"]' ).find( 'progress' );
                            progressPercent = file.percent;
                            $progressBar.val( progressPercent );
                        }
                    },
                    FileUploaded: function ( up, file, info ) {

                        jq('#aw-whats-new-submit-bbmedia').prop('disabled', false);

                        var responseJSON = jq.parseJSON( info.response );
                        //console.log('// ----- upload response ----- //');
                        //console.log(up,file,info,responseJSON);
                        $file = jq( 'div[data-fileid="' + file.id + '"]' );
                        $file.removeClass( 'uploading' );


                        $file.data( 'media-id', responseJSON.attachment_id );
                        $file.find( '>img' ).attr( 'src', responseJSON.url );

                        $file.find( 'progress' ).replaceWith(
                            "<a href='#' onclick='return window.BuddyBoss_Media_Uploader.removeUploaded(\"" + file.id + "\");' class='delete'>+</a>"
                        );

                        pics_uploaded.push( responseJSON );
                        //jq.fancybox.update();
                    },
                    Error: function ( up, args ) {
                        jq('#aw-whats-new-submit-bbmedia').prop('disabled', false);
                        alert( lang( 'error_uploading_photo' ) );

                        $progressWrap.removeClass( 'uploading' );
                        $postButton.prop( "disabled", false ).removeClass( 'loading' );

                        //uploader_state = 'closed';
                    }
                }
            } );

            uploader.init();

            jq('.fancybox-close').on('click',function() {
                jq('#aw-whats-new-submit-bbmedia').prop('disabled', false);
                // images display in background when fancybox is closed
            });

            if ( jq( '#buddyboss-media-bulk-uploader-reception-fake' ).length > 0 ) {
                var additional_dropzone = document.getElementById( 'buddyboss-media-bulk-uploader-reception-fake' );
                $additional_dropzone = jq( additional_dropzone );

                var dropzone = new moxie.file.FileDrop( {
                    drop_zone: additional_dropzone
                } );

                dropzone.ondrop = function ( event ) {
                    _el.$open_uploder_button.click();//to open modal window
                    uploader.addFile( dropzone.files );
                };

                dropzone.init();

                // -- Configure FILEINPUT -- //
                if( jq( "#buddyboss-edit-media-add-photo .browse-file-button").is(':visible') ) {
                    var input = new moxie.file.FileInput({
                        browse_button: jq("#buddyboss-edit-media-add-photo .browse-file-button", $additional_dropzone)[0],
                        container: additional_dropzone,
                        multiple: true
                    });

                    input.onchange = function (event) {
                        _el.$open_uploder_button.click();//to open modal window
                        uploader.addFile(input.files);
                    };

                    input.init();
                }

            }

            //Show upload box when clicking on Add Photo button INSTEAD of opening lightbox
            if ( jq( '#buddyboss-edit-media-add-photo a.buddyboss-media-add-photo' ).length > 0 && 'false' === BBOSS_MEDIA.is_photo_page ) {

                var additional_dropzone = document.getElementById( 'buddyboss-media-add-photo' );
                $additional_dropzone = jq( additional_dropzone );

                // -- Configure FILEINPUT -- //

                var input = new moxie.file.FileInput( {
                    browse_button: jq( "#buddyboss-edit-media-add-photo .browse-file-button" )[ 0 ],
                    container: additional_dropzone,
                    multiple: true
                } );

                input.onchange = function ( event ) {
                    //_el.$open_uploder_button.click();//to open modal window
                    uploader.addFile( input.files );
                };

                input.init();
            }

        }, // start_uploader();

        removeUploaded: function ( fileid ) {
            /* remove from upload files list */
            var $file = jq( 'div[data-fileid="' + fileid + '"]' );
            if ( pics_uploaded.length > 0 ) {
                var pics_uploaded_temp = [ ];
                for ( var i = 0; i < pics_uploaded.length; i ++ ) {
                    if ( pics_uploaded[i].attachment_id !== $file.data( 'attachment_id' ) ) {
                        pics_uploaded_temp.push( pics_uploaded[i] );
                    }
                }

                pics_uploaded = pics_uploaded_temp;
            }

            var file_to_remove = false;
            /* remove from plupload queue */
            jq.each( uploader.files, function ( i, ufile ) {
                if ( ufile.hasOwnProperty( 'id' ) && ufile.id == fileid ) {
                    file_to_remove = ufile;
                }
            } );

            if ( file_to_remove ) {
                uploader.removeFile( file_to_remove );

				// Permanently delete media attachment
				var media = new wp.api.models.Media( { id: $file.data('attachment_id') } );
				media.requireForceForDelete = true;
				media.destroy();

                filesAdded --;
            }

            /* delete html */
            $file.remove();
            return false;
        },

        //Remove single media from preview wrapper
        editActivityRemoveMedia: function( el ) {
            var $el = jq( el );
            $el.parent().remove();
        },

    } // APP


    var API = {
        setup: function () {
            APP.init();
        },
        teardown: function () {
            APP.destroy();
        },
        removeUploaded: function ( file ) {
            return APP.removeUploaded( file );
        },
        editActivityRemoveMedia: function( el ) {
            return APP.editActivityRemoveMedia( el );
        },
    } // API

    jq( document ).ready( function () {
        APP.init();
    } );

    return API;
}
(
    window,
    window.jQuery,
    window.BuddyBoss_Media_Util
) );


/**
 * Replica of BuddyBoss_Media_Uploader for comment media
 * 3.1 BuddyBoss Comment Media Uploader
 * ====================================================================
 * @returns {object} BuddyBoss_Comment_Media_Uploader
 *
 * window.BuddyBoss_Comment_Media_Uploader = {
 *   /.../
 * }
 *
 */

window.BuddyBoss_Comment_Media_Uploader = ( function ( window, jq, util, undefined ) {

	var uploader = false;

	var _cl = { },
		filesAdded = 0;

	var state = util.state || { },
		lang = util.lang;

	var pics_uploaded = [ ];

	var APP = {
		/**
		 * Startup
		 *
		 * @return {void}
		 */
		init: function () {

			var self = this;

			this.inject_markup();

			if ( ! this.get_elements() ) {
				return false;
			}

			jq( document ).on( 'click', '.buddyboss-comment-media-add-photo-button', APP.set_post_button );

			//Set plupload file browse button
			jq(document).on( 'focus', '.ac-form textarea', APP.set_file_browse_button );

			this.setup_modal();
			//this.setup_textbox();

			setTimeout( function () {
				self.start_uploader();
			}, 10 );

			jq.ajaxPrefilter( APP.prefilter );
			jq( document ).ajaxSuccess( APP.ajaxSuccessHandler );
		},
		/**
		 * Would handle teardowns if AJAX was implemented for page
		 * navigations.
		 *
		 * @return {void}
		 */
		destroy: function () {
			// this.destroy_button();
		},
		/**
		 * Dynamically inject markup, this avoids relying on BuddyPress
		 * templating and helps handle plugin conflicts
		 *
		 * @return {void}
		 */
		inject_markup: function () {

			// For our add photo, progress and preview area we rely
			// on #what-new-content
			var $ac_form_submit = jq( "input[name='ac_form_submit']:not(.has-comment-media-button)" );

			// Add photo button + progress area
			var $add_photo = jq( '#buddyboss-comment-media-tpl-add-photo' );

			if ( $add_photo.length && $ac_form_submit.length ) {
				$ac_form_submit.before( $add_photo.html() );
				$ac_form_submit.addClass('has-comment-media-button');
			}

			// Add photo preview pane
			var $preview_pane = jq( '#buddyboss-comment-media-tpl-preview' );

			if ( $preview_pane.length && $ac_form_submit.length  ) {
				var $ac_reply_content = jq('.ac-reply-content:not(.has-comment-media-button)');
				$ac_reply_content.after( $preview_pane.html() );
				$ac_reply_content.addClass('has-comment-media-button');
			}
		},
		/**
		 * Get DOM elements we'll need
		 *
		 * @return {boolean} True if we have the required elements
		 */
		get_elements: function () {

			_cl.$comment_input = null;
			_cl.$browse_file_button = null;
			_cl.$comment_media_container = null;
			_cl.$add_photo = jq( '#buddyboss-comment-media-bulk-uploader' );
			_cl.$open_uploder_button = jq( '.open-uploader-button.buddyboss-comment-media-add-photo-button' );
			_cl.$add_photo_button = jq( '.logo-comment-file-browser-button' );
			_cl.$post_button = null;
			_cl.$uploader = jq( '#buddyboss-comment-media-bulk-uploader' );
			_cl.$uploaded = jq( '.buddyboss-comment-media-bulk-uploader-uploaded .images' );
			_cl.$preview_pane = jq( '.buddyboss-comment-media-preview-inner' );

			return true;
		},
		/**
		 * Setup fancybox
		 *
		 * @return {void}
		 */
		setup_modal: function () {
			jq(".buddyboss-comment-media-add-photo [data-fancybox]").each(function(){
				var _this = jq(this);
				_this.fancybox( {
					minWidth: 500,
					beforeLoad: function () {
						$attach_comment_media_button 	= _this.parents('.ac-reply-content');
						_cl.$post_button 				= $attach_comment_media_button.find('input[name=ac_form_submit]');
						_cl.$comment_input 				= $attach_comment_media_button.find('textarea');
						jq( '.buddyboss-comment-media-bulk-uploader-text' ).val( _cl.$comment_input.val() );
					},
					beforeClose: function () {
						if ( jq( '.buddyboss-comment-media-bulk-uploader-text' ).length > 0 ) {
							_cl.$comment_input.val( jq( '.buddyboss-comment-media-bulk-uploader-text' ).val() );
						}
					},
					afterClose: function () {
						_cl.$preview_pane.html(_cl.$uploaded.html());
						_cl.$preview_pane.show();
					}
				} );
			});


			jq( '#buddyboss-comment-media-attach' ).click( function () {
				jq.fancybox.close();
				_cl.$post_button.trigger( 'click' );
			} );
		},

		/**
		 * Set Current form Post button and Textarea
		 * @param e
		 */
		set_post_button: function( e ) {
			$attach_comment_media_button 	= jq( this ).parents('.ac-reply-content');
			_cl.$post_button 				= $attach_comment_media_button.find('input[name=ac_form_submit]');
			_cl.$comment_input 				= $attach_comment_media_button.find('textarea');
		},

		//Show upload box when clicking on Add Photo button INSTEAD of opening lightbox
		set_file_browse_button: function(e) {

			$acomment_reply_button = jq(this);

			setTimeout(function(){
				$activity_comment_div = $acomment_reply_button.parents('.activity-content').next('.activity-comments');

				if ( 0 === $activity_comment_div.length ) {
					$activity_comment_div	= $acomment_reply_button.parent().next();
				}

				_cl.$browse_file_button			= $activity_comment_div.find('.browse-file-button');
				_cl.$comment_media_container	= $activity_comment_div.find('.buddyboss-comment-media-add-photo');

				// -- Configure FILEINPUT -- //

				var input = new moxie.file.FileInput( {
					browse_button: _cl.$browse_file_button.get(0),
					container:  _cl.$comment_media_container.get(0),
					multiple: true
				} );

				input.onchange = function ( event ) {
					//_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( input.files );
				};

				input.init();
			}, 1000 );

		},

		/**
		 * We use jQuery's Ajax.preFilter hook to add picture related
		 * uploads to new status update's when needed. Be wary of the
		 * dragons.
		 *
		 * @param  {object} options      jQuery ajax options that are sending
		 * @param  {object} origOptions  Original jQuery ajax options
		 * @param  {object} jqXHR        jQuery XHR object
		 * @return {void}
		 */
		prefilter: function ( options, origOptions, jqXHR ) {

			var action = bbmedia_getQueryVariable( options.data, 'action' );

			if ( typeof action == 'undefined' || action != 'new_activity_comment' )
				return;

			var new_data,
				pic_html = '';

			if ( pics_uploaded.length > 0 ) {
				for ( var i = 0; i < pics_uploaded.length; i ++ ) {
					var pic = jq( '<a/>' )
						.attr( 'href', pics_uploaded[i].url )
						.attr( 'target', '_blank' )
						.attr( 'title', pics_uploaded[i].name )
						.addClass( 'buddyboss-media-photo-link' )
						.html( pics_uploaded[i].name )[0].outerHTML;

					pic_html += pic;
				}

				new_data = jq.extend( { }, origOptions.data, {
					content: origOptions.data.content + ' ' + pic_html,
					pics_uploaded: pics_uploaded
				} );

				options.data = jq.param( new_data );

				options.success = ( function ( old_success ) {

					return function ( response, txt, xhr ) {
						if ( jq.isFunction( old_success ) ) {
							old_success( response, txt, xhr );
						}

						if ( response[0] + response[1] !== '-1' ) {
							APP.post_success( response, txt, xhr );
						}
					}
				} )( options.success );
			}
			else if ( origOptions.data && origOptions.data.action === 'get_single_activity_content' ) {
				options.success = ( function ( old_success ) {
					return function ( response, txt, xhr ) {
						if ( jq.isFunction( old_success ) ) {
							old_success( response, txt, xhr );
						}

						if ( response[0] + response[1] !== '-1' ) {
							APP.readmore_success( response, txt, xhr );
						}
					}
				} )( options.success );
			}
		},

		/**
		 * Add Photo button in ajax loaded activity
		 * @param e
		 * @param xhr
		 * @param options
		 */
		ajaxSuccessHandler: function( e, xhr, options ) {

			var action = bbmedia_getQueryVariable( options.data, 'action' );
			var action_arr = [ 'activity_get_older_updates', 'post_update', 'activity_widget_filter' ];

			if( -1 != action_arr.indexOf( action ) ) {

				setTimeout(function () {
					APP.inject_markup();
				}, 2000 );
			}
		},
		/**
		 * This callback fires after a photo was posted as part of
		 * an activity update, we'll animate the preview closed
		 * and reset.
		 *
		 * @param  {object} response Ajax response
		 * @return {void}
		 */
		post_success: function ( response ) {

			/* BuddyBoss: If we're using pics, we need to attach PhotoSwipe */
			var $new = jq( "li.new-update" ).find( '.buddyboss-media-photo-wrap' );
			if ( $new.length > 0 && typeof BuddyBossSwiper == 'object'
				&& BuddyBossSwiper.hasOwnProperty( 'reset' ) ) {
				BuddyBossSwiper.reset();
			}

			/* reset everything upload related */
			pics_uploaded = [ ];
			_cl.$preview_pane.html( '' );
			_cl.$uploaded.html( '' );
			uploader.splice( 0, uploader.files.length );
			filesAdded = 0;
		},


		/**
		 * Handles upload, upload progress and previewing pics
		 *
		 * @return {void}
		 */
		start_uploader: function () {
			var $progressBar, progressPercent = 0;

			//var uploader_state = 'closed';
			var ieMobile = navigator.userAgent.indexOf( 'IEMobile' ) !== - 1;

			// IE mobile
			if ( ieMobile ) {
				_cl.$add_photo.addClass( 'legacy' );
			}

			uploader = new plupload.Uploader( {
				runtimes: state.uploader_runtimes || 'html5,flash,silverlight,html4',
				browse_button: _cl.$add_photo_button[0],
				container: 'buddyboss-comment-media-bulk-uploader-reception',
				drop_element: 'buddyboss-comment-media-bulk-uploader-wrapper',
				multi_selection: state.uploader_multiselect || false,
				url: ajaxurl,
				multipart: true,
				multipart_params: {
					action: 'buddyboss_media_post_photo',
					'cookie': encodeURIComponent( document.cookie ),
					'_wpnonce_post_update': BBOSS_MEDIA.media_upload_nonce
				},
				flash_swf_url: state.uploader_swf_url || '',
				silverlight_xap_url: state.uploader_xap_url || '',
				filters: {
					max_file_size: state.uploader_filesize || '10mb',
					mime_types : state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					title: lang( 'file_browse_title' ),
					//extensions: state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					//prevent_duplicates: true
				},
				init: {
					Init: function () {
						if ( _cl.$add_photo.find( '.moxie-shim' ).find( "input" ).length == '0' ) {
							_cl.$add_photo.find( '.moxie-shim' ).first().css( "z-index", 10 );
							_cl.$add_photo.find( '.moxie-shim' ).css( "cursor", 'pointer' );
						} else {
							clone = jq( _cl.$add_photo_button[0] ).clone();
							jq( _cl.$add_photo_button[0] ).after( clone ).remove();
							_cl.$add_photo_button[0] = clone;
							jq( _cl.$add_photo_button[0] ).on( "click", function () {
								_cl.$add_photo.find( '.moxie-shim' ).find( "input" ).click();
							} );
						}
					},
					FilesAdded: function ( up, files ) {

						jQuery('.has-comment-media-button').prop('disabled', true);
						jQuery('#buddyboss-comment-media-attach').prop('disabled', true);

						if ( up.files.length > state.uploader_max_files || files.length > state.uploader_max_files ) {
							uploader.splice( filesAdded, uploader.files.length );

							alert( lang( 'exceed_max_files_per_batch' ) );
							return false;
						}

						for ( var i = 0; i < files.length; i ++ ) {
							if ( jq( 'div[data-fileid="' + files[i].id + '"]' ).length === 0 ) {
								var newimg = "<div data-fileid='" + files[i].id + "' class='file uploading'><img src='" + state.uploader_temp_img + "'><progress class='buddyboss-media-progress-bar' value='0' max='100'></progress></div>";
								_cl.$uploaded.append( newimg );
								_cl.$preview_pane.append( newimg );
								filesAdded ++;
							}
						}
						//jq.fancybox.update();
						up.start();
					},
					UploadProgress: function ( up, file ) {

						if ( file && file.hasOwnProperty( 'percent' ) ) {
							$progressBar = jq( 'div[data-fileid="' + file.id + '"]' ).find( 'progress' );
							progressPercent = file.percent;
							$progressBar.val( progressPercent );
						}
					},
					FileUploaded: function ( up, file, info ) {

                                                jQuery('.has-comment-media-button').prop('disabled', false);
                                                jQuery('#buddyboss-comment-media-attach').prop('disabled', false);

						var responseJSON = jq.parseJSON( info.response );
						//console.log('// ----- upload response ----- //');
						//console.log(up,file,info,responseJSON);
						$file = jq( 'div[data-fileid="' + file.id + '"]' );
						$file.removeClass( 'uploading' );

						$file.data( 'attachment_id', responseJSON.attachment_id );
						$file.find( '>img' ).attr( 'src', responseJSON.url );

						$file.find( 'progress' ).replaceWith(
							"<a href='#' onclick='return window.BuddyBoss_Comment_Media_Uploader.removeUploaded(\"" + file.id + "\");' class='delete'>+</a>"
						);

						pics_uploaded.push( responseJSON );
						//jq.fancybox.update();
					},
					Error: function ( up, args ) {

                                                jQuery('.has-comment-media-button').prop('disabled', false);

						alert( lang( 'error_uploading_photo' ) );

						$progressWrap.removeClass( 'uploading' );
						$postButton.prop( "disabled", false ).removeClass( 'loading' );

						//uploader_state = 'closed';
					}
				}
			} );

			uploader.init();


			if ( jq( '#buddyboss-comment-media-bulk-uploader-reception-fake' ).length > 0 ) {
				var additional_dropzone = document.getElementById( 'buddyboss-media-bulk-uploader-reception-fake' );
				$additional_dropzone = jq( additional_dropzone );

				var dropzone = new moxie.file.FileDrop( {
					drop_zone: additional_dropzone
				} );

				dropzone.ondrop = function ( event ) {
					_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( dropzone.files );
				};

				dropzone.init();

				// -- Configure FILEINPUT -- //

				var input = new moxie.file.FileInput( {
					browse_button: jq( "#buddyboss-comment-media-add-photo .browse-file-button", $additional_dropzone )[ 0 ],
					container: additional_dropzone,
					multiple: true
				} );

				input.onchange = function ( event ) {
					_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( input.files );
				};

				input.init();

			}

		}, // start_uploader();


		removeUploaded: function ( fileid ) {
			/* remove from upload files list */
			var $file = jq( 'div[data-fileid="' + fileid + '"]' );
			if ( pics_uploaded.length > 0 ) {
				var pics_uploaded_temp = [ ];
				for ( var i = 0; i < pics_uploaded.length; i ++ ) {
					if ( pics_uploaded[i].attachment_id !== $file.data( 'attachment_id' ) ) {
						pics_uploaded_temp.push( pics_uploaded[i] );
					}
				}

				pics_uploaded = pics_uploaded_temp;
			}

			var file_to_remove = false;
			/* remove from plupload queue */
			jq.each( uploader.files, function ( i, ufile ) {
				if ( ufile.hasOwnProperty( 'id' ) && ufile.id == fileid ) {
					file_to_remove = ufile;
				}
			} );

			if ( file_to_remove ) {
				uploader.removeFile( file_to_remove );

				// Permanently delete media attachment
				var media = new wp.api.models.Media( { id: $file.data('attachment_id') } );
				media.requireForceForDelete = true;
				media.destroy();

				filesAdded --;
			}

			/* delete html */
			$file.remove();
			return false;
		}
	} // APP


	var API = {
		setup: function () {
			APP.init();
		},
		teardown: function () {
			APP.destroy();
		},
		removeUploaded: function ( file ) {
			return APP.removeUploaded( file );
		}
	};  // API

	jq( document ).ready( function () {
		APP.init();
	} );

	return API;
}
(
	window,
	window.jQuery,
	window.BuddyBoss_Media_Util
) );


/**
 * Replica of BuddyBoss_Media_Uploader for bbpress media
 * 3.1 BuddyBoss bbPress Media Uploader
 * ====================================================================
 * @returns {object} BuddyBoss_bbPress_Media_Uploader
 *
 * window.BuddyBoss_bbPress_Media_Uploader = {
 *   /.../
 * }
 *
 */

window.BuddyBoss_bbPress_Media_Uploader = ( function ( window, jq, util, undefined ) {

	var uploader = false;

	var _cl = { },
		filesAdded = 0;

	var state = util.state || { },
		lang = util.lang;

	var pics_uploaded = [ ];

	var APP = {
		/**
		 * Startup
		 *
		 * @return {void}
		 */
		init: function () {

			var self = this;

			this.inject_markup();

			if ( ! this.get_elements() ) {
				return false;
			}

			jq( document ).on( 'click', '.buddyboss-bbpress-media-add-photo-button', APP.set_post_button );

			this.setup_modal();
			//this.setup_textbox();

			setTimeout( function () {
				self.start_uploader();
			}, 10 );

			jq.ajaxPrefilter( APP.prefilter );
			jq( document ).ajaxSuccess( APP.ajaxSuccessHandler );
		},
		/**
		 * Would handle teardowns if AJAX was implemented for page
		 * navigations.
		 *
		 * @return {void}
		 */
		destroy: function () {
			// this.destroy_button();
		},
		/**
		 * Dynamically inject markup, this avoids relying on BuddyPress
		 * templating and helps handle plugin conflicts
		 *
		 * @return {void}
		 */
		inject_markup: function () {


			// For our add photo, progress and preview area we rely
			// on #what-new-content
			var $whats_new_content = jq( '.bbp-submit-wrapper' );

			// Add photo button + progress area
			var $add_photo = jq( '#buddyboss-bbpress-media-tpl-add-photo' );

			if ( $add_photo.length && $whats_new_content.length ) {
				$whats_new_content.append( $add_photo.html() );
			}



		},
		/**
		 * Get DOM elements we'll need
		 *
		 * @return {boolean} True if we have the required elements
		 */
		get_elements: function () {

			_cl.$bbpress_input = null;
			_cl.$add_photo = jq( '#buddyboss-bbpress-media-bulk-uploader' );
			_cl.$open_uploder_button = jq( '.open-uploader-button.buddyboss-bbpress-media-add-photo-button' );
			_cl.$add_photo_button = jq( '.logo-bbpress-file-browser-button' );
			_cl.$post_button = null;
			_cl.$uploader = jq( '#buddyboss-bbpress-media-bulk-uploader' );
			_cl.$uploaded = jq( '.buddyboss-bbpress-media-bulk-uploader-uploaded .images' );
			_cl.$preview_pane = jq( '.buddyboss-bbpress-media-preview-inner' );

			return true;
		},
		/**
		 * Setup fancybox
		 *
		 * @return {void}
		 */
		setup_modal: function () {
			jq(".buddyboss-bbpress-media-add-photo [data-fancybox]").each(function(){
				var _this = jq(this);
				_this.fancybox( {
					minWidth: 500,
					beforeLoad: function () {
						$attach_bbpress_media_button 	= _this.parents('.ac-reply-content');
						_cl.$post_button 				= $attach_bbpress_media_button.find('input[name=ac_form_submit]');
						_cl.$bbpress_input 				= $attach_bbpress_media_button.find('textarea');
						jq( '.buddyboss-bbpress-media-bulk-uploader-text' ).val( _cl.$bbpress_input.val() );
					},
					beforeClose: function () {
						if ( jq( '.buddyboss-bbpress-media-bulk-uploader-text' ).length > 0 ) {
							_cl.$bbpress_input.val( jq( '.buddyboss-bbpress-media-bulk-uploader-text' ).val() );
						}
					}
				} );
			});


			jq( '#buddyboss-bbpress-media-attach' ).click( function () {
				jq.fancybox.close();
				_cl.$post_button.trigger( 'click' );
			} );

		},

		/**
		 * Set Current form Post button and Textarea
		 * @param e
		 */
		set_post_button: function( e ) {
			$attach_bbpress_media_button 	= jq( this ).parents('.ac-reply-content');
			_cl.$post_button 				= $attach_bbpress_media_button.find('input[name=ac_form_submit]');
			_cl.$bbpress_input 				= $attach_bbpress_media_button.find('textarea');
		},

		/**
		 * We use jQuery's Ajax.preFilter hook to add picture related
		 * uploads to new status update's when needed. Be wary of the
		 * dragons.
		 *
		 * @param  {object} options      jQuery ajax options that are sending
		 * @param  {object} origOptions  Original jQuery ajax options
		 * @param  {object} jqXHR        jQuery XHR object
		 * @return {void}
		 */
		prefilter: function ( options, origOptions, jqXHR ) {

			var action = bbmedia_getQueryVariable( options.data, 'action' );

			if ( typeof action == 'undefined' || action != 'new_activity_bbpress' )
				return;

			var new_data,
				pic_html = '';

			if ( pics_uploaded.length > 0 ) {
				for ( var i = 0; i < pics_uploaded.length; i ++ ) {
					var pic = jq( '<a/>' )
						.attr( 'href', pics_uploaded[i].url )
						.attr( 'target', '_blank' )
						.attr( 'title', pics_uploaded[i].name )
						.addClass( 'buddyboss-media-photo-link' )
						.html( pics_uploaded[i].name )[0].outerHTML;

					pic_html += pic;
				}

				new_data = jq.extend( { }, origOptions.data, {
					content: origOptions.data.content + ' ' + pic_html,
					pics_uploaded: pics_uploaded
				} );

				options.data = jq.param( new_data );

				options.success = ( function ( old_success ) {

					return function ( response, txt, xhr ) {
						if ( jq.isFunction( old_success ) ) {
							old_success( response, txt, xhr );
						}

						if ( response[0] + response[1] !== '-1' ) {
							APP.post_success( response, txt, xhr );
						}
					}
				} )( options.success );
			}
			else if ( origOptions.data && origOptions.data.action === 'get_single_activity_content' ) {
				options.success = ( function ( old_success ) {
					return function ( response, txt, xhr ) {
						if ( jq.isFunction( old_success ) ) {
							old_success( response, txt, xhr );
						}

						if ( response[0] + response[1] !== '-1' ) {
							APP.readmore_success( response, txt, xhr );
						}
					}
				} )( options.success );
			}
		},

		/**
		 * Add Photo button in ajax loaded activity
		 * @param e
		 * @param xhr
		 * @param options
		 */
		ajaxSuccessHandler: function( e, xhr, options ) {

			var action = bbmedia_getQueryVariable( options.data, 'action' );

			if( 'activity_get_older_updates' == action ) {
				APP.inject_markup();
			}
		},
		/**
		 * This callback fires after a photo was posted as part of
		 * an activity update, we'll animate the preview closed
		 * and reset.
		 *
		 * @param  {object} response Ajax response
		 * @return {void}
		 */
		post_success: function ( response ) {

			/* BuddyBoss: If we're using pics, we need to attach PhotoSwipe */
			var $new = jq( "li.new-update" ).find( '.buddyboss-media-photo-wrap' );
			if ( $new.length > 0 && typeof BuddyBossSwiper == 'object'
				&& BuddyBossSwiper.hasOwnProperty( 'reset' ) ) {
				BuddyBossSwiper.reset();
			}

			/* reset everything upload related */
			pics_uploaded = [ ];
			_cl.$preview_pane.html( '' );
			_cl.$uploaded.html( '' );
			uploader.splice( 0, uploader.files.length );
			filesAdded = 0;
		},
		/**
		 * Handles upload, upload progress and previewing pics
		 *
		 * @return {void}
		 */
		start_uploader: function () {
			var $progressBar, progressPercent = 0;

			//var uploader_state = 'closed';
			var ieMobile = navigator.userAgent.indexOf( 'IEMobile' ) !== - 1;

			// IE mobile
			if ( ieMobile ) {
				_cl.$add_photo.addClass( 'legacy' );
			}

			uploader = new plupload.Uploader( {
				runtimes: state.uploader_runtimes || 'html5,flash,silverlight,html4',
				browse_button: _cl.$add_photo_button[0],
				container: 'buddyboss-bbpress-media-bulk-uploader-reception',
				drop_element: 'buddyboss-bbpress-media-bulk-uploader-wrapper',
				multi_selection: state.uploader_multiselect || false,
				url: ajaxurl,
				multipart: true,
				multipart_params: {
					action: 'buddyboss_media_post_photo',
					'cookie': encodeURIComponent( document.cookie ),
					'_wpnonce_post_update': BBOSS_MEDIA.media_upload_nonce
				},
				flash_swf_url: state.uploader_swf_url || '',
				silverlight_xap_url: state.uploader_xap_url || '',
				filters: {
					max_file_size: state.uploader_filesize || '10mb',
					mime_types : state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					title: lang( 'file_browse_title' ),
					//extensions: state.uploader_filetypes || 'jpg,jpeg,gif,png,bmp',
					//prevent_duplicates: true
				},
				init: {
					Init: function () {
						if ( _cl.$add_photo.find( '.moxie-shim' ).find( "input" ).length == '0' ) {
							_cl.$add_photo.find( '.moxie-shim' ).first().css( "z-index", 10 );
							_cl.$add_photo.find( '.moxie-shim' ).css( "cursor", 'pointer' );
						} else {
							clone = jq( _cl.$add_photo_button[0] ).clone();
							jq( _cl.$add_photo_button[0] ).after( clone ).remove();
							_cl.$add_photo_button[0] = clone;
							jq( _cl.$add_photo_button[0] ).on( "click", function () {
								_cl.$add_photo.find( '.moxie-shim' ).find( "input" ).click();
							} );
						}
					},
					FilesAdded: function ( up, files ) {

						//jQuery('#aw-whats-new-submit-bbmedia').prop('disabled', true);

						if ( up.files.length > state.uploader_max_files || files.length > state.uploader_max_files ) {
							uploader.splice( filesAdded, uploader.files.length );

							alert( lang( 'exceed_max_files_per_batch' ) );
							return false;
						}

						for ( var i = 0; i < files.length; i ++ ) {
							if ( jq( 'div[data-fileid="' + files[i].id + '"]' ).length === 0 ) {
								var newimg = "<div data-fileid='" + files[i].id + "' class='file uploading'><img src='" + state.uploader_temp_img + "'><progress class='buddyboss-media-progress-bar' value='0' max='100'></progress></div>";
								_cl.$uploaded.append( newimg );
								_cl.$preview_pane.append( newimg );
								filesAdded ++;
							}
						}
						//jq.fancybox.update();
						up.start();
					},
					UploadProgress: function ( up, file ) {

						if ( file && file.hasOwnProperty( 'percent' ) ) {
							$progressBar = jq( 'div[data-fileid="' + file.id + '"]' ).find( 'progress' );
							progressPercent = file.percent;
							$progressBar.val( progressPercent );
						}
					},
					FileUploaded: function ( up, file, info ) {

						//	jQuery('#aw-whats-new-submit-bbmedia').prop('disabled', false);

						var responseJSON = jq.parseJSON( info.response );
						//console.log('// ----- upload response ----- //');
						//console.log(up,file,info,responseJSON);
						$file = jq( 'div[data-fileid="' + file.id + '"]' );
						$file.removeClass( 'uploading' );

						$file.data( 'attachment_id', responseJSON.attachment_id );

						$file.append("<input type='hidden' value='"+ responseJSON.attachment_id +"' name='bbm_bbpress_attachments[]'>");

						$file.find( '>img' ).attr( 'src', responseJSON.url );

						$file.find( 'progress' ).replaceWith(
							"<a href='#' onclick='return window.BuddyBoss_bbPress_Media_Uploader.removeUploaded(\"" + file.id + "\");' class='delete'>+</a>"
						);

						pics_uploaded.push( responseJSON );
						//jq.fancybox.update();
					},
					Error: function ( up, args ) {

						alert( lang( 'error_uploading_photo' ) );

						$progressWrap.removeClass( 'uploading' );
						$postButton.prop( "disabled", false ).removeClass( 'loading' );

						//uploader_state = 'closed';
					}
				}
			} );

			uploader.init();


			if ( jq( '#buddyboss-bbpress-media-bulk-uploader-reception-fake' ).length > 0 ) {
				var additional_dropzone = document.getElementById( 'buddyboss-media-bulk-uploader-reception-fake' );
				$additional_dropzone = jq( additional_dropzone );

				var dropzone = new moxie.file.FileDrop( {
					drop_zone: additional_dropzone
				} );

				dropzone.ondrop = function ( event ) {
					_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( dropzone.files );
				};

				dropzone.init();

				// -- Configure FILEINPUT -- //

				var input = new moxie.file.FileInput( {
					browse_button: jq( "a.browse-file-button", $additional_dropzone )[ 0 ],
					container: additional_dropzone,
					multiple: true
				} );

				input.onchange = function ( event ) {
					_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( input.files );
				};

				input.init();

			}

			//Show upload box when clicking on Add Photo button INSTEAD of opening lightbox
			if ( jq( '.buddyboss-bbpress-media-add-photo' ).length > 0 ) {

				var additional_dropzone = document.getElementsByClassName( 'buddyboss-bbpress-media-add-photo' );

				// -- Configure FILEINPUT -- //
				var input = new moxie.file.FileInput( {
					browse_button: jq( ".browse-file-button" )[ 0 ],
					container: additional_dropzone[0],
					multiple: true
				} );

				input.onchange = function ( event ) {
					//_l.$open_uploder_button.click();//to open modal window
					uploader.addFile( input.files );
				};

				input.init();
			}

		}, // start_uploader();

		removeUploaded: function ( fileid ) {
			/* remove from upload files list */
			var $file = jq( 'div[data-fileid="' + fileid + '"]' );
			if ( pics_uploaded.length > 0 ) {
				var pics_uploaded_temp = [ ];
				for ( var i = 0; i < pics_uploaded.length; i ++ ) {
					if ( pics_uploaded[i].attachment_id !== $file.data( 'attachment_id' ) ) {
						pics_uploaded_temp.push( pics_uploaded[i] );
					}
				}

				pics_uploaded = pics_uploaded_temp;
			}

			var file_to_remove = false;
			/* remove from plupload queue */
			jq.each( uploader.files, function ( i, ufile ) {
				if ( ufile.hasOwnProperty( 'id' ) && ufile.id == fileid ) {
					file_to_remove = ufile;
				}
			} );

			if ( file_to_remove ) {
				uploader.removeFile( file_to_remove );

				// Permanently delete media attachment
				var media = new wp.api.models.Media( { id: $file.data('attachment_id') } );
				media.requireForceForDelete = true;
				media.destroy();

				filesAdded --;
			}

			/* delete html */
			$file.remove();
			return false;
		}
	} // APP


	var API = {
		setup: function () {
			APP.init();
		},
		teardown: function () {
			APP.destroy();
		},
		removeUploaded: function ( file ) {
			return APP.removeUploaded( file );
		}
	} // API

	jq( document ).ready( function () {
		APP.init();
	} );

	return API;
}
(
	window,
	window.jQuery,
	window.BuddyBoss_Media_Util
) );

/* get querystring value */
function bbmedia_getQueryVariable( query, variable ) {
	if ( typeof query !== 'string' || query == '' || typeof variable == 'undefined' || variable == '' )
		return '';

	var vars = query.split( "&" );

	for ( var i = 0; i < vars.length; i ++ ) {
		var pair = vars[i].split( "=" );

		if ( pair[0] == variable ) {
			return pair[1];
		}
	}
	return( false );
}

bbmedia_move_media_opened = false;
bbmedia_tag_friends_opened = false;
jq( document ).ready( function () {
	//escape key press
	//lets hide 'move media' form if its open
	jq( document ).keyup( function ( e ) {
		if ( e.keyCode == 27 && ( bbmedia_move_media_opened === true || bbmedia_tag_friends_opened === true ) ) {
			if ( bbmedia_move_media_opened === true )
				buddyboss_media_move_media_close();
			if ( bbmedia_tag_friends_opened === true )
				buddyboss_media_tag_friends_close();
		}
	} );

	setMediaPageCookie();

	/**
	 * Fix for nth-child selector messup in grid layout, due to hidden 'load_more' child elements.
	 *
	 * Intercept response for load more activities and remove the 'load_more' link element.
	 */
	jq.ajaxPrefilter( function ( options, originalOptions, jqXHR ) {
		var originalSuccess = options.success;

		var action = bbmedia_getQueryVariable( options.data, 'action' );

		if ( typeof action == 'undefined' || action != 'activity_get_older_updates' )
			return;

		options.success = function ( data ) {
			jq( '#bbmedia-grid-wrapper #activity-stream li.load-more' ).remove();
			if ( originalSuccess != null ) {
				originalSuccess( data );
			}
		};
	} );

	//Updating activity markup for photoswipe favorite icon
	jq('#activity-stream').on( 'click', '.activity-meta > a.fav', function () {

		var img_wrap = jq( this ).parents( '.activity-content' ).find( '.buddyboss-media-photos-wrap-container' );

		if ( ! ( img_wrap.length > 0 ) ) {
			return;
		}

		bbm_ps_mark_favorite( img_wrap );
	} );


	jq('#activity-stream').on( 'click', '.activity-meta > a.unfav', function () {

		var img_wrap = jq( this ).parents( '.activity-content' ).find( '.buddyboss-media-photos-wrap-container' );

		if ( ! ( img_wrap.length > 0 ) ) {
			return;
		}

		bbm_ps_mark_unfavorite( img_wrap );
	} );

	jq('#activity-stream').on( 'click', '.acomment-options > a.fav-comment', function () {

		var img_wrap = jq( this ).parent().parent().find( '.acomment-content > .buddyboss-media-photos-wrap-container' );

		//console.log( img_wrap.html() );
		if ( ! ( img_wrap.length > 0 ) ) {
			return;
		}

		bbm_ps_mark_favorite( img_wrap );
	} );


	jq('#activity-stream').on( 'click', '.acomment-options > a.unfav-comment', function () {

		var img_wrap = jq( this ).parent().parent().find( '.acomment-content > .buddyboss-media-photos-wrap-container' );

		//console.log( img_wrap.html() );
		if ( ! ( img_wrap.length > 0 ) ) {
			return;
		}

		bbm_ps_mark_unfavorite( img_wrap );
	} );




	/**
	 * Update comment count in photoswipe
	 */
	jq('#activity-stream').on( 'click', 'input[name=ac_form_submit]', function ( e ) {

		var $activity_elm = jq(this).parents('.activity-item');

		var img_wrap = $activity_elm.find( '.activity-inner > .buddyboss-media-photos-wrap-container' );

		if ( ! ( img_wrap.length > 0 ) ) {
			return;
		}

		jq( img_wrap ).find( '.buddyboss-media-photo-wrap .buddyboss-media-photo' ).each( function () {

			var img 				= jq(this);
			var comment_count 		= img.attr( 'data-comment-count' );
			var new_comment_count 	= parseInt( comment_count ) + 1;

			img.attr( 'data-comment-count', new_comment_count );
		});
	});

	jq('#activity-stream').on( 'click', 'a.acomment-delete', function ( e ) {

		var $activity_elm = jq(this).parents('.activity-item');
		var $reply_thread = jq(this).parents('li[id^=acomment]:first').find('li[id^=acomment]');

		if ( 0 < $reply_thread.length ) {
			var deleted_comments = $reply_thread.length + 1;
		} else {
			var deleted_comments = 1;
		}

		var img_wrap = $activity_elm.find( '.activity-inner > .buddyboss-media-photos-wrap-container' );

		if ( ! ( img_wrap.length > 0 ) ) {
			return;
		}

		jq( img_wrap ).find( '.buddyboss-media-photo-wrap .buddyboss-media-photo' ).each( function () {

			var img 				= jq(this);
			var comment_count 		= img.attr( 'data-comment-count' );

			if( 0 <= comment_count ) {
				var new_comment_count 	= parseInt( comment_count ) - deleted_comments;
				img.attr( 'data-comment-count', new_comment_count );
			}
		});
	});


	//Block of 2 tall images height scaling
	jq('div.buddyboss-media-photos-wrap-container').each( function( index, val ) {
		var tall_images = jq(this).find('a.size-activity-2-thumbnail-tall');
		tall_images_height_scale( tall_images[0], tall_images[1] );
	});

	//Stop page scroll to the top when "Select File" link has clicked
	jq( document ).on( 'click', 'a.browse-file-button', function(e) {
		e.preventDefault();
	} );

	// Open comment post form when user click on reply button from photoswipe area
	buddyboss_media_open_comment_form();
} );

var hidden, visibilityChange;
if (typeof document.hidden !== "undefined") { // Opera 12.10 and Firefox 18 and later support
	hidden = "hidden";
	visibilityChange = "visibilitychange";
} else if (typeof document.msHidden !== "undefined") {
	hidden = "msHidden";
	visibilityChange = "msvisibilitychange";
} else if (typeof document.webkitHidden !== "undefined") {
	hidden = "webkitHidden";
	visibilityChange = "webkitvisibilitychange";
}

/**
 * Set is_media_page cookie when Global Media page tab get focused
 */
function setMediaPageVisibilityChange() {
	if (!document[hidden]) {
		setMediaPageCookie();
	}
}

function setMediaPageCookie() {

	/**
	 * Conditional tags like is_page() dont work in ajax request.
	 * So there's no direct way to detect if we are on global media page.
	 *
	 * Therefore, we set a cookie, which is passed along in ajax request.
	 * There might be a better way though.
	 */

	if ( jq('body').hasClass('buddyboss-media-all-media') ) {
		docCookies.setItem( "bp-bboss-is-media-page", "yes", "", "/" );
	} else {
		docCookies.removeItem( "bp-bboss-is-media-page", "/" );
	}
}

document.addEventListener(visibilityChange, setMediaPageVisibilityChange, false);

function tall_images_height_scale( img1, img2 ) {

	var h1 = jq(img1).find('img').height();
	var h2 = jq(img2).find('img').height();
	//it can be done only with height but using double check          ratio of the images is a bit more acurate
	//using lower image as refference when minimize

	if (h1 < h2) {
		jq(img1).height(h2);
		jq(img1).find('img').height(h2);
		jq(img2).height(h2);
		jq(img2).find('img').height(h2);

	}
	else if (h2 < h1) {
		jq(img2).height(h1);
		jq(img2).find('img').height(h1);
		jq(img1).height(h1);
		jq(img1).find('img').height(h1);
	}
}

function  bbm_ps_mark_favorite( img_wrap ) {

	jq( img_wrap ).find( '.buddyboss-media-photo-wrap .buddyboss-media-photo' ).each( function () {
		var fav_count = jq( this ).attr( 'data-favorite-count' ) || 0;
		jq( this ).attr( 'data-bbmfav', 'bbm-unfav' );
		jq( this ).attr( 'data-favorite-count', parseInt( fav_count ) + 1 );

	} );
}

function  bbm_ps_mark_unfavorite( img_wrap ) {

	jq( img_wrap ).find( '.buddyboss-media-photo-wrap .buddyboss-media-photo' ).each( function () {
		var fav_count = jq( this ).attr( 'data-favorite-count' ) || 0;
		jq( this ).attr( 'data-bbmfav', 'bbm-fav' );
		if ( fav_count == 0 ) {
			jq( this ).attr( 'data-favorite-count', parseInt( fav_count ) );
		} else {
			jq( this ).attr( 'data-favorite-count', parseInt( fav_count ) - 1 );
		}

	} );
}
/* ++++++++++++++++++++++++++++++++++++
 * Move photos betweeen albums
 ++++++++++++++++++++++++++++++++++++ */
function buddyboss_media_initiate_media_move( link ) {
	$link = jq( link );
	$form = jq( '#frm_buddyboss-media-move-media' );
	$form_wrapper = $form.parent();

	//slideup comment form
	$link.closest( '.activity' ).find( 'form.ac-form' ).slideUp();

	if ( $form_wrapper.is( ':visible' ) ) {
		buddyboss_media_move_media_close();
		return false;
	}

	// Move group albums from Personal or global activity stream
	if ( 0 === $('.activity.single-group').length
		&& $link.parents('.activity-item').hasClass('groups') ) {

		var $albumDropDown = $('#buddyboss_media_move_media_albums');
		$albumDropDown.find('option:gt(0)').remove().end();

		var newOption = document.createElement("option");
		newOption.text = BBOSS_MEDIA.fetchingL10n;
		newOption.value = '';
		$albumDropDown[0].appendChild(newOption);

		$.ajax({
			url: ajaxurl,
			method: 'GET',
			data: {
				action: 'buddyboss_get_group_albums',
				activity_id: $link.data( 'activity_id' )
			},
			success: function( response ) {

				$albumDropDown.find('option:gt(0)').remove().end();

				if ( response.success ) {
					var albums = response.data;
					albums.forEach(function(album){
						var newOption = document.createElement("option");
						newOption.text = album.title;
						newOption.value = album.id;
						$albumDropDown[0].appendChild(newOption);
					});
				}
			}
		})
	}

	//Tab should auto close on opening another tab.
	jq('.buddyboss-activity-comments-form').hide();

	$link.closest( '.activity-content' ).after( $form_wrapper );

	//Highlight previously selected album
	var selected_album = $link.data( 'album_id' );
	if ( ! selected_album )
		selected_album = '';//string
	$form.find( '#buddyboss_media_move_media_albums' ).val( selected_album );

	$form_wrapper.slideDown( 200 );
	bbmedia_move_media_opened = true;

	//setup form data
	$form.find( 'input[name="activity_id"]' ).val( $link.data( 'activity_id' ) );

	return false;
}

function buddyboss_media_submit_media_move() {
	$form = jq( '#frm_buddyboss-media-move-media' );
	$submit_button = $form.find( 'input[type="submit"]' );

	if ( $submit_button.hasClass( 'loading' ) )
		return false;//previous request hasn't finished yet!

	/**
	 * 1. gather data
	 * 2. start ajax
	 * 3. receive response
	 * 4. process response
	 *      - remove loading class
	 *      - remove activity item entry if required
	 *    - move form to a different place first
	 *      - slideup form
	 */

	var bbm_move_media_albums_id = $form.find( 'select[name="buddyboss_media_move_media_albums"]' ).val();

	var data = {
		'action': $form.find( 'input[name="action"]' ).val(),
		'bboss_media_move_media_nonce': $form.find( 'input[name="bboss_media_move_media_nonce"]' ).val(),
		'activity_id': $form.find( 'input[name="activity_id"]' ).val(),
		'buddyboss_media_move_media_albums': bbm_move_media_albums_id
	};

	$submit_button.addClass( 'loading' );
	$form.find( "#message" ).removeAttr( 'class' ).html( '' );

	jq.ajax( {
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function ( response ) {
			response = jq.parseJSON( response );
			if ( response.status ) {
				$form.find( "#message" ).addClass( 'updated' ).html( "<p>" + response.message + "</p>" );
				if ( $form.find( 'input[name="is_single_album"]' ).val() == 'yes' ) {
					setTimeout( function () {
						buddyboss_media_media_move_cleanup( $form, true );
					}, 2000 );
				} else {
					setTimeout( function () {
						buddyboss_media_media_move_cleanup( $form, false );
					}, 2000 );
				}

				//Set new album id for pre select value
				$form.parents('li.activity-item').find('a.buddyboss_media_move').data('album_id', bbm_move_media_albums_id );

			} else {
				$form.find( "#message" ).addClass( 'error' ).html( "<p>" + response.message + "</p>" );
			}

			$submit_button.removeClass( 'loading' );
		}
	} );

	return false;
}

function buddyboss_media_media_move_cleanup( $form, remove_activity_item ) {
	$form.find( "#message" ).removeAttr( 'class' ).html( '' );
	$form_wrapper = $form.parent();

	buddyboss_media_move_media_close();
	if ( remove_activity_item ) {
		$activity = $form.closest( '.activity' );

		jq( 'body' ).append( $form_wrapper );
		$form_wrapper.hide();
		$activity.slideUp( 200, function () {
			$activity.remove();
		} );
	}
}

function buddyboss_media_move_media_close() {
	$form = jq( '#frm_buddyboss-media-move-media' );
	$form_wrapper = $form.parent();

	$form_wrapper.slideUp( 200 );
	bbmedia_move_media_opened = false;

	return false;
}
/* ________________________________ */

/**
 * Open comment post form when user click on reply button from photoswipe area
 */
function buddyboss_media_open_comment_form() {
	if ( -1 !== window.location.search.indexOf('mcomment') && $('body.activity-permalink').length > 0 ) {
		var activity_id =  window.location.href.match(/mcomment=(\d+)/i);

		setTimeout(function () {
			jq('#acomment-comment-' + activity_id[1]).trigger('click');
		}, 500);
	}
}

/* ++++++++++++++++++++++++++++++++++++
 * Tag Friends
 ++++++++++++++++++++++++++++++++++++ */
function buddyboss_media_initiate_tagging( link ) {
    $link = jq( link );
	$form = jq( '#frm_buddyboss-media-tag-friends' );
	$form_wrapper = $form.parent();

	//slideup comment form
	$link.closest( '.activity' ).find( 'form.ac-form' ).slideUp();

	if ( $form_wrapper.is( ':visible' ) ) {
		buddyboss_media_tag_friends_close();
		return false;
	}

	//Tab should auto close on opening another tab.
	jq('.buddyboss-activity-comments-form').hide();

	$link.closest( '.activity-content' ).after( $form_wrapper );
	$form_wrapper.slideDown( 200 );
	bbmedia_tag_friends_opened = true;

    $form.find('input[name="ac_search"]').off( 'input' ).on( 'input', function(){ buddyboss_media_tag_friends_search();} );

    $form.find( '.preloading' ).show();

	//setup form data
	$form.find( 'input[name="activity_id"]' ).val( $link.data( 'activity_id' ) );

    buddyboss_media_tag_friends_search();
    return false;
}

function buddyboss_media_tag_friends_close() {
	$form = jq( '#frm_buddyboss-media-tag-friends' );
	$form_wrapper = $form.parent();

    $form.data( 'doingsearch', 'no' );
    $form.find('#invite-list > ul').html('');
    $form.find('input[name="ac_search"]').off('input').val('').hide();
	$form_wrapper.slideUp( 200 );
	bbmedia_tag_friends_opened = false;

	return false;
}

function buddyboss_media_tag_friends_search(){
    $form = jq( '#frm_buddyboss-media-tag-friends' );

    if( $form.data( 'doingsearch' )=='yes' )
        return false;

    var search_term = jq.trim( $form.find('input[name="ac_search"]').val() );
    var exclude_ids = [];
    $form.find('#invite-list > ul > li').each(function(){
        if(jq(this).hasClass('more_i10')){
            jq(this).remove();
        } else {
            if( jq(this).find('input[type="checkbox"]').is(':checked') ){
                exclude_ids.push( jq(this).find('input[type="checkbox"]').val() );
            } else {
                jq(this).remove();
            }
        }
    });

    $form.data( 'doingsearch', 'yes' );
    $form.find('.preloading').show();
    $form.find('#message').each(function(){ jq(this).remove(); });
    //populate friends list
	var data = {
		'action': $form.find( 'input[name="action"]' ).val(),
		'buddyboss_media_tag_friends_nonce': $form.find( 'input[name="buddyboss_media_tag_friends_nonce"]' ).val(),
		'activity_id': $form.find( 'input[name="activity_id"]' ).val(),
        'search_term'   : search_term,
        'exclude_ids'   : exclude_ids
	};
	jq.ajax( {
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function ( response ) {
            if( $form.data( 'doingsearch' )=='no' )
                return;

            $form.find('.preloading').hide();
			response = jq.parseJSON( response );

            $ul = $form.find('#invite-list > ul');
            if( response.friends_list ){
                var count = response.friends_list.length;
                for( var i=0; i<count; i++ ){
                    $ul.append(response.friends_list[i]);
                }

                if( count < response.total ){
                    var more = response.total - count;
                    var more_i10 = response.more_i10.replace( 'xx', more );
                    $ul.append("<li class='more_i10'>"+ more_i10 +"</li>");
                }
                $ul.show();
            } else {
                if( $ul.html() != '' ){
                    $form.find('#invite-list').append(response.error);
                }
            }

			//bind events
			jq( '#invite-list input[name="friends[]"]' ).unbind('change').bind( 'change', function () {
				buddyboss_media_tag_friends_toggle_tag( data.activity_id, jq( this ).val() );
			} );
			jq( '#friend-list .action .remove' ).click( function ( e ) {
				e.preventDefault();
				buddyboss_media_tag_friends_toggle_tag( data.activity_id, jq( this ).data( 'userid' ) );
			} );

            if( search_term == '' && response.show_search ){
                $form.find('input[name="ac_search"]').show();
            }

            $form.data( 'doingsearch', 'no' );
		}
	} );

	return false;
}

function buddyboss_media_tag_friends_toggle_tag( activity_id, friend_id ) {
	$form = jq( '#frm_buddyboss-media-tag-friends' );

	var data = {
		'action': $form.find( 'input[name="action_tag"]' ).val(),
		'buddyboss_media_tag_friends_nonce': $form.find( 'input[name="buddyboss_media_tag_friends_nonce"]' ).val(),
		'activity_id': activity_id,
		'friend_id': friend_id
	};

	jq.ajax( {
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function ( response ) {
            /*
			response = jq.parseJSON( response );
			$form.find( '#invite-list' ).html( response.friends_list );
			$form.find( '.main-column-content' ).html( response.tagged_friends );

			//bind events
			jq( '#invite-list input[name="friends[]"]' ).change( function () {
				buddyboss_media_tag_friends_toggle_tag( data.activity_id, jq( this ).val() );
			} );

			jq( '#friend-list .action .remove' ).click( function ( e ) {
				e.preventDefault();
				buddyboss_media_tag_friends_toggle_tag( data.activity_id, jq( this ).data( 'userid' ) );
			} );*/
		}
	} );
}

function buddyboss_media_tag_friends_complete() {
	$form = jq( '#frm_buddyboss-media-tag-friends' );

	var data = {
		'action': $form.find( 'input[name="action_tag_complete"]' ).val(),
		'buddyboss_media_tag_friends_nonce': $form.find( 'input[name="buddyboss_media_tag_friends_nonce"]' ).val(),
		'activity_id': $form.find( 'input[name="activity_id"]' ).val(),
		'update_action': false
	};

	//can we update activity action(to update tagged people details) ?
	if ( $form.closest( '.activity' ).find( BuddyBoss_Media_Appstate.activity_header_selector ).length > 0 ) {
		data.update_action = true;
	}

	$form.find( 'input[type="submit"]' ).addClass( 'loading' );

	jq.ajax( {
		type: "POST",
		url: ajaxurl,
		data: data,
		success: function ( response ) {
			$form.find( 'input[type="submit"]' ).removeClass( 'loading' );
			if ( data.update_action === true ) {
				response = jq.parseJSON( response );

				$activity = $form.closest( '.activity' );
				$activity.find( BuddyBoss_Media_Appstate.activity_header_selector ).html( response.activity_action );

				if ( response.activity_tooltip ) {
					$activity.find( '.buddyboss-media-tt-content' ).remove();
					$activity.append( response.activity_tooltip );

					window.BBMediaTooltips.initTooltips();
				}
			}
			buddyboss_media_tag_friends_close();
		}
	} );

	return false;
}
/* ________________________________ */

( function ( window, $ ) {

	var BBMediaTooltips = { };
	var $el = { };

	/**
	 * Init

	 * @return {void}
	 */
	BBMediaTooltips.init = function () {
		BBMediaTooltips.initTooltips();
	}

	/**
	 * Prepare tooltips
	 *
	 * @return {void}
	 */
	BBMediaTooltips.initTooltips = function () {
		// Find tooltips on page
		$el.tooltips = jq( '.buddyboss-media-tt-others' );

		// Init tooltips
		if ( $el.tooltips.length ) {
			$el.tooltips.tooltipster( {
				contentAsHTML: true,
				functionInit: BBMediaTooltips.getTooltipContent,
				interactive: true,
				position: 'top-left',
				theme: 'tooltipster-buddyboss'
			} );
		}
	}

	/**
	 * Get tooltip content
	 *
	 * @param  {object} origin  Original tooltip element
	 * @param  {string} content Current tooltip content
	 *
	 * @return {string}         Tooltip content
	 */
	BBMediaTooltips.getTooltipContent = function ( origin, content ) {

		var $content = origin.closest( 'li' ).find( '.buddyboss-media-tt-content' ).detach().html();

		return $content;
	}

	jq( document ).ready( function () {
		if ( BuddyBoss_Media_Appstate.enable_tagging == true ) {
			BBMediaTooltips.initTooltips();
			window.BBMediaTooltips = BBMediaTooltips;
		}
	} );

}( window, window.jQuery ) );


/*\
 |*|
 |*|  :: cookies.js ::
 |*|
 |*|  A complete cookies reader/writer framework with full unicode support.
 |*|
 |*|  Revision #1 - September 4, 2014
 |*|
 |*|  https://developer.mozilla.org/en-US/docs/Web/API/document.cookie
 |*|  https://developer.mozilla.org/User:fusionchess
 |*|
 |*|  This framework is released under the GNU Public License, version 3 or later.
 |*|  http://www.gnu.org/licenses/gpl-3.0-standalone.html
 |*|
 |*|  Syntaxes:
 |*|
 |*|  * docCookies.setItem(name, value[, end[, path[, domain[, secure]]]])
 |*|  * docCookies.getItem(name)
 |*|  * docCookies.removeItem(name[, path[, domain]])
 |*|  * docCookies.hasItem(name)
 |*|  * docCookies.keys()
 |*|
 \*/
var docCookies = docCookies || {
	getItem: function ( sKey ) {
		if ( ! sKey ) {
			return null;
		}
		return decodeURIComponent( document.cookie.replace( new RegExp( "(?:(?:^|.*;)\\s*" + encodeURIComponent( sKey ).replace( /[\-\.\+\*]/g, "\\$&" ) + "\\s*\\=\\s*([^;]*).*$)|^.*$" ), "$1" ) ) || null;
	},
	setItem: function ( sKey, sValue, vEnd, sPath, sDomain, bSecure ) {
		if ( ! sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test( sKey ) ) {
			return false;
		}
		var sExpires = "";
		if ( vEnd ) {
			switch ( vEnd.constructor ) {
				case Number:
					sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
					break;
				case String:
					sExpires = "; expires=" + vEnd;
					break;
				case Date:
					sExpires = "; expires=" + vEnd.toUTCString();
					break;
			}
		}
		document.cookie = encodeURIComponent( sKey ) + "=" + encodeURIComponent( sValue ) + sExpires + ( sDomain ? "; domain=" + sDomain : "" ) + ( sPath ? "; path=" + sPath : "" ) + ( bSecure ? "; secure" : "" );
		return true;
	},
	removeItem: function ( sKey, sPath, sDomain ) {
		if ( ! this.hasItem( sKey ) ) {
			return false;
		}
		document.cookie = encodeURIComponent( sKey ) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + ( sDomain ? "; domain=" + sDomain : "" ) + ( sPath ? "; path=" + sPath : "" );
		return true;
	},
	hasItem: function ( sKey ) {
		if ( ! sKey ) {
			return false;
		}
		return ( new RegExp( "(?:^|;\\s*)" + encodeURIComponent( sKey ).replace( /[\-\.\+\*]/g, "\\$&" ) + "\\s*\\=" ) ).test( document.cookie );
	},
	keys: function () {
		var aKeys = document.cookie.replace( /((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "" ).split( /\s*(?:\=[^;]*)?;\s*/ );
		for ( var nLen = aKeys.length, nIdx = 0; nIdx < nLen; nIdx ++ ) {
			aKeys[nIdx] = decodeURIComponent( aKeys[nIdx] );
		}
		return aKeys;
	}
};
