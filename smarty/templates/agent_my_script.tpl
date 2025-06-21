<script>
	$(document).ready(function () {
		$('.change-status').click(function (e) {
			e.preventDefault();
			var that = $(this);

			Swal.fire({
				title: 'Confirmation',
				text: 'Really assign ticket to me?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/ticket/status/assign.json", that.data(), function (res) {
						if (res.status) {
							window.location.reload();
						} else {
							$.unblockUI();
							Swal.fire('Error', res.message, 'error');
						}
					}, "json");
				}
			});
		});

		$('.btnCloseTicket').click(function () {
			var that = $(this);

 		Swal.fire({
 			title: 'Confirmation',
 			text: 'Really close ticket?',
 			icon: 'warning',
 			showCancelButton: true,
 			confirmButtonText: 'Yes',
 			cancelButtonText: 'No'
 		}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/ticket/status/close.json", that.data(), function (res) {
						if (res.status) {
							window.location.reload();
						} else {
							$.unblockUI();
							Swal.fire('Error', res.message, 'error');
						}
					}, "json");
				}
			});
		});

 	$('.bulk-action-extend-due').click(function (e) {
 		e.preventDefault();
 		var selectedTickets = $('input[name="ticket[]"]:checked').map(function () {
 			return $(this).val();
 		}).get();

 		if (selectedTickets.length === 0) {
 			Swal.fire('Error', 'Please select at least one ticket.', 'error');
 			return;
 		}

			$.blockUI();

			var requests = selectedTickets.map(function (ticketId) {
				return $.post("/api/agent/ticket/duedate/change.json", {
					ticket:  ticketId,
					target: '24h',
					custom: false
				});
			});

			Promise.all(requests)
				.then(function (responses) {
					var hasError = responses.some(function (res) {
						return !res.status;
					});

 				if (!hasError) {
 					window.location.reload();
 				} else {
 					$.unblockUI();
 					Swal.fire('Error', 'Error processing some tickets', 'error');
 				}
				})
 			.catch(function () {
 				$.unblockUI();
 				Swal.fire('Error', 'An error occurred', 'error');
 			});
		});

 	$('.bulk-action-combine').click(function (e) {
 		e.preventDefault();
 		var selectedTickets = $('input[name="ticket[]"]:checked').map(function () {
 			return $(this).val();
 		}).get();

 		if (selectedTickets.length === 0) {
 			Swal.fire('Error', 'Please select at least one ticket.', 'error');
 			return;
 		}

			$.blockUI();
 		$.post("/api/agent/tickets/combine.json", { tickets: selectedTickets }, function (res) {
 			if (res.status) {
 				window.location.reload();
 			} else {
 				$.unblockUI();
 				Swal.fire('Error', res.message, 'error');
 			}
 		}, "json");
		});




		$('.calendarDayItem').click(function () {
			var that = $(this);
			var date = that.data('date');
			var target = $('#agentTicketCalendarContent');
			target.empty().block();

			var template = ""+
				"<div class=\"d-flex align-items-center mb-6\">"+
					"<span data-kt-element=\"bullet\" class=\"bullet bullet-vertical d-flex align-items-center min-h-70px mh-100 me-4\"></span>"+
					"<div class=\"flex-grow-1 me-5\">"+
						"<span class=\"ticketIsDue badge badge-warning text-gray-800 fw-semibold fs-8\">#due#</span>"+
						"<span class=\"ticketStatus\"></span>"+
						"<div class=\"ticketCreated text-gray-800 fw-semibold fs-6\"></div>"+
						"<div class=\"ticketSubject text-gray-700 fw-semibold fs-5\"></div>"+
						"<div class=\"text-gray-500 fw-semibold fs-7\">"+
							"<span class=\"ticketDescription\"></span>"+
							"<a class=\"ticketOrganizationUser\" href=\"\" style=\"max-width: 175px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;\">{$ticket->MessengerName}</a>"+
							"<span class=\"ticketNonOrganizationUser\">"+
								"<span style=\"max-width: 175px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;\"></span>"+
								"<span style=\"max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;\" title=\"\"></span>"+
							"</span>"+
						"</div>"+
					"</div>"+
					"<a href=\"\" class=\"ticketLink btn btn-sm btn-light\"><i class=\"fas fa-arrow-right\"></i></a>"+
				"</div>";

			//fetch the items of that day
			$.post("/api/agent/tickets/calendar/day.json", { date: date }, function (res) {
				res.forEach(function (ticket) {

					var templateElement = $(template);

					if (!ticket.isDue) {
						templateElement.find('.ticketIsDue').remove();
						if (ticket.isOpen)
							templateElement.find('.bullet').addClass("bg-info");
						else
							templateElement.find('.bullet').addClass("bg-success");
					} else {
						templateElement.find('.ticketIsDue').text("since "+ticket.dueDatetime+" due");
						templateElement.find('.bullet').addClass("bg-danger");
					}

					templateElement.find('.ticketStatus').html(ticket.statusBadge);

					templateElement.find('.ticketCreated').text(ticket.created);

					templateElement.find('.ticketSubject').text(ticket.subject || '');

					var description = "Ticket #"+ticket.ticketNumber+" von <img src=\""+ticket.reportee.imageLink+"\" style=\"border-radius: 50%; width: 24px;\" title=\""+ticket.reportee.name+"\">";
					templateElement.find('.ticketDescription').html(description);

					if (ticket.reportee.organizationUser) {
						templateElement.find('ticketNonOrganizationUser').remove();
						templateElement.find('.ticketOrganizationUser')
							.text(ticket.reportee.organizationUser.displayName)
							.attr('href', '/agent/organizationuser/' + ticket.reportee.organizationUser.guid);
					} else {
						templateElement.find('ticketOrganizationUser').remove();

						templateElement.find('.ticketOrganizationUser').hide();
						templateElement.find('.ticketNonOrganizationUser span:first').text(ticket.reportee.name);
						templateElement.find('.ticketNonOrganizationUser span:last').text(ticket.reportee.email).attr('title', ticket.MessengerEmail || '');
					}

					templateElement.find('.ticketLink').attr('href', '/ticket/' + ticket.ticketNumber);
					templateElement.find('[data-kt-element="bullet"]').addClass('bg-' + (ticket.Status === 'Open' ? 'primary' : 'success'));

					target.append(templateElement);
				});

				target.unblock();
			});
		});

		function selectTodayCalendarNavItem() {

			var today = new Date();
			var currentDate = today.getFullYear() + '-' +
				String(today.getMonth() + 1).padStart(2, '0') + '-' +
				String(today.getDate()).padStart(2, '0');
			var that = $('.nav-item[data-date="' + currentDate + '"]');
			that.children(".nav-link").addClass("active").click();
		}
		selectTodayCalendarNavItem();


		$.getJSON("/api/agent/tickets/my/open.json", function (tickets) {

			var openTicketCounter = $('.countTicketsOpen');
			openTicketCounter.text(tickets.length);

			var template = $(`<tr>
                <td>
                    <div class="d-flex align-items-center ticketOpenListElement1">
                        <div class="d-flex justify-content-start flex-column">
                            <a href="">
                                <span class="text-gray-800 fw-bold mb-1 fs-6"></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center ticketOpenListElement2">
                        <div class="symbol symbol-45px me-5"></div>
                        <div class="d-flex justify-content-start flex-column">
                            <span class="badge mb-2"></span>
                            <a href="" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6"></a>
                            <span class="ticketReporteeName text-muted fw-semibold text-muted d-block fs-7"></span>
                        </div>
                    </div>
                </td>
                <td class="ticketOpenListElement3">##categoryName##</td>
            </tr>`);

			var target = $('#agentTicketListOpen');
			target.empty();

			tickets.forEach(ticket => {
				var html = template.clone();
				html.find(".ticketOpenListElement1")
					.find("a")
					.attr("href", "/ticket/"+ticket.ticketNumber+"/")
						.children("span")
						.text(ticket.ticketNumber);

				html.find(".ticketOpenListElement2")
					.children(".symbol").empty().append($(ticket.reportee.image).css("width",""));

				html.find(".ticketOpenListElement2").find(".badge").text(ticket.dueDatetime);
				if (ticket.isDue)
					html.find(".ticketOpenListElement2").find(".badge").addClass("badge-danger");
				else
					html.find(".ticketOpenListElement2").find(".badge").addClass("badge-info");

				html.find(".ticketOpenListElement2").find("a").attr("href", "/ticket/"+ticket.ticketNumber+"/").text(ticket.subject);
				html.find(".ticketOpenListElement2").find(".ticketReporteeName").text(ticket.reportee.name);


				html.find(".ticketOpenListElement3").html(ticket.category.markup);



				target.append(html);
			});


		});


	});


</script>
