var max_alert_count;
var page_refresh;
var done;
var alerts;
var error;

function get_alerts() {
	$('div.alerts').empty();
	$('div.alerts').after('<img src="/images/loading.gif" class="loading" />');

	done = 1;
	alerts = 0;
	error = 0;

	for (i = 0; i < max_alert_count; i++) {
		$.get('/dashboard/'+i).done(function(data) {
			/* Success
			 */
			var list = $(data).find('list');
			if (list.length > 0) {
				var item = '<div class="panel panel-default top-alert">' + 
						   '<div class="panel-heading">' + $(list).attr('title') + '</div>' +
						   '<div class="panel-body"><table class="table table-condensed table-xs">' +
						   '<thead><tr><th>Name</th><th>Count</th><th>Change</th></tr></thead>' +
						   '<tbody>';

				$(list).find('item').each(function(){
					item += '<tr><td>' + $(this).text() + '</td>' +
							'<td>' + $(this).attr('count') + '</td>' +
							'<td>' + $(this).attr('change') + '%</td></tr>';
				});

				item += '</tbody></table></div></div>\n';

				$('div.alerts').append(item);

				alerts++;
			}

			if (done++ == max_alert_count) {
				$('img.loading').remove();
				if (alerts == 0) {
					$('div.alerts').append('There are no alerts at this moment.');
				}

				if (page_refresh > 0) {
					setTimeout(get_alerts, page_refresh * 60000);
				}
			}
		}).fail(function() {
			/* Error
			 */
			if (error == 0) {
				error = 1;
				$('img.loading').remove();
				$('div.alerts').append('Error while retrieving alerts. Login session expired?');
			}
		});
	}
}

$(document).ready(function() {
	$("#dialog").dialog({
		autoOpen:false
	});

	$("#opener").click(function() {
		$("#dialog").dialog("open");
	});

	$.get("/dashboard").done(function(data) {
		max_alert_count = $(data).find("max_alert_count").text();	
		page_refresh = parseInt($(data).find("page_refresh").text());
		get_alerts();
	});
});
