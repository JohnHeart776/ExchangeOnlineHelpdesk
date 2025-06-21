<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">AI Cache Records</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Latest 100 AI cache entries</span>
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
				<tr class="fs-7 text-muted">
					<th class="p-0 min-w-100px">ID</th>
					<th class="p-0 min-w-100px">Created</th>
					<th class="p-0 min-w-100px text-end">Actions</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=AicacheController::getAll(100, "DESC", "AiCacheId") item=cache}
					<tr data-bs-toggle="collapse" data-bs-target="#collapse{$cache->getAiCacheId()}" class="accordion-toggle">
						<td>{$cache->getAiCacheId()}</td>
						<td>{$cache->getCreatedAtAsDateTime()->format("Y-m-d H:i:s")}</td>
						<td class="text-end">
							<button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
								<i class="ki-outline ki-eye fs-2"></i>
							</button>
						</td>
					</tr>
					<tr>
						<td colspan="3" class="p-0">
							<div id="collapse{$cache->getAiCacheId()}" class="accordion-body collapse">
								<div class="card-body">
									<h6>Payload:</h6>
									<pre>{d($cache->getPayload())}</pre>
									<h6>Response:</h6>
									<pre>{d($cache->getResponse())}</pre>
								</div>
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
<!--end::Tables Widget 10-->