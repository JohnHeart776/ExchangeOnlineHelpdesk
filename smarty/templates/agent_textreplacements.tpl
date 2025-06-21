<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Text Replacements</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Configuration for automatic text replacements</span>
		</h3>
		<div class="card-toolbar">
			<a href="#" class="btnAddNewTextReplacement btn btn-sm btn-light-primary">
				<i class="ki-outline ki-plus fs-2"></i>New Text Replacement</a>
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
					<th>Enabled</th>
					<th>Search for</th>
					<th>Replace with</th>
					<th class="text-end">Actions</th>
				</tr>
				</thead>
				<!--end::Table head-->
				<!--begin::Table body-->
				<tbody>

                {foreach from=TextReplaceController::getAll(0, "ASC", "SearchFor") item=replacement}
					<tr>
						<td>
							<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 {if $replacement->isEnabled()}btn-success{/if}"
								  data-url="/api/agent/textreplace/update.json"
								  data-uiaction="reload"
								  data-prompt="Do you really want to make this change?"
								  data-success=""
								  data-pk="{$replacement->getGuid()}"
								  data-action="toggle"
								  data-name="Enabled"
								  data-value="Enabled"
							>
								<i class="ki-outline ki-check fs-2"></i>
							</span>
						</td>
						<td>
							<span href="#" class="editable"
								  data-type="text"
								  data-pk="{$replacement->getGuid()}"
								  data-name="SearchFor"
								  data-url="/api/agent/textreplace/update.json"
								  data-value="{$replacement->getSearchFor()}"
							>{$replacement->getSearchFor()}</span>
						</td>
						<td><span href="#" class="editable"
								  data-type="text"
								  data-pk="{$replacement->getGuid()}"
								  data-name="ReplaceBy"
								  data-url="/api/agent/textreplace/update.json"
								  data-value="{$replacement->getReplaceBy()}"
							>{$replacement->getReplaceBy()}</span>
						</td>
						<td class="text-end">
                						<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
											  data-url="/api/agent/textreplace/delete.json"
											  data-uiaction="reload"
											  data-prompt="Do you really want to delete this entry?"
											  data-success=""
											  data-pk="{$replacement->getGuid()}"
										>
							<i class="ki-outline ki-trash fs-2"></i>
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
