<script>
	$(document).ready(function () {

		$('.btnAddNewTemplateText').on('click', function (e) {
			e.preventDefault();

			Swal.fire({
				title: 'Neue Vorlage hinzufügen',
				html: `
				                <input type="text" id="templateName" class="swal2-input" placeholder="Vorlagenname">
				                <input type="text" id="templateDescription" class="swal2-input" placeholder="Beschreibung">
				            `,
				showCancelButton: true,
				confirmButtonText: 'Hinzufügen',
				preConfirm: () => {
					return {
						name: document.getElementById('templateName').value,
						description: document.getElementById('templateDescription').value
					}
				}
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/templatetext/add.json', result.value)
						.done(function (response) {
							if (response.status) {
								Swal.fire('Erfolg', 'Vorlage erfolgreich hinzugefügt', 'success')
									.then(() => location.reload());
							} else {
								Swal.fire('Fehler', response.message || 'Fehler beim Hinzufügen der Vorlage', 'error');
							}
						})
						.fail(function () {
							Swal.fire('Fehler', 'Fehler beim Hinzufügen der Vorlage', 'error');
						});
				}
			});
		});

	});
</script>