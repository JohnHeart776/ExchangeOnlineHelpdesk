<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Aktionsgruppen</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Verwalten Sie hier Ihre Aktionsgruppen</span>
		</h3>
		<div class="card-toolbar">
			<button class="btn btnAddActionGroup btn-sm btn-primary hover-elevate-up">
				<i class="ki-outline ki-plus fs-2 me-1"></i>
				<span>Neue Aktionsgruppe</span>
			</button>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
        {foreach from=ActionGroupController::getAll() item=group}
			<div class="card mb-5">
				<div class="card-header border-0 py-5 bg-light-primary bg-opacity-50 shadow-sm">
					<div class="card-title d-flex align-items-center">
						<h3 class="m-0"><a href="#" class="editable text-hover-primary text-gray-800 text-decoration-none"
										   data-type="text"
										   data-pk="{$group->getGuid()}"
										   data-name="Name"
										   data-url="/api/agent/actiongroup/update.json"
							>{$group->getName()}</a></h3>
					</div>
					<div class="card-toolbar">
						<span class="btn btnDistributeSortOrder btn-sm btn-light-info hover-elevate-up me-2" data-actiongroup="{$group->getGuid()}">
							<i class="fas fa-shuffle"></i>
							<span>Sortierung neu verteilen</span>
						</span>

						<span class="btn btnAddActionItem btn-sm btn-primary hover-elevate-up me-2" data-actiongroup="{$group->getGuid()}">
							<i class="ki-outline ki-plus fs-2 me-1"></i>
							<span>Neues Element am Ende anfügen</span>
						</span>
					</div>
				</div>
				<div class="card-body">
					<div class="action-items-container">
                        {foreach from=$group->getActionItems() item=item}
							<div class="action-item p-3 border-bottom">
								<div class="d-flex flex-column">
									<div class="d-flex align-items-center mb-2">
										<div class="d-flex align-items-center flex-grow-1">

											<span class="me-3">Sortierung: #</span>
											<span href="#" class="editable"
											   data-type="number"
											   data-pk="{$item->getGuid()}"
											   data-name="SortOrder"
											   data-url="/api/agent/actionitem/update.json"
											>{$item->getSortOrder()}</span>

											<span class="ms-4 me-2 fw-bold me-3">Titel:</span>
											<a href="#" class="editable"
											   data-type="text"
											   data-pk="{$item->getGuid()}"
											   data-name="Title"
											   data-url="/api/agent/actionitem/update.json"
											>{$item->getTitle()}</a>

										</div>
										<div class="action-item-controls">
                                            {*											<a href="#" class="btn btn-icon btn-light btn-sm me-1">*}
                                            {*												<i class="ki-outline ki-pencil fs-2"></i>*}
                                            {*											</a>*}
											<span class="btn btnDeleteActionItem btn-icon btn-light-danger btn-sm" data-actionitem="{$item->getGuid()}">
												<i class="ki-outline ki-trash fs-2"></i>
											</span>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<span class="fw-bold me-3">Beschreibung:</span>
										<span href="#" class="editable"
										   data-type="text"
										   data-pk="{$item->getGuid()}"
										   data-name="Description"
										   data-url="/api/agent/actionitem/update.json"
										>{$item->getDescription()}</span>
									</div>
								</div>
							</div>
                        {/foreach}
					</div>
				</div>
			</div>
        {/foreach}
		<!--end::Table-->
	</div>
	<!--end::Table container-->
</div>
<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->