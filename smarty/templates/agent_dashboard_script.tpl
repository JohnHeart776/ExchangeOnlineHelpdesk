<script>
	$(document).ready(function () {
		let highestTicketNumber = '0';
		let originalTitle = document.title;
		let titleInterval;

		// Find highest ticket number
		$('.ticketNumberContainer').each(function () {
			let ticketNumber = $(this).data('ticketnumber');
			if (parseInt(ticketNumber, 16) > parseInt(highestTicketNumber, 16)) {
				highestTicketNumber = ticketNumber;
			}

		});

		let checkTicketsInterval;

		function checkForNewTickets() {
			$.get("/api/agent/tickets/since.json", { since: highestTicketNumber }, function (res) {
				console.log("searching for new tickets since ", highestTicketNumber, " got ", res);
				if (res.newTicketsAvailable) {
					clearInterval(checkTicketsInterval);
					if (!titleInterval) {
						let titleFlip = true;
						titleInterval = setInterval(function () {
							document.title = titleFlip ? '*** NEW TICKETS ***' : originalTitle;
							titleFlip = !titleFlip;
						}, 1000);
					}

					Swal.fire({
						title: 'New Tickets',
						text: 'New tickets are available!',
						icon: 'info',
						confirmButtonText: 'OK'
					}).then((result) => {
						if (result.isConfirmed) {
							window.location.reload();
						}
					});
				}
			});
		}

		checkTicketsInterval = setInterval(checkForNewTickets, 20000);


		$('.btnAssignTicketToMe').click(function () {
			var that = $(this);

			$.blockUI();
			$.post("/api/agent/ticket/assign/me.json", that.data(), function (res) {
				$.unblockUI();
				if (res.status) {
					$.blockUI();
					window.location.reload();
				}
			}, "json");
		});

		$('.btnCloseTicket').click(function () {
			var that = $(this);
			$.blockUI();
			$.post("/api/agent/ticket/status/close.json", that.data(), function (res) {
				$.unblockUI();
				if (res.status) {
					that.closest('tr').slideUp(400, function () {
						$(this).remove();
					});
				}
			}, "json");
		});

		$('.btnSolveTicket').click(function () {
			var that = $(this);
			$.blockUI();
			$.post("/api/agent/ticket/status/solve.json", that.data(), function (res) {
				$.unblockUI();
				if (res.status) {
					that.closest('tr').slideUp(400, function () {
						$(this).remove();
					});
				}
			}, "json");
		});

		$('.btnAssignAndWorkOnTicket').click(function () {
			var that = $(this);
			$.blockUI();
			$.post("/api/agent/ticket/status/assignAndWorkOn.json", that.data(), function (res) {
				$.unblockUI();
				if (res.status) {
					Swal.fire({
						title: 'Redirect',
						text: 'Do you want to be redirected to the ticket?',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Yes',
						cancelButtonText: 'No',
						timer: 1250,
						timerProgressBar: true,
						showCloseButton: false,
						allowEscapeKey: true
					}).then((result) => {

						$.blockUI();
						if (result.isConfirmed || (result.isDismissed && result.dismiss === "timer")) {
							window.location.href = res.data.link;
						} else {
							window.location.reload();
						}
					});
				}
			}, "json");
		});




		$('.btnCloseSelectedTickets').click(function () {
			var checkedTickets = $('.ticket-check:checked');
			var count = checkedTickets.length;
			var tickets = [];

			if (count === 0) return;

			checkedTickets.each(function () {
				tickets.push($(this).val());
			});

			Swal.fire({
				title: 'Confirmation',
				text: 'Really close ' + count + ' selected tickets?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/tickets/status/close.json", { tickets: tickets, assignMe: "yes", }, function (res) {
						// $.unblockUI();
						if (res.status) {
							window.location.reload();
						}
					}, "json");
				}
			});
		});

		$('.btnSolveSelectedTickets').click(function () {
			var checkedTickets = $('.ticket-check:checked');
			var count = checkedTickets.length;
			var tickets = [];

			if (count === 0) return;

			checkedTickets.each(function () {
				tickets.push($(this).val());
			});

			Swal.fire({
				title: 'Confirmation',
				text: 'Really mark ' + count + ' selected tickets as solved? (Solving notifies the reporter)',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/tickets/status/solve.json", { tickets: tickets, assignMe: "yes", }, function (res) {
						// $.unblockUI();
						if (res.status) {
							window.location.reload();
						}
					}, "json");
				}
			});
		});

		$('.btnCombineSelectedTickets').click(function () {
			var checkedTickets = $('.ticket-check:checked');
			var tickets = [];

			checkedTickets.each(function () {
				tickets.push($(this).val());
			});

			if (tickets.length < 2) return;

			Swal.fire({
				title: 'Confirmation',
				text: 'Really combine ' + tickets.length + ' selected tickets?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/tickets/combine.json", { tickets: tickets }, function (res) {
						if (res.status) {
							window.location.reload();
						} else {
							$.unblockUI();
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: res.message
							});
						}
					}, "json");
				}
			});
		});

		$('.btnAssignMeOnSelectedTickets').click(function () {
			var checkedTickets = $('.ticket-check:checked');
			var count = checkedTickets.length;

			if (count === 0) return;

			Swal.fire({
				title: 'Confirmation',
				text: 'Assign ' + count + ' selected tickets to you?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					var completed = 0;

					checkedTickets.each(function () {
						$.post("/api/agent/ticket/assign/me.json", { ticket: $(this).val() }, function (res) {
							completed++;
							if (completed === count) {
								$.unblockUI();
								window.location.reload();
							}
						}, "json");
					});
				}
			});
		});

		$("#collapseAllGroups").on("click", function () {
			var that = $(this);
			that.hide();
			let headers = $(".groupHeader");
			console.log(headers);
			headers.each(function (index, ele) {
				if (index > 0) {
					$(ele).click();
				}
			});
		});

		$(".groupHeader").click(function () {
			console.log("group header clicked", this);
			var date = $(this).data("date");
			console.log(date);
			//get all classes ticket-row with date = date
			var tickets = $(".ticket-row[data-date='" + date + "']");
			console.log(tickets);
			tickets.toggle();
			let ic = $(this).find(".collapse-icon");
			ic.css("transform", ic.css("transform") === "rotate(-90deg)" ? "" : "rotate(-90deg)");
		});


		$('.ticket-check').change(function () {
			$(this).closest('tr').toggleClass('table-info', this.checked);
		});

		$('#checkAllVisibleTickets').change(function () {
			$('.ticket-check').each(function () {
				if ($(this).closest('tr').is(':visible')) {
					$(this).prop('checked', $('#checkAllVisibleTickets').prop('checked')).change();
				}
			});
		});

		$('#ticketSearch').on('input', function () {
			var searchText = $(this).val().toLowerCase();
			$('#ticketsTableBody tr.ticket-row').each(function () {
				var row = $(this);
				var text = row.text().toLowerCase();
				if (searchText === '') {
					row.show();
				} else if (searchText.includes('*')) {
					// Simple wildcard search - replace * with any characters
					var searchPattern = searchText.replace(/\*/g, '.*');
					var regex = new RegExp(searchPattern, 'i');
					if (regex.test(text)) {
						row.show();
					} else {
						row.hide();
					}
				} else {
					if (text.includes(searchText)) {
						row.show();
					} else {
						row.hide();
					}
				}
			});
		});

		function updateClearSelectionsVisibility() {
			if ($('.ticket-check:checked').length > 0) {
				$('#clearSelections').show();
				$('#segmentBatchActions').show();
			} else {
				$('#clearSelections').hide();
				$('#segmentBatchActions').hide();
			}
		}

		$('#clearSelections').click(function () {
			$('.ticket-check:checked').prop('checked', false).change();
			$('#checkAllVisibleTickets').prop('checked', false);
			$('.checkVisibleTicketsOf').prop('checked', false);
			updateClearSelectionsVisibility();
		});

		// Update visibility when checkboxes change
		$('.ticket-check, #checkAllVisibleTickets, .checkVisibleTicketsOf').change(function () {
			updateClearSelectionsVisibility();
		});

		// Track currently selected row
		let selectedRow = null;

		// Handle keyboard navigation
		$(document).on('keydown', function (e) {
			// Skip if user is typing in an input field
			if ($(e.target).is('input, textarea')) return;

			if (!selectedRow && (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 32 || e.keyCode === 13)) {
				selectedRow = $('.ticket-row:first');
				selectedRow.addClass('bg-light-info border border-2 border-dashed border-info');
				selectedRow.focus();
				return;
			}

			switch (e.keyCode) {

				case 27: // Esc
					$('.ticket-check:checked').prop('checked', false).change();
					$('#checkAllVisibleTickets').prop('checked', false);
					$('.checkVisibleTicketsOf').prop('checked', false);
					selectedRow.removeClass('bg-light-info border border-2 border-dashed border-info');
					selectedRow = null;
					window.scrollTo(0, 0);
					break;

				case 67: // C
					if (e.ctrlKey) {
						e.preventDefault();
						$('.btnCloseSelectedTickets').click();
					}
					break;

				case 83: // S
					if (e.ctrlKey) {
						e.preventDefault();
						$('.btnSolveSelectedTickets').click();
					}
					break;

				case 79: // O
					if (e.ctrlKey) {
						e.preventDefault();
						$('.btnSolveSelectedTickets').click();
					}
					break;
				case 77: // M
					if (e.ctrlKey) {
						e.preventDefault();
						$('.btnCombineSelectedTickets').click();
					}
					break;

				case 38: // Arrow up
					e.preventDefault();
					let prevRow = selectedRow;
					while (prevRow.prev().length) {
						prevRow = prevRow.prev();
						if (prevRow.hasClass('ticket-row')) {
							selectedRow.removeClass('bg-light-info border border-2 border-dashed border-info');
							selectedRow = prevRow;
							selectedRow.addClass('bg-light-info border border-2 border-dashed border-info');
							selectedRow.focus();
							break;
						}
					}
					break;

				case 40: // Arrow down
					e.preventDefault();
					let nextRow = selectedRow;
					while (nextRow.next().length) {
						nextRow = nextRow.next();
						if (nextRow.hasClass('ticket-row')) {
							selectedRow.removeClass('bg-light-info border border-2 border-dashed border-info');
							selectedRow = nextRow;
							selectedRow.addClass('bg-light-info border border-2 border-dashed border-info');
							selectedRow.focus();
							break;
						}
					}
					break;

				case 32: // Spacebar
					e.preventDefault();
					let checkbox = selectedRow.find('.ticket-check');
					checkbox.prop('checked', !checkbox.prop('checked')).change();
					break;

				case 13: // Enter
					e.preventDefault();
					if ($('.ticket-check:checked').length === 0 && selectedRow) {
						let ticketUrl = selectedRow.data('url');
						if (ticketUrl) {
							window.location.href = ticketUrl;
						}
					}
					break;
			}
		});

		$('.checkVisibleTicketsOf').change(function () {
			var targetClass = $(this).data('target-class');
			var dataKey = $(this).data('data-key');
			var dataValue = $(this).data('data-value');
			var isChecked = $(this).prop('checked');

			$('.' + targetClass).each(function () {
				var checkbox = $(this);
				if (checkbox.closest('tr').is(':visible') && checkbox.data(dataKey) === dataValue) {
					checkbox.prop('checked', isChecked).change();
				}
			});
		});

		$('.checkVisibleTicketsOf').dblclick(function () {
			var targetClass = $(this).data('target-class');
			var dataKey = $(this).data('data-key');
			var dataValue = $(this).data('data-value');

			$(this).prop('checked', true);
			$('.' + targetClass).each(function () {
				var checkbox = $(this);
				if (checkbox.data(dataKey) === dataValue) {
					checkbox.prop('checked', true).change();
				}
			});
		});






	});
</script>
