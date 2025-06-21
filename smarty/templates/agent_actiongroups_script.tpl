<!-- script -->
<script>
	$(document).ready(function () {
		$('.btnAddActionGroup').click(function () {
			Swal.fire({
				title: 'Add Action Group',
				input: 'text',
				inputLabel: 'Action Group Name',
				showCancelButton: true,
				confirmButtonText: 'Add',
				cancelButtonText: 'Cancel',
				inputValidator: (value) => {
					if (!value) {
						return 'Please enter a name for the action group';
					}
				}
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/actiongroup/add.json', {
						name: result.value,
						position: "end"
					})
						.done(function (response) {
							if (response.status) {
								Swal.fire({
									text: 'Action group successfully added',
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(() => {
									$.blockUI();
									location.reload();
								});
							} else {
								Swal.fire({
									text: 'Error adding action group',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						})
						.fail(function () {
							Swal.fire({
								text: 'Error occurred while adding action group',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						});
				}
			});
		});

		$('.btnDistributeSortOrder').click(function () {
			var actiongroup = $(this).data('actiongroup');

			Swal.fire({
				title: 'Distribute Sort Order',
				text: 'Are you sure you want to distribute the sort order?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, distribute',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/actiongroup/distribute.json', {
						actiongroup: actiongroup
					})
						.done(function (response) {
							if (response.status) {
								Swal.fire({
									text: 'Sort order successfully distributed',
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(() => {
									$.blockUI();
									location.reload();
								});
							} else {
								Swal.fire({
									text: 'Error distributing sort order',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						})
						.fail(function () {
							Swal.fire({
								text: 'Error occurred while distributing sort order',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						});
				}
			});
		});
		$('.btnDeleteActionItem').click(function () {
			var actionitem = $(this).data('actionitem');

			Swal.fire({
				title: 'Delete Action Item',
				text: 'Are you sure you want to delete this action item?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/actionitem/delete.json', {
						actionitem: actionitem
					})
						.done(function (response) {
							if (response.status) {
								Swal.fire({
									text: 'Action item successfully deleted',
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(() => {
									$.blockUI();
									location.reload();
								});
							} else {
								Swal.fire({
									text: 'Error deleting action item',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						})
						.fail(function () {
							Swal.fire({
								text: 'Error occurred while deleting action item',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						});
				}
			});
		});

		$('.btnAddActionItem').click(function () {
			var actiongroup = $(this).data('actiongroup');

			Swal.fire({
				title: 'Add Action Item',
				input: 'text',
				inputLabel: 'Action Item Name',
				showCancelButton: true,
				confirmButtonText: 'Add',
				cancelButtonText: 'Cancel',
				inputValidator: (value) => {
					if (!value) {
						return 'Please enter a name for the action item';
					}
				}
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/actionitem/add.json', {
						actiongroup: actiongroup,
						name: result.value,
						position: "end",
					})
						.done(function (response) {
							console.log(response);

							if (response.status) {
								Swal.fire({
									text: 'Aktionselement erfolgreich hinzugefügt',
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(() => {
									$.blockUI();
									location.reload();
								});
							} else {
								Swal.fire({
									text: 'Fehler beim Hinzufügen des Aktionselements',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						})
						.fail(function () {
							Swal.fire({
								text: 'Fehler beim Hinzufügen des Aktionselements aufgetreten',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						});
				}
			});
		});
	});
</script>
