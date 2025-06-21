<!--begin::Tables Widget 9-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-3">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-light fs-3">{$dashboardTitle}</span>
		</h3>
		<div class="card-toolbar">

			<a href="/agent/ticket/new/" class="btn btn-sm btn-info me-2">
				<i class="fas fa-plus"></i> Neues Ticket
			</a>

			<div class="dropdown">
				<button type="button" class="btn btn-sm btn-light-primary btn-active-primary dropdown-toggle" data-bs-toggle="dropdown">
					<i class="fas fa-swatchbook fs-2"></i> Massenaktionen
				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item btnCloseSelectedTickets" href="#"><i class="fas fa-times me-2"></i> Markierte Tickets schließen</a>
					<a class="dropdown-item btnCombineSelectedTickets" href="#"><i class="fas fa-merge me-2"></i> Tickets kombinieren</a>
					<a class="dropdown-item btnAssignMeOnSelectedTickets" href="#"><i class="fas fa-user me-2"></i> mir zuweisen</a>
				</div>
			</div>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body py-0">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-2">
			<!--begin::Table head-->
				<thead>
				<tr class="fw-medium text-muted">

					<th class="min-w-100px" style="padding: 0.5em;">Ticket</th>
					<th class="min-w-150px" style="padding: 0.5em;">Titel</th>
					<th class="min-w-150px" style="padding: 0.5em;">Melder</th>
					<th class="min-w-150px" style="padding: 0.5em;">Bearbeiter</th>
					<th class="min-w-150px" style="padding: 0.5em;">Status</th>
					<th class="min-w-150px" style="padding: 0.5em;">Action Items</th>
					<th class="w-25px" style="padding: 0.5em;">
					<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".ticket-check"/>
						</div>
					</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=$tickets item=ticket}

                    {assign category $ticket->getCategory()}
                    {assign status $ticket->getStatus()}
                    {assign organizationUserFromMessenger $ticket->getOrganizationUserFromMessenger()}
					<tr>

						<td style="padding: 0.5em;">
						<a href="{$ticket->getLink()}" class="text-gray-900 fw-medium text-hover-primary d-block fs-6">
								<span class="text-gray-900 fw-medium text-hover-primary d-block fs-6">{$ticket->TicketNumber}</span>
							</a>
						</td>

						<td>
							<a href="{$ticket->getLink()}" class="text-gray-900 text-hover-primary fs-6 mw-lg-525px mw-400px"
							   style="max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;">{$ticket->Subject}</a>
							<span class="d-block">
								<a href="{$category->getAdminLink()}">
									<span class="text-muted fw-light text-muted fs-7">
										<i class="fas {$category->Icon}"></i> {$category->PublicName} -
									</span>
								</a>
								<span class="text-gray-700 fw-thin fs-8">
									Fällig: {$ticket->getDueDatetimeAsDateTime()->format("d.m. H:i")}

									{if !$ticket->isDue()}
										<span data-kt-element="status" class="badge badge-light-success">in {$ticket->getDueDatetimeAsDateEta()->toEtaString()}</span>
                                    {else}
										<span data-kt-element="status" class="badge badge-light-warning">seit {$ticket->getDueDatetimeAsDateEta()->toEtaString()}</span>

									{/if}

								</span>
							</span>
						</td>

						<td>
							<div class="d-flex align-items-center">
								<div class="symbol symbol-25px me-3">
									<img src="/api/organizationuser/{$ticket->MessengerEmail}/image.jpg" alt="{$ticket->MessengerEmail}" class="rounded-circle w-25px h-25px"/>
								</div>
								<div class="d-flex justify-content-start flex-column">

                                    {if $organizationUserFromMessenger}
										<a href="{$organizationUserFromMessenger->getAgentLink()}"
										   class="text-gray-800 fw-light text-hover-primary fs-6"
										   style="max-width: 175px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{$ticket->MessengerName}</a>
                                    {else}
										<span class="text-gray-900 fs-6"
											  style="max-width: 175px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{$ticket->MessengerName}</span>
										<span class="text-muted text-muted d-block fs-7 text-truncate"
											  style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{$ticket->MessengerEmail}">{$ticket->MessengerEmail}</span>
                                    {/if}

								</div>
							</div>
						</td>

						<td>
                            {if $ticket->hasAssignee()}
                                {assign assignee $ticket->getAssigneeAsUser()}
								<div class="d-flex align-items-center fs-6">
									<div class="symbol symbol-20px me-3">
										<img src="/api/user/{$assignee->getUpn()}/image.jpg" alt="{$assignee->getUpn()}" class="rounded-circle w-20px h-20px""/>
									</div>
									<div class="d-flex justify-content-start flex-column">
										<span class="text-gray-800 fw-light text-hover-primary fs-7">{$assignee->getDisplayName()}</span>
									</div>
								</div>
                            {else}
								<span class="text-muted fs-7">Kein Bearbeiter</span>
                            {/if}
						</td>

						<td class="text-end">
							<div class="d-flex flex-column w-100 me-2">
								<span class="badge badge-light-{$status->Color}">
									<i class="fas {$status->Icon} me-2"></i> {$status->PublicName}
								</span>
							</div>
						</td>

						<td>
                            {if $ticket->hasTicketActionItems()}
								<div class="mt-2">
									<div class="d-flex flex-stack mb-2">
										<span class="text-muted me-2 fs-7 fw-medium">{$ticket->countCompletedTicketActionItems()}/{$ticket->countTicketActionItems()} ActionItems</span>
									</div>
									<div class="progress h-6px w-100">
										<div class="progress-bar bg-primary" role="progressbar"
											 style="width: {$ticket->countCompletedTicketActionItemsInPercent()}%"
											 aria-valuenow="{$ticket->countCompletedTicketActionItems()}"
											 aria-valuemin="0"
											 aria-valuemax="{$ticket->countTicketActionItems()}"></div>
									</div>
								</div>
                            {/if}
						</td>

						<td>
							<div class="d-flex justify-content-end flex-shrink-0">
								<div class="form-check form-check-sm form-check-custom form-check-solid">
									<input class="form-check-input ticket-check"
										   type="checkbox" name="ticket[]"
										   value="{$ticket->GetGuid()}"/>
								</div>
								<a href="{$ticket->getLink()}" class="btn btn-icon btn-sm btn-light ms-2">
									<i class="ki-outline ki-book-open"></i>
								</a>
								<button type="button" class="btnCloseTicket btn btn-icon btn-sm btn-light-info ms-2 btnCloseTicket"
										data-assignme="true"
										data-ticket="{$ticket->GetGuid()}">
									<i class="ki-outline ki-cross-square"></i>
								</button>
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
<!--end::Tables Widget 9-->
									