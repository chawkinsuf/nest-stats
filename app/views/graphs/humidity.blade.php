<div id="humidity-graph" class="highcharts-graph"></div>
<div id="data-sets"></div>

<script type="text/javascript">
$(function() {
	$('#data-sets').treeview({ data: {{ $dataSets }}, levels: 1, nodeIcon: 'glyphicon glyphicon-repeat', selectLeafOnly: true, noDeselect: true })
	.on('nodeSelected', function(event, node) {
		$.getJSON('{{ URL::to('graphs/data') }}/' + node.dataSetId )
		.done( function( data ){

			// Make our series data
			var seriesData = formatSeriesData( data );
			
			// Update each series in the chart
			$.each( seriesData.series, function( index, value ){
				chart.series[ index ].setData( value.data, false );
			});

			// Redraw all at once
			chart.redraw();

			// Call update on the navigator series to make sure it displays properly
			chart.series[ seriesData.series.length ].update( {}, true );
		})
		.fail( function(){
			alertify.error( 'There was a problem loading this data set.' );
		});
	});

	var chart = null;
	var chartOptions = {
		chart: {
			renderTo: 'humidity-graph',
			zoomType: 'x'
		},
		colors: [
			'#2f7ed8',
			'#0d233a',
			'#8bbc21',
			'#1aadce',
			'#debd16',
			'#910000'
		],
		title: {
			text: 'Humidity Data',
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
		},
		rangeSelector : {
			enabled: true,
			selected : 0,
			labelStyle: {
				display: 'inline'
			},
			buttons: [
				{ type: 'day', count: 1, text: '1d' },
				{ type: 'day', count: 2, text: '2d' },
				{ type: 'day', count: 3, text: '3d' },
				{ type: 'day', count: 5, text: '5d' },
				{ type: 'week', count: 1, text: '1w' },
				{ type: 'week', count: 2, text: '2w' },
				{ type: 'month', count: 1, text: '1m' },
				{ type: 'all', count: 1, text: 'All' }
			]
		},
		navigator: {
			enabled: true
		},
		scrollbar : {
			enabled : false
		},
		tooltip: {
			formatter: function() {
				var date = Highcharts.dateFormat( '%A, %b %e, %H:%M', this.x );
				var s = '<span style="font-size:10px">'+ date +'</span>';
				$.each( this.points, function( i, point ){
					if ( /Humidity/.test(point.series.name) ){
						s += '<br/><span style="fill:' + point.series.color + '">' + point.series.name + '</span><span>: </span>';
						s += '<span style="font-weight:bold">' + point.y + '%</span>';
					}
				});
				return s;
			},
			shared: true
		},
		yAxis: [{
			title: {
				text: 'Humidity (%)'
			},
			lineWidth: 2,
			endOnTick: false,
		},{
			title: {
				text: 'Status'
			},
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
		}]
	};

	var optionsSmall = {
		optionsSet: true,
		exporting: {
			sourceWidth: 700,
			sourceHeight: 400
		},
		legend: {
			verticalAlign: 'bottom',
			align: 'center',
			floating: false,
			x: 0,
			y: 0
		},
		rangeSelector: {
			inputEnabled: false
		},
		navigator: {
			height: 20
		},
		yAxis: [{
			height: 155,
			startOnTick: false
		},{
			top: 250,
			height: 40
		}]
	};

	var optionsDefault = {
		optionsSet: true,
		exporting: {
			sourceWidth: 1050,
			sourceHeight: 600
		},
		legend: {
			verticalAlign: 'top',
			align: 'right',
			floating: true,
			x: -40,
			y: -8
		},
		rangeSelector: {
			inputEnabled: true
		},
		navigator: {
			height: 40
		},
		yAxis: [{
			height: 360,
			startOnTick: true
		},{
			top: 455,
			height: 60
		}]
	};

	enquire.register('(max-width: 650px)', {
		match : function() {

			// If we already have a chart we need to use its options
			if ( chart ){
				chartOptions = chart.options;
			}

			// Set the new options
			$.extend( true, chartOptions, optionsSmall );

			// Recreate the chart
			if ( chart ){
				chart.destroy();
				chart = new Highcharts.StockChart( chartOptions );
			}
		},
		unmatch : function() {

			// If we already have a chart we need to use its options
			if ( chart ){
				chartOptions = chart.options;
			}

			// Set the new options
			$.extend( true, chartOptions, optionsDefault );

			// Recreate the chart
			if ( chart ){
				chart.destroy();
				chart = new Highcharts.StockChart( chartOptions );
			}
		}
	});

	$.getJSON('{{ URL::to('graphs/data') }}' )
	.done( function( data ){

		// Make our series data
		var seriesData = formatSeriesData( data );

		// Add the data to our options
		$.extend( true, chartOptions, seriesData );

		// Add default options if needed
		if ( ! chartOptions.optionsSet ){
			$.extend( true, chartOptions, optionsDefault );
		}

		// Render the chart
		chart = new Highcharts.StockChart( chartOptions );
	})
	.fail( function(){
		alertify.error( 'There was a problem loading the default data set.' );
	});

	function formatSeriesData( data ){

		var seriesData = {
			series : [{
				name: 'Inside Humidity',
				yAxis: 0,
				data: data['humidity']
			},{
				name: 'Outside Humidity',
				yAxis: 0,
				data: data['outside_humidity']
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
				name: 'Aux Heat',
				yAxis: 1,
				data: data['aux_heat']
			}]
		};

		return seriesData;
	}

});
</script>