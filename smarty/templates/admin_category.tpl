<!--begin::Properties Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Kategorie <a href="#" class="editable text-hover-primary text-gray-800 text-decoration-none" data-type="text" data-pk="{$category->getGuid()}" data-name="PublicName"
																	data-url="/api/admin/category/update.json">{$category->getPublicName()}</a></span>
			<span class="text-muted mt-1 fw-semibold fs-7">Details der ausgewählten Kategorie</span>
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
								<i class="fas {$category->getIcon()} fs-2x text-{$category->getColor()}"></i>
							</div>
							<div class="d-flex justify-content-start flex-column">
								<div class="mb-2">
									<span class="fw-bold me-2">Öffentlicher Name:</span>
									<a href="#" class="editable text-gray-900 fw-bold text-hover-primary fs-6"
									   data-type="text"
									   data-pk="{$category->getGuid()}"
									   data-name="PublicName"
									   data-url="/api/admin/category/update.json"
									   data-value="{$category->getPublicName()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Interner Name:</span>
									<span>
									   {$category->getInternalName()}
									</span>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Icon:</span>
									<a href="#" class="editable text-gray-900 fw-bold text-hover-primary fs-7"
									   data-type="text"
									   data-pk="{$category->getGuid()}"
									   data-name="Icon"
									   data-url="/api/admin/category/update.json"
									   data-value="{$category->getIcon()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Farbe:</span>
									<a href="#" class="editable text-gray-900 fw-bold text-hover-primary fs-7"
									   data-type="select"
									   data-source='[
                                               { "value": "primary", "text": "Primary"},
                                               { "value": "secondary", "text": "Secondary"},
                                               { "value": "success", "text": "Success"},
                                               { "value": "danger", "text": "Danger"},
                                               { "value": "warning", "text": "Warning"},
                                               { "value": "info", "text": "Info"},
                                               { "value": "dark", "text": "Dark"}
                                           ]'
									   data-pk="{$category->getGuid()}"
									   data-name="Color"
									   data-url="/api/admin/category/update.json"
									   data-value="{$category->getColor()}"
									></a>
								</div>

								<span class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
									ID: {$category->getCategoryId()}
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

<!--begin::Tickets Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Tickets in dieser Kategorie</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table id="ticketsTable" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<!--begin::Table head-->
				<thead>
				<tr class="border-0">
					<th class="p-0">Ticket ID</th>
					<th class="p-0">Datum</th>
					<th class="p-0">Melder</th>
					<th class="p-0">Titel</th>
					<th class="p-0">Status</th>
					<th class="p-0 min-w-100px text-end">Aktionen</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody id="tickets-table-body">
				</tbody>
				<!--end::Table body-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table container-->
	</div>
	<!--begin::Body-->
</div>
<!--end::Tickets Widget-->