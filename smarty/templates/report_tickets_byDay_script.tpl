<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', { 'packages': [ 'line', 'corechart' ] });
	google.charts.setOnLoadCallback(drawChart);

	let fullData;

	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('date', 'Date');
		data.addColumn('number', 'Total Tickets');
		data.addColumn('number', 'Closed Tickets');
		data.addColumn('number', 'Open Tickets');

        {foreach from=$report->getDailyStats() item=element}
		data.addRow([
			new Date('{$element->date->format('Y-m-d')}'),
            {$element->totalCount},
            {$element->closedCount},
            {$element->openCount}
		]);
        {/foreach}

		fullData = data;

		var options = {
			chart: {
				title: 'Tickets by Day'
			},
			height: 400,
			legend: { position: 'top' },
			hAxis: {
				format: 'yyyy-MM-dd'
			}
		};

		var chart = new google.charts.Line(document.getElementById('chart'));
		chart.draw(data, google.charts.Line.convertOptions(options));
	}



</script>