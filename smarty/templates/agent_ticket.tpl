{assign category $ticket->getCategory()}
{assign status $ticket->getStatus()}
{assign organizationUserFromMessenger $ticket->getOrganizationUserFromMessenger()}

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
	<!--begin::Content container-->
	<div id="kt_app_content_container" class="app-container container-fluid">
		<!--begin::Navbar-->
		<div class="card mb-5 mb-xxl-8">
			<div class="card-body pt-0 pb-0">
				<!--begin::Details-->
				<div class="d-flex flex-wrap flex-sm-nowrap mb-6">
					<!--begin::Image-->
					<div class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
						<img class="mw-50px mw-lg-75px" src="/api/organizationuser/{$ticket->MessengerEmail}/image.jpg" alt="{$ticket->MessengerEmail}"/>
					</div>
					<!--end::Image-->
					<!--begin::Wrapper-->
					<div class="flex-grow-1">
						<!--begin::Head-->
						<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
							<!--begin::Details-->
							<div class="d-flex flex-column">

								<!--begin::CreatedDatetime-->
								<div class="d-flex align-items-center mb-1 fs-8">
									<div class="d-flex align-items-center me-5 text-gray-700">
										<i class="ki-outline ki-calendar me-1"></i> vor {$ticket->getCreatedAsDateEta()}, {$ticket->getCreatedDatetimeAsDateTime()->format('d.m.Y H:i')}
									</div>
								</div>
								<!--end::CreatedDatetime-->

								<!--begin::Title-->
								<h1 class="page-heading d-flex flex-column justify-content-center mb-6">
									<span class="badge badge-lg bg-info text-white fw-bold fs-3">#{$ticket->getTicketNumber()}</span>
								</h1>
								<!--end::Title-->

								<div class="d-flex">
									<div class="mb-6 btn btn-lg btn-light-{$status->getColor()} px-4 py-2 rounded me-3 cursor-pointer"
										 data-kt-menu-trigger="click"
										 data-kt-menu-placement="bottom-end">
										<div class="d-flex align-items-center">
											<i class="fa {$status->getIcon()} fs-4 me-2"></i>
											<span class="fw-bold fs-6">Status {$status->getPublicName()}</span>
										</div>
									</div>
									<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
										 data-kt-menu="true">
										<!--begin::Heading-->
										<div class="menu-item px-3">
											<div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Select New Status</div>
										</div>
										<!--end::Heading-->
                                        {foreach from=$ticket->getAssignableStatus() item=newStatus}
											<!--begin::Menu item-->
											<div class="menu-item px-3">
											<span class="menu-link assignTicketStatus px-3 text-{$newStatus->getColor()} {if $newStatus->isOpen()}border-start border-success border-3{elseif $newStatus->isClosed()}border-start border-danger border-3{/if}"
												  data-ticket="{$ticket->getGuid()}"
												  data-status="{$newStatus->getGuid()}">
												<i class="fa {$newStatus->getIcon()}"></i> {$newStatus->getPublicName()}
                                                {if $newStatus->hasCustomerNotificationTemplate()}
													<span class="m-2" title="Customer Notification">
														C<i class="fa-solid fa-user-group"></i><i class="fas fa-bell me-2"></i>
													</span>
                                                {/if}
                                                {if $newStatus->hasCustomerNotificationTemplate()}
													<span class="m-2" title="Agent Notification">
														A<i class="fa-solid fa-user-crown"></i><i class="fas fa-bell me-2"></i>
													</span>
                                                {/if}
											</span>
											</div>
											<!--end::Menu item-->
                                        {/foreach}
									</div>

                                    {if login::isAgent() && $status->isOpen()}
                                        {if $ticket->hasTicketAssociates()}
											<div class="mb-6 btn btn-lg btn-light-success px-4 py-2 rounded me-3 cursor-pointer btnSolveTicket" data-ticket="{$ticket->getGuid()}">
												<div class="d-flex align-items-center">
													<i class="fa fa-user-check fs-4 me-2"></i>
													<span class="fw-bold fs-6">Resolve Ticket</span>
												</div>
											</div>
                                        {else}
											<div class="mb-6 btn btn-lg btn-light px-4 py-2 rounded me-3" disabled>
												<div class="d-flex align-items-center">
													<i class="fa fa-user-times fs-4 me-2"></i>
													<span class="fw-bold fs-6">Ticket can only be resolved once a person is connected</span>
												</div>
											</div>
                                        {/if}
										<div class="mb-6 btn btn-lg btn-light-danger px-4 py-2 rounded me-3 cursor-pointer btnCloseTicket" data-ticket="{$ticket->getGuid()}">
											<div class="d-flex align-items-center">
												<i class="fa fa-user-times fs-4 me-2"></i>
												<span class="fw-bold fs-6">Close Ticket</span>
											</div>
										</div>
                                    {/if}
								</div>


								<!--begin::Subject-->
								<div class="d-flex align-items-center mb-1">
									<i class="ki-outline ki-text fs-4 me-1"></i>
									<a class="editable text-gray-900 text-hover-primary fs-2 fw-bold me-3"
									   data-type="text"
									   data-pk="{$ticket->getGuid()}"
									   data-name="Subject"
									   data-value="{$ticket->getValueForEditable("Subject")}"
									   data-url="/api/agent/ticket/update.json">{$ticket->getValueForEditable("Subject")}</a>
								</div>
								<!--end::Subject-->

								<!--begin::Category-->
								<div class="d-flex flex-column">
									<div class="d-flex align-items-center me-5 mb-2">
										<i class="ki-outline ki-category fs-4 me-1"></i>
										<span class="editable text-gray-800 text-hover-info"
											  data-name="CategoryId"
											  data-pk="{$ticket->getGuid()}"
											  data-value="{$ticket->getCategoryId()}"
											  data-type="select"
											  data-url="/api/agent/ticket/update.json"
											  data-source="/api/agent/categories.json?format=editable"
										>{$category->getName()}</span>
									</div>
								</div>
								<!--end::Category-->

								<!--begin::Description-->
								<div class="d-flex flex-column flex-wrap fw-semibold mb-4 fs-6 text-gray-500">
									<div class="d-flex align-items-center me-5 mb-2">
										<i class="ki-outline ki-profile-user fs-4 me-1"></i>
                                        {if $organizationUserFromMessenger}
											<a href="/agent/organizationuser/{$organizationUserFromMessenger->getGuid()}" class="text-gray-600 text-hover-primary">
                                                {$ticket->getMessengerName()} &lt;{$ticket->getMessengerEmail()}&gt;
											</a>
                                        {else}
											<span class="text-gray-600 text-hover-primary">
												{$ticket->getMessengerName()} &lt;{$ticket->getMessengerEmail()}&gt;
											</span>
                                        {/if}
									</div>

									<div class="d-flex align-items-center me-5 mb-2">
										<i class="ki-outline ki-calendar-tick fs-4 me-1"></i>
										<small>Letztes Update: {$ticket->getUpdatedDatetimeAsDateTime()->format('d.m.Y H:i')}</small>
									</div>
								</div>

                                {if login::isAdmin()}
									<div class="d-flex flex-column text-muted">
										<div class="d-flex flex-wrap gap-4">
											Debug-Info:
											<div class="">ID: {$ticket->getTicketId()}</div>
											<div class="">Status: {$status->getPublicName()}</div>
											<div class="">StatusIsFinal: {$status->getIsFinalAsInt()}</div>
										</div>
									</div>
                                {/if}


								<div class="d-flex align-items-center gap-2 gap-lg-3 py-2">
                                    {if login::isAdmin()}
										<a href="/admin/ticket/{$ticket->getGuid()}/export/text.txt" target="_blank" class="btn btn-sm btn-outline btn-active-primary bg-body">
											<i class="ki-outline ki-eye fs-4 me-3"></i> Text Export
										</a>
                                    {/if}

									<span class="btnCopyTicket btn btn-sm btn-outline btn-active-primary bg-body" data-guid="{$ticket->getGuid()}">
										<i class="ki-outline ki-copy me-3"></i> Ticket kopieren
									</span>

                                    {if $ticket->hasMails()}
										<span class="btn btn-sm btn-outline btn-active-primary btnSuggestTicketSubject" data-ticket="{$ticket->getGuid()}">
											<i class="fas fa-wand-magic-sparkles me-3"></i> Ticket Betreff automatisch optimieren
										</span>
                                    {/if}
								</div>

							</div>
							<!--end::Head-->
							<!--begin::Info-->
							<div class="d-flex flex-wrap justify-content-start">
								<!--begin::Stats-->
								<div class="d-flex flex-wrap">

                                    {if $status->isOpen()}
										<!--begin::Stat-->
										<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
											<!--begin::Number-->
											<div class="d-flex align-items-center">
												<div class="">
                                                    {if !$ticket->isDue()}
														in
														<span class="fs-4 fw-bold">{$ticket->getDueDatetimeAsDateEta()->toEtaString()}</span>
														-
														<span class="fw-medium">{$ticket->getDueDatetimeAsDateTime()->format('d.m.Y H:i')}</span>
                                                    {else}
														<span class="in-out-animation text-danger">
														<i class="fa fa-exclamation-triangle"></i> Ticket seit {$ticket->getDueDatetimeAsDateTime()->format('d.m.Y H:i')} fällig!
													</span>
                                                    {/if}
													<div class="mt-2">
														<button class="btn btn-sm btn-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start">
															<i class="ki-outline ki-calendar-add"></i> Fälligkeitsdatum ändern
														</button>
														<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 py-4 w-200px" data-kt-menu="true">

															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="ai">
																	<i class="fas fa-wand-magic-sparkles me-3"></i> Fälligkeit automatisch erkennen
																</a>
															</div>
															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="tomorrow">
																	<i class="ki-outline ki-calendar-tick me-2"></i>Tomorrow
																</a>
															</div>
															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="endweek">
																	<i class="ki-outline ki-calendar-8 me-2"></i>End of this week
																</a>
															</div>
															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="nextweek">
																	<i class="ki-outline ki-calendar-8 me-2"></i>Next week
																</a>
															</div>
															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="nextmonth">
																	<i class="ki-outline ki-calendar-add me-2"></i>Next month
																</a>
															</div>

															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="nextyear">
																	<i class="ki-outline ki-calendar-add me-2"></i>Next year
																</a>
															</div>

															<div class="separator my-3"></div>

															<div class="menu-item px-3">
																<a href="#" class="changeDueDate menu-link px-3" data-ticket="{$ticket->getGuid()}" data-target="custom">
																	<i class="ki-outline ki-calendar-edit me-2"></i>Custom
																</a>
															</div>
														</div>
													</div>

												</div>
											</div>
											<!--end::Number-->
										</div>
										<!--end::Stat-->
                                    {/if}

                                    {if $ticket->hasTicketActionItems()}
										<!--begin::Stat-->
										<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
											<!--begin::Number-->
											<div class="d-flex align-items-center">
                                                {*											<i class="ki-outline ki-arrow-down fs-3 text-danger me-2"></i>*}
												<div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="{$ticket->countTicketActionItems()}">0</div>
											</div>
											<!--end::Number-->
											<!--begin::Label-->
											<div class="fw-semibold fs-6 text-gray-500">Action Items</div>
											<!--end::Label-->
										</div>
										<!--end::Stat-->
                                    {/if}

									<!--begin::Assignee-->
									<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
										<!--begin::Number-->
										<div class="d-flex align-items-center">
                                            {if $ticket->hasAssignee()}
                                                {assign assignee $ticket->getAssigneeAsUser()}
												<div class="symbol symbol-35px me-2">
													<img alt="{$assignee->getUpn()}" src="/api/user/{$assignee->getUpn()}/image.jpg"/>
												</div>
												<div class="fs-4 fw-bold">{$assignee->getDisplayName()}</div>
                                            {else}
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
											<div class="d-flex align-items-center">
											<span class="btn btn-sm btn-icon btn-light-primary ms-2" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
												<i class="fas fa-mail-forward"></i>
											</span>
												<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 py-4 w-200px" data-kt-menu="true">
													<div class="menu-item px-3">
														<div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Select User</div>
													</div>
													<div id="assignableUsers" class="assignableUsers">
														<!-- Users will be filled via AJAX -->
													</div>
												</div>
											</div>
                                            {if $ticket->hasAssignee()}
												<span class="btnRemoveAssignee btn btn-sm btn-icon btn-light-danger ms-2" data-ticket="{$ticket->getGuid()}" title="Zuweisung aufheben">
													<i class="fas fa-user-minus"></i>
												</span>
                                            {/if}
										</div>
										<!--end::Number-->
										<div class="d-flex fw-semibold fs-6 text-gray-500">
											<div class="fw-semibold fs-6 text-gray-500">Current Assignee</div>
										</div>
									</div>
									<!--end::Upload-->

									<!--begin::Linked Users-->
									<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
										<!--begin::Number-->
										<div class="d-flex align-items-center">
											<div class="symbol-group symbol-hover">
												<!--begin::User-->
												<div class="btnAddTicketAssociate symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" title="Add Associate">
													<span class="symbol-label bg-warning text-inverse-warning fw-bold">+</span>
												</div>
												<!--end::User-->

                                                {foreach from=$ticket->getTicketAssociates() item=ta}

                                                    {assign ouser $ta->getOrganizationUser()}
													<!--begin::User-->
													<div class="symbol symbol-35px symbol-circle"
														 data-bs-toggle="dropdown"
														 title="{$ouser->getDisplayName()}">
														<img alt="Pic" src="{$ouser->getAvatarLink()}"/>
													</div>
													<div class="dropdown-menu">
														<a href="{$ouser->getAgentLink()}" class="dropdown-item">
															<i class="fas fa-user me-2"></i> Go to User
														</a>
														<span class="dropdown-item text-danger btnRemoveTicketAssociate" data-ticketassociate="{$ta->getGuid()}">
														<i class="fas fa-user-minus me-2"></i> Remove
													</span>
													</div>
													<!--end::User-->
                                                {/foreach}

											</div>
										</div>
										<!--end::Number-->
										<!--begin::Label-->
										<div class="fw-semibold fs-6 text-gray-500">Connected Users</div>
										<!--end::Label-->
									</div>
									<!--end::Linked Users-->


								</div>
								<!--end::Stats-->

							</div>
							<!--end::Info-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Details-->
					<div class="separator"></div>

				</div>
			</div>
			<!--end::Navbar-->

			<!--begin::Files-->
			<div class="card mb-5 mb-xxl-8">
				<!--begin::Card head-->
				<div class="card-header card-header-stretch cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filesContainer">
					<!--begin::Title-->
					<div class="card-title d-flex align-items-center">
						<i class="ki-outline ki-file fs-1 text-primary me-3 lh-0"></i>
						<div class="d-flex align-items-center">
							<h3 class="fw-bold m-0 text-gray-800">Files</h3>
                            {if $ticket->hasTicketFiles()}<span class="badge badge-primary badge-circle ms-2">{$ticket->countTicketFiles()}</span>{/if}
						</div>
					</div>
					<!--end::Title-->
				</div>
				<!--end::Card head-->
				<!--begin::Card body-->
				<div class="card-body collapse {if $ticket->hasTicketFiles()}show{/if} transition-all" id="filesContainer">
					<div class="dropzone mb-5" id="ticketUploadFile" style="min-height: unset; padding: unset; border: 1px dashed rgba(0,0,0,.25);">
						<div class="dz-message" data-dz-message style="margin: 1em;">
							<i class="ki-outline ki-file-up fs-3x"></i>
							<div class="ms-4">
								<h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload</h3>
								<span class="fs-7 fw-semibold text-gray-500">Maximum file size 10MB</span>
							</div>
						</div>
					</div>
					<div class="d-flex flex-wrap gap-5">
                        {foreach from=$ticket->getTicketFiles() item=ticketFile}
							<!--begin::File-->
							<div class="d-flex flex-aligns-center pe-10 pe-lg-20">
								<img alt="" class="w-30px me-3" src="/assets/media/svg/file-icon-vectors/{$ticketFile->getFile()->getExtension()}.svg"/>
								<div class="ms-1 fw-semibold">
									<a href="{$ticketFile->getFile()->getLink()}" class="fs-6 text-hover-primary fw-bold">
                                        {$ticketFile->getFile()->getName()}
									</a>
									<div class="text-gray-500">
                                        {$ticketFile->getFile()->getSizeWithUnit()}
                                        {if $ticketFile->isAccessLevelPublic()}
											<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
												  data-url="/api/agent/ticket/file/update.json"
												  data-uiaction="reload"
												  data-prompt=""
												  data-pk="{$ticketFile->getGuid()}"
												  data-name="AccessLevel"
												  data-value="Internal"
											>
												<i class="fas fa-globe-americas text-success ms-2" title="Öffentliche Datei"></i>
											</span>
                                        {else}
											<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
												  data-url="/api/agent/ticket/file/update.json"
												  data-uiaction="reload"
												  data-prompt=""
												  data-pk="{$ticketFile->getGuid()}"
												  data-name="AccessLevel"
												  data-value="Public"
											>
												<i class="fas fa-lock text-danger ms-2"></i>
											</span>
                                        {/if}
									</div>
								</div>
							</div>
							<!--end::File-->
                        {/foreach}
					</div>
				</div>
				<!--end::Card body-->
			</div>
		</div>
		<!--end::Files-->


		<!--begin::TicketActionItem-->
		<div class="card">
			<!--begin::Card head-->
			<div class="card-header card-header-stretch cursor-pointer" data-bs-toggle="collapse" data-bs-target="#tasksContainer">
				<!--begin::Title-->
				<div class="card-title d-flex align-items-center">
					<i class="ki-outline ki-pencil fs-1 text-primary me-3 lh-0"></i>
					<div class="d-flex align-items-center">
						<h3 class="fw-bold m-0 text-gray-800">Tasks</h3>
                        {if $ticket->hasTicketActionItems()}<span class="badge badge-primary badge-circle ms-2">{$ticket->countTicketActionItems()}</span>{/if}
					</div>
				</div>
				<!--end::Title-->

			</div>
			<!--end::Card head-->
			<!--begin::Card body-->
			<div class="card-body">
				<!--begin::Tab Content-->
				<div class="tab-content collapse {if $ticket->hasTicketActionItems()}show{/if} transition-all" id="tasksContainer">
					<!--begin::Tab panel-->
					<div id="kt_activity_today" class="card-body p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_today_tab">

                        {foreach from=$ticket->getTicketActionItems() item=tai}
							<!--begin::Timeline item-->
							<div class="timeline-item">

								<!--begin::Timeline content-->
								<div class="timeline-content mb-10 mt-n1">
									<!--begin::Timeline heading-->
									<div class="pe-3 mb-3">
										<!--begin::Title-->
										<div class="d-flex align-items-center">
                                            {if !$tai->isCompleted()}
												<span class="btnToggleTicketActionItem btn btn-sm btn-info me-3" data-ticketactionitem="{$tai->getGuid()}">
													<i class="ki-duotone ki-star fs-2"></i>
												</span>
                                            {else}
												<span class="btnToggleTicketActionItem btn btn-sm btn-success me-3" data-ticketactionitem="{$tai->getGuid()}">
													<i class="ki-duotone ki-check fs-2"></i>
												</span>
                                            {/if}
											<div>
												<div class="fs-5 fw-semibold mb-2">
                                                    {$tai->getTitle()}
												</div>
												<div class="fs-6 text-muted mb-2">
                                                    {$tai->getDescription()}
												</div>


                                                {if $tai->isCompleted()}
                                                    {assign completedByUser $tai->getCompletedByAsUser()}
                                                    {if $completedByUser}
														<div class="fs-5 fw-light fs-8 mb-2 ">
															Completed by <img src="{$completedByUser->getUserImageLink()}" style="height: 16px; width: 16px; border-radius: 50%;"
																			  alt="{$completedByUser->getName()}"/> {$completedByUser->getDisplayName()} at {$tai->getCompletedAtAsDateTime()->format('d.m.Y H:i')}
														</div>
                                                    {/if}
                                                {/if}

												<div class="fs-8 text-muted mb-2">
													Internal Comment: <span href="#" class="editable"
																			  data-type="text"
																			  data-pk="{$tai->getGuid()}"
																			  data-name="Comment"
																			  data-url="/api/agent/ticket/actionitem/update.json"
																			  data-value="{$tai->getComment()}"
													>{$tai->getComment()}</span>
												</div>

											</div>
										</div>
										<!--end::Title-->

									</div>
									<!--end::Timeline heading-->
								</div>
								<!--end::Timeline content-->
							</div>
							<!--end::Timeline item-->
                            {foreachelse}
							<!--begin::Empty state container-->
							<div class="border border-gray-300 border-dashed rounded p-3 p-md-5 my-3">
								<div class="row g-5 p-4">

									<!--begin::Action Groups section-->
									<div class="col-12 col-md-6">
										<h4 class="text-center mb-4">Action Groups</h4>
										<div class="d-flex flex-column align-items-center justify-content-center h-100">
											<select class="form-select form-select-sm mb-4" id="actionGroupSelect">
												<option disabled="disabled" value="">Select Action Group</option>
                                                {foreach from=ActionGroupController::getAll() item=group}
													<option value="{$group->getGuid()}">{$group->getName()}</option>
                                                {/foreach}
											</select>
											<span id="btnAddSelectedActionGroup" class="btn btn-sm btn-light-success"
												  data-ticket="{$ticket->getGuid()}">
										        <i class="ki-outline ki-plus fs-3 me-2"></i>Add Selected Group
											</span>
										</div>
									</div>
									<!--end::Action Groups section-->

									<!--begin::AI Actions section-->
									<div class="col-12 col-md-6 mb-5 mb-md-0">
										<h4 class="text-center mb-4">Automatically Determine Actions</h4>
										<div class="d-flex flex-column align-items-center justify-content-center h-100">
										    <span class="btnAddTicketActionsWithAi btn btn-sm btn-light-primary mb-4"
												  data-ticket="{$ticket->getGuid()}" title="Generate AI Actions">
										        <i class="ki-outline ki-abstract-26 fs-3 me-2"></i> Generate AI Action
										    </span>
											<div class="fw-semibold fs-6 text-gray-500">Use AI for Action Generation</div>
										</div>
									</div>
									<!--end::AI Actions section-->

								</div>
							</div>
							<!--end::Empty state container-->
                        {/foreach}

					</div>
					<!--end::Tab panel-->

					<!--begin::Tab panel-->
					<div id="kt_new_action_item" class="card-body p-0" role="tabpanel">
						<button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="collapse" data-bs-target="#addActionItemForm">
							<i class="ki-outline ki-plus fs-2"></i> Add Manual Task
						</button>
						<div id="addActionItemForm" class="collapse mt-3">
							<div class="card border">
								<div class="card-body">
									<form id="addActionItem" method="post">
										<input type="hidden" name="ticket" value="{$ticket->getGuid()}">

										<div class="mb-5">
											<label class="form-label required">Task Title</label>
											<input type="text" name="title" class="form-control" required>
										</div>

										<div class="mb-5">
											<label class="form-label">Description (optional)</label>
											<textarea name="description" class="form-control" rows="3"></textarea>
										</div>

										<div class="d-flex justify-content-start">
										<span type="submit" class="btn btn-primary btnSaveThisForm"
											  data-url="/api/agent/ticket/actionitem/add.json"
											  data-ticket="{$ticket->getGuid()}"
											  data-onsave="location.reload">
										    Add Task
										</span>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!--end::Tab panel-->

					</div>
					<!--end::Tab Content-->
				</div>
				<!--end::Card body-->
			</div>
			<!--end::TicketActionItem-->

		</div>


		<!--begin::Timeline TicketComments-->
		<div class="card">
			<!--begin::Card head-->
			<div class="card-header card-header-stretch">
				<!--begin::Title-->
				<div class="card-title d-flex align-items-center">
					<i class="ki-outline ki-calendar-8 fs-1 text-primary me-3 lh-0"></i>
					<h3 class="fw-bold m-0 text-gray-800">Ticket History</h3>
				</div>
				<!--end::Title-->

			</div>
			<!--end::Card head-->
			<!--begin::Card body-->
			<div class="card-body collapse show">
				<!--begin::Tab Content-->
				<div class="tab-content">
					<!--begin::Tab panel-->
					<div id="kt_activity_today" class="card-body p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_today_tab">
						<!--begin::Timeline-->
						<div class="timeline timeline-border-dashed">

                            {foreach from=$ticket->getTicketComments() item=ticketComment}
                                {assign fac $ticketComment->getFacility()}
                                {assign isEditable $ticketComment->getIsEditableAsBool()}

								<!--begin::Timeline item-->
								<div id="{$ticketComment->getGuid()}"
									 class="timeline-item"
									 data-ticketcomment="{$ticketComment->getGuid()}"
								>

									<!--begin::Timeline line-->
									<div class="timeline-line {if $fac == 'user'}border-primary border-3{/if}"></div>
									<!--end::Timeline line-->
									<!--begin::Timeline icon-->
									<div class="timeline-icon {if $fac == 'user'}border-3 border-primary{/if}">
                                        {if $fac == 'automatic'}
											<i class="ki-outline ki-compass fs-2 text-warning"></i>
                                        {elseif $fac == 'user'}
											<i class="ki-outline ki-profile-user fs-2 text-info"></i>
                                        {elseif $fac == 'system'}
											<i class="ki-outline ki-gear fs-2 text-gray-500"></i>
                                        {else}
											<i class="ki-outline ki-question-square fs-2 text-muted"></i>
                                        {/if}
									</div>
									<!--end::Timeline icon-->
									<!--begin::Timeline content-->
									<div class="timeline-content {if $fac == 'user'}mb-10{else}mb-3 ms-10{/if} mt-n1">
										<!--begin::Timeline heading-->
										<div class="pe-3 mb-2 ticketComment">
											<!--begin::Title-->
											<div class="ticketCommentContent mb-2">
                                                {if $ticketComment->hasMail()}
													<div class="mailContainer mb-4">
														<span class="btnLoadOriginalMail btn btn-light-info btn-sm" data-ticketcomment="{$ticketComment->getGuid()}">
														    <i class="fas fa-envelope"></i> load original mail
														</span>
													</div>
                                                {/if}



                                                {if $ticketComment->isTextTypeTxt()}
                                                    {if $fac == 'user'}
                                                        {assign tcstyle 'box-shadow: 2px 2px 5px rgba(0,0,0,.33); padding: 0.5em; font-size: 1.4em; font-family: "Segoe UI", sans-serif; border: 1px solid #e4e6ef; box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.05); padding: 1rem; border-radius: 0.475rem;'}
                                                    {else}
                                                        {assign tcstyle 'font-family: Consolas, monospace;'}
                                                    {/if}
                                                {else}
                                                    {assign tcstyle 'font-size: 1.3em;'}
                                                {/if}


												<div style="{$tcstyle}"
													 class="ticketCommentContentText">
                                                    {$ticketComment->getTextFormattedForTicket()}
												</div>

											</div>
											<!--end::Title-->
											<!--begin::Description-->
											<div class="overflow-auto pb-5">
												<!--begin::Wrapper-->
												<div class="d-flex align-items-center mt-1">

                                                    {if $ticketComment->getAccessLevel() == 'Internal'}
														<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
															  data-url="/api/agent/ticket/comment/update.json"
															  data-uiaction="reload"
															  data-prompt=""
															  data-pk="{$ticketComment->getGuid()}"
															  data-name="AccessLevel"
															  data-value="Public"
														>
															<i class="fas fa-lock fs-6 me-2 text-warning" title="Internal Comment"></i>
														</span>
                                                    {elseif $ticketComment->getAccessLevel() == 'Public'}
														<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
															  data-url="/api/agent/ticket/comment/update.json"
															  data-uiaction="reload"
															  data-prompt=""
															  data-pk="{$ticketComment->getGuid()}"
															  data-name="AccessLevel"
															  data-value="Internal"
														>
															<i class="fas fa-lock-open fs-6 me-2 text-success" title="Public Comment"></i>
														</span>
                                                    {else}
														<i class="fas fa-question-circle fs-6 me-2 text-info"></i>
                                                    {/if}

													<!--begin::Info-->
													<div class="text-muted me-2 fs-7">{$ticketComment->getCreatedDatetimeAsDateTime()->format("D d.m.Y H:i")} von</div>
													<!--end::Info-->
													<!--begin::User-->
                                                    {if $ticketComment->hasUser()}
														<a href="{$ticketComment->getUser()->getLink()}" class="text-primary fw-bold me-1">{$ticketComment->getUser()->getDisplayName()}</a>
                                                    {else}
														System
                                                    {/if}
                                                    {if $isEditable}
														<span class="btnEditTicketComment btn btn-icon btn-bg-light btn-active-color-primary btn-sm ms-2"
															  data-guid="{$ticketComment->getGuid()}"
															  title="Kommentar bearbeiten">
															<i class="fas fa-edit"></i>
														</span>
                                                    {/if}
													<!--end::User-->
												</div>
												<!--end::Wrapper-->
											</div>
											<!--end::Description-->
										</div>
										<!--end::Timeline heading-->
									</div>
									<!--end::Timeline content-->
								</div>
								<!--end::Timeline item-->

                                {if $ticketComment->hasMail()}
                                    {assign mail $ticketComment->getMail()}
                                    {if $ticketComment->getMail()->hasAttachments()}

                                        {assign attachments $mail->getAttachments()}
                                        {if count($attachments)>0}
											<!--begin::Timeline item-->
											<div class="timeline-item">
												<!--begin::Timeline line-->
												<div class="timeline-line"></div>
												<!--end::Timeline line-->
												<!--begin::Timeline icon-->
												<div class="timeline-icon">
													<i class="ki-outline ki-disconnect fs-2 text-gray-500"></i>
												</div>
												<!--end::Timeline icon-->
												<!--begin::Timeline content-->
												<div class="timeline-content mb-10 mt-n1">
													<!--begin::Timeline heading-->
													<div class="mb-5 pe-3">
														<!--begin::Title-->
														<a href="#" class="fs-3 fw-semibold text-gray-800 text-hover-primary mb-2">
                                                            {count($attachments)} Anhänge hinzugefügt
														</a>
														<!--end::Title-->
														<!--begin::Description-->
														<div class="d-flex align-items-center mt-1 fs-6">
															<!--begin::Info-->
															<div class="text-muted me-2 fs-7">
                                                                {$mail->getReceivedDatetimeAsDateTime()->format("d.m.Y H:i")}
															</div>
															<!--end::Info-->
															<!--begin::User-->
                                                            {*														<div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="Jan Hummer">*}
                                                            {*															<img src="/assets/media/avatars/300-23.jpg" alt="img"/>*}
                                                            {*														</div>*}
															<!--end::User-->
														</div>
														<!--end::Description-->
													</div>
													<!--end::Timeline heading-->
													<!--begin::Timeline details-->
													<div class="overflow-auto pb-5">
														<div class="d-flex flex-wrap gap-5 align-items-center border border-dashed border-gray-300 rounded p-5">
                                                            {foreach from=$attachments item=attachment}
																<!--begin::Item-->
																<div class="d-flex align-items-center">
																	<!--begin::Icon-->
																	<img alt="{$attachment->getFileExtension()}" class="w-30px me-3" src="/assets/media/svg/file-icon-vectors/{$attachment->getFileExtension()}.svg"/>
																	<!--end::Icon-->
																	<!--begin::Info-->
																	<div class="ms-1 me-2 fw-semibold">
																		<!--begin::Desc-->
																		<a target="_blank" href="{$attachment->getPublicDownloadLink()}"
																		   class="fs-6 text-hover-primary fw-bold">
                                                                            {$attachment->getName()}
																		</a>
																		<!--end::Desc-->
																		<!--begin::Number-->
																		<div class="text-gray-500">{$attachment->getSizeWithUnit()}</div>
																		<!--end::Number-->
																	</div>
																	<span class="actionPostButton btn btn-sm btn-icon btn-light-info ms-2"
																		  data-url="/api/agent/ticket/file/fromMailAttachment.json"
																		  data-uiaction="reload"
																		  data-prompt=""
																		  data-success="File successfully copied."
																		  data-mailattachment="{$attachment->getGuid()}"
																		  data-ticket="{$ticket->getGuid()}"
																		  title="Copy to ticket file">
																		<i class="fas fa-copy"></i>
																	</span>
                                                                    {if login::isAdmin() && !MailAttachmentIgnore::isMailAttachmentIgnored($attachment)}
																		<span class="actionPostButton btn btn-sm btn-icon btn-light-danger"
																			  data-url="/api/admin/mail/attachment/ignore.json"
																			  data-uiaction="reload"
																			  data-prompt="Do you want to ignore exactly identical files like this in the future?"
																			  data-success="File will be ignored in the future."
																			  data-guid="{$attachment->getGuid()}"
																			  data-extension="{$attachment->getFileExtension()}"
																			  title="Ignore this file in the future">
																			<i class="fas fa-ban"></i>
																		</span>
                                                                    {/if}
                                                                    {if $attachment->hasTextRepresentation()}
																		<span class="btnShowTextInPopup btn btn-sm btn-icon btn-sm btn-light-primary"
																			  data-url="{$attachment->getPublicTextLink()}"
																			  data-title="{$attachment->getName()}">
																			<i class="fas fa-text"></i>
																		</span>
                                                                    {/if}

																	<!--begin::Info-->
																</div>
																<!--end::Item-->
                                                            {/foreach}
														</div>
													</div>
													<!--end::Timeline details-->
												</div>
												<!--end::Timeline content-->
											</div>
											<!--end::Timeline item-->
                                        {/if}
                                    {/if}
                                {/if}

                            {/foreach}

						</div>
						<!--end::Timeline-->
					</div>
					<!--end::Tab panel-->

				</div>
				<!--end::Tab Content-->
			</div>
			<!--end::Card body-->
		</div>
		<!--end::Timeline-->


		<!--begin::Timeline new Comment-->
		<div class="card">
			<!--begin::Card head-->
			<div class="card-header card-header-stretch">
				<!--begin::Title-->
				<div class="card-title d-flex align-items-center">
					<i class="ki-outline ki-pencil fs-1 text-primary me-3 lh-0"></i>
					<h3 class="fw-bold m-0 text-gray-800">Add Comment</h3>

				</div>
				<!--end::Title-->

			</div>
			<!--end::Card head-->
			<!--begin::Card body-->
			<div class="card-body">
				<!--begin::Tab Content-->
				<div class="tab-content">
					<!--begin::Tab panel-->
					<div id="kt_activity_today" class="card-body p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="kt_activity_today_tab">

						<div class="py-3">

							<div class="d-flex flex-wrap gap-2 mb-5">
								<span class="btnInsertTemplateText btn btn-sm btn-light-success">
									<i class="fas fa-cogs fs-3 me-2"></i> Insert Text Template
								</span>

								<span class="btnInsertArticleLink btn btn-sm btn-light-primary">
									<i class="fas fa-link fs-3 me-2"></i> Link Article
								</span>
							</div>

							<div class="d-flex flex-wrap gap-2">
								<span class="btnGenerateTicketReplyWithAi btn btn-sm btn-light-info me-3" data-ticket="{$ticket->getGuid()}" title="generate general response">
									<i class="fas fa-cogs fs-3 me-2"></i> Generate General Response
								</span>

								<span class="btnGenerateSpecificTicketReplyWithAi btn btn-sm btn-light-info me-3" data-ticket="{$ticket->getGuid()}" title="generate specific response">
									<i class="fas fa-cogs fs-3 me-2"></i> Generate Specific Response
								</span>

								<span class="btnGenerateTicketRecapWithAi btn btn-sm btn-light-info me-3" data-ticket="{$ticket->getGuid()}" title="generate summary">
									<i class="fas fa-list fs-3 me-2"></i> Generate Summary
								</span>
							</div>
						</div>


						<form id="addComment" method="post">
							<input type="hidden" name="ticket" value="{$ticket->getGuid()}">


							<div class="mb-5">
								<textarea name="commentText" class="form-control tinymce" style="height: 400px;"></textarea>
							</div>

							<div class="d-flex justify-content-start">
								<span type="submit"
									  class="saveTicketComment btn btn-success me-2"
									  data-url="/api/agent/ticket/comment/add.json"
									  data-ticket="{$ticket->getGuid()}"
									  data-onsave="reloadToNewComment"
									  data-confirm=""
									  data-type="internal"
									  data-accesslevel="Internal"
								>
									<i class="fas fa-user-secret fa-lg"></i> Internal Comment
								</span>

                                {if $ticket->hasTicketAssociates()}
									<span type="submit"
										  class="saveTicketComment btn btn-primary"
										  data-url="/api/agent/ticket/comment/add.json"
										  data-ticket="{$ticket->getGuid()}"
										  data-confirm="Warning, this message will be sent externally. Really send?"
										  data-onsave="reloadToNewComment"
										  data-type="external"
										  data-accesslevel="Public"
									>
											<i class="fas fa-globe"></i> Send comment to connected persons
										</span>
                                {else}
									<span class="btn btn-dark"><i class="fas fa-user-times"></i> To send a message you must first connect a person.</span>
                                {/if}

							</div>
						</form>
					</div>
				</div>
				<!--end::Tab panel-->

			</div>
			<!--end::Tab Content-->
		</div>
		<!--end::Card body-->

		<!--begin::Closing Actions-->
		<div class="card">
			<!--begin::Card head-->
			<div class="card-header card-header-stretch">
				<!--begin::Title-->
				<div class="card-title d-flex align-items-center">
					<i class="ki-outline ki-check-square fs-1 text-primary me-3 lh-0"></i>
					<h3 class="fw-bold m-0 text-gray-800">Closing Actions</h3>
				</div>
				<!--end::Title-->
			</div>
			<!--end::Card head-->
			<!--begin::Card body-->
			<div class="card-body">
				<div class="d-flex flex-wrap gap-2">
					<div class="mb-6 btn btn-lg btn-light-{$status->getColor()} px-4 py-2 rounded me-3 cursor-pointer"
						 data-kt-menu-trigger="click"
						 data-kt-menu-placement="bottom-end">
						<div class="d-flex align-items-center">
							<i class="fa {$status->getIcon()} fs-4 me-2"></i>
							<span class="fw-bold fs-6">Status {$status->getPublicName()}</span>
						</div>
					</div>
					<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
						 data-kt-menu="true">
						<!--begin::Heading-->
						<div class="menu-item px-3">
							<div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Select New Status</div>
						</div>
						<!--end::Heading-->
                        {foreach from=$ticket->getAssignableStatus() item=newStatus}
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<span class="menu-link assignTicketStatus px-3 text-{$newStatus->getColor()} {if $newStatus->isOpen()}border-start border-success border-3{elseif $newStatus->isClosed()}border-start border-danger border-3{/if}"
									  data-ticket="{$ticket->getGuid()}"
									  data-status="{$newStatus->getGuid()}">
									<i class="fa {$newStatus->getIcon()}"></i> {$newStatus->getPublicName()}
                                    {if $newStatus->hasCustomerNotificationTemplate()}
										<span class="m-2" title="Customer Notification">
											C<i class="fa-solid fa-user-group"></i><i class="fas fa-bell me-2"></i>
										</span>
                                    {/if}
                                    {if $newStatus->hasAgentNotificationTemplate()}
										<span class="m-2" title="Agent Notification">
											A<i class="fa-solid fa-user-crown"></i><i class="fas fa-bell me-2"></i>
										</span>
                                    {/if}
								</span>
							</div>
							<!--end::Menu item-->
                        {/foreach}
					</div>

                    {if login::isAgent() && $status->isOpen()}
                        {if $ticket->hasTicketAssociates()}
							<div class="mb-6 btn btn-lg btn-light-success px-4 py-2 rounded me-3 cursor-pointer btnSolveTicket" data-ticket="{$ticket->getGuid()}">
								<div class="d-flex align-items-center">
									<i class="fa fa-user-check fs-4 me-2"></i>
									<span class="fw-bold fs-6">Resolve Ticket</span>
								</div>
							</div>
                        {else}
							<div class="mb-6 btn btn-lg btn-light px-4 py-2 rounded me-3" disabled>
								<div class="d-flex align-items-center">
									<i class="fa fa-user-times fs-4 me-2"></i>
									<span class="fw-bold fs-6">Ticket can only be resolved once a person is connected</span>
								</div>
							</div>
                        {/if}
						<div class="mb-6 btn btn-lg btn-light-danger px-4 py-2 rounded me-3 cursor-pointer btnCloseTicket" data-ticket="{$ticket->getGuid()}">
							<div class="d-flex align-items-center">
								<i class="fa fa-user-times fs-4 me-2"></i>
								<span class="fw-bold fs-6">Close Ticket</span>
							</div>
						</div>
                    {/if}
				</div>
			</div>
			<!--end::Card body-->
		</div>
		<!--end::Closing Actions-->


	</div>
	<!--end::Content container-->
</div>
<!--end::Content-->
