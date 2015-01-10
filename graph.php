<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Watch streams and videos with destiny.gg!">
	<link rel="icon" href="favicon.ico">

	<title>Connecting...</title>
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-49711133-1', 'overrustle.com');
	  ga('send', 'pageview');
	</script>

	</head>
	<body>
		<div id="container" style="width: 99%; height: 99%; margin: 0; position:absolute;"></div>
	</body>
</html>
<script src="//api.overrustle.com/socket.io/socket.io.js"></script>
<script src="js/jquery-1.11.2.min.js"></script>
<script src="js/highstock.js"></script>
<script src="js/highstock-exporting.js"></script>
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
					var chart = this;
					var socket = io.connect('http://api.overrustle.com/streams');
					socket.on('strims', function (api_data) {
						var connections = api_data["connections"]
						var idle = api_data["idlecount"]
						var viewer = api_data["viewercount"]
						var x = (new Date()).getTime();

						chart.series[0].addPoint([x, connections]);
						chart.series[1].addPoint([x, idle]);
						chart.series[2].addPoint([x, viewer]);
						document.title = connections + " Connected (" + viewer + "/" + idle + ")";

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
			},{
				count: 1,
				type: 'hour',
				text: '1H'
			},{
				count: 6,
				type: 'hour',
				text: '6H'
			},{
				count: 12,
				type: 'hour',
				text: '12H'
			},{
				count: 24,
				type: 'hour',
				text: '1D'
			},{
				type: 'all',
				text: 'All'
			}],
			inputEnabled: false,
			selected: 0
		},
		
		title : {
			text : 'api.overrustle.com - Socket.IO Websocket Connections'
		},
		
		exporting: {
			enabled: true
		},
		
		series : [{
			name : 'Total Connections',
			data: [null]
		}, {
			name : 'Idle Connections',
			data: [null]
		}, {
			name : 'Viewer Connections',
			data: [null]
		}]
	});

});

</script>
  </body>
</html>
