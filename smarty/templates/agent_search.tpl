<!--begin::Search Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Body-->
	<div class="card-body py-3">
		<!--begin::Search-->
		<input type="text"
			   class="form-control form-control-solid mb-3 border-primary"
			   placeholder="Suchbegriff eingeben..."
			   id="searchInput">
		<!--end::Search-->

		<!--begin::Results-->
		<div id="searchResults" class="mt-10">
		</div>
		<!--end::Results-->
	</div>
	<!--end::Body-->
</div>
<!--end::Search Widget-->

<style>
    .searchResultItem {
        max-width: 45%;
        overflow: scroll;
        float: left;
    }

    .searchCategoryHeading {
        clear: both;
    }
</style>

