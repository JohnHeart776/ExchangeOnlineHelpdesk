<!--begin::Properties Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Benutzer {$user->getDisplayName()}</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Details des ausgewählten Benutzers</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="d-flex">
			<div class="me-5">
				<img src="/api/user/{$user->getGuid()}/image.jpg" class="rounded-circle" width="150" height="150" alt="{$user->getDisplayName()}">
			</div>
			<div class="flex-grow-1">
				<div class="row g-5 overflow-hidden">
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">TenantId</div>
							<div class="fw-bold text-break">{$user->getTenantId()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">AzureObjectId</div>
							<div class="fw-bold text-break">{$user->getAzureObjectId()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">Upn</div>
							<div class="fw-bold text-break">{$user->getUpn()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">DisplayName</div>
							<div class="fw-bold text-break">{$user->getDisplayName()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">Name</div>
							<div class="fw-bold text-break">{$user->getName()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">Surname</div>
							<div class="fw-bold text-break">{$user->getSurname()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">Title</div>
							<div class="fw-bold text-break">{$user->getTitle()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">Mail</div>
							<div class="fw-bold text-break">{$user->getMail()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">Telephone</div>
							<div class="fw-bold text-break">{$user->getTelephone()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">OfficeLocation</div>
							<div class="fw-bold text-break">{$user->getOfficeLocation()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">CompanyName</div>
							<div class="fw-bold text-break">{$user->getCompanyName()}</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">BusinessPhones</div>
							<div class="fw-bold">
								<ul class="list-unstyled mb-0">
                                    {foreach from=$user->getBusinessPhonesDecoded() item=phone}
										<li><a href="tel:{$phone}" class="text-break">{$phone}</a></li>
                                    {/foreach}
								</ul>
							</div>
						</div>
					</div>

					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">AccountEnabled</div>
							<div class="fw-bold">
								<a href="#" class="editable"
								   data-name="AccountEnabled"
								   data-type="select"
								   data-source="[{ 'value': 1, 'text': 'Yes' }, { 'value': 0, 'text': 'No' }]"
								   data-pk="{$user->getGuid()}"
								   data-url="/api/admin/user/update.json"
								   data-value="{$user->getAccountEnabledAsInt()}"></a>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-6">
						<div class="border rounded p-3 mb-1">
							<div class="fs-7 text-muted">UserRole</div>
							<div class="fw-bold">
								<a href="#" class="editable"
								   data-name="UserRole"
								   data-type="select"
								   data-source="[{ 'value': 'guest', 'text': 'Guest' }, { 'value': 'user', 'text': 'User' }, { 'value': 'agent', 'text': 'Agent' }, { 'value': 'admin', 'text': 'Admin' }]"
								   data-pk="{$user->getGuid()}"
								   data-url="/api/admin/user/update.json"
								   data-value="{$user->getUserRole()}"></a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
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
			<span class="card-label fw-bold fs-3 mb-1">Zugeordnete Tickets</span>
		</h3>
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
					<th class="p-0">Ticketnummer</th>
					<th class="p-0">Datum</th>
					<th class="p-0">Melder</th>
					<th class="p-0">Titel</th>
					<th class="p-0">Status</th>
					<th class="p-0 min-w-100px text-end">Aktionen</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>
                {if $user->isAgent()}
                    {foreach from=$user->getAgentWrapper()->getTickets() item=ticket}
						<tr>
							<td><a href="{$ticket->getLink()}">{$ticket->getTicketNumber()}</a></td>
							<td>{$ticket->getCreatedDatetimeAsDateTime()->format("d.m.Y H:i")}</td>
							<td>{$ticket->getReporteeImage(32)}</td>
							<td>{$ticket->getSubject()}</td>
							<td>{$ticket->getStatus()->getBadge()}</td>
							<td class="text-end">
								<a href="{$ticket->getLink()}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
									<i class="ki-outline ki-book-open fs-2"></i>
								</a>
							</td>
						</tr>
                        {foreachelse}
						<tr>
							<td colspan="6" class="text-center fw-semibold"><i class="fas fa-info-circle"></i> No Tickets found</td>
						</tr>
                    {/foreach}
                {else}
					<tr>
						<td colspan="6" class="text-center fw-semibold"><i class="fas fa-info-circle"></i> User is not an Agent</td>
					</tr>
                {/if}
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