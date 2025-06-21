<!--begin::Toolbar-->
<div class="d-flex flex-wrap flex-stack mb-8 py-3">
<!--begin::Left-->
	<div class="d-flex flex-wrap align-items-center my-1">
		<a href="/admin/stati" class="btn btn-sm btn-primary me-2">
			<i class="fas fa-chevron-circle-left"></i> Zurück zur Übersicht
		</a>


	</div>
	<!--end::Left-->
</div>
<!--end::Toolbar-->
<!--begin::Properties Widget-->
<div class="card mb-6 mb-xl-9 shadow-sm">
<!--begin::Header-->
	<div class="card-header border-0 pt-6 pb-5">
	<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Status: <span class="text-{$status->getColor()}">{$status->getPublicName()}</span></span>
			<span class="text-muted mt-1 fw-semibold fs-7">ID: {$status->getStatusId()}</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body py-5 px-4">
	<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-5">
			<!--begin::Table body-->
				<tbody>

				<tr>
					<td>
						<div class="d-flex align-items-center">
							<div class="d-flex justify-content-start flex-column">
								<div class="mb-2">
									<span class="fw-bold me-2">Name:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-6"
									   data-type="text"
									   data-pk="{$status->getGuid()}"
									   data-name="Name"
									   data-url="/api/admin/status/update.json"
									   data-value="{$status->getPublicName()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Color:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-source="/api/agent/colors.json?format=editable"
									   data-pk="{$status->getGuid()}"
									   data-name="Color"
									   data-url="/api/admin/status/update.json"
									   data-value="{$status->getColor()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Sort Order:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="number"
									   data-pk="{$status->getGuid()}"
									   data-name="SortOrder"
									   data-url="/api/admin/status/update.json"
									   data-value="{$status->getSortOrder()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Icon:</span>
									<i class="fa {$status->getIcon()}"></i>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7 ms-2"
									   data-type="text"
									   data-pk="{$status->getGuid()}"
									   data-name="Icon"
									   data-url="/api/admin/status/update.json"
									   data-value="{$status->getIcon()}"
									></a>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Properties:</span>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" {if $status->getIsOpen()}checked{/if} disabled>
										<span class="form-check-label">Ist Offen</span>
									</label>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" {if $status->getIsFinal()}checked{/if} disabled>
										<span class="form-check-label">Ist Final</span>
									</label>
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" {if $status->getIsDefault()}checked{/if} disabled>
										<span class="form-check-label">Ist Standard</span>
									</label>
								</div>
								<h3 class="mt-8 mb-4 fs-2">Vorlagen</h3>
								<div class="mb-2">
									<span class="fw-bold me-2">CustomerNotificationTemplateId:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-source="/api/admin/notificationtemplates.json?format=editable"
									   data-pk="{$status->getGuid()}"
									   data-name="CustomerNotificationTemplateId"
									   data-url="/api/admin/status/update.json"
									   data-value="{$status->getCustomerNotificationtemplateId()}"
									></a>
									<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
										  data-url="/api/admin/status/update.json"
										  data-uiaction="reload"
										  data-pk="{$status->getGuid()}"
										  data-name="CustomerNotificationTemplateId"
										  data-value=""
									>
										<i class="ki-outline ki-cross fs-2"></i>
									</span>
								</div>
								<div class="mb-4">
								<span class="fw-bold me-2">AgentNotificationTemplateId:</span>
									<a href="#" class="editable text-gray-900 text-hover-primary fs-7"
									   data-type="select"
									   data-source="/api/admin/notificationtemplates.json?format=editable"
									   data-pk="{$status->getGuid()}"
									   data-name="AgentNotificationTemplateId"
									   data-url="/api/admin/status/update.json"
									   data-value="{$status->getAgentNotificationtemplateId()}"
									></a>
									<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
										  data-url="/api/admin/status/update.json"
										  data-uiaction="reload"
										  data-pk="{$status->getGuid()}"
										  data-name="AgentNotificationTemplateId"
										  data-value=""
									>
										<i class="ki-outline ki-cross fs-2"></i>
									</span>
								</div>


								<span class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7 mt-4">
									ID: {$status->getGuid()}
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



