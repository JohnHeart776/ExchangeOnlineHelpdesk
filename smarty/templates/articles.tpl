<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Artikel</span>
			<span class="text-muted mt-1 fw-semibold fs-7">verfügbare Artikel</span>
		</h3>
		<div class="card-toolbar">
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
            {assign userIsAgent login::getUser()->isAgent()}
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<tbody>
                {foreach from=$articles item=article}
                    {if (!$article->getAccessLevelIsAgent() && !$userIsAgent)}
                        {continue}
                    {/if}
					<tr>
						<td>
							<a href="{$article->getLink()}">{$article->getTitle()}</a>
						</td>
						<td>Letzte Änderung vor {$article->getUpdatedAtAsDateEta()}</td>
					</tr>
					{foreachelse}
					<tr>
						<td colspan="2">Keine Artikel vorhanden</td>
					</tr>
                {/foreach}
				</tbody>
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table container-->
	</div>
	<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->