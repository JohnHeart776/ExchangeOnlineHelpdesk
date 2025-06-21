<script>
	function saveTemplateContent() {
		const $button = $('#save-template-content');
		const content = tinymce.get('templatetext-content').getContent();
		const guid = $('#templatetext-guid').val();

		$button.attr('data-kt-indicator', 'on').prop('disabled', true);

		$.post('/api/agent/templatetext/update.json', {
			pk: guid,
			name: 'Content',
			value: content,
		})
			.done(function (data) {
				if (data.status) {
					Swal.fire({
						text: 'Vorlageninhalt erfolgreich aktualisiert',
						icon: 'success',
						buttonsStyling: false,
						confirmButtonText: 'Ok',
						timer: 2000,
						customClass: {
							confirmButton: 'btn btn-primary'
						}
					});
				} else {
					Swal.fire({
						text: 'Vorlageninhalt konnte nicht aktualisiert werden',
						icon: 'error',
						buttonsStyling: false,
						confirmButtonText: 'Ok',
						timer: 2000,
						customClass: {
							confirmButton: 'btn btn-danger'
						}
					});
				}
			})
			.fail(function () {
				Swal.fire({
					text: 'Fehler beim Aktualisieren des Vorlageninhalts',
					icon: 'error',
					buttonsStyling: false,
					confirmButtonText: 'Ok',
					timer: 2000,
					customClass: {
						confirmButton: 'btn btn-danger'
					}
				});
			})
			.always(function () {
				$button.removeAttr('data-kt-indicator').prop('disabled', false);
			});
	}

	$('#save-template-content').on('click', saveTemplateContent);

	$('#generateAiText').on('click', function () {
		const addToPrompt = $('#addToGeneratePrompt').val();

		Swal.fire({
			title: 'KI-Text generieren',
			input: 'text',
			inputLabel: 'Welchen Text möchten Sie generieren?',
			showCancelButton: true,
			confirmButtonText: 'Generieren',
			showLoaderOnConfirm: true,
			preConfirm: (prompt) => {
				return $.post('/api/agent/ai/generate.json', {
					prompt: prompt + " " + addToPrompt,
				})
					.then(response => {
						if (!response.status) {
							throw new Error(response.message || 'Failed to generate text')
						}
						return response.data.text
					})
					.catch(error => {
						Swal.showValidationMessage(error.message)
					})
			},
			allowOutsideClick: () => !Swal.isLoading()
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: 'Generierter Text',
					text: result.value,
					icon: 'success',
					showDenyButton: true,
					showCancelButton: true,
					confirmButtonText: 'Vorhandenen ersetzen',
					denyButtonText: 'Verwerfen',
					cancelButtonText: 'An Cursor einfügen'
				}).then((insertChoice) => {
					const editor = tinymce.get('templatetext-content');
					if (insertChoice.isConfirmed) {
						editor.setContent(result.value);
					} else if (insertChoice.dismiss === Swal.DismissReason.cancel) {
						editor.selection.setContent(result.value);
					}
					// Discard does nothing, just closes the dialog
				});
			}
		});
	});


</script>