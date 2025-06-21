{if login::isAgent()}
	<script>
		$(document).ready(function () {
			// Scroll to comment if hash exists
			if (window.location.hash) {
				const commentId = window.location.hash.substring(1);
				const $element = $('#' + commentId);
				if ($element.length) {
					$('html, body').animate({
						scrollTop: $element.offset().top - 100
					}, 1000);
				}
			}

			// Load assignable agents
			$.getJSON('/api/agent/agents.json', function (response) {
				if (response.length < 1) {
					console.error('Error loading agents:', response);
					return;
				}

				const agents = response;
				const $container = $('#assignableUsers');

				if (!$container.length) {
					console.error('Container #assignableUsers not found');
					return;
				}

				if (!agents || !agents.length) {
					console.warn('No agents returned from API');
					$container.append('<div class="menu-item px-3">No agents available</div>');
					return;
				}

				agents.forEach(agent => {
					if (!agent.guid || !agent.name) {
						console.warn('Invalid agent data:', agent);
						return;
					}

					const $item = $(
						"<div class=\"menu-item px-3\">" +
						"<a href=\"#\" class=\"menu-link px-3 assignAgent\" data-ticket=\"{$ticket->getGuid()}\" data-agent=\"" + agent.guid + "\">" +
						"<img src=\"" + (agent.image || '/images/default-avatar.png') + "\" class=\"rounded-circle me-2\" width=\"24\" height=\"24\" onerror=\"this.src='/images/default-avatar.png'\" />" +
						agent.name +
						"</a>" +
						"</div>"
					);

					try {
						$container.append($item);
					} catch (e) {
						console.error('Error appending agent:', e, agent);
					}
				});

				// Add click handler for agent assignment
				$('.assignAgent').click(function (e) {
					e.preventDefault();
					const agentGuid = $(this).data('agent');

					if (!agentGuid) {
						console.error('No agent GUID found for clicked element');
						return;
					}

					$.blockUI();
					$.post("/api/agent/ticket/assign.json", {
						ticket: "{$ticket->getGuid()}",
						agent: agentGuid
					}, function (res) {
						$.unblockUI();
						if (res.status) {
							reloadWithoutHash();
						} else {
							console.error('Error assigning ticket:', res);
						}
					}).fail(function (xhr, status, error) {
						console.error('AJAX error:', status, error);
						$.unblockUI();
					});
				});
			}).fail(function (xhr, status, error) {
				console.error('Error loading agents:', status, error);
			});

			$('.assignTicketStatus').click(function () {
				var that = $(this);

				$.blockUI();
				$.post("/api/agent/ticket/status/assign.json", that.data(), function (res) {
					$.unblockUI();
					if (res.status) {
						reloadWithoutHash();
					}
				}, "json");
			});

			$('.btnAddTicketAssociate').click(function () {
				Swal.fire({
					title: 'Who should be added to the ticket? Connected persons receive info via email and can view the ticket.',
					html: '<select id="ticketAssociateSelect" class="form-control" style="width: 100%;"></select>',
					showCancelButton: true,
					confirmButtonText: 'Ok',
					cancelButtonText: 'Cancel',
					backdrop: true, // Ensure the backdrop stays visible
					didOpen: () => {
						$('#ticketAssociateSelect').select2({
							ajax: {
								url: '/api/agent/ticket/associate/search.json',
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										query: params.term // search term
									};
								},
								processResults: function (data) {
									console.log(data);
									return {
										results: data.results.map(item => ({
											id: item.id,
											text: item.text,
											image: item.image
										}))
									};
								},
								cache: true
							},
							placeholder: 'Search for a person...',
							minimumInputLength: 1,
							width: '100%',
							dropdownParent: $('.swal2-container'),
							templateResult: function (item) {
								if (!item.image) return item.text;
								return $(`<span><img src="${ item.image }" class="rounded-circle me-2" width="32" height="32" />${ item.text }</span>`);
							}
						}).on('select2:open', () => {
							// $('.select2-dropdown').css('z-index', 1100);
						});
					}
				}).then((result) => {
					if (result.isConfirmed) {
						var selectedValue = $('#ticketAssociateSelect').val();
						if (selectedValue) {
							// Perform the action with selected value
							// console.log('Selected Associate ID:', selectedValue);
							$.blockUI();
							$.post("/api/agent/ticket/associate/add.json", {
									ticket: "{$ticket->getGuid()}",
									ouser: selectedValue
								}, function (res) {
									$.unblockUI();
									if (res.status) {
										reloadWithoutHash();
									}
								}
							)
						} else {
							Swal.fire('Please select an associate!', '', 'warning');
						}
					}
				});
			});

			$('.btnLoadOriginalMail').click(function () {
				var that = $(this);
				$.blockUI();
				$.post("/api/agent/ticket/comment/mail.json", that.data(), function (res) {
					$.unblockUI();
					if (res.status) {
						const mailCard = `
							<div class="card">
								<div class="card-header bg-light-info">
									<div class="d-flex align-items-center">
										<i class="fas fa-envelope me-2"></i>
										<div>
											<div class="text-muted small">${ res.data.received }</div>
											<div class="fw-bold">${ res.data.subject }</div>
											<div class="text-muted small">
												${ res.data.sender.name } &lt;${ res.data.sender.mail }&gt;
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									${ res.data.body }
								</div>
								<div class="card-footer">
									<div class="d-flex flex-wrap gap-2">
										${ res.data.attachments.map(attachment => `
											<a href="${ attachment.downloadLink }" class="btn btn-sm btn-light">
											<i class="fas fa-paperclip me-2"></i>${ attachment.name } (${ attachment.size })
											</a>
											`).join('') }
									</div>
								</div>
							</div>
						`;
						that.parent('.mailContainer').empty().append(mailCard);
					}
				}, "json");
			});

			$('.btnToggleTicketActionItem').click(function () {
				var that = $(this);
				$.blockUI();
				$.post("/api/agent/ticket/actionitem/toggle.json", that.data(), function (res) {
					$.unblockUI();
					if (res.status) {
						reloadWithoutHash();
					}
				})
			});

			$('.btnSuggestTicketSubject').click(function () {
				var that = $(this);
				$.blockUI();
				$.post("/api/agent/ticket/subject/suggest.json", {
					ticket: that.data('ticket')
				}, function (res) {
					$.unblockUI();
					if (res.status) {
						Swal.fire({
							title: 'Suggested Subject',
							text: res.data.subject,
							icon: 'question',
							showCancelButton: true,
							confirmButtonText: 'Apply',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								$.blockUI();
								$.post("/api/agent/ticket/update.json", {
									pk: that.data('ticket'),
									name: 'Subject',
									value: res.data.subject
								}, function (updateRes) {
									$.unblockUI();
									if (updateRes.status) {
										reloadWithoutHash();
									}
								});
							}
						});
					}
				});
			});

			$('.btnAddTicketActionsWithAi').click(function () {
				var that = $(this);
				Swal.fire({
					title: 'Generate AI Actions',
					text: 'Do you really want to generate AI actions for this ticket?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No'
				}).then((result) => {
					if (result.isConfirmed) {
						$.blockUI();
						$.post("/api/agent/ticket/actionitem/generateAi.json", that.data(), function (res) {
							$.unblockUI();
							if (res.status) {
								$.blockUI();
								reloadWithoutHash();
							}
						})
					}
				});
			})

			$('#btnAddSelectedActionGroup').click(function () {
				var selectedGroup = $('#actionGroupSelect').val();
				if (!selectedGroup) {
					return;
				}
				var that = $(this);
				Swal.fire({
					title: 'Add Action Group',
					text: 'Do you really want to add this action group to the ticket?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No'
				}).then((result) => {
					if (result.isConfirmed) {
						$.blockUI();
						$.post("/api/agent/ticket/actiongroup/add.json", {
							ticket: that.data('ticket'),
							group: selectedGroup
						}, function (res) {
							$.unblockUI();
							if (res.status) {
								$.blockUI();
								reloadWithoutHash();
							}
						});
					}
				});
			});


			$('.btnAssignTicketToMe').click(function () {
				var that = $(this);

				$.blockUI();
				$.post("/api/agent/ticket/assign/me.json", that.data(), function (res) {
					$.unblockUI();
					if (res.status) {
						$.blockUI();
						reloadWithoutHash();
					}
				}, "json");
			});

			$('.changeDueDate').click(function () {
				var that = $(this);

				if (that.data('target') === 'custom') {
					Swal.fire({
						title: 'Change Due Date',
						html: '<input type="datetime-local" id="dueDateInput" class="form-control" value="{date("Y-m-d\T08:00", strtotime("tomorrow"))}">',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Yes',
						cancelButtonText: 'No'
					}).then((result) => {
						if (result.isConfirmed) {
							$.blockUI();
							$.post("/api/agent/ticket/duedate/change.json", {
								ticket: that.data('ticket'),
								target: that.data('target'),
								custom: true,
								date: $('#dueDateInput').val(),
							}, function (res) {
								$.unblockUI();
								if (res.status) {
									$.blockUI();
									reloadWithoutHash();
								}
							});
						}
					});
				} else {
					Swal.fire({
						title: 'Change Due Date',
						text: 'Are you sure you want to change the due date?',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Yes',
						cancelButtonText: 'No',
						timer: 1000,
						timerProgressBar: true,
						allowEscapeKey: true
					}).then((result) => {
						if (result.isConfirmed || (result.isDismissed && result.dismiss === "timer")) {
							$.blockUI();
							$.post("/api/agent/ticket/duedate/change.json", {
								ticket: that.data('ticket'),
								target: that.data('target'),
								custom: false
							}, function (res) {
								$.unblockUI();
								if (res.status) {
									$.blockUI();
									reloadWithoutHash();
								}
							});
						}
					});
				}
			});

			$('.btnRemoveAssignee').click(function () {
				var that = $(this);
				Swal.fire({
					title: 'Remove Assignment',
					text: 'Are you sure you want to remove the assignment?',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes',
					cancelButtonText: 'No'
				}).then((result) => {
					if (result.isConfirmed) {
						$.blockUI();
						$.post("/api/agent/ticket/unassign.json", {
							ticket: that.data('ticket'),
						}, function (res) {
							$.unblockUI();
							if (res.status) {
								$.blockUI();
								reloadWithoutHash();
							}
						});
					}
				});
			});

		});
	</script>
	<script>
		$(document).ready(function () {

			$('.saveTicketComment').on('click', function (e) {
				e.preventDefault();

				// Get the clicked button
				var button = $(this);

				// Get the URL from the button's data-url attribute
				var url = button.data('url');
				if (!url) {
					console.error('No data-url attribute found on button.');
					return;
				}

				// Collect all data-* attributes from the button, excluding data-url and data-onsave
				var payload = { /* ... */ };
				$.each(button.data(), function (key, value) {
					if (key !== 'url' && key !== 'onsave') {
						payload[key] = value;
					}
				});

				// Get content from all TinyMCE editors on the page and name them editor0, editor1, etc.
				tinymce.editors.forEach(function (editor, index) {
					payload['editor' + index] = editor.getContent();
				});

				const doSave = () => {
					// Block UI before making the request
					$.blockUI();
					$.ajax({
						url: url,
						type: 'POST',
						data: payload,
						success: function (response) {
							console.log('Save successful:', response);
							Swal.fire({
								icon: 'success',
								title: 'Saved!',
								timer: 1000,
								timerProgressBar: true,
								text: response.message || 'The message was saved successfully!',
							}).then(function (funre2) {
								// If a data-onsave function is defined, execute it
								if (button.data('onsave')) {
									var onSaveFunction = button.data('onsave');
									if (onSaveFunction === 'location.reload') {
										reloadWithoutHash();
									} else if (onSaveFunction === "reloadToNewComment") {
										window.location.href = window.location.pathname + '#' + response.data.ticketComment.guid;
										window.location.reload();


									} else if (typeof window[onSaveFunction] === 'function') {
										window[onSaveFunction]();
									} else {
										$.unblockUI();
										console.error('Invalid data-onsave function:', onSaveFunction);
									}
								} else {
									$.unblockUI();
								}
							});
						},
						error: function (xhr) {
							console.error('Save failed:', xhr.responseText);
							Swal.fire({
								icon: 'error',
								title: 'Error!',
								text: 'An error occurred. Please try again.',
							});
						},
						complete: function () {
							// Unblock UI after the request is completed
							$.unblockUI();
						},
					});
				};

				const confirmText = button.data('confirm');
				if (!confirmText) {
					doSave();
					return;
				}

				// Perform the AJAX request
				Swal.fire({
					title: 'Save Changes?',
					text: confirmText === true ? 'Do you want to save these changes?' : confirmText,
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes, save!',
					cancelButtonText: 'No, cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						doSave();
					} else {
						$.unblockUI();
					}
				});
			});


			$('.btnGenerateTicketRecapWithAi').click(function () {
				var that = $(this);
				$.blockUI();
				$.post("/api/agent/ticket/ai/recap.json", {
					guid: that.data('ticket')
				}, function (res) {
					$.unblockUI();
					if (res.status) {
						tinymce.activeEditor.setContent(res.data.text);
					}
				}, "json");
			});

			$('.btnGenerateTicketReplyWithAi').click(function () {
				var that = $(this);
				$.blockUI();
				$.post("/api/agent/ticket/ai/generateAnswer.json", {
					guid: that.data('ticket')
				}, function (res) {
					$.unblockUI();
					if (res.status) {
						tinymce.activeEditor.setContent(res.data.text);
					}
				}, "json");
			});



			$('.btnGenerateSpecificTicketReplyWithAi').click(function () {
				var that = $(this);
				Swal.fire({
					title: 'Generate AI Response',
					input: 'textarea',
					inputLabel: 'What response should be generated?',
					showCancelButton: true,
					customClass: {
						input: 'form-control'
					},
					inputAttributes: {
						style: 'height: 150px'
					},
					inputValidator: (value) => {
						if (!value) {
							return 'Please enter a text!'
						}
					}
				}).then((result) => {
					if (result.isConfirmed) {
						$.blockUI();
						$.post("/api/agent/ticket/ai/generateAnswer.json", {
							guid: that.data('ticket'),
							specific: true,
							reason: result.value
						}, function (res) {
							$.unblockUI();
							if (res.status) {
								tinymce.activeEditor.setContent(res.data.text);
							}
						}, "json");
					}
				});
			});


			$('.btnInsertTemplateText').click(function () {
				$.getJSON('/api/agent/templatetexts.json', function (response) {
					console.log(response);
					if (!response.success)
						return;

					const templates = response.templates.sort((a, b) => a.name.localeCompare(b.name));
					console.log(templates);
					let selectHtml = '<select id="templateTextSelect" class="form-control">';
					selectHtml += '<option value="">Please select...</option>';
					templates.forEach(template => {
						selectHtml += '<option value="' + template.guid + '">' + template.name + ' - ' + template.description + '</option>';
					});
					selectHtml += '</select>';

					bootbox.dialog({
						title: 'Insert Template',
						message: selectHtml,
						buttons: {
							cancel: {
								label: 'Cancel',
								className: 'btn-light'
							},
							ok: {
								label: 'Insert',
								className: 'btn-primary',
								callback: function () {
									const selectedGuid = $('#templateTextSelect').val();
									if (!selectedGuid) return;

									$.blockUI();
									$.getJSON('/api/agent/templatetext.json', { guid: selectedGuid }, function (response) {
										$.unblockUI();
										if (!response)
											return;
										let content = response.content;
										if (content) {
											tinymce.activeEditor.setContent(content);
										}
									});
								}
							}
						}
					});
				});
			});

			// Initialize Dropzone
			let myDropzone = new Dropzone("#ticketUploadFile", {
				url: "/api/agent/ticket/file/upload.json",
				paramName: "file",
				maxFilesize: 20, // MB
				acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar",
				addRemoveLinks: true,
				dictRemoveFile: "Remove",
				dictCancelUpload: "Cancel",
				dictCancelUploadConfirmation: "Do you really want to cancel the upload?",
				init: function () {
					this.on("sending", function (file, xhr, formData) {
						$.blockUI();
						formData.append("ticket", "{$ticket->getGuid()}");
					});
					this.on("success", function (file, response) {
						$.unblockUI();
						if (response.status) {
							file.previewElement.classList.add("dz-success");
						} else {
							file.previewElement.classList.add("dz-error");
							this.emit("error", file, response.message || "Upload failed");
						}
					});
					this.on("error", function (file, message) {
						$.unblockUI();
						file.previewElement.classList.add("dz-error");
						const messageElement = file.previewElement.querySelector("[data-dz-errormessage]");
						messageElement.textContent = message;
					});
					this.on("complete", function (file) {
						if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
							reloadWithoutHash();
						}
					});
				}
			});

		});

		$('.btnInsertArticleLink').click(function () {
			Swal.fire({
				title: 'Select Article',
				html: '<select id="articleSelect" class="form-control" style="width: 100%;"></select>',
				showCancelButton: true,
				confirmButtonText: 'Insert',
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$('#articleSelect').select2({
						ajax: {
							url: '/api/agent/articles.json?format=select2',
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									search: params.term
								};
							},
							processResults: function (data) {
								return {
									results: data.results
								};
							},
							cache: true
						},
						placeholder: 'Search articles...',
						minimumInputLength: 3,
						width: '100%',
						dropdownParent: $('.swal2-container')
					});
				}
			}).then((result) => {
				if (result.isConfirmed) {
					let selectedArticle = $('#articleSelect').val();
					console.log('Selected article:', selectedArticle);
					if (selectedArticle) {
						$.getJSON('/api/agent/article.json', { guid: selectedArticle }, function (response) {
							console.log('API response:', response);
							if (response.guid) {
								const linkHtml = 'Here you can find all necessary information: <a href="' + response.link + '">' + response.title + '</a>';
								console.log('Generated link HTML:', linkHtml);
								tinymce.activeEditor.selection.setContent(linkHtml);
								console.log('Content inserted into editor');
							} else {
								console.error('API request failed:', response);
							}
						}).fail(function (jqXHR, textStatus, errorThrown) {
							console.error('AJAX error:', textStatus, errorThrown);
						});
					} else {
						console.warn('No article selected');
					}
				} else {
					console.log('Dialog cancelled');
				}
			});


		});

		$('.btnRemoveTicketAssociate').click(function () {
			var that = $(this);
			Swal.fire({
				title: 'Remove Person',
				text: 'Do you really want to remove this person from the ticket?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/ticket/associate/delete.json", {
						guid: that.data('ticketassociate')
					}, function (res) {
						$.unblockUI();
						if (res.status) {
							reloadWithoutHash();
						}
					});
				}
			});
		});

		$('.btnCopyTicket').click(function () {
			var that = $(this);
			Swal.fire({
				title: 'Copy Ticket',
				text: 'Do you really want to copy this ticket?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/ticket/copy.json", {
						guid: that.data('guid')
					}, function (res) {
						$.unblockUI();
						if (res.status) {
							Swal.fire({
								title: 'Ticket Copied',
								text: 'Do you want to be redirected to the new ticket?',
								icon: 'success',
								showCancelButton: true,
								confirmButtonText: 'Yes',
								cancelButtonText: 'No',
								timer: 2000,
								timerProgressBar: true
							}).then((result) => {
								if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
									window.location.href = res.data.link;
								}
							});
						}
					});
				}
			});
		});

		$('.btnShowTextInPopup').click(function() {
			var that = $(this);
			$.blockUI();
			$.get(that.data('url'), function (response) {
				$.unblockUI();
				Swal.fire({
					title: that.data('title'),
					html: '<pre style="text-align:left; white-space: pre-wrap; word-break: break-word; overflow-x: hidden;">' + response + '</pre>',
					width: '80%',
					confirmButtonText: 'Close'
				});
			}).fail(function () {
				$.unblockUI();
				Swal.fire('Error', 'Failed to load content', 'error');
			});

		});

		$('.btnEditTicketComment').click(function () {
			var that = $(this);
			var a = that.parents('.ticketComment');
			var contentContainer = a.find('.ticketCommentContent');
			var textContainer = contentContainer.find('.ticketCommentContentText');
			var originalContent = textContainer.html();

			var rndEditorContainerClassNames = 'editor-container-' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);

			// Create editor container and buttons
			var editorContainer = $('<div />').addClass(rndEditorContainerClassNames);
			var buttonContainer = $('<div class="d-flex gap-2 mt-2"></div>');
			var saveButton = $('<button class="btn btn-primary btn-sm">Save</button>');
			var cancelButton = $('<button class="btn btn-light btn-sm">Cancel</button>');

			buttonContainer.append(saveButton).append(cancelButton);
			textContainer.html(editorContainer);
			textContainer.append(buttonContainer);

			// Initialize TinyMCE
			let editor;
			tinymce.init({
				selector: '.'+rndEditorContainerClassNames,
				height: 300,
				// menubar: false,
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
				setup: function (ed) {
					editor = ed;
					editor.on('init', function () {
						editor.setContent(originalContent);
					});
				}
			});

			// Handle cancel
			cancelButton.click(function () {
				tinymce.remove('.'+rndEditorContainerClassNames);
				textContainer.html(originalContent);
			});

			// Handle save
			saveButton.click(function () {
				var content = editor.getContent();

				$.blockUI();
				$.post("/api/agent/ticket/comment/update.json", {
					pk: that.data('guid'),
					name: 'Text',
					value: content
				}, function (res) {
					$.unblockUI();
					if (res.status) {
						tinymce.remove('.'+rndEditorContainerClassNames);
						textContainer.html(content);
					} else {
						Swal.fire('Error', 'Failed to update comment', 'error');
					}
				}).fail(function () {
					$.unblockUI();
					Swal.fire('Error', 'Failed to update comment', 'error');
				});
			});
		});




		$('.btnCloseTicket').click(function () {
			var that = $(this);
			Swal.fire({
				title: 'Close Ticket',
				text: 'Do you really want to close this ticket? (Closing sends no information)',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/ticket/status/close.json", that.data(), function (res) {
						$.unblockUI();
						if (res.status) {
							Swal.fire({
								title: 'Success',
								text: 'Back to dashboard?',
								icon: 'success',
								showCancelButton: true,
								confirmButtonText: 'Yes',
								cancelButtonText: 'No',
								timer: 2000,
								timerProgressBar: true
							}).then((result) => {
								if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
									window.location.href = '/';
								} else {
									reloadWithoutHash();
								}
							});
						}
					});
				}
			});
		});

		$('.btnSolveTicket').click(function () {
			var that = $(this);
			Swal.fire({
				title: 'Solve Ticket',
				text: 'Do you really want to mark this ticket as solved? (Solving informs reporter)',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.isConfirmed) {
					$.blockUI();
					$.post("/api/agent/ticket/status/solve.json", that.data(), function (res) {
						$.unblockUI();
						if (res.status) {
							Swal.fire({
								title: 'Success',
								text: 'Back to dashboard?',
								icon: 'success',
								showCancelButton: true,
								confirmButtonText: 'Yes',
								cancelButtonText: 'No',
								timer: 2000,
								timerProgressBar: true
							}).then((result) => {
								if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
									window.location.href = '/';
								} else {
									reloadWithoutHash();
								}
							});
						}
					});
				}
			});
		});


		function reloadWithoutHash() {
			//reload the page but remove a hash if there is one
			window.location.hash = '';
			window.location.replace(window.location.pathname + window.location.search);

		}

	</script>
{/if}
