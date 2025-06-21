<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Users</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Showing all Users</span>
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
					<th class="p-0"></th>
					<th class="p-0 min-w-100px">Function</th>
					<th class="p-0 min-w-100px text-end">Actions</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=UserController::getAll(0, "ASC", "DisplayName") item=user}
					<tr>
						<td>
							<div class="d-flex align-items-center">
								<!--begin::Avatar-->
								<div class="symbol symbol-45px me-5">
									<img alt="Pic" src="/api/user/{$user->getUpn()}/image.jpg"/>
								</div>
								<!--end::Avatar-->
								<!--begin::Name-->
								<div class="d-flex justify-content-start flex-column">
									<a href="{$user->getAdminLink()}" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">{$user->getDisplayName()}</a>
									<span href="#" class="text-muted text-hover-primary fw-semibold text-muted d-block fs-7">
									<span class="text-gray-900">Upn</span>: {$user->getUpn()}
								</span>
								</div>
								<!--end::Name-->
							</div>
						</td>

						<td class="text-start">
                            {$user->getUserRole()}
						</td>

						<td class="text-end">

							<a href="/admin/user/{$user->getGuid()}/" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
								<i class="ki-outline ki-pencil fs-2"></i>
							</a>

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