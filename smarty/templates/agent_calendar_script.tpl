<script>
	document.addEventListener('DOMContentLoaded', function () {
		var calendarEl = document.getElementById('calendar');
		var calendar = new FullCalendar.Calendar(calendarEl, {
			// initialView: 'dayGridMonth',
			initialView: 'timeGridWeek',
			firstDay: 1,
			locale: 'de',
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay'
			},
			eventTimeFormat: { hour: undefined, minute: undefined, omitZeroMinute: true },
			eventContent: function(arg) {
				return { html: '<div class="fc-event-title">' + arg.event.title + '</div>' };
			},

			displayEventEnd: false,

			events: '/api/agent/calendar.json',
			eventDidMount: function (info) {
				var event = info.event;
				var start = event.start;
				var eventHour = start.getHours();
				if (eventHour < calendar.getOption('slotMinTime').split(':')[0]) {
					calendar.setOption('slotMinTime', eventHour + ':00:00');
				}
				if (eventHour > calendar.getOption('slotMaxTime').split(':')[0]) {
					calendar.setOption('slotMaxTime', (eventHour + 1) + ':00:00');
				}
			},
			slotMinTime: '06:00:00',
			slotMaxTime: '20:00:00',
			editable: true,
			dayMaxEvents: true,
			height: 'auto',
			allDaySlot: false
		});
		calendar.render();
	});
</script>