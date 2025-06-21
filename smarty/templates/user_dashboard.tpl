{assign user login::getUser()}

<!--begin::Content container-->
<div id="kt_app_content_container" class="app-container container-fluid">
	<!--begin::Row-->
	<div class="row gx-5 gx-xl-10 mb-xl-10">

		<div class="col-lg-12 mb-2">
			<!--begin::User Info Card-->
			<div class="card mb-6">
				<div class="card-body pt-9 pb-0">
					<div class="d-flex flex-wrap flex-sm-nowrap">
						<!--begin: Pic-->
						<div class="me-7 mb-4">
							<div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
								<img src="/api/me/image.jpg" alt="image"/>
							</div>
						</div>
						<!--end::Pic-->
						<!--begin::Info-->
						<div class="flex-grow-1">
							<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
								<div class="d-flex flex-column">
									<div class="d-flex align-items-center mb-2">
										<span class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{$user->getDisplayName()}</span>
									</div>
									<div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
										<span class="d-flex align-items-center text-gray-500 me-5 mb-2">
											<i class="ki-outline ki-profile-circle fs-4 me-1"></i>
											{$user->getUserRole()}
										</span>
										<span class="d-flex align-items-center text-gray-500 mb-2">
											<i class="ki-outline ki-sms fs-4 me-1"></i> {$user->getMail()}
										</span>
									</div>
									<div>
										<!-- Optional weitere Userinfos -->
									</div>
								</div>
							</div>
							<div class="d-flex flex-wrap flex-stack">
								<div class="d-flex flex-column flex-grow-1 pe-8">
									<div class="d-flex flex-wrap">
										<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
											<div class="d-flex align-items-center">
												<i class="ki-solid ki-arrow-up fs-3 text-success me-2"></i>
												<div class="fs-2 fw-bold"
													 data-kt-countup="true"
													 data-kt-countup-value="{count($tickets)}"
												>0</div>
											</div>
											<div class="fw-semibold fs-6 text-gray-500">Meine Tickets</div>
										</div>
										<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
											<div class="d-flex align-items-center">
												<i class="ki-solid ki-star fs-3 text-info me-2"></i>
												<div class="fs-2 fw-bold"
													 data-kt-countup="true"
													 data-kt-countup-value="{count($openTickets)}"
												>0</div>
											</div>
											<div class="fw-semibold fs-6 text-gray-500">Offen</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--end::Info-->
					</div>
				</div>
			</div>
			<!--end::User Info Card-->
		</div>

		<!--begin::Tickets Table Widget-->
		<div class="card mb-5 mb-xl-8">
			<!--begin::Header-->
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bold fs-3 mb-1">Meine Tickets</span>
					<span class="text-muted mt-1 fw-semibold fs-7">{count($tickets)} Tickets insgesamt</span>
				</h3>
			</div>
			<!--end::Header-->
			<!--begin::Body-->
			<div class="card-body pt-3">
				<div class="table-responsive">
					<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
						<thead>
						<tr class="border-0">
							<th>Ticketnummer</th>
							<th>Betreff</th>
							<th>Erstellt am</th>
							<th>Status</th>
							<th class="text-end">Aktionen</th>
						</tr>
						</thead>
						<tbody>
                        {foreach from=$tickets item=ticket}
							<tr>
								<td>
									<a href="{$ticket->getLink()}" class="text-gray-800 fw-bold mb-1 fs-6">
										#{$ticket->getTicketNumber()}
									</a>
								</td>
								<td>
									<span class="text-gray-900 fw-bold text-hover-primary fs-6">{$ticket->getSubject()}</span>
								</td>
								<td>
									<span class="text-muted fw-semibold fs-7">{$ticket->getCreatedDatetimeAsDateTime()->format("d.m.Y H:i")}</span>
								</td>
								<td>{$ticket->getStatus()->getBadge()}</td>
								<td class="text-end">
									<a href="{$ticket->getLink()}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
										<i class="ki-outline ki-arrow-right fs-2"></i>
									</a>
								</td>
							</tr>
                            {foreachelse}
							<tr>
								<td colspan="5" class="text-center text-muted">Keine Tickets gefunden.</td>
							</tr>
                        {/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<!--end::Body-->
		</div>
		<!--end::Tickets Table Widget-->

	</div>
	<!--end::Row-->
</div>
<!--end::Content container-->