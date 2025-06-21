<script>
	$(document).ready(function () {
		$('.btnAddNewTextReplacement').on('click', function (e) {
			e.preventDefault();

			Swal.fire({
				title: 'Neue Text-Ersetzung',
				input: 'text',
				inputLabel: 'Such-Filter eingeben',
				showCancelButton: true,
				confirmButtonText: 'Erstellen',
				showLoaderOnConfirm: true,
				preConfirm: (searchFor) => {
					return $.ajax({
						url: '/api/agent/textreplace/create.json',
						method: 'POST',
						data: {
							searchFor: searchFor
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