<script>
	$(document).ready(function () {
		$('.btnAddNewCategorySuggestion').on('click', function (e) {
			e.preventDefault();

			Swal.fire({
				title: 'Neue Kategorievorschlag',
				input: 'text',
				inputLabel: 'Filtertext eingeben',
				showCancelButton: true,
				confirmButtonText: 'Erstellen',
				showLoaderOnConfirm: true,
				preConfirm: (filterText) => {
					return $.ajax({
						url: '/api/agent/categorysuggestion/create.json',
						method: 'POST',
						data: {
							filter: filterText
						}
					});
				}
			}).then((result) => {
				if (result.isConfirmed && result.value.status) {
					$.blockUI();
					location.reload();
				}
			});
		});
	});

</script>