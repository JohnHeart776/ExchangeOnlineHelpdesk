<!--begin::Properties Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Organisationsbenutzer {$organizationUser->getDisplayName()}</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Details des ausgewählten Benutzers</span>
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
						<div class="d-flex align-items-start">
							<div class="symbol symbol-64px me-5">
								<img src="{$organizationUser->getAvatarLink()}" class="rounded-circle" alt="{$organizationUser->getDisplayName()}"/>
							</div>
							<div class="d-flex justify-content-start flex-column">
								<div class="mb-2">
									<span class="fw-bold me-2">Vorname:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getGivenName()}</span>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">Nachname:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getSurname()}</span>
								</div>
								<div class="mb-2">
									<span class="fw-bold me-2">E-Mail:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getMail()}</span>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">JobTitle:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getJobTitle()}</span>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Abteilung:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getDepartment()}</span>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Mobiltelefon:</span>
									<span class="text-gray-900 fw-bold fs-6"><a href="tel:{$organizationUser->getMobilePhone()}">{$organizationUser->getMobilePhone()}</a></span>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Büro:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getOfficeLocation()}</span>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Firma:</span>
									<span class="text-gray-900 fw-bold fs-6">{$organizationUser->getCompanyName()}</span>
								</div>

								<div class="mb-2">
									<span class="fw-bold me-2">Geschäftliche Telefonnummern:</span>
									<span class="text-gray-900 fw-bold fs-6">
										{foreach from=$organizationUser->getBusinessPhonesDecoded() item=phone}
											<div><a href="tel:{$phone}">{$phone}</a></div>
                                        {/foreach}
									</span>
								</div>

								<span class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
									ID: {$organizationUser->getOrganizationUserId()}
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
                {foreach from=$organizationUser->getTicketAssociates() item=ta}
                    {assign ticket $ta->getTicket()}

					<tr>
						<td><a href="{$ticket->getLink()}">{$ticket->getTicketNumber()}</a></td>
						<td>{$ticket->getCreatedDatetimeAsDateTime()->format("d.m.Y H:i")}</td>
						<td>
							{if $ticket->messengerIsOrganizationUser()}
								{assign ouser $ticket->getOrganizationUserFromMessenger()}
								<a href="{$ouser->getAgentLink()}">{$ticket->getReporteeImage(24)}</a>
								{else}
								{$ticket->getReporteeImage(24)}
							{/if}
						</td>
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
						<td colspan="6" class="text-center fw-semibold"><i class="fas fa-info-circle"></i> Keine Tickets gefunden</td>
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
<!--end::Tickets Widget-->