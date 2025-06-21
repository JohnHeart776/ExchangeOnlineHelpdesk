<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', { 'packages': [ 'corechart' ] });
	google.charts.setOnLoadCallback(drawChart);

	let fullData;

	function getDayName(dayNum) {
		const days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
		return days[dayNum - 1];
	}

	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Day of Week');
		data.addColumn('number', 'Hour of Day');
		data.addColumn('number', 'Ticket Count');
		data.addColumn('number', 'Bubble Size');

        {foreach from=$report->getTimeScatterStats() item=element}
		data.addRow([
			getDayName({$element->dayOfWeek}),
			{$element->hourOfDay},
            {$element->ticketCount},
			{$element->ticketCount}
		]);
        {/foreach}

		fullData = data;

		var options = {
			title: 'Tickets by Day and Time',
			height: 400,
			hAxis: {
				title: 'Day of Week',
				slantedText: true,
			},
			vAxis: {
				title: 'Hour of Day',
				minValue: 0,
				maxValue: 23,
				viewWindow: {
					min: 0,
					max: 23
				}
			},
			legend: {
				position: 'right'
			},
			colors: [ '#CCE5FF', '#004C99' ],
			pointSize: 15,
			dataOpacity: 0.7
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('chart'));
		chart.draw(data, options);
	}



</script>