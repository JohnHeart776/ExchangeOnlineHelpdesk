<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Benachrichtigungstemplates</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Verwalten Sie Ihre Benachrichtigungstemplates</span>
		</h3>
		<div class="card-toolbar">
			<button class="btn btnAddTemplate btn-sm btn-primary hover-elevate-up">
				<i class="ki-outline ki-plus fs-2 me-1"></i>
				<span>Neues Template</span>
			</button>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
			<thead>
			<tr class="fw-bold text-muted">
				<th>Name</th>
				<th>Enabled</th>
				<th>Mail Subject</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
            {foreach from=NotificationTemplateController::getAll() item=template}
				<tr class="">
					<td>
						<a href="#"
						   class="editable"
						   data-type="text"
						   data-name="Name"
						   data-pk="{$template->getGuid()}"
						   data-value="{$template->getName()}"
						   data-url="/api/admin/notificationtemplate/update.json">
						</a>
					</td>
					<td>
						<a href="#"
						   class="editable"
						   data-type="select"
						   data-name="Enabled"
						   data-pk="{$template->getGuid()}"
						   data-value="{$template->getEnabled()}"
						   data-url="/api/admin/notificationtemplate/update.json"
						   data-source="[ { value: 0, text: 'No' },{ value: 1, text: 'Yes' } ]">
						</a>
					</td>
					<td>{$template->getMailSubject()}</td>
					<td>
						<a href="/admin/notificationtemplate/{$template->getGuid()}" class="btn btn-icon btn-light btn-sm">
							<i class="ki-outline ki-notepad-edit fs-2"></i>
						</a>
					</td>
				</tr>
            {/foreach}
			</tbody>
		</table>
	</div>
	<!--end::Table container-->
</div>
<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->