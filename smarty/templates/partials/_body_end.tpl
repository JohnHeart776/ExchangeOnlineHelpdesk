<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop"
	 data-kt-scrolltop="true">
	<i class="ki-outline ki-arrow-up"></i>
</div>
<!--end::Scrolltop-->

<!--begin::Javascript-->
<script>
	var hostUrl = "/assets/";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="/assets/plugins/global/plugins.bundle.js"></script>
<script src="/assets/js/scripts.bundle.js"></script>
<script src="/assets/js/custom.js"></script>
<!--end::Global Javascript Bundle-->


<script src="/assets/vendor/select2/4.0.13/js/select2.full.min.js"></script>

<script src="/assets/vendor/bootbox.js/6.0.0/bootbox.min.js"></script>
<script src="/assets/vendor/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script>
	$.blockUI.defaults.message = '<i class="fas fa-3x fa-spinner fa-pulse"></i>';
	$.blockUI.defaults.css.backgroundColor = 'rgba(0,0,0,0)';
	$.blockUI.defaults.css.color = '#ffffff';
	$.blockUI.defaults.css.border = '0';
</script>
<script src="/assets/vendor/jQuery.serializeObject/2.0.3/jquery.serializeObject.min.js"></script>
<script src="/assets/vendor/sweetalert2/11.7.5/sweetalert2.all.min.js"></script>

<script src="/assets/vendor/poshytip/1.2/jquery.poshytip.min.js"></script>
<script src="/assets/vendor/x-editable/1.5.1/jquery-editable/js/jquery-editable-poshytip.min.js"></script>
<script>
	$.fn.editable.defaults.mode = 'inline';
	$.fn.editable.defaults.inputclass = 'form-control';
	$.fn.editableform.buttons = '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="fas fa-check"></i></button><button type="button" class="btn btn-secondary btn-sm editable-cancel"><i class="fas fa-times"></i></button>';
</script>
<script>
	$(document).ready(function () {
		$('.editable').editable();
	});
</script>

<!-- include dropzone -->
<script src="/assets/vendor/dropzonejs/5.9.3/dropzone.min.js"></script>
<script>
	Dropzone.autoDiscover = false;
</script>

{*Fullcalendar*}
<script src="/assets/vendor/fullcalendar/5.11.5/main.js"></script>
<script src="/assets/vendor/fullcalendar/5.11.5/locales-all.min.js"></script>

{*Generic Button Press*}
<script>
	$(document).ready(function () {
		$('.actionPostButton').on('click', function (e) {
			e.preventDefault();
			var button = $(this);
			var url = button.data('url');
			if (!url) {
				console.error('No URL specified in data-url');
				return;
			}

			var payload = { /* ... */ };
			$.each(button.data(), function (key, value) {
				if (key !== 'url' && key !== 'uiaction' && key !== 'prompt') {
					payload[key] = value;
				}
			});

			var makeRequest = function () {
				$.blockUI();
				$.ajax({
					url: url,
					type: 'POST',
					data: payload,
					success: function (response) {
						if (button.data('success')) {
							Swal.fire({
								icon: 'success',
								title: 'Success!',
								text: response.message || 'Operation completed successfully',
							}).then(function () {
								if (button.data('uiaction') === 'reload') {
									$.blockUI();
									window.location.reload();
								}
							});
						} else if (button.data('uiaction') === 'reload') {
							$.blockUI();
							window.location.reload();
						}
					},
					error: function (xhr) {
						Swal.fire({
							icon: 'error',
							title: 'Error!',
							text: 'An error occurred. Please try again.',
						});
					},
					complete: function () {
						$.unblockUI();
					}
				});
			};

			if (button.data('prompt')) {
				Swal.fire({
					title: 'Confirm',
					text: button.data('prompt'),
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No'
				}).then((result) => {
					if (result.isConfirmed) {
						makeRequest();
					}
				});
			} else {
				makeRequest();
			}
		});
	});
</script>


