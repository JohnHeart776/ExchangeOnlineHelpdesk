<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Status</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Verwalten Sie Ihre Status</span>
		</h3>
		<div class="card-toolbar">
            {if login::isAdmin()}
				<button class="btn btnAddStatus btn-sm btn-primary hover-elevate-up">
					<i class="ki-outline ki-plus fs-2 me-1"></i>
					<span>Neuer Status</span>
				</button>
            {/if}
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
			<thead>
			<tr class="fw-bold text-muted">
				<th>Name</th>
				<th>Ist Offen</th>
				<th>Ist Final</th>
				<th>Standard</th>
				<th>Standard Zugewiesen</th>
				<th>Standard Kundenantwort</th>
				<th>Standard Gelöst</th>
				<th>Standard Geschlossen</th>
				<th>Sortierung</th>
				<th>Agent Vorlage</th>
				<th>Kunden Vorlage</th>
				<th>Aktionen</th>
			</tr>
			</thead>
			<tbody>
            {foreach from=StatusController::getAll(0, "ASC", "SortOrder") item=status}
				<tr class="">
					<td class="text-{$status->getColor()}">
						<i class="fas {$status->getIcon()}"></i> {$status->getPublicName()}
					</td>
					<td>{if $status->getIsOpen() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{if $status->getIsFinal() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{if $status->getIsDefault() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{if $status->isDefaultAssignedStatus() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{if $status->isDefaultCustomerReplyStatus() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{if $status->isDefaultResolvedStatus() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{if $status->isDefaultClosedStatus() == 1}<i class="ki-outline ki-check fs-2 text-success"></i>{/if}</td>
					<td>{$status->getSortOrder()}</td>
					<td>
                        {if $status->hasAgentNotificationTemplate()}
							<a href="/admin/notificationtemplate/{$status->getAgentNotificationTemplate()->getGuid()}">{$status->getAgentNotificationTemplate()->getName()}</a>
                        {/if}
					</td>
					<td>
                        {if $status->hasCustomerNotificationTemplate()}
							<a href="/admin/notificationtemplate/{$status->getCustomerNotificationTemplate()->getGuid()}">{$status->getCustomerNotificationTemplate()->getName()}</a>
                        {/if}
					</td>
					<td>
						<a href="/admin/status/{$status->getGuid()}" class="btn btn-icon btn-light-{$status->getColor()} btn-sm">
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