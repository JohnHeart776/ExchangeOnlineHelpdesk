<script>
	$(document).ready(function () {
		let lastGuid = null;
		let hasMore = true;

		function loadTickets() {
			if (!hasMore) return;

			let params = {
				guid: '{$category->getGuid()}',
				limit: 100
			};

			if (lastGuid) {
				params.last = lastGuid;
			}

			$('#tickets-table-body').block();
			$.post('/api/admin/category/tickets.json', params, function (data) {
				hasMore = data.length >= 100;

				if (data.length > 0) {
					lastGuid = data[data.length - 1].guid;
					
					data.forEach(function (ticket) {
						let row = "<tr>" +
							"<td>"+ticket.ticketNumber+"</td>" +
							"<td>"+ticket.created+"</td>" +
							"<td>"+ticket.reportee.image+"</td>" +
							"<td>"+ticket.subject+"</td>" +
							"<td>"+ticket.statusBadge+"</td>" +
							"<td class='text-end'>" +
								"<a href=\"" + ticket.link + "\" class=\"btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1\">" +
									"<i class='ki-outline ki-book-open fs-2'></i>" +
								"</a>" +
							"</td>" +
							"</tr>";
						$('#tickets-table-body').append(row);
					});

					if (hasMore) {
						loadTickets();
						return;
					} else {
						$('#ticketsTable').DataTable({
							"pageLength": 500,

							"searchDelay": 500,
							"search": {
								"smart": true
							},
							"searching": true,

							"paging": true,
							"ordering": true,
							"lengthChange": false,
							"dom": "<'row'<'col-sm-12'f>>" +
								"<'row'<'col-sm-12'tr>>" +
								"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

						});
					}
				}

				$('#tickets-table-body').unblock();
			});
		}

		loadTickets();
		
		
	});
</script>