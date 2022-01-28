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

	// $('.bpsts-icon-img').draggable({
	// 	revert: "invalid",
	// 	stack: ".draggable",
	// 	helper: 'clone'
	// });

	// $('.bpsts-drop-reaction-icons').droppable({
	//     accept: ".bpsts-icon-img",
	//     drop: function (event, ui) {
	//         var droppable = $(this);
	//         var draggable = ui.draggable;
	//         // Move draggable into droppable
	//         draggable.clone().appendTo(droppable);
	//         //draggable.css({top: '5px', left: '5px'});
	//     }
	// });

	/*======================================================
	=            drag drop images for reaction.            =
	======================================================*/
	$(".bpsts-image-holder .bpsts-icon-div").draggable({
		connectToSortable: '.bpsts-drop-reaction-icons',
		helper: 'clone',
		revertDuration: 0,
		create: function () {
			//var eq = $(this).index();
			//$(this).attr('data-index', eq);
		}
	});

	$(".bpsts-drop-reaction-icons").sortable({
		connectWith: '.bpsts-drop-reaction-icons',
		placeholder: "ui-state-highlight",
		receive: function (event, ui) {
			var uiIndex = ui.item.attr('data-index');
			var img_name = ui.item.attr('data-imgname');
			var fol_der = ui.item.attr('data-folder');
			var item =  $(this).find('[data-index=' + uiIndex + ']');
			if (item.length > 1) {
				item.last().remove();
			}else{

				item.append('<div class="'+uiIndex+'"></div>');
				//$(this).append('<div class="'+uiIndex+'"></div>');
				//$(this).append('<div class="'+uiIndex+'"></div>');

				//$('.bpsts-drop-reaction-icons').append('<div class="'+uiIndex+'"></div>');
				$('<span class="remove-reaction" data-close="'+uiIndex+'"><i class="fa fa-times" aria-hidden="true"></i></span>').appendTo('.'+uiIndex);
				$('<input>').attr({
					type: 'hidden',
					name: 'bpsts_icon_settings[reactions]['+uiIndex+'][imgname]',
					value: img_name,
				}).appendTo('.'+uiIndex);
				$('<input>').attr({
					type: 'hidden',
					name: 'bpsts_icon_settings[reactions]['+uiIndex+'][folder]',
					value: fol_der,
				}).appendTo('.'+uiIndex);
			}
		},
		revert: true
	});

	// $(".bpsts-drop-reaction-icons img").draggable({
	// 	connectToSortable: '.bpsts-drop-reaction-icons',
	// 	placeholder: "ui-state-highlight",
	// 	revert: true
	// });
	
	/*=====  End of drag drop images for reaction.  ======*/
	
	$(document).on('click','.remove-reaction', function(){
		console.log( $(this).parent().parent('.rc-div:first') );
		$(this).parent().parent('.bpsts-icon-div:first').remove();
		// var to_remove = $(this).data('close');
		// $(this).parent().prev('.bpsts-icon-img:first').remove();
		// $(this).parent().remove();
	});
})( jQuery );
