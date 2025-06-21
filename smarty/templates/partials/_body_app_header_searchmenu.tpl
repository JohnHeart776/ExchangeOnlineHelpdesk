<!--begin::Search Button-->
<div id="kt_header_search"
	 class="header-search d-flex align-items-stretch"
	 data-kt-search-keypress="true"
	 data-kt-search-min-length="2"
	 data-kt-search-enter="enter"
	 data-kt-search-layout="menu"
	 data-kt-menu-trigger="auto"
	 data-kt-menu-overflow="false"
	 data-kt-menu-permanent="true"
	 data-kt-menu-placement="bottom-end"
>
	<!--begin::Search toggle-->
	<div class="d-flex align-items-center" data-kt-search-element="toggle" id="kt_header_search_toggle">
		<div class="btn btn-sm btn-icon btn-custom h-35px w-35px">
			<i class="ki-outline ki-magnifier fs-3"></i>
		</div>
	</div>
	<!--end::Search toggle-->

	<!--begin::Menu-->
	<div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px">
		<!--begin::Wrapper-->
		<div data-kt-search-element="wrapper">
			<!--begin::Form-->
			<form data-kt-search-element="form" class="w-100 position-relative mb-3" autocomplete="off">
				<!--begin::Icon-->
				<i class="ki-outline ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-0"></i>
				<!--end::Icon-->
				<!--begin::Input-->
				<input type="text"
					   class="search-input form-control form-control-flush ps-10"
					   name="search"
					   value=""
					   placeholder="Suche..."
					   data-kt-search-element="input"
				/>
				<!--end::Input-->
				<!--begin::Spinner-->
				<span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-1"
					  data-kt-search-element="spinner">
					<span class="spinner-border h-15px w-15px align-middle text-gray-500"></span>
				</span>
				<!--end::Spinner-->
				<!--begin::Reset-->
				<span class="search-reset btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none"
					  data-kt-search-element="clear">
					<i class="ki-outline ki-cross fs-2 fs-lg-1 me-0"></i>
				</span>
				<!--end::Reset-->

			</form>
			<!--end::Form-->
			<!--begin::Separator-->
			<div class="separator border-gray-200 mb-6"></div>
			<!--end::Separator-->


			<!--begin::Recently viewed-->
			<div class="mb-5" data-kt-search-element="main">
				<!--begin::Heading-->
				<div class="d-flex flex-stack fw-semibold mb-4">
					<!--begin::Label-->
					<span class="text-muted fs-6 me-2">Suchhilfe:</span>
					<!--end::Label-->
				</div>
				<!--end::Heading-->
				<!--begin::Items-->
				<div class="scroll-y mh-200px mh-lg-325px">

					<!--begin::Item-->
					<div class="d-flex align-items-center mb-5">
						<!--begin::Symbol-->
						<div class="symbol symbol-40px me-4">
							<span class="symbol-label bg-light">
								<i class="ki-outline ki-information fs-2 text-primary"></i>
							</span>
						</div>
						<!--end::Symbol-->
						<!--begin::Title-->
						<div class="d-flex flex-column">
							<a href="/agent/search" class="fs-6 text-gray-800 text-hover-primary fw-semibold">Zur vollen Suche wechseln</a>
							<span class="fs-7 text-muted fw-semibold">Stern (*) für Platzhalter</span>
						</div>
						<!--end::Title-->
					</div>
					<!--end::Item-->

				</div>
				<!--end::Items-->
			</div>
			<!--end::Recently viewed-->


			<!--begin::Recently viewed-->
			<div data-kt-search-element="results" class="d-none scroll-y mh-400px mh-lg-550px">
				<!--begin::Items-->
				<!--end::Items-->
			</div>
			<!--end::Recently viewed-->

			<!--begin::Empty-->
			<div data-kt-search-element="empty" class="text-center d-none">
				<!--begin::Icon-->
				<div class="pt-10 pb-10">
					<i class="ki-outline ki-search-list fs-4x opacity-50"></i>
				</div>
				<!--end::Icon-->
				<!--begin::Message-->
				<div class="pb-15 fw-semibold">
					<h3 class="text-gray-600 fs-5 mb-2">Keine Ergebnisse <i class="fas fa-frown"></i></h3>
					<div class="text-muted fs-7">Versuche es nochmals mit anderen Daten</div>
				</div>
				<!--end::Message-->
			</div>
			<!--end::Empty-->
		</div>
		<!--end::Wrapper-->

	</div>
	<!--end::Menu-->
</div>
<!--end::Search-->
<!--end::Search Button-->