<script src="/assets/vendor/tinymce/5.10.9/tinymce.min.js"></script>
<script>
	$(document).ready(function () {
		tinymce.init({
			selector: 'textarea.tinymce',
			plugins: [
				'advlist autolink lists link image charmap print preview anchor',
				'searchreplace visualblocks code fullscreen',
				'insertdatetime media table paste code help wordcount'
			],
			toolbar: 'undo redo | formatselect | bold italic backcolor | \
					  alignleft aligncenter alignright alignjustify | \
					  bullist numlist outdent indent | removeformat | help',
			// height: 300,

			paste_data_images: true,
			// setup: function (editor) {
			// 	editor.on('Paste', function (e) {
			// 		if (e.clipboardData && e.clipboardData.items) {
			// 			var items = e.clipboardData.items;
			// 			for (var i = 0; i < items.length; i++) {
			// 				if (items[i].type.indexOf('image') !== -1) {
			// 					var file = items[i].getAsFile();
			// 					var reader = new FileReader();
			// 					reader.onload = function (event) {
			// 						var base64String = event.target.result;
			// 						editor.insertContent('<img src="' + base64String + '" />');
			// 					};
			// 					reader.readAsDataURL(file);
			// 				}
			// 			}
			// 		}
			// 	});
			// },
		});

		$('.btnSaveThisForm').click(function (e) {
			var that = $(this);
			e.preventDefault();

			// Get the form - either from data-form attribute or parent form
			var $form = that.data('form') ? $('#' + that.data('form')) : that.closest('form');
			if (!$form.length) {
				console.error('No form found');
				return;
			}

			// Get the URL from button or form
			var url = that.data('url') || $form.attr('action');
			if (!url) {
				console.error('No URL found');
				return;
			}

			// Collect form data and additional data-* attributes
			var formData = $form.serializeObject();
			$.each(that.data(), function (key, value) {
				if (key !== 'form' && key !== 'url' && key !== 'onsave') {
					formData[key] = value;
				}
			});

			// Block UI before request
			$.blockUI();

			// Confirm and submit
			Swal.fire({
				title: 'Save changes?',
				text: 'Do you want to save these changes?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes, save it!',
				cancelButtonText: 'No, cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: url,
						type: 'POST',
						data: formData,
						success: function (response) {
							console.log('Save successful:', response);
							Swal.fire({
								icon: 'success',
								title: 'Saved!',
								text: response.message || 'Your changes have been saved successfully.',
							}).then(function () {
								// Execute onsave action if specified
								if (that.data('onsave')) {
									var onSaveFunction = that.data('onsave');
									if (onSaveFunction === 'location.reload') {
										$.blockUI();
										window.location.reload();
									} else if (typeof window[onSaveFunction] === 'function') {
										window[onSaveFunction]();
									}
								}
							});
						},
						error: function (xhr) {
							console.error('Save failed:', xhr.responseText);
							Swal.fire({
								icon: 'error',
								title: 'Error!',
								text: 'An error occurred while saving. Please try again.',
							});
						},
						complete: function () {
							$.unblockUI();
						}
					});
				}
			});

		});

	});
</script>

<script>
	// Check at app start (after login) if redirect data exists and is still valid
	(function () {
		const data = localStorage.getItem("redirectData");

		if (data) {
			console.log(data);
			const { url, timestamp } = JSON.parse(data);
			const now = Date.now();
			const maxAge = 5 * 60 * 1000; // 5 minutes in milliseconds

			// Skip redirect if URL starts with /login or matches current path
			if (url.startsWith('/login') || url === window.location.pathname) {
				localStorage.removeItem("redirectData");
				return;
			}

			if (now - timestamp < maxAge) {
				// SWAL2 dialog (SweetAlert2 must be included in the project)
				Swal.fire({
					title: "Redirect",
					text: "Do you want to be redirected to your original target page?",
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Yes",
					cancelButtonText: "No",
					customClass: {
						confirmButton: 'btn btn-primary',
						cancelButton: 'btn btn-secondary',
					},
					buttonsStyling: false, // So that Tailwind classes are applied
				}).then((result) => {
					// Delete the stored data – the dialog should only appear once!
					localStorage.removeItem("redirectData");
					if (result.isConfirmed) {
						// On confirmation: redirect to target page
						window.location.href = url;
					}
				});
			} else {
				// Data is older than 5 minutes → remove it
				localStorage.removeItem("redirectData");
			}
		}
	})();
</script>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->

<!--end::Javascript-->
