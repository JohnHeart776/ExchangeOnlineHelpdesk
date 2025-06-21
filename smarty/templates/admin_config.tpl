<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Config</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Edit Config Values here</span>
		</h3>
		<div class="card-toolbar">
			<div class="d-flex align-items-center position-relative">
				<span class="svg-icon svg-icon-1 position-absolute ms-4">
					<i class="fas fa-search"></i>
				</span>
				<input type="text" id="config-search" class="form-control form-control-solid w-250px ps-14" placeholder="Search config names...">
				<button id="reset-search" class="btn btn-sm btn-light ms-2">Reset</button>
			</div>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<!--begin::Table head-->
				<thead>
				<tr class="border-0">
					<th class="p-0"></th>
					<th class="p-0 min-w-150px mw-lg-350px"></th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

				{* Get configs and group them by their first dot-separated name *}
				{assign var="configs" value=ConfigController::getAll(0, null, "Name")}
				{assign var="grouped_data" value=Config::groupConfigsByFirstPart($configs)}
				{assign var="root_configs" value=$grouped_data.root_configs}
				{assign var="grouped_configs" value=$grouped_data.grouped_configs}

				{* Display root configs (without dots) *}
				{foreach from=$root_configs item=config}
				<tr>
					<td class="fw-semibold">
						{$config->getName()}
					</td>
					<td style="max-width: 300px;" class="text-end">
						<span class="editable text-muted"
							  data-name="Name"
							  data-pk="{$config->getGuid()}"
							  data-value="{$config->getValueForEditable()}"
							  data-type="textarea"
							  data-url="/api/admin/config/update.json"
						></span>
					</td>
				</tr>
				{/foreach}

				{* Display grouped configs (with dots) *}
				{foreach from=$grouped_configs key=group_name item=group_configs}
				<tr class="group-header">
					<td colspan="2" class="fw-bold">
						<div class="d-flex align-items-center">
							<span class="toggle-icon me-2" data-bs-toggle="collapse" data-bs-target="#group-{$group_name}" aria-expanded="false">
								<i class="fas fa-chevron-right"></i>
							</span>
							{$group_name}
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="p-0">
						<div class="collapse" id="group-{$group_name}">
							<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 mb-0">
								{foreach from=$group_configs item=config}
								<tr>
									<td class="fw-semibold ps-10">
										{$config->getName()}
									</td>
									<td style="max-width: 300px;" class="text-end">
										<span class="editable text-muted"
											  data-name="Name"
											  data-pk="{$config->getGuid()}"
											  data-value="{$config->getValueForEditable()}"
											  data-type="textarea"
											  data-url="/api/admin/config/update.json"
										></span>
									</td>
								</tr>
								{/foreach}
							</table>
						</div>
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
