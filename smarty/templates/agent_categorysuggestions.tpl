<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Category Suggestions</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Configuration of automatic category suggestions</span>
		</h3>
		<div class="card-toolbar">
			<a href="#" class="btnAddNewCategorySuggestion btn btn-sm btn-light-primary">
				<i class="far fa-plus fs-2"></i>New Category Suggestion</a>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" data-kt-table="true">
				<!--begin::Table head-->
				<thead>
				<tr class="fw-bold fs-6 text-gray-800">
					<th>Priority</th>
					<th>Enabled</th>
					<th>Filter</th>
					<th>Category</th>
					<th>Auto-Close</th>
					<th class="text-end">Actions</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=$suggestions item=suggestion}
					<tr>
						<td><span href="#" class="editable"
								  data-type="number"
								  data-pk="{$suggestion->getGuid()}"
								  data-name="Priority"
								  data-url="/api/agent/categorysuggestion/update.json"
								  data-value="{$suggestion->getPriority()}"
							>{$suggestion->getPriority()}</span>
						</td>
						<td>
							<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 {if $suggestion->isEnabled()}btn-success{/if}"
								  data-url="/api/agent/categorysuggestion/update.json"
								  data-uiaction="reload"
								  data-prompt="really?"
								  data-success=""
								  data-pk="{$suggestion->getGuid()}"
								  data-action="toggle"
								  data-name="Enabled"
								  data-value="Enabled"
							>
								<i class="fas fa-check fs-2"></i>
							</span>
						</td>
						<td><span href="#" class="editable"
								  data-type="text"
								  data-pk="{$suggestion->getGuid()}"
								  data-name="Filter"
								  data-url="/api/agent/categorysuggestion/update.json"
								  data-value="{$suggestion->getFilter()}"
							>{$suggestion->getFilter()}</span>
							<div class="text-muted fs-7">{$suggestion->getFilter()|strlen} characters</div>
						</td>
						<td>
                            {if $suggestion->getCategoryIdAsInt()<1}<i class="fas fa-exclamation-triangle fa-beat me-3 text-danger"></i>{/if}
							<span href="#" class="editable"
								  data-type="select"
								  data-pk="{$suggestion->getGuid()}"
								  data-name="CategoryId"
								  data-url="/api/agent/categorysuggestion/update.json"
								  data-value="{$suggestion->getCategory()->getCategoryIdAsInt()}"
								  data-source="/api/agent/categories.json?format=editable"
							>{$suggestion->getCategory()->getPublicName()}</span>
						</td>
						<td>
							<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 {if $suggestion->shallAutoClose()}btn-warning{/if}"
								  data-url="/api/agent/categorysuggestion/update.json"
								  data-uiaction="reload"
								  data-prompt="really?"
								  data-success=""
								  data-pk="{$suggestion->getGuid()}"
								  data-action="toggle"
								  data-name="AutoClose"
								  data-value="AutoClose"
							>
								<i class="fas fa-door-closed"></i>
							</span>
						</td>
						<td class="text-end">

						<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
							  data-url="/api/agent/categorysuggestion/delete.json"
							  data-uiaction="reload"
							  data-prompt="really delete?"
							  data-success=""
							  data-pk="{$suggestion->getGuid()}"
						>
							<i class="far fa-trash fs-2"></i>
						</span>
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
