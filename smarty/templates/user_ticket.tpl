{assign category $ticket->getCategory()}
{assign status $ticket->getStatus()}
{assign organizationUserFromMessenger $ticket->getOrganizationUserFromMessenger()}
{assign category $ticket->getCategory()}

<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar mb-0 top-0" style="z-index: 100; margin-top: 0; background-color: white;">
	<!--begin::Toolbar container-->
	<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
		<!--begin::Toolbar wrapper-->
		<div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
			<!--begin::Page title-->
			<div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
				<!--begin::Title-->
				<h1 class="page-heading d-flex flex-column justify-content-center m-0">
					<span class="badge badge-lg bg-info text-white fw-bold fs-3">#{$ticket->getTicketNumber()}</span>
				</h1>

				<!--end::Title-->
				<!--begin::Breadcrumb-->
				<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
					<!--begin::Item-->
					<li class="breadcrumb-item text-muted">
						<a href="/dashboard" class="text-muted text-hover-primary">Übersicht</a>
					</li>
					<!--end::Item-->
					<!--begin::Item-->
					<li class="breadcrumb-item">
						<span class="bullet bg-gray-500 w-5px h-2px"></span>
					</li>
					<!--end::Item-->
					<!--begin::Item-->
					<li class="breadcrumb-item text-muted">Ticket {$ticket->getTicketNumber()}</li>
					<!--end::Item-->
				</ul>
				<!--end::Breadcrumb-->
			</div>
			<!--end::Page title-->

		</div>
		<!--end::Toolbar wrapper-->
	</div>
	<!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
	<!--begin::Content container-->
	<div id="kt_app_content_container" class="app-container container-fluid">
		<!--begin::Navbar-->
		<div class="card mb-5 mb-xxl-8">
			<div class="card-body pt-9 pb-0">
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
										<i class="ki-outline ki-calendar me-1"></i> {$ticket->getCreatedDatetimeAsDateTime()->format('d.m.Y H:i')}
									</div>
								</div>
								<!--end::CreatedDatetime-->

								<!--begin::Subject-->
								<div class="d-flex align-items-center mb-1">
									<i class="ki-outline ki-text fs-4 me-1"></i>
									<span class="text-gray-900 fs-2 fw-bold me-3">{$ticket->getValueForEditable("Subject")}</span>
								</div>
								<!--end::Subject-->

								<!--begin::Category-->
								<div class="d-flex flex-column">
									<div class="d-flex align-items-center me-5 mb-2">
										<i class="ki-outline ki-category fs-4 me-1"></i>
										<span class="text-gray-800">{$category->getName()}</span>
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


								</div>

								<!--end::Description-->
							</div>
							<!--end::Details-->

						</div>
						<!--end::Head-->


						<!--begin::Info-->
						<div class="d-flex flex-wrap justify-content-start">
							<!--begin::Stats-->
							<div class="d-flex flex-wrap">


								<!--begin::Stat-->
								<div class="border text-{$status->getColor()} rounded min-w-125px py-3 px-4 me-6 mb-3">
									<!--begin::Number-->
									<i class="fa {$status->getIcon()} fs-4 me-2"></i>
									<span class="fw-bold fs-6">{$status->getPublicName()}</span>
									<!--end::Number-->
									<!--begin::Label-->
									<div class="fw-semibold fs-6 text-gray-500">aktueller Status</div>
									<!--end::Label-->
								</div>
								<!--end::Stat-->

								<!--begin::Stat-->
								<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
									<!--begin::Number-->
									<div class="d-flex align-items-center">
										<div class="">
											<span>{$ticket->getDueDatetimeAsDateTime()->format('d.m.Y H:i')}</span>
										</div>
									</div>
									<!--end::Number-->
									<!--begin::Label-->
									<div class="fw-semibold fs-6 text-gray-500">geplante Erledigung</div>
									<!--end::Label-->
								</div>
								<!--end::Stat-->


								<!--begin::Linked Users-->
								<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
									<!--begin::Number-->
									<div class="d-flex align-items-center">
										<div class="symbol-group symbol-hover">

                                            {foreach from=$ticket->getTicketAssociates() item=ta}

                                                {assign ouser $ta->getOrganizationUser()}
												<!--begin::User-->
												<div class="symbol symbol-35px symbol-circle"
													 data-bs-toggle="tooltip"
													 title="{$ouser->getDisplayName()}">
													<img alt="Pic" src="{$ouser->getAvatarLink()}"/>
												</div>
												<!--end::User-->
                                            {/foreach}

										</div>
									</div>
									<!--end::Number-->
									<!--begin::Label-->
									<div class="fw-semibold fs-6 text-gray-500">Verbundene Benutzer</div>
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
        {if !empty($ticketFiles)}
			<div class="card mb-5 mb-xxl-8">
				<!--begin::Card head-->
				<div class="card-header card-header-stretch">
					<!--begin::Title-->
					<div class="card-title d-flex align-items-center">
						<i class="ki-outline ki-file fs-1 text-primary me-3 lh-0"></i>
						<h3 class="fw-bold m-0 text-gray-800">Dateien</h3>
					</div>
					<!--end::Title-->
				</div>
				<!--end::Card head-->
				<!--begin::Card body-->
				<div class="card-body">
					<div class="d-flex flex-wrap gap-5">
                        {foreach from=$ticketFiles item=ticketFile}
							<!--begin::File-->
							<div class="d-flex flex-aligns-center pe-10 pe-lg-20">
								<img alt="" class="w-30px me-3" src="/assets/media/svg/file-icon-vectors/{$ticketFile->getFile()->getExtension()}.svg"/>
								<div class="ms-1 fw-semibold">
									<a href="{$ticketFile->getFile()->getLink()}" class="fs-6 text-hover-primary fw-bold">
                                        {$ticketFile->getFile()->getName()}
									</a>
									<div class="text-gray-500">
                                        {$ticketFile->getFile()->getSizeWithUnit()}
									</div>
								</div>
							</div>
							<!--end::File-->
                        {/foreach}
					</div>
				</div>
				<!--end::Card body-->
			</div>
        {/if}
		<!--end::Files-->


		<!--begin::Timeline TicketComments-->
		<div class="card">
			<!--begin::Card head-->
			<div class="card-header card-header-stretch">
				<!--begin::Title-->
				<div class="card-title d-flex align-items-center">
					<i class="ki-outline ki-calendar-8 fs-1 text-primary me-3 lh-0"></i>
					<h3 class="fw-bold m-0 text-gray-800">Verlauf</h3>
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
						<!--begin::Timeline-->
						<div class="timeline timeline-border-dashed">

                            {foreach from=$ticket->getTicketComments() item=ticketComment}
                                {if $ticketComment->getAccessLevel() != 'Public'}
                                    {continue}
                                {/if}

                                {assign fac $ticketComment->getFacility()}

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
										<div class="pe-3 mb-5">
											<!--begin::Title-->
											<div class="ticketCommentContent mb-2">

												<div style="font-size: .85em;">
                                                    {if $ticketComment->isTextTypeTxt()}
                                                        {if $fac == 'user'}
															<p style="font-size: 1.6em; font-family: 'Segoe UI', sans-serif; border: 1px solid #e4e6ef; box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.05); padding: 1rem; border-radius: 0.475rem;">{$ticketComment->getText()}</p>
                                                        {else}
															<p style="font-family: Consolas, Monaco, monospace">{$ticketComment->getText()}</p>
                                                        {/if}
                                                    {elseif $ticketComment->isTextTypeHtml()}
                                                        {if $fac == 'user'}
															<div style="font-size: 1.7em; font-family: 'Segoe UI', sans-serif; border: 1px solid #e4e6ef; box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.05); padding: 1rem; border-radius: 0.475rem;">{$ticketComment->getText()}</div>
                                                        {else}
                                                            {$ticketComment->getText()}
                                                        {/if}
                                                    {/if}
												</div>

											</div>
											<!--end::Title-->
											<!--begin::Description-->
											<div class="overflow-auto pb-5">
												<!--begin::Wrapper-->
												<div class="d-flex align-items-center mt-1">

													<!--begin::Info-->
													<div class="text-muted me-2 fs-7">{$ticketComment->getCreatedDatetimeAsDateTime()->format("D d.m.Y H:i")} von</div>
													<!--end::Info-->
													<!--begin::User-->
                                                    {if $ticketComment->hasUser()}
														<div class="symbol symbol-20px me-3">
															<img src="/api/user/{$ticketComment->getUser()->getUpn()}/image.jpg" alt="{$ticketComment->getUser()->getDisplayName()}"/>
														</div>
														<span class="text-primary fw-bold me-1">{$ticketComment->getUser()->getDisplayName()}</a>
                                                    {else}
														System
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
													<a href="#" class="fs-3 fw-semibold text-gray-800 text-hover-primary mb-2">{count($attachments)} Anhänge hinzugefügt</a>
													<!--end::Title-->
													<!--begin::Description-->
													<div class="d-flex align-items-center mt-1 fs-6">
														<!--begin::Info-->
														<div class="text-muted me-2 fs-7">{$mail->getReceivedDatetimeAsDateTime()->format("d.m.Y H:i")}</div>
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
													<div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                                        {foreach from=$attachments item=attachment}
                                                            {*															{s($attachment)}*}
															<!--begin::Item-->
															<div class="d-flex flex-aligns-center pe-10 pe-lg-20">
																<!--begin::Icon-->
                                                                {*															<img alt="" class="w-30px me-3" src="/assets/media/svg/files/{$attachment->getFileExtension()}.svg"/>*}
																<img alt="" class="w-30px me-3" src="/assets/media/svg/file-icon-vectors/{$attachment->getFileExtension()}.svg"/>
																<!--end::Icon-->
																<!--begin::Info-->
																<div class="ms-1 fw-semibold">
																	<!--begin::Desc-->
																	<a href="{$attachment->getPublicDownloadLink()}"
																	   class="fs-6 text-hover-primary fw-bold">
                                                                        {$attachment->getFileNameWithoutExtension()}</a>
																	<!--end::Desc-->
																	<!--begin::Number-->
																	<div class="text-gray-500">{$attachment->getSizeWithUnit()}</div>
																	<!--end::Number-->
																</div>
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

		<!--begin::DEBUG-->

		<!--end::DEBUG-->

	</div>
	<!--end::Content container-->
</div>
<!--end::Content-->