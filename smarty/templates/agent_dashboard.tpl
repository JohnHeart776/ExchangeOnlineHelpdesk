<!--begin::Tables Widget 9-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Tickets</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Showing {count($tickets)} tickets.</span>
		</h3>
		<div class="card-toolbar">

			<div class="d-flex flex-column flex-md-row gap-2 align-items-center">
				<div class="input-group order-md-1 flex-grow-1">
					<input type="text" class="form-control border-dark" placeholder="Search ..." id="ticketSearch">
					<span class="btn border-dark btn-dark" type="button" onclick="$('#ticketSearch').val('').trigger('input')">
						<i class="fas fa-times"></i>
					</span>
				</div>

				<span class="btn btn-sm btn-secondary border-dark me-md-2" id="collapseAllGroups" style="white-space: nowrap">
					<i class="fas fa-compress"></i> Collapse Older
				</span>

				<a href="/agent/ticket/new/">
					<span class="btn btn-sm btn-info me-md-2" style="white-space: nowrap">
						<i class="fas fa-plus"></i> New Ticket
					</span>
				</a>

				<span class="btn btn-sm btn-warning me-2" id="clearSelections" style="white-space: nowrap; display: none;">
					<i class="fas fa-times"></i> Clear Selection
				</span>

				<div class="dropdown" id="segmentBatchActions" style="display: none;">
					<span class="btn btn-sm btn-light-info dropdown-toggle" data-bs-toggle="dropdown" style="white-space: nowrap">
						<i class="fas fa-swatchbook fs-2"></i> Massenaktionen
					</span>
					<div class="dropdown-menu">
						<a class="dropdown-item btnSolveSelectedTickets text-success" href="#"><i class="fas fa-check me-2"></i> Solve Selected Tickets</a>
						<a class="dropdown-item btnCloseSelectedTickets text-danger" href="#"><i class="fas fa-times me-2"></i> Close Selected Tickets</a>
						<a class="dropdown-item btnCombineSelectedTickets text-info" href="#"><i class="fas fa-merge me-2"></i> Combine Tickets</a>
						<a class="dropdown-item btnAssignMeOnSelectedTickets text-primary" href="#"><i class="fas fa-user me-2"></i> Assign to Me</a>
					</div>
				</div>
			</div>
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
					<th class="min-w-150px">Title</th>
					<th class="min-w-150px">Reporter</th>
					<th class="min-w-150px">Assignee</th>
					<th class="min-w-150px">Status</th>
					<th class="w-25px">
						<div class="form-check form-check-sm form-check-custom form-check-solid">
							<input class="form-check-input" type="checkbox" value="1" id="checkAllVisibleTickets"/>
						</div>
					</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->

				<tbody id="ticketsTableBody">

                {assign var="currentDate" value=""}
                {foreach from=$tickets item=ticket}
                    {assign var="ticketDate" value=$ticket->getCreatedDatetimeAsDateTime()}
                    {assign var="dateStr" value=$ticketDate->format('Y-m-d')}
                    {assign var="daysOld" value=$ticketDate->diff(DateTimeHelper::getNow())->days}

                    {* Group Header *}
                    {if $dateStr != $currentDate && ($daysOld < 7 || ($daysOld >= 7 && $currentDate == ''))}
                        {assign var="currentDate" value=$dateStr}
						<tr class="">

							<td colspan="5" class="groupHeader cursor-pointer bg-light" data-date="{$dateStr}" role="button">
								<h3 class="fs-6 text-primary fw-bold mb-0">
									<i class="fas fa-chevron-down me-2 collapse-icon" style="transition: transform .2s"></i>
                                    {if $daysOld >= 7}
										Older
                                    {else}
                                        {DateTimeHelper::getWeekdayInEnglish($ticketDate)}, {$ticketDate->format('Y-m-d')}
                                    {/if}
								</h3>
							</td>

							<td class="bg-light text-end">
								<div class="form-check form-check-sm form-check-custom form-check-solid">
									<input class="form-check-input checkVisibleTicketsOf" type="checkbox" data-target-class="ticket-check" data-data-key="ticketdate" data-data-value="{$dateStr}"/>
								</div>
							</td>

						</tr>
                    {/if}

                    {assign category $ticket->getCategory()}
                    {assign status $ticket->getStatus()}
                    {assign organizationUserFromMessenger $ticket->getOrganizationUserFromMessenger()}
					<tr data-date="{$dateStr}" class="ticket-row" tabindex="0" data-ticket="{$ticket->getGuid()}" data-url="{$ticket->getLink()}">

						<td class="ticketNumberContainer" data-ticketnumber="{$ticket->TicketNumber}">
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
									<img
											src="/api/user/{$assignee->getUpn()}/image.jpg"
											alt="{$assignee->getUpn()}"
											class="rounded-circle w-35px h-35px"
											data-bs-toggle="tooltip"
											data-bs-title="{$assignee->getDisplayName()}"
									/>
								</div>
                            {else}
								<div class="btn btn-danger btn-sm symbol symbol-45px me-5" data-bs-toggle="tooltip" data-bs-title="No assignee">
									<i class="fas fa-exclamation fa-beat"></i>
								</div>
                            {/if}

                            {if login::isAgent() && !$ticket->isAssignedToUser(login::getUser())}
								<span data-ticket="{$ticket->GetGuid()}"
									  class="btnAssignTicketToMe btn btn-sm btn-light-secondary ms-2"
									  data-bs-toggle="tooltip"
									  data-bs-title="Assign this ticket to me">
									<i class="fas fa-mail-forward"></i>
								</span>
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
												<span role='button' class="btnAssignAndWorkOnTicket dropdown-item text-info"
													  data-assignme="true" data-ticket="{$ticket->GetGuid()}">
													<i class="fas fa-spinner fa-pulse me-2"></i> Assign to me and work
												</span>
											</li>
                                        {/if}
                                        {if $ticket->hasTicketAssociates()}
											<li>
												<span role='button' class="btnSolveTicket dropdown-item text-success" data-assignme="true" data-ticket="{$ticket->GetGuid()}">
													<i class="fas fa-user-check me-2"></i> Solve
												</span>
											</li>
                                        {/if}
										<li>
											<span role='button' class="btnCloseTicket dropdown-item text-danger" data-assignme="true" data-ticket="{$ticket->GetGuid()}">
												<i class="fas fa-user-times me-2"></i> Close
											</span>
										</li>
									</ul>
								</div>

                                {if $ticket->hasTicketActionItems()}
									<div class="mt-1">
										<div class="d-flex flex-stack mb-1">
											<span class="fs-8 text-muted">
												{if $ticket->countOpenTicketActionItems()==0}🥳🎉{/if} {$ticket->countCompletedTicketActionItems()}/{$ticket->countTicketActionItems()} Actions completed</span>
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

								<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
									<input class="form-check-input ticket-check"
										   type="checkbox"
										   name="ticket[]"
										   data-ticketdate="{$ticket->getCreatedDateTimeAsDateTime()->format("Y-m-d")}"
										   value="{$ticket->GetGuid()}"/>
								</div>

                                {if $ticket->isDue()}
									<span title="Ticket is due" class="btn btn-icon btn-light-warning btn-sm me-1 in-out-animation"
										  data-kt-element="hover"
										  data-kt-placement="bottom"
										  data-kt-title="Ticket is overdue">
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
