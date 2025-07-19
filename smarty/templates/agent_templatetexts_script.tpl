<script>
	$(document).ready(function () {

		$('.btnAddNewTemplateText').on('click', function (e) {
			e.preventDefault();

			Swal.fire({
				title: 'Add New Template',
				html: `
				                <input type="text" id="templateName" class="swal2-input" placeholder="Template Name">
				                <input type="text" id="templateDescription" class="swal2-input" placeholder="Description">
				            `,
				showCancelButton: true,
				confirmButtonText: 'Add',
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
								Swal.fire('Success', 'Template successfully added', 'success')
									.then(() => location.reload());
							} else {
								Swal.fire('Error', response.message || 'Error adding template', 'error');
							}
						})
						.fail(function () {
							Swal.fire('Error', 'Error adding template', 'error');
						});
				}
			});
		});

	});
</script>