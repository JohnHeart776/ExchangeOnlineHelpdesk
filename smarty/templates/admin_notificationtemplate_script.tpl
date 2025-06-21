<script>
	$('#save-template-content').on('click', function () {
		const $button = $(this);
		const content = tinymce.activeEditor.getContent();
		const guid = $('#templatetext-guid').val();

		$button.attr('data-kt-indicator', 'on').prop('disabled', true);

		$.post('/api/admin/notificationtemplate/update.json', {
			pk: guid,
			name: 'MailText',
			value: content,
		})
			.done(function (data) {
				if (data.status) {
					Swal.fire({
						text: 'Template content successfully updated',
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
						text: 'Fehler beim Aktualisieren des Vorlageninhalt',
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
					text: 'Error updating template content',
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
	});

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
					confirmButtonText: 'Replace existing',
					denyButtonText: 'Discard',
					cancelButtonText: 'Insert at cursor'
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

	// Create a context menu for placeholders
	let contextMenu = $('<div id="placeholder-context-menu" class="dropdown-menu" style="display:none; position:absolute; z-index:1000;"></div>');
	$('body').append(contextMenu);

	// Handle right-click on placeholders
	$('.addToEditor').on('contextmenu', function (e) {
		e.preventDefault();

		const $placeholder = $(this);
		const content = $placeholder.text();

		// Position and show the context menu
		contextMenu.html('<a class="dropdown-item" href="#" id="insert-placeholder">Insert placeholder</a>');
		contextMenu.css({
			top: e.pageY + 'px',
			left: e.pageX + 'px'
		}).show();

		// Handle click on the context menu option
		$('#insert-placeholder').on('click', function (e) {
			e.preventDefault();

			// Get the editor
			const editor = tinymce.get('templatetext-content');

			// Scroll to the editor
			const $editorElement = $('#templatetext-content').closest('.tox-tinymce');
			if ($editorElement.length && $editorElement.offset()) {
				$('html, body').animate({
					scrollTop: $editorElement.offset().top - 100
				}, 500, function() {
					// Focus the editor
					editor.focus();

					// Insert the placeholder at the cursor position
					editor.selection.setContent(content);
				});
			} else {
				// If we can't find the editor or get its offset, just insert the content
				editor.focus();
				editor.selection.setContent(content);
			}

			// Hide the context menu
			contextMenu.hide();
		});
	});

	// Hide context menu when clicking elsewhere
	$(document).on('click', function () {
		contextMenu.hide();
	});

</script>
