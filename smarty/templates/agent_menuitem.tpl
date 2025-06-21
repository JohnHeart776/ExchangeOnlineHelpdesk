<!--begin::Toolbar-->
<div class="d-flex flex-wrap flex-stack mb-6">
	<!--begin::Left-->
	<div class="d-flex flex-wrap align-items-center my-1">
		<a href="/agent/menus" class="btn btn-sm btn-primary me-2">
			<i class="fas fa-chevron-circle-left"></i> Zurück zur Übersicht
		</a>


	</div>
	<!--end::Left-->
</div>
<!--end::Toolbar-->
{if $menuItem->amIAChild()}
	<!--begin::Toolbar-->
	<div class="d-flex flex-wrap flex-stack mb-6">
		<!--begin::Left-->
		<div class="d-flex flex-wrap align-items-center my-1">

			<a href="/agent/menuitem/{$menuItem->getParent()->getGuid()}" class="btn btn-sm btn-secondard me-2">
				<i class="fas fa-arrow-left"></i> Zurück zum übergeordneten Menü: {$menuItem->getParent()->getTitle()}
			</a>
		</div>
		<!--end::Left-->
	</div>
	<!--end::Toolbar-->
{/if}
<!--begin::Properties Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Menüeintrag <a href="#" class="editable text-hover-primary text-gray-800 text-decoration-none" data-type="text" data-pk="{$menuItem->getGuid()}" data-name="Title"
																	  data-url="/api/agent/menuitem/update.json">{$menuItem->getTitle()}</a></span>
			<span class="text-muted mt-1 fw-semibold fs-7">Details des ausgewählten Menüeintrags</span>
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
							<div class="symbol symbol-35px me-3">
                                {if $menuItem->hasIcon()}
									<i class="fas {$menuItem->getIcon()} fs-2x"></i>
                                {/if}
							</div>

							<div class="d-flex justify-content-start flex-column">
								<div class="mb-2">
									<span class="fw-bold me-2">Enabled:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="Enabled"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getEnabled()}"
									   data-source="[ { value: 1, text: 'Yes' },{ value: 0, text: 'No' } ]"
									></a>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Title:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-6"
									   data-type="text"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="Title"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getTitle()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Link:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="text"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="Link"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getLink()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Icon:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="text"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="Icon"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getIcon()}"
									></a>
								</div>

								<hr class="separator mt-3 mb-3">

								<div class="mb-2">
									<span class="fw-bold me-2">Requires User:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="requireIsUser"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getRequireIsUser()}"
									   data-source="[ { value: 1, text: 'Yes' },{ value: 0, text: 'No' } ]"
									></a>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Requires Agent:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="requireIsAgent"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getRequireIsAgent()}"
									   data-source="[ { value: 1, text: 'Yes' },{ value: 0, text: 'No' } ]"
									></a>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Requires Admin:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-pk="{$menuItem->getGuid()}"
									   data-name="requireIsAdmin"
									   data-url="/api/agent/menuitem/update.json"
									   data-value="{$menuItem->getRequireIsAdmin()}"
									   data-source="[ { value: 1, text: 'Yes' },{ value: 0, text: 'No' } ]"
									></a>
								</div>


								<span class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
									ID: {$menuItem->getMenuItemId()}
								</span>
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


<!--begin::Upload Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Image Management</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
        {if $menuItem->hasImage()}
			<div class="current-image mb-4">
				<img src="{$menuItem->getImageAsFile()->getLink()}"
					 class="img-fluid"
					 style="max-width: 500px; max-height: 500px;"
					 alt="{$menuItem->getTitle()}"
					 onload="showImageDimensions(this)"/>
				<div class="image-info mt-2">
					<span class="image-dimensions text-muted"></span>
					<span class="btn btn-sm btn-danger ms-3 actionPostButton"
						  data-url="/api/agent/menuitem/update.json"
						  data-pk="{$menuItem->getGuid()}"
						  data-name="ImageFileId"
						  data-value=""
						  data-uiaction="reload"
						  data-prompt="Do you really want to delete the image?"
						  data-icon="warning">
						<i class="ki-outline ki-trash"></i> Delete Image
					</span>
				</div>
				<script>
					function showImageDimensions(img) {
						const dimensions = img.naturalWidth + ' x ' + img.naturalHeight + ' pixels';
						img.parentElement.querySelector('.image-dimensions').textContent = dimensions;
					}
				</script>
			</div>
        {/if}
		<div class="upload-container mt-4">
			<div class="dropzone" id="menuitem_image_upload">
				<div class="dz-message needsclick">
					Drop files here or click to upload an image.
				</div>
			</div>
		</div>

		<div class="icon-selection-container mt-4">
			<span data-guid="{$menuItem->getGuid()}" class="btn btn-sm btn-secondary btnFromIconFinder">
				<i class="fas fa-icons me-2"></i> Select Icon from IconFinder
			</span>
		</div>
		<div class="icon-selection-container mt-4">
			<span data-guid="{$menuItem->getGuid()}" class="btn btn-sm btn-secondary btnFromNounProject">
				<i class="fas fa-icons me-2"></i> Select Icon from NounProject
			</span>
		</div>

	</div>
	<!--begin::Body-->
</div>
<!--end::Upload Widget-->
