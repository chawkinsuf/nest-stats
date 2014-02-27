<div id="temperature-graph"   class="highcharts-graph" style="height:600px;"></div>

<script type="text/javascript">
$(function() {

	Highcharts.setOptions({
		global : {
			timezoneOffset: (new Date()).getTimezoneOffset()
		}
	});

	var rangeButtons = [
		{ type: 'day', count: 1, text: '1d' },
		{ type: 'week', count: 1, text: '1w' },
		{ type: 'week', count: 2, text: '2w' },
		{ type: 'month', count: 1, text: '1m' }
	];

	$.getJSON('{{ URL::to('graphs/data') }}', function( data ) {

		$('#temperature-graph').highcharts('StockChart', {

			rangeSelector : {
				selected : 1,
				buttons: rangeButtons
			},
			colors: [
				'#2f7ed8',
				'#0d233a',
				'#8bbc21',
				'#1aadce',
				'#debd16',
				'#910000'
			],
			exporting: {
				sourceWidth: 1000,
				sourceHeight: 600
			},
			title: {
				text: 'Temperature Data',
				align: 'left',
				x: 20,
				y: 25,
				style: {
					fontSize: '28px'
				}
			},
			legend: {
				width: 295,
				shadow: true,
				enabled: true,
				itemDistance: 16,
				verticalAlign: 'top',
				align: 'right',
				floating: true,
				x: -40,
				y: -8
			},
			tooltip: {
				formatter: function() {
					var date = Highcharts.dateFormat( '%A, %b %e, %H:%M', this.x );
					var s = '<span style="font-size:10px">'+ date +'</span>';
					$.each( this.points, function( i, point ){
						if ( /Temperature/.test(point.series.name) ){
							s += '<br/><span style="fill:' + point.series.color + '">' + point.series.name + '</span><span>: </span>';
							s += '<span style="font-weight:bold">' + point.y + '</span>';
						}
					});
					return s;
				},
				shared: true
			},
			yAxis: [{
				title: {
					text: 'Temperature (°F)'
				},
				height: 300,
				lineWidth: 2,
				endOnTick: false
			},{
				title: {
					text: 'Status'
				},
				top: 400,
				height: 100,
				lineWidth: 2,
				offset: 0,
				max: 1.2,
				tickInterval: 1,
				gridLineWidth: 0,
				showLastLabel: true,
				startOnTick: false,
				endOnTick: false,
				labels: {
					y: -5,
					formatter: function(){
						return this.value == 1 ? 'On' : 'Off';
					}
				}
			}],
			series : [{
				name: 'Inside Temperature',
				yAxis: 0,
				data: data['temperature']
			},{
				name: 'Outside Temperature',
				yAxis: 0,
				data: data['outside_temperature']
			},{
				name: 'Fan',
				yAxis: 1,
				data: data['fan']
			},{
				name: 'AC',
				yAxis: 1,
				data: data['ac']
			},{
				name: 'Heat',
				yAxis: 1,
				data: data['heat']
			},{
				name: 'Alt Heat',
				yAxis: 1,
				data: data['alt_heat']
			}]
		});
	});

});
</script>