(function ($) {
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



  $(document).ready(function () {

    $('#bpsts-status').keyup(function () {
      $('#bpsts-status').removeClass('bpsts-empty-status');
    });

    /*==================================================
	 =            Status textarea countdown.            =
	 ==================================================*/
    var text_max = $('#bpsts-status').attr('maxlength');
    $('#bpsts-charleft').html(text_max + '&nbsp;' + bpsts_ajax_object.char_left_txt);
    $('#bpsts-status').keyup(function () {
      var text_length = $('#bpsts-status').val().length;
      var text_remaining = text_max - text_length;

      $('#bpsts-charleft').html(text_remaining + bpsts_ajax_object.char_left_txt);
    });
    /*=====  End of Status textarea countdown.  ======*/

    /*====================================================================
    =            Ajax request to update comment in user meta.            =
    ====================================================================*/

    $(document).on('click', '.bpsts-add', function () {
      var clk_obj = $(this);
      var text = $(this).html();
      var bpsts_status = $('#bpsts-status').val();
      var user_id = $(this).data('userid');
      var setcurrent;
      if ($(this).data('setcurrent')) {
        setcurrent = true;
      } else {
        setcurrent = false;
      }

      if (bpsts_status) {
        clk_obj.html(text + ' <i class="fa fa-spinner fa-spin"></i>');
        var data = {
          'action': 'bpsts_add_status',
          'bpsts_status': bpsts_status,
          'user_id': user_id,
          'setcurrent': setcurrent,
          'ajax_nonce': bpsts_ajax_object.ajax_nonce
        };

        $.post(bpsts_ajax_object.ajax_url, data, function (response) {
          $('.bpsts-statuses-table').prepend(response);
          $('#bpsts-status').val('');
          clk_obj.html(text);
          if ($('.bpsts-placeholder-status-tr')) {
            $('.bpsts-placeholder-status').hide();
          }
          if (setcurrent) {
            location.reload(true);
          }
        });
      } else {
        $('#bpsts-status').addClass('bpsts-empty-status');
      }


    });

    /*=====  End of Ajax request to update comment in user meta.  ======*/

    /*======================================================
    =            Ajax request to delete status.            =
    ======================================================*/

    $(document).on('click', '.bpsts-del-status', function () {
      var click_event = $(this);
      var user_id = $(this).data('userid');
      var status_id = $(this).data('statusid');
      if (status_id && user_id && confirm(bpsts_ajax_object.cnf_del_txt)) {
        $(this).html('<i class="fa fa-spinner fa-spin"></i>');
        var data = {
          'action': 'bpsts_delete_status',
          'user_id': user_id,
          'status_id': status_id,
          'ajax_nonce': bpsts_ajax_object.ajax_nonce
        };

        $.post(bpsts_ajax_object.ajax_url, data, function (response) {
          click_event.parent().parent().remove();
        });
      } else {
        if (user_id && confirm(bpsts_ajax_object.cnf_del_icon)) {

          $(this).html('<i class="fa fa-spinner fa-spin"></i>');
          var data = {
            'action': 'bpsts_delete_status_icon',
            'user_id': user_id,
            'ajax_nonce': bpsts_ajax_object.ajax_nonce
          };
          $.post(bpsts_ajax_object.ajax_url, data, function (response) {
            click_event.parent().parent().remove();
          });

        }
      }
    });



    /*=====  End of Ajax request to delete status.  ======*/

    /*==============================================================
    =            Ajax request to update current status.            =
    ==============================================================*/

    $(document).on('click', '.bpsts-current-status', function () {
      var user_id = $(this).data('userid');
      var status_id = $(this).data('statusid');
      if (status_id && user_id) {
        $(this).html('<i class="fa fa-spinner fa-spin"></i>');
        var data = {
          'action': 'bpsts_current_status',
          'user_id': user_id,
          'status_id': status_id,
          'ajax_nonce': bpsts_ajax_object.ajax_nonce
        };

        $.post(bpsts_ajax_object.ajax_url, data, function (response) {
          window.location.reload();
        });
      }
    });

    /*=====  End of Ajax request to update current status.  ======*/

    /*====================================================
    =            Ajax request to edit status.            =
    ====================================================*/

    $(document).on('click', '.bpsts-edit-status', function () {
      var user_id = $(this).data('userid');
      var status_id = $(this).data('statusid');
      if (status_id && user_id) {

        $('.bpsts-add-actions').hide();
        $('.bpsts-update-actions').show();

        var old_text = $(this).parent().siblings().closest('.bpsts-user-status').text();
        $('#bpsts-status').val(old_text);

        $("#bpsts-for-update").data("userid", user_id);
        $("#bpsts-for-update").data("statusid", status_id);
      }
    });

    $(document).on('click', '.bpsts-update', function () {
      var clk_obj = $(this);
      var text = $(this).html();
      var user_id = $('#bpsts-for-update').data('userid');
      var status_id = $('#bpsts-for-update').data('statusid');
      if (status_id && user_id) {

        var bpsts_status = $('#bpsts-status').val();

        var setcurrent;
        if ($(this).data('setcurrent')) {
          setcurrent = true;
        } else {
          setcurrent = false;
        }

        if (bpsts_status) {
          clk_obj.html(text + ' <i class="fa fa-spinner fa-spin"></i>');
          var data = {
            'action': 'bpsts_update_status',
            'bpsts_status': bpsts_status,
            'user_id': user_id,
            'status_id': status_id,
            'setcurrent': setcurrent,
            'ajax_nonce': bpsts_ajax_object.ajax_nonce
          };

          $.post(bpsts_ajax_object.ajax_url, data, function (response) {

            $('#bpsts-status').val('');
            $('.bpsts-update-actions').hide();
            $('.bpsts-add-actions').show();
            $("#bpsts-for-update").data("userid", '');
            $("#bpsts-for-update").data("statusid", '');
            $('.bpsts-status-div').text(bpsts_status);
            $('.bpsts-user-status').text(bpsts_status);
            clk_obj.html(text);
            if (setcurrent) {
              window.location.reload();
            } else {
              window.location.reload();
            }
          });
        } else {
          $('#bpsts-status').addClass('bpsts-empty-status');
        }
      }
    });
    /*=====  End of Ajax request to edit status.  ======*/

    /*========================================
    =            Set icon images.            =
    ========================================*/

    $(document).on('click', '.bpsts-icon-img', function () {
      var id = $(this).data('id');
      var img_div = $('#' + id).clone();
      $('.bpsts-icon-dialog-title').html(img_div);
      $('.bpsts-icon-dialog').addClass('visible');
    });

    /*=====  End of Set icon images.  ======*/

    $(document).on('click', '.bpsts-icon-dialog-set', function () {
      var clk_obj = $(this);
      var okay_txt = $(this).html();

      var img = $('.bpsts-icon-dialog-title img.bpsts-icon-img');

      var imgname = img.data('imgname');
      var setnam = img.data('setnam');
      var userid = img.data('userid');
      var folder = img.data('folder');
      if (imgname && setnam) {
        $(clk_obj).html(okay_txt + ' <i class="fa fa-spinner fa-spin"></i>');
        var data = {
          'action': 'bpsts_update_icon_status',
          'imgname': imgname,
          'setnam': setnam,
          'userid': userid,
          'folder': folder,
          'ajax_nonce': bpsts_ajax_object.ajax_nonce
        };

        $.post(bpsts_ajax_object.ajax_url, data, function (response) {
          $('.bpsts-icon-dialog').removeClass('visible');
          $(clk_obj).html(okay_txt);
          window.location.reload();
        });
      }
    });

    $(document).on('click', '.bpsts-icon-dialog-cancel', function () {
      $('.bpsts-icon-dialog').removeClass('visible');
    });

    // $( document ).on( 'click', '.bpsts-open-reaction', function() {
    // 	$(this).parent().siblings('.bpsts-reaction-box').toggle();
    // });

    $(document).on('click', '.bpsts-mark-reaction', function () {
      var clk_obj = $(this);
      var obj_txt = $(this).html();

      var img = $('.bpsts-mark-reaction');

      var activityid = clk_obj.data('activityid');
      var imgname = clk_obj.data('imgname');
      var folder = clk_obj.data('folder');
      var imgurl = clk_obj.data('src');
      var imgindex = clk_obj.data('index');

      if (imgname && folder && activityid) {
        $(clk_obj).html('<i class="fa fa-spinner fa-spin"></i>');
        var data = {
          'action': 'bpst_activity_reaction',
          'imgname': imgname,
          'folder': folder,
          'imgurl': imgurl,
          'activityid': activityid,
          'imgindex': imgindex,
          'ajax_nonce': bpsts_ajax_object.ajax_nonce
        };

        $.post(bpsts_ajax_object.ajax_url, data, function (response) {
          var response = JSON.parse(response);

          var activity_div = clk_obj.parents().closest('.activity-content');
          //console.log(activity_div);
          if (activity_div.find('.bpsts-reactions-list').length !== 0) {
            activity_div.find('.bpsts-reactions-list').replaceWith(response.bpsts_activity_entry_reactions);
          } else {
            activity_div.find('.activity-inner').append(response.bpsts_activity_entry_reactions);
          }

          var reaction_div = $(clk_obj).parents().siblings('.bpsts-open-reaction-div').children('.bpsts-open-reaction');

          var ht_ml = '<a href="JavaScript:vid(0)" class="bpsts-mark-reaction" data-index="' + reaction_div.data('index') + '" data-imgname="' + reaction_div.data('imgname') + '" data-folder="' + reaction_div.data('folder') + '" data-activityid="' + reaction_div.data('activityid') + '" data-src="' + reaction_div.attr('src') + '"><img class="bpsts-icon-img" src="' + reaction_div.attr('src') + '"></a>';

          var check_replacement = $(clk_obj).parents().siblings('.bpsts-open-reaction-div');
          if (check_replacement.hasClass('first-reaction')) {
            var cr = false;
          } else {
            var cr = true;
          }
          $(clk_obj).parents().siblings('.bpsts-open-reaction-div').html(response.bpsts_open_reaction_div);
          if (cr) {
            $(clk_obj).replaceWith(ht_ml);
          } else {
            $(clk_obj).replaceWith('');
            check_replacement.removeClass('first-reaction');
          }

        });
      }
    });

  });
})(jQuery);