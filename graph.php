<html>
<body>
<script src="http://api.overrustle.com/socket.io/socket.io.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://github.highcharts.com/highstock.js"></script>
<script src="http://code.highcharts.com/stock/modules/exporting.js"></script>

<div id="container" style="min-width: 100%; height: 100%; margin: 0 auto"></div>

<script>
$(function() {
	
	Highcharts.setOptions({
		global : {
			useUTC : false
		}
	});
	
	// Create the chart
	$('#container').highcharts('StockChart', {
		chart : {
			events : {
				load : function() {

					// set up the updating of the chart each second
					var series = this.series[0];
					var chart = this;

					var socket = io.connect('http://api.overrustle.com');
					socket.on('strims', function (api_data) {
						var connections = api_data["connections"]
						var x = (new Date()).getTime();

						series.addPoint([x, connections]);
						chart.redraw();
					});

					socket.on('error', function(error){
						console.log(error)
					});
				}
			}
		},
		
		rangeSelector: {
			buttons: [{
				count: 1,
				type: 'minute',
				text: '1M'
			}, {
				count: 5,
				type: 'minute',
				text: '5M'
			}, {
				type: 'all',
				text: 'All'
			}],
			inputEnabled: false,
			selected: 0
		},
		
		title : {
			text : 'api.overrustle.com - Connections (Streams + Idlers)'
		},
		
		exporting: {
			enabled: true
		},
		
		series : [{
			name : 'Connections (Streams + Idlers)',
			data: [null]
		}]
	});

});

</script>