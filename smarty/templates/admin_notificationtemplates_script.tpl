<script>
	$(document).ready(function () {
		$('.btnAddTemplate').on('click', function () {
			Swal.fire({
				title: 'Enter template name',
				input: 'text',
				inputLabel: 'Template Name',
				showCancelButton: true,
				inputValidator: (value) => {
					if (!value) {
						return 'Template name is required!'
					}
				}
			}).then((result) => {
				if (result.isConfirmed) {
					var templateData = {
						name: result.value,
					};

					$.post('/api/admin/notificationtemplate/add.json', templateData)
						.done(function (response) {
							if (response.status) {
								window.location.href = '/admin/notificationtemplate/' + response.data.template.guid;
							} else {
								bootbox.alert(response.message);
							}
						})
						.fail(function () {
							bootbox.alert('Error saving template');
						});
				}
			});
		});
	});
</script>