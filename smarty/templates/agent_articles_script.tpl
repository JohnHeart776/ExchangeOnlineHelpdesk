<!-- script -->
<script>
	$(document).ready(function () {
		$('.btnAddArticle').click(function () {
			Swal.fire({
				title: 'Add Article',
				input: 'text',
				inputLabel: 'Article Title',
				showCancelButton: true,
				confirmButtonText: 'Add',
				cancelButtonText: 'Cancel',
				inputValidator: (value) => {
					if (!value) {
						return 'Please enter an article title';
					}
				}
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/agent/article/add.json', {
						title: result.value,
					})
						.done(function (response) {
							if (response.status) {
								Swal.fire({
									text: 'Article successfully added',
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(() => {
									$.blockUI();
									location.reload();
								});
							} else {
								Swal.fire({
									text: 'Error adding article',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						})
						.fail(function () {
							Swal.fire({
								text: 'Error adding article',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						});
				}
			});
		});


		$('.btnDeleteArticle').click(function () {
			var article = $(this).data('article');

 		Swal.fire({
 			title: 'Delete Article',
 			text: 'Do you really want to delete this article?',
 			icon: 'warning',
 			showCancelButton: true,
 			confirmButtonText: 'Yes, delete',
 			cancelButtonText: 'Cancel'
 		}).then((result) => {
				if (result.isConfirmed) {
					$.post('/api/admin/article/delete.json', {
						guid: article
					})
						.done(function (response) {
							if (response.status) {
								Swal.fire({
									text: 'Article successfully deleted',
									icon: 'success',
									confirmButtonText: 'OK'
								}).then(() => {
									$.blockUI();
									location.reload();
								});
							} else {
								Swal.fire({
									text: 'Error deleting article',
									icon: 'error',
									confirmButtonText: 'OK'
								});
							}
						})
						.fail(function () {
							Swal.fire({
								text: 'Error deleting article',
								icon: 'error',
								confirmButtonText: 'OK'
							});
						});
				}
			});
		});


	});
</script>
