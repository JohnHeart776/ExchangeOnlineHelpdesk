<script>
	$(document).ready(function () {
		const $form = $('#frmNewTicket');

		// Initialize category select
		$('select[name="category"]').select2({
			allowClear: true,
			ajax: {
				url: '/api/agent/categories.json?format=select2',
				dataType: 'json'
			}
		});

		// Initialize owner select
		$('select[name="owner"]').select2({
			allowClear: true,
			ajax: {
				url: '/api/agent/agents.json?format=select2',
				dataType: 'json'
			},
			templateResult: function (data) {
				if (!data.image) return data.text;
				return $('<span><img src="' + data.image + '" alt="Photo" class="img-avatar me-2" width="24"/> ' + data.text + '</span>');
			}
		});

		// Initialize reporter select
		$('select[name="reportee"]').select2({
			allowClear: true,
			ajax: {
				url: '/api/agent/organizationusers.json?format=select2',
				dataType: 'json'
			},
			templateResult: function (data) {
				if (!data.image) return data.text;
				return $('<span><img src="' + data.image + '" alt="Photo" class="img-avatar me-2" width="24"/> ' + data.text + '</span>');
			}
		});

		// Initialize additional persons (assignees)
		$('select[name="assignees[]"]').select2({
			allowClear: true,
			ajax: {
				url: '/api/agent/organizationusers.json?format=select2',
				dataType: 'json'
			},
			templateResult: function (data) {
				if (!data.image) return data.text;
				return $('<span><img src="' + data.image + '" alt="Photo" class="img-avatar me-2" width="24"/> ' + data.text + '</span>');
			}
		});

		$('#saveTicket').on('click', function () {
			// TinyMCE-Inhalt holen
			$('textarea[name="text"]').val(tinymce.activeEditor.getContent());

			// Pflichtfelder prüfen
			const requiredFields = [ 'subject', 'category', 'reportee', 'text' ];
			const emptyFields = requiredFields.filter(field => !$form.find(`[name="${ field }"]`).val());

			if (emptyFields.length > 0) {
				Swal.fire({
					icon: 'error',
					title: 'Please fill in all required fields'
				});
				return;
			}

			const postData = {
				subject: $form.find('[name="subject"]').val(),
				category: $form.find('[name="category"]').val(),
				reportee: $form.find('[name="reportee"]').val(),
				owner: $form.find('[name="owner"]').val(),
				assignees: $form.find('[name="assignees[]"]').val(),
				text: $form.find('[name="text"]').val()
			};

			console.log('Sending ticket data:', postData);

			Swal.fire({
				title: 'Create ticket?',
				text: 'Do you want to create the ticket now?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/ticket/new.json', postData, (res) => {
						if (res.status) {
							Swal.fire({
								icon: 'success',
								title: res.message || 'Ticket has been created'
							}).then(() => {
								if (res.data && res.data.link) {
									window.location.href = res.data.link;
								} else {
									location.reload();
								}
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: res.message || 'Ticket could not be created'
							});
						}
					}, 'json').fail(() =>
						Swal.fire({
							icon: 'error',
							title: 'Unexpected error while creating the ticket'
						})
					);
				}
			});
		});
	});
</script>
