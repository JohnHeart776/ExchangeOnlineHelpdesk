<script>
	$(document).ready(function () {
		$("#saveContent").on("click", function () {
			var content = tinymce.activeEditor.getContent();

			$.ajax({
				url: '/api/agent/article/update.json',
				method: 'POST',
				data: {
					pk: '{$article->getGuid()}',
					name: 'Content',
					value: content
				},
				success: function (response) {
					if (response.status)
						toastr.success('Inhalt erfolgreich gespeichert');
					else
						toastr.error('Fehler beim Speichern des Inhalts');
				},
				error: function (xhr, status, error) {
					toastr.error('Error saving content');
				}
			});
		});


		$("#generateArticle").on("click", function () {
			Swal.fire({
				title: 'Artikel generieren',
				input: 'text',
				inputLabel: 'Worum soll es in dem Artikel gehen?',
				showCancelButton: true,
				confirmButtonText: 'Generieren'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.ajax({
						url: '/api/agent/article/generate.json',
						method: 'POST',
						data: {
							topic: result.value,
						},
						success: function (response) {
							$.unblockUI();
							tinymce.activeEditor.setContent(response.data.content);
							toastr.success('Artikel erfolgreich generiert');
						},
						error: function (xhr, status, error) {
							$.unblockUI();
							toastr.error('Fehler beim Generieren des Artikels');
						}
					});
				}
			});
		});

	});
</script>