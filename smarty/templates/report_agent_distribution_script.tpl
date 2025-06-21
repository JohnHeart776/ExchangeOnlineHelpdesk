<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', { 'packages':['corechart'] });
	google.charts.setOnLoadCallback(drawChart);

	let showUnassigned = false;
	let fullData;

	function drawChart(filterData = true) {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Agent');
		data.addColumn('number', 'Tickets');

        {foreach from=$report->getDistribution() item=element}
		data.addRow([
			'{if $element->user}{$element->user->DisplayName|escape:'javascript'}{else}Unassigned{/if}',
            {$element->count}
		]);
        {/foreach}

		if (!fullData) {
			fullData = data;
		}

		var displayData = filterData && !showUnassigned ?
			filterUnassigned(fullData) : fullData;

		var options = {
			pieHole: 0.4,
			legend: { position: 'right' },
			titleTextStyle: {
				fontSize: 16
			},
			subtitleTextStyle: {
				fontSize: 12,
				italic: true
			}
		};

		var chart = new google.visualization.PieChart(document.getElementById('chart'));
		chart.draw(displayData, options);
	}

	function filterUnassigned(data) {
		var view = new google.visualization.DataView(data);
		var rows = [];
		for (var i = 0; i < data.getNumberOfRows(); i++) {
			if (data.getValue(i, 0) !== 'Unassigned') {
				rows.push(i);
			}
		}
		view.setRows(rows);
		return view;
	}

	document.querySelector('.toggle-unassigned').addEventListener('click', function () {
		showUnassigned = !showUnassigned;
		this.innerHTML = `<i class="ki-duotone ki-eye fs-2"></i> ${ showUnassigned ? 'Hide' : 'Show' } Unassigned`;
		drawChart();
	});


</script>