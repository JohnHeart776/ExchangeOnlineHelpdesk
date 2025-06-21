<!--begin::Content container-->
<div id="kt_app_content_container" class="app-container container-fluid">
	<!--begin::Row-->
	<div class="row gx-5 gx-xl-10 mb-xl-10">
		<!--begin::Col-->
		<div class="col-12">
			<!--begin::Card-->
			<div class="card card-xl-stretch mb-xl-10">
				<!--begin::Card header-->
				<div class="card-header">
					<h3 class="card-title align-items-start flex-column">
						<span class="card-label fw-bold text-dark">Tickets by Day</span>
						<span class="text-gray-400 mt-1 fw-semibold fs-6">
			                {$report->start->format("d.m.Y")} - {$report->end->format("d.m.Y")}
			            </span>
					</h3>
					<!--begin::Card toolbar-->
					<div class="card-toolbar">
					</div>
					<!--end::Card toolbar-->
				</div>
				<!--end::Card header-->

				<!--begin::Card body-->
				<div class="card-body">
					<div id="chart" style="width: 100%; height: 800px;"></div>
				</div>
				<!--end::Card body-->
			</div>
			<!--end::Card-->
		</div>
		<!--end::Col-->
	</div>
	<!--end::Row-->
</div>
<!--end::Content container-->