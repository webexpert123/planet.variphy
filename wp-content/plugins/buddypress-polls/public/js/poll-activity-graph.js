(function($) {
  'use strict';
  var load_Chart;
  var ajax_Chart;
  window.onload = function() {
    $('.poll-bar-chart').each(function() {
      var act_id = $(this).data('id');
      var id = $(this).attr('id');
      var res = JSON.parse(bpolls_wiget_obj.votes);
      
      if (res[act_id]) {
        var arr_label = [];
        var arr_per = [];
        var arr_color = [];
        $.each(res[act_id], function(i, item) {
          arr_label.push(item.label.toString());
          arr_per.push(item.y.toString());
          arr_color.push(item.color);
        });

        load_Chart = new Chart($("#" + id), {
          type: 'pie',
          data: {
            labels: arr_label,
            datasets: [{
              label: "Poll Activity Graph (%)",
              backgroundColor: arr_color,
              data: arr_per
            }]
          },
          options: {
            title: {
              display: true,
              text: res[act_id][0].poll_title
            }
          }
        });
      }

    });



    $('.poll-activity-chart').each(function() {
      var act_id = $(this).data('id');
      var id = $(this).attr('id');
      var res = JSON.parse(bpolls_wiget_obj.votes);
      
      if (res[act_id]) {
        var arr_label = [];
        var arr_per = [];
        var arr_color = [];
        $.each(res[act_id], function(i, item) {
          arr_label.push(item.label.toString());
          arr_per.push(item.y.toString());
          arr_color.push(item.color);
        });

        load_Chart = new Chart($("#" + id), {
          type: 'pie',
          data: {
            labels: arr_label,
            datasets: [{
              label: "Poll Activity Graph (%)",
              backgroundColor: arr_color,
              data: arr_per
            }]
          },
        });
      }

    });
  }
  
  if ( $( ".bpolls-activities-list option:selected" ).val() != '' ) {
	  $('.bpolls-activities-list').trigger("change");
  }
  $(document).on(
    'change', '.bpolls-activities-list',
    function() {
      var actid = $(this).find(":selected").val();
      var clickd_obj = $(this);
      var data = {
        'action': 'bpolls_activity_graph_ajax',
        'actid': actid,
        'ajax_nonce': bpolls_wiget_obj.ajax_nonce
      };

      $.post(bpolls_wiget_obj.ajax_url, data, function(response) {
        clickd_obj.parents().siblings('.poll-bar-chart').remove();
        $('<canvas class="poll-bar-chart" data-id="' + actid + '" id="bpolls-activity-chart-' + actid + '"></canvas>').insertAfter(clickd_obj.parents('.bpolls-activity-select'));
        var resp = JSON.parse(response);
        var arr2_label = [];
        var arr2_per = [];
        var arr2_color = [];
        var arr2_title = [];
        var title = '';
        $.each(resp[actid], function(i, item) {
          arr2_label.push(item.label.toString());
          arr2_per.push(item.y.toString());
          arr2_color.push(item.color);
          title = item.poll_title;
        });

        ajax_Chart = new Chart(clickd_obj.parents().siblings('.poll-bar-chart'), {
          type: 'pie',
          data: {
            labels: arr2_label,
            datasets: [{
              label: "Poll Activity Graph (%)",
              backgroundColor: arr2_color,
              data: arr2_per
            }]
          },
          options: {
            title: {
              display: true,
              text: title
            }
          }
        });
      });
    }
  );
	
	$( document ).ready(function(){
		$(document).on( 'change', '.bpolls-activities-list', function(e) {
			e.preventDefault();
			$('#export-poll-data').attr( 'href', '?export_csv=1&buddypress_poll=1&activity_id=' + $(this).val() );
		});
	});
})(jQuery);