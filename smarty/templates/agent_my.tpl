{assign me login::getUser()}
<!--begin::Content container-->
<div id="kt_app_content_container" class="app-container container-fluid">
	<!--begin::Row-->
	<div class="row gx-5 gx-xl-10 mb-xl-10">

		<div class="col-lg-12 mb-2">

			<!--begin::Navbar-->
			<div class="card mb-6">
				<div class="card-body pt-9 pb-0">
					<!--begin::Details-->
					<div class="d-flex flex-wrap flex-sm-nowrap">
						<!--begin: Pic-->
						<div class="me-7 mb-4">
							<div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
								<img src="/api/me/image.jpg" alt="image"/>
								<div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
							</div>
						</div>
						<!--end::Pic-->
						<!--begin::Info-->
						<div class="flex-grow-1">
							<!--begin::Title-->
							<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
								<!--begin::User-->
								<div class="d-flex flex-column">
									<!--begin::Name-->
									<div class="d-flex align-items-center mb-2">
										<a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{$me->getDisplayName()}</a>
										<a href="#">
											<i class="ki-outline ki-verify fs-1 text-primary"></i>
										</a>
									</div>
									<!--end::Name-->
									<!--begin::Info-->
									<div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
										<a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
											<i class="ki-outline ki-profile-circle fs-4 me-1"></i> {$me->getUserRole()}</a>

                                        {*										<a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">*}
                                        {*											<i class="ki-outline ki-geolocation fs-4 me-1"></i>SF, Bay Area</a>*}

										<a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
											<i class="ki-outline ki-sms fs-4 me-1"></i> {$me->getMail()}</a>
									</div>
									<!--end::Info-->
								</div>
								<!--end::User-->

							</div>
							<!--end::Title-->
							<!--begin::Stats-->
							<div class="d-flex flex-wrap flex-stack">
								<!--begin::Wrapper-->
								<div class="d-flex flex-column flex-grow-1 pe-8">
									<!--begin::Stats-->
									<div class="d-flex flex-wrap">
{*										<!--begin::Stat-->*}
{*										<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">*}
{*											<!--begin::Number-->*}
{*											<div class="d-flex align-items-center">*}
{*												<i class="ki-solid ki-arrow-up fs-3 text-success me-2"></i>*}
{*												<div class="fs-2 fw-bold" >0</div>*}
{*											</div>*}
{*											<!--end::Number-->*}
{*											<!--begin::Label-->*}
{*											<div class="fw-semibold fs-6 text-gray-500">Tickets</div>*}
{*											<!--end::Label-->*}
{*										</div>*}
{*										<!--end::Stat-->*}

										<!--begin::Stat-->
										<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
											<!--begin::Number-->
											<div class="d-flex align-items-center">
												<i class="ki-solid ki-star fs-3 text-info me-2"></i>
												<div class="fs-2 fw-bold countTicketsOpen">0</div>
											</div>
											<!--end::Number-->
											<!--begin::Label-->
											<div class="fw-semibold fs-6 text-gray-500">Tickets offen</div>
											<!--end::Label-->
										</div>
										<!--end::Stat-->
									</div>
									<!--end::Stats-->
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Stats-->
						</div>
						<!--end::Info-->
					</div>
					<!--end::Details-->

				</div>
			</div>
			<!--end::Navbar-->

		</div>


		<!--begin::Tables Widget 10-->
		<div class="card mb-5 mb-xl-8">
			<!--begin::Header-->
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bold fs-3 mb-1">Meine offenen Tickets</span>
					<span class="text-muted mt-1 fw-semibold fs-7"><span class="countTicketsOpen">0</span> Tickets offen</span>
				</h3>
				<div class="card-toolbar">
				</div>
			</div>
			<!--end::Header-->
			<!--begin::Body-->
			<div class="card-body pt-3">
				<!--begin::Table-->
				<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
					<!--begin::Table body-->
					<tbody id="agentTicketListOpen">

					</tbody>
					<!--end::Table body-->
				</table>
				<!--end::Table-->
			</div>
			<!--begin::Body-->
		</div>
		<!--end::Tables Widget 10-->


		<!--begin::Col-->
		<div class="col-lg-12 mb-0">
			<!--begin::Timeline widget 3-->
			<div class="card h-md-100">
				<!--begin::Header-->
				<div class="card-header border-0 pt-5">
					<h3 class="card-title align-items-start flex-column">
						<span class="card-label fw-bold text-gray-900">Mein Kalender</span>
						<span class="text-muted mt-1 fw-semibold fs-7"></span>
					</h3>
					<!--begin::Toolbar-->
					<div class="card-toolbar">
                        {*						<a href="#" class="btn btn-sm btn-light">Report Cecnter</a>*}
					</div>
					<!--end::Toolbar-->
				</div>
				<!--end::Header-->
				<!--begin::Body-->
				<div class="card-body pt-7 px-0">
					<!--begin::Nav-->
					<ul class="nav nav-stretch nav-pills nav-pills-custom nav-pills-active-custom d-flex justify-content-between mb-8 px-5">
						{foreach from=$calendar->getDays() key=i item=day}
							<!--begin::Nav item-->
							<li class="nav-item p-0 ms-0 calendarDayItem" data-date="{$day->format('Y-m-d')}">
								<!--begin::Date-->
								<a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-45px py-4 px-3 btn-active-danger">
									<span class="fs-7 fw-semibold">{$day->format('D')}</span>
									<span class="fs-6 fw-bold">{$day->format('d')}</span>
								</a>
								<!--end::Date-->
							</li>
							<!--end::Nav item-->
                        {/foreach}
					</ul>
					<!--end::Nav-->
					<!--begin::Tab Content-->
					<div class="tab-content mb-2 px-9" id="agentTicketCalendarContent">
					</div>
					<!--end::Tab Content-->

				</div>
				<!--end: Card Body-->
			</div>
			<!--end::Timeline widget 3-->

		</div>
		<!--end::Col-->
	</div>
	<!--end::Row-->


</div>
<!--end::Content container-->