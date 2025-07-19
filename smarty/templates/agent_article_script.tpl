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
						toastr.success('Content saved successfully');
					else
						toastr.error('Error saving content');
				},
				error: function (xhr, status, error) {
					toastr.error('Error saving content');
				}
			});
		});


		$("#generateArticle").on("click", function () {
			Swal.fire({
				title: 'Generate Article',
				input: 'text',
				inputLabel: 'What should the article be about?',
				showCancelButton: true,
				confirmButtonText: 'Generate'
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
							toastr.success('Article generated successfully');
						},
						error: function (xhr, status, error) {
							$.unblockUI();
							toastr.error('Error generating article');
						}
					});
				}
			});
		});

	});
</script>