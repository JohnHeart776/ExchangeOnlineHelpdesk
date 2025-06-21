<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Template Texts</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Manage template text configurations</span>
		</h3>
		<div class="card-toolbar">
			<a href="#" class="btnAddNewTemplateText btn btn-sm btn-light-primary">
				<i class="ki-outline ki-plus fs-2"></i>New Template Text</a>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" data-kt-table="true">
				<!--begin::Table head-->
				<thead>
				<tr class="fw-bold fs-6 text-gray-800">
					<th>Name</th>
					<th>Description</th>
					<th>Last Modified</th>
					<th class="text-end">Actions</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=TemplateTextController::getAll(0, "ASC", "Name") item=template}
					<tr>
						<td>
							<a href="/agent/templatetext/{$template->getGuid()}" class="text-gray-800 text-hover-primary mb-1">{$template->getName()}</a>
						</td>
						<td>{$template->getDescription()}</td>
						<td>{$template->getCreatedDatetimeAsDateTime()->format("Y-m-d H:i:s")}</td>
						<td class="text-end">
							<a href="{$template->getAgentLink()}">
								<span class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
									<i class="ki-outline ki-pencil fs-2"></i>
								</span>
							</a>
							<span class="actionPostButton btn btn-icon btn-bg-light btn-warning-color-primary btn-sm"
								  data-url="/api/agent/templatetext/delete.json"
								  data-uiaction="reload"
								  data-prompt="Really delete this template?"
								  data-success=""
								  data-pk="{$template->getGuid()}"
							>
                								<i class="ki-outline ki-trash fs-2"></i>
                							</span>
						</td>
					</tr>
                {/foreach}

				</tbody>
				<!--end::Table body-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table container-->
	</div>
	<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->
