(function($) {
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
	jQuery(document).ready(
		function() {

			$("form#whats-new-form")
				.attr("enctype", "multipart/form-data");
			var poll_html;

			if (bpolls_ajax_object.reign_polls) {
				var body = document.body;
				body.classList.add("reign-polls");
			}

			if (bpolls_ajax_object.rt_poll_fix && bpolls_ajax_object.nouveau) {

				$(document).on('click', '.bpolls-icon', function() {
					//if($('#rtmedia_uploader_filelist li').length != 0 ){
					$('#rtmedia-add-media-button-post-update').hide();
					//}
				});
				$(document).on('click', '.bpolls-cancel', function() {
					$('#rtmedia-add-media-button-post-update').show();
				});


				$(document).on('focus', '#whats-new', function() {
					if ($('#rtmedia-add-media-button-post-update').is(':hidden')) {
						$('#rtmedia-add-media-button-post-update').show();
					}
				});

				$(document).on('click', '#rtmedia-add-media-button-post-update', function() {
					$('.bpolls-html-container').hide();
				});

				$(document).on('focus', '#whats-new', function() {
					if ($('.bpolls-html-container').is(':hidden')) {
						$('.bpolls-html-container').show();
					}
				});

				$(document).ready(function() {
					$(document).on('focus', '#whats-new', function() {
						//alert($(".rtmedia-plupload-container .bpolls-html-container").length);
						if ($(".rtmedia-plupload-container .bpolls-html-container").length == 0) {
							//$(".bpolls-html-container").detach();
							// $(".bpolls-html-container").insertAfter("#rtmedia-action-update");
							$(".bpolls-html-container").appendTo(".rtmedia-plupload-container");
						}
						$('#whats-new-options .bpolls-html-container').hide();
					});
				});

			}




			// $('#bpolls-datetimepicker').datetimepicker();
			// var poll_html = $( '.bpolls-polls-option-html' ).html();
			// var option_html = $('.bpolls-option').html();

			// $(
			// 	function() {
			// 		$('.bpolls-sortable').sortable({
			// 			handle: '.bpolls-sortable-handle'
			// 		});
			// 		$('.bpolls-sortable').disableSelection();
			// 	}
			// );

			$(document).on(
				'focus', '#whats-new', function() {
					$('#whats-new-options').addClass('bpolls-rtm-class');
				});

			/*==============================================
			=            add new poll option js            =
			==============================================*/

			$(document).on(
				'click', '.bpolls-add-option', function() {
					var clonedObj = $(this).parent().siblings().find('.bpolls-option:first').clone().insertAfter($(this).parent().siblings().find('.bpolls-option:last'));
					clonedObj.find('input').each(
						function() {
							this.value = '';
							this.placeholder = '';
						}
					);
					if (clonedObj.length == 0) {
						$('.bpolls-sortable').html('<div class="bpolls-option">' + option_html + '</div>');
					}
				}
			);

			/*=====  End of add new poll option js  ======*/

			/*==========================================
			=            delete poll option            =
			==========================================*/

			$(document).on(
				'click', '.bpolls-option-delete', function() {
					$(this).parent('.bpolls-option').remove();
				}
			);

			/*=====  End of delete poll option  ======*/

			/*============================================
			=            Show hide poll panel            =
			============================================*/

			$(document).on(
				'click', '.bpolls-icon', function() {
					$('.bpolls-polls-option-html').slideToggle(500);

					$('#bpolls-datetimepicker').datetimepicker();
					var poll_html = $('.bpolls-polls-option-html').html();
					var option_html = $('.bpolls-option').html();

					$(
						function() {
							$('.bpolls-sortable').sortable({
								handle: '.bpolls-sortable-handle'
							});
							$('.bpolls-sortable').disableSelection();
						}
					);
				}
			);

			/*=====  End of Show hide poll panel  ======*/

			/*==================================================================
			=            clear html and toggle on poll cancellation            =
			==================================================================*/

			$(document).on(
				'click', '.bpolls-cancel', function() {
					$('.bpolls-polls-option-html').html(poll_html);
					$('.bpolls-polls-option-html').slideUp(500);
					$('.bpolls-sortable').sortable({
						handle: '.bpolls-sortable-handle'
					});
					$('.bpolls-sortable').disableSelection();
				}
			);

			/*=====  End of clear html and toggle on poll cancellation  ======*/

			$(document).on('change', 'input[name=bpolls_input_options]', function() {

				var poll_option = [];
				$("input[name='bpolls_input_options']").each(function() {
					if ($(this).val()) {
						poll_option.push($(this).val());
					}
				});
				var is_poll;
				if (poll_option.length !== 0) {
					is_poll = 'yes';
				} else {
					is_poll = 'no'
				}

				var data = {
					'action': 'bpolls_set_poll_type_true',
					'poll_option': poll_option,
					'is_poll': is_poll,
					'ajax_nonce': bpolls_ajax_object.ajax_nonce
				};

				$.post(bpolls_ajax_object.ajax_url, data, function(response) {
					console.log(response);
				});

			});

			/*==========================================================
			=            solve glitch on post update submit            =
			==========================================================*/

			$("#aw-whats-new-submit").click(
				function() {
					$('.bpolls-polls-option-html').html(poll_html);
					if ($('.bpolls-polls-option-html').is(':visible')) {
						$('.bpolls-polls-option-html').slideToggle(500);
					}
				}
			);

			/*=====  End of solve glitch on post update submit  ======*/

			/*======================================================
			=            Ajax request to save poll vote            =
			======================================================*/

			$(document).on('click', '.bpolls-vote-submit', function() {
				var submit_event = $(this);
				var submit_event_text = $(this).html();
				var s_array = $(this).closest('.bpolls-vote-submit-form').serializeArray();
				var len = s_array.length;
				var dataObj = {};
				for (var i = 0; i < len; i++) {
					dataObj[s_array[i].name] = s_array[i].value;
				}
				var bpoll_activity_id = dataObj['bpoll_activity_id'];

				if (dataObj['bpolls_vote_optn[]'] == undefined) {
					submit_event.html(bpolls_ajax_object.optn_empty_text + ' <i class="fa fa-exclamation-triangle"></i>');
					return;
				} else {
					submit_event.html(submit_event_text);
				}

				submit_event.html(bpolls_ajax_object.submit_text + ' <i class="fa fa-refresh fa-spin"></i>');
				var poll_data = $(this).closest('.bpolls-vote-submit-form').serialize();

				var data = {
					'action': 'bpolls_save_poll_vote',
					'poll_data': poll_data,
					'ajax_nonce': bpolls_ajax_object.ajax_nonce
				};

				$.post(bpolls_ajax_object.ajax_url, data, function(response) {

					var res = JSON.parse(response);
					if (res.bpolls_thankyou_feedback != '' ) {
						submit_event.after('<p class="bpolls-feedback-message">'+ res.bpolls_thankyou_feedback +'</p>');
					}
					
					$.each(res, function(i, item) {
						
						var input_obj = submit_event.closest('.bpolls-vote-submit-form').find("#" + i);

						$(input_obj).parents('.bpolls-check-radio-div').siblings('.bpolls-item-width').animate(
							{
								width: item.vote_percent
							}, 500
						);

						$(input_obj).parents('.bpolls-check-radio-div').find('.bpolls-percent').text(item.vote_percent);
						$(input_obj).parents('.bpolls-check-radio-div').siblings('.bpolls-votes').html(item.bpolls_votes_txt);

					});
					$('#activity-' + bpoll_activity_id + ' .bpolls-item input').hide();
					submit_event.remove();
				});
			});

			/*=====  End of Ajax request to save poll vote  ======*/
			$('#whats-new,#bppfa-whats-new').focus(function() {
				jQuery('.bpolls-icon').click(function() {
					jQuery('.bpquotes-bg-selection-div').hide();
					jQuery('.bp-checkin-panel').hide();
				});
			});
		});
})(jQuery);

(function($) {

	$(document).ready(function() {
		var file_frame;
		$(document).on('click', '#bpolls-attach-image', function(event) {
			event.preventDefault();			
			if (file_frame) {
				file_frame.open();
				return;
			}

			file_frame = wp.media.frames.file_frame = wp.media({
				title: $(this).data('uploader_title'),
				button: {
					text: $(this).data('uploader_button_text'),
				},
				multiple: false
			});

			file_frame.on('select', function() {
				attachment = file_frame.state().get('selection').first().toJSON();
				//$( '#frontend-button' ).hide();
				$('#bpolls-image-preview').attr('src', attachment.url);
				if (attachment.url) {
					var data = {
						'action': 'bpolls_save_image',
						'image_url': attachment.url,
						'ajax_nonce': bpolls_ajax_object.ajax_nonce
					};
					$.post(bpolls_ajax_object.ajax_url, data, function(response) {

					});
				}
			});
			file_frame.open();
			$('.media-router button:first-child').click();
		});
	});
})(jQuery);
