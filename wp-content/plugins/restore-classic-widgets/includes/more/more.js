jQuery(document).ready(function ($) {
  $('#rcwimagewaitfbl').hide();
  // $( "*" ).click(function() {
  $("#restoreclassicmore").click(function (evt) {
    evt.preventDefault();
    prodclass = 'restore-classic-more';
    $billmodal = $('.' + prodclass + '-wrap-deactivate');
    $billmodal.prependTo($('#wpcontent')).slideDown();
    $('html, body').scrollTop(0);
    $billmodal = $('.' + prodclass + '-wrap-deactivate');
    $("." + prodclass + "-close-dialog").click(function (evt) {
      if (!$(this).hasClass('disabled')) {
        $('#rcwimagewaitfbl').hide();
        $billmodal.slideUp();
      }
    });
    $("." + prodclass + "-close-submit").click(function (evt) {
      $('#rcwimagewaitfbl').show();

      $( "."+prodclass+"-close-submit" ).addClass('disabled');
      $( "."+prodclass+"-close-dialog" ).addClass('disabled');
      $( "."+prodclass+"-deactivate" ).addClass('disabled');

      
      jQuery.ajax({
        url: ajaxurl,
        data: {
          'action': 'restoreclassic_install_wptools'
        },
        success: function (data) {
          if (data == 'OK') {
            console.log(data);
            $('#rcwimagewaitfbl').hide();
            alert('WP TOOLS Installed Successively!\nGo to Menu => WP Tools');
          }
          else {
            alert('WP TOOLS Fail! Please, click Detais and Install Manually');
          }
          $billmodal.slideUp();
          window.location.reload(true);
        },
        error: function (errorThrown) {
          //console.log(errorThrown);
          alert('WP TOOLS Fail! Please, click Detais and Install Manually');
        }
      });
    }); // end clicked button share ...
  });
});  // end jQuery  