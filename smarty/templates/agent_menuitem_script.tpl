<script>
	Dropzone.autoDiscover = false;

	$(document).ready(function () {
		$("#menuitem_image_upload").dropzone({
			url: "/api/agent/menuitem/image/upload.json",
			previewsContainer: false,
			acceptedFiles: '.png,.jpg,.jpeg,.svg',
			maxFilesize: 1,
			init: function () {
				console.log("Dropzone initialized");

				this.on("addedfile", function (file) {
					console.log("File added:", file.name);
				});

				this.on("sending", function (file, xhr, formData) {
					console.log("Sending file:", file.name);
					$.blockUI({
						message: 'Uploading...',
						css: {
							border: 'none',
							padding: '15px',
							backgroundColor: '#000',
							'-webkit-border-radius': '10px',
							'-moz-border-radius': '10px',
							opacity: .5,
							color: '#fff'
						}
					});
					formData.append("menuitem", "{$menuItem->getGuid()}");
				});

				this.on("success", function (file, response) {
					console.log("Upload successful:", file.name);
					$.unblockUI();
					location.reload();
				});

				this.on("error", function (file, errorMessage) {
					console.error("Upload error:", errorMessage);
					$.unblockUI();
					if (file.size > 104857600) {
						alert('File is too large. Maximum file size is 1MB.');
					} else {
						alert('Upload failed: ' + errorMessage);
					}
				});

				this.on("complete", function (file) {
					console.log("Upload completed:", file.name);
				});
			}
		});

		$(".btnFromNounProject").click(function () {
			$.blockUI();

			$.post('/api/agent/menuitem/image/fromNounProject.json', {
				guid: $(this).data('guid')
			})
				.done(function (response) {
					$.unblockUI();
					location.reload();
				})
				.fail(function (xhr, status, error) {
					$.unblockUI();
					alert('Failed to load image: ' + error);
				});
		});


		$(".btnFromIconFinder").click(function () {
			$.blockUI();

			$.post('/api/agent/menuitem/image/fromIconFinder.json', {
				guid: $(this).data('guid')
			})
				.done(function (response) {
					$.unblockUI();
					location.reload();
				})
				.fail(function (xhr, status, error) {
					$.unblockUI();
					alert('Failed to load image: ' + error);
				});
		});

	});
</script>