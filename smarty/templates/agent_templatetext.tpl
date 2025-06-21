<a href="/agent/templatetexts/" class="btn btn-sm btn-light-info mb-5">
	<i class="fas fa-arrow-left"></i> Back
</a>

<!--begin::Properties Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">

		<h3 class="card-title align-items-start flex-column">
				<span class="card-label fw-bold fs-3 mb-1">Template Text <a href="#" class="editable text-hover-primary text-gray-800 text-decoration-none" data-type="text" data-pk="{$templateText->getGuid()}"
																			data-name="Name"
																			data-url="/api/agent/templatetext/update.json">{$templateText->getName()}</a></span>
			<span class="text-muted mt-1 fw-semibold fs-7">Template Text Details</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<!--begin::Table body-->
				<tbody>

				<tr>
					<td>
						<div class="d-flex align-items-center">
							<div class="d-flex justify-content-start flex-column w-100">
								<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 mb-4">
									<tbody>
									<tr>
										<td class="fw-bold" style="width: 200px">Name:</td>
										<td>
											<a href="#" class="editable text-gray-900 text-hover-primary fs-6" id="template-name"
											   data-type="text"
											   data-pk="{$templateText->getGuid()}"
											   data-name="Name"
											   data-url="/api/agent/templatetext/update.json"
											   data-value="{$templateText->getName()}"
											></a>
										</td>
									</tr>
									<tr>
										<td class="fw-bold">Description:</td>
										<td>
											<a href="#" class="editable text-gray-900 text-hover-primary fs-7" id="template-description"
											   data-type="text"
											   data-pk="{$templateText->getGuid()}"
											   data-name="Description"
											   data-url="/api/agent/templatetext/update.json"
											   data-value="{$templateText->getDescription()}"
											></a>
										</td>
									</tr>
									</tbody>
								</table>
								<h3 class="card-title align-items-start flex-column mb-4">
									<span class="card-label fw-bold fs-3 mb-1">Content</span>
								</h3>
								<div class="mb-4 w-100">
									<textarea id="templatetext-content" class="tinymce w-100" style="height: 800px;">{$templateText->getContentForTinyMce()}</textarea>

									<input type="hidden" id="templatetext-guid" value="{$templateText->getGuid()}"/>
									<input type="hidden" id="addToGeneratePrompt" value="{config::get("ai.prompt.suffix.templatetext.generate")}" />

									<div class="d-flex justify-content-end mt-2">

										<button type="button" class="btn btn-light-info ms-2 me-3" id="generateAiText">
											<span class="indicator-label"><i class="fas fa-gear-code"></i> Text generieren</span>
										</button>

										<button type="button" class="btn btn-primary" id="save-template-content">
										<span class="indicator-label"><i class="fas fa-save"></i> Save Changes</span>
										</button>

									</div>

								</div>

							</div>
						</div>
					</td>
				</tr>

				</tbody>
				<!--end::Table body-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table container-->
	</div>
	<!--begin::Body-->
</div>
<!--end::Properties Widget-->
