(function($) {

	plotGraph();

	// hardcode colors
	var i = 0;
    $.each(mc4wp_statistics_data, function(key, val) {
        val.color = i;
        ++i;
    });

	
	function plotGraph() {
		var graphData = [];

		$("#mc4wp-graph-line-toggles :input:checked").each(function() {
			if($(this).is(':checked')) {
				console.log(mc4wp_statistics_data[$(this).val()]);
				graphData.push(mc4wp_statistics_data[$(this).val()]);
			}
		});

		$.plot( 
			"#mc4wp-graph",
			graphData,
			{
				xaxis: { 
					mode: "time",
					//min: startDate.getTime(),
					//max: endDate.getTime(),
					timeFormat: "%d/%b",
					minTickSize: mc4wp_statistics_settings.ticksize
				},
				yaxis: {
					min: 0, tickDecimals: 0
	   			},
				series: {
					lines: { show: true },
					points: { show: true }
				},
				grid: {
					hoverable: true
				}

			}
		);
	}

	$("#mc4wp-graph-line-toggles :input").change(plotGraph)

	

	function tooltip(x, y, contents)
	{
		$('<div id="mc4wp-graph-tooltip">' + contents + '</div>').css( {
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5,
			border: '1px solid #fdd',
			padding: '2px',
			'background-color': '#fee',
			opacity: 0.80
		}).appendTo("body").fadeIn(200);
	}

	var previousPoint = null;
	$("#mc4wp-graph").bind("plothover", function (event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));

		if (item) {
			if (previousPoint != item.dataIndex) {
				previousPoint = item.dataIndex;
				$("#mc4wp-graph-tooltip").remove();

				var x = item.datapoint[0],
				y = item.datapoint[1];

				tooltip( item.pageX, item.pageY, item.series.label + ': ' + y );
			}
		} else {
			$("#mc4wp-graph-tooltip").remove();
			previousPoint = null;
		}
	});

	$("#mc4wp-graph-range-options").change(function() {
		if($(this).val() == 'custom') {
			$("#mc4wp-graph-custom-range-options").show();
		} else {
			$("#mc4wp-graph-custom-range-options").hide();
		}

	});

})(jQuery);