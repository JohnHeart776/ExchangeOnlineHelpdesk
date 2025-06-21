<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">{$article->getTitle()}</span>
		</h3>
		<div class="card-toolbar">
			<a href="/articles" class="btn btn-sm btn-light-primary">
				<i class="ki-outline ki-arrow-left fs-2"></i>Zurück zur Übersicht</a>
            {if login::getUser()->isAgent()}
				<a href="/agent/article/{$article->getGuid()}/" class="btn btn-sm btn-light-info ms-2">
					<i class="ki-outline ki-gear fs-2"></i>Artikel bearbeiten</a>
            {/if}
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
        {$article->getContent()}
	</div>
	<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->