<!--begin::Tables Widget 9-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Team Übersicht</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Offene Tickets nach Agent</span>
		</h3>
		<div class="card-toolbar">
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body py-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<!--begin::Table head-->
				<thead>
				<tr class="fw-bold text-muted">

					<th class="min-w-100px">Ticket</th>
					<th class="min-w-150px">Titel</th>
					<th class="min-w-150px">Melder</th>
					<th class="min-w-150px">Bearbeiter</th>
					<th class="min-w-150px">Status</th>
					<th class="w-25px">
					</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=$agents item=agent}
                    {assign aw $agent->getAgentWrapper()}
					<tr class="bg-light-primary">
						<td colspan="6">
							<div class="d-flex align-items-center mb-2">
								<div class="symbol symbol-45px me-5">
									<img src="/api/user/{$agent->getUpn()}/image.jpg" alt="{$agent->getDisplayName()}" class="rounded-circle w-35px h-35px"/>
								</div>
								<div class="d-flex justify-content-start flex-column">
									<span class="text-gray-900 fw-bold fs-6">{$agent->getDisplayName()}</span>
								</div>
							</div>
						</td>
					</tr>
                    {foreach from=$aw->getOpenTickets() item=ticket}

                        {assign category $ticket->getCategory()}
                        {assign status $ticket->getStatus()}
                        {assign organizationUserFromMessenger $ticket->getOrganizationUserFromMessenger()}

						<tr data-group="{$dateStr}" class="ticket-row">

							<td>
								<a href="{$ticket->getLink()}" class="text-gray-900 fw-semibold text-hover-primary fs-6">
									<span class="text-gray-900 ext-hover-primary d-block fs-6">{$ticket->TicketNumber}</span>
								</a>
							</td>

							<td>
								<span class="text-muted fw-light fs-9">vor {$ticket->getCreatedAsDateEta()}, {$ticket->getCreatedDatetimeAsDateTime()->format("d.m.Y H:i")}</span><br/>
								<a href="{$ticket->getLink()}" class="text-gray-900 fw-semibold text-hover-primary fs-6">{$ticket->getSubject()}</a><br/>
								<span class="d-block">
								<a href="{$category->getAdminLink()}">
									<span class="text-{$category->getColor()} fw-medium fs-7">
										<i class="fas {$category->Icon}"></i> {$category->PublicName} -
									</span>
								</a>
								<span class="text-gray-700 fw-light fs-8">
									Fällig: {$ticket->getDueDatetimeAsDateTime()->format("d.m. H:i")}

                                    {if !$ticket->isDue()}
										<span data-kt-element="status" class="badge badge-light-success">
											in {$ticket->getDueDatetimeAsDateEta()->toEtaString()}
										</span>
									{else}
										<span data-kt-element="status" class="badge badge-light-warning">
											seit {$ticket->getDueDatetimeAsDateEta()->toEtaString()}
										</span>
                                    {/if}

								</span>
							</span>
							</td>

							<td>
								<div class="d-flex align-items-center">
									<div class="symbol symbol-45px me-5">
										<img src="/api/organizationuser/{$ticket->MessengerEmail}/image.jpg" alt="{$ticket->MessengerEmail}" class="rounded-circle w-35px h-35px"/>
									</div>
									<div class="d-flex justify-content-start flex-column">

                                        {if $organizationUserFromMessenger}
											<a href="{$organizationUserFromMessenger->getAgentLink()}"
											   class="text-gray-800 fw-medium text-hover-primary fs-6"
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
									<div class="symbol symbol-45px me-5">
										<img src="/api/user/{$assignee->getUpn()}/image.jpg" alt="{$assignee->getUpn()}" class="rounded-circle w-35px h-35px">
									</div>
                                {else}
									<div class="d-flex justify-content-start align-items-center">
									<span class="badge badge-danger fs-7 mb-2" title="Kein Bearbeiter zugewiesen">
										<i class="fas fa-exclamation-triangle fa-beat me-3"></i> Kein Bearbeiter
									</span>
									</div>
                                {/if}

                                {if login::isAgent()}
                                    {if !$ticket->isAssignedToUser(login::getUser())}
										<div class="d-flex justify-content-start align-items-center">
										<span data-ticket="{$ticket->GetGuid()}"
											  class="btnAssignTicketToMe btn btn-sm btn-secondary"
											  title="Dieses Ticket mir zuweisen">
											<i class="ki-outline ki-user"></i> Mir zuweisen
										</span>
										</div>
                                    {/if}
                                {/if}
							</td>

							<td class="text-end">
								<div class="d-flex flex-column w-100 me-2">

									<div class="btn-group mb-2 w-100">
										<a href="{$ticket->getLink()}" class="btn btn-sm btn-{$status->Color} text-start text-nowrap" style="width:130px">
											<i class="fas {$status->Icon} me-2"></i> {$status->PublicName}
										</a>
										<span class="btn btn-sm dropdown-toggle dropdown-toggle-split text-start" data-bs-toggle="dropdown" aria-expanded="false"
											  style="background-color: rgba(0,0,0,.08); max-width:40px">
									</span>
										<ul class="dropdown-menu">
                                            {if !$status->isDefaultWorkingStatus()}
												<li>
												<span role='button' class="btnAssignAndWorkOnTicket dropdown-item text-success"
													  data-assignme="true" data-ticket="{$ticket->GetGuid()}">
													<i class="fas fa-spinner fa-pulse me-2"></i> mir zuweisen und bearbeiten
												</span>
												</li>
                                            {/if}
											<li>
											<span role='button' class="btnCloseTicket dropdown-item text-danger" data-assignme="true" data-ticket="{$ticket->GetGuid()}">
												<i class="fas fa-times me-2"></i> Schließen
											</span>
											</li>
										</ul>
									</div>

                                    {if $ticket->hasTicketActionItems()}
										<div class="mt-1">
											<div class="d-flex flex-stack mb-1">
											<span class="fs-8 text-muted">
												{if $ticket->countOpenTicketActionItems()==0}🥳🎉{/if} {$ticket->countCompletedTicketActionItems()}/{$ticket->countTicketActionItems()} Aktionen erledigt</span>
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

								</div>
							</td>

							<td>
								<div class="d-flex justify-content-end flex-shrink-0">

                                    {if $ticket->isDue()}
										<span title="Ticket is due" class="btn btn-icon btn-light-warning btn-sm me-1 in-out-animation"
											  data-kt-element="hover"
											  data-kt-placement="bottom"
											  data-kt-title="Ticket is due">
										<i class="fas fa-exclamation-circle"></i>
									</span>
                                    {else}
										<span class="btn btn-icon btn-light-success btn-sm me-1 ">
										<i class="fas fa-check"></i>
									</span>
                                    {/if}

									<a href="{$ticket->getLink()}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
										<i class="ki-outline ki-book-open fs-2"></i>
									</a>
								</div>

							</td>

						</tr>
                    {/foreach}
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
