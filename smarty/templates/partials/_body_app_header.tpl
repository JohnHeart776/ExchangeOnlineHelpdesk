<div id="kt_app_header" class="app-header">
	<!--begin::Header primary-->
	<div class="app-header-primary">
		<!--begin::Header primary container-->
		<div class="app-container container-fluid d-flex align-items-stretch justify-content-between m-2"
			 id="kt_app_header_primary_container">
			<!--begin::Header primary wrapper-->
			<div class="d-flex flex-stack flex-grow-1">
				<div class="d-flex">
					<!--begin::Logo-->
					<div class="app-header-logo d-flex flex-center gap-2 me-lg-15">
						<!--begin::Sidebar toggle-->
						<button class="btn btn-icon btn-sm btn-custom d-flex d-lg-none ms-n2" id="kt_header_secondary_mobile_toggle">
							<i class="ki-outline ki-abstract-14 fs-2"></i>
						</button>
						<!--end::Sidebar toggle-->
						<!--begin::Logo image-->
						<a href="/">
							<img alt="Logo" src="/logo/menu.svg" class="min-h-35px"/>
						</a>
						<!--end::Logo image-->
					</div>
					<!--end::Logo-->
					<!--begin::Menu wrapper-->
					<div class="d-flex align-items-stretch" id="kt_app_header_menu_wrapper">
						<!--begin::Menu holder-->
						<div class="app-header-menu app-header-mobile-drawer align-items-stretch"
							 data-kt-drawer="true"
							 data-kt-drawer-name="app-header-menu"
							 data-kt-drawer-activate="{ default: true, lg: false }"
							 data-kt-drawer-overlay="true"
							 data-kt-drawer-width="{ default:'200px', '300px': '250px' }"
							 data-kt-drawer-direction="start"
							 data-kt-drawer-toggle="#kt_app_header_menu_toggle"
							 data-kt-swapper="true"
							 data-kt-swapper-mode="{ default: 'append', lg: 'prepend' }"
							 data-kt-swapper-parent="{ default: '#kt_app_body', lg: '#kt_app_header_menu_wrapper' }"
						>
							<!--begin::Menu-->
							<div class="menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-700 menu-state-gray-900 menu-icon-gray-500 menu-arrow-gray-500 menu-state-icon-primary menu-state-bullet-primary fw-semibold fs-6 align-items-stretch my-5 my-lg-0 px-2 px-lg-0"
								 id="#kt_app_header_menu" data-kt-menu="true">
								<!-- this app menu is left intentionally blank -->
							</div>
							<!--end::Menu-->
						</div>
						<!--end::Menu holder-->
					</div>
					<!--end::Menu wrapper-->
				</div>
				<!--begin::Navbar-->
				<div class="app-navbar flex-shrink-0 gap-2">
                    {if login::getUser()->isAgent()}
                        {include file="partials/_body_app_header_searchmenu.tpl"}
                        {include file="partials/_body_app_header_myapps.tpl"}
                    {/if}
                    {include file="partials/_body_app_header_usermenu.tpl"}

				</div>
				<!--end::Navbar-->
			</div>
			<!--end::Header primary wrapper-->
		</div>
		<!--end::Header primary container-->
	</div>
	<!--end::Header primary-->
	<!--begin::Header secondary-->
	<div class="app-header-secondary app-header-mobile-drawer"
		 data-kt-drawer="true"
		 data-kt-drawer-name="app-header-secondary"
		 data-kt-drawer-activate="{ default: true, lg: false }"
		 data-kt-drawer-overlay="true"
		 data-kt-drawer-width="250px"
		 data-kt-drawer-direction="start"
		 data-kt-drawer-toggle="#kt_header_secondary_mobile_toggle"
		 data-kt-swapper="true"
		 data-kt-swapper-mode="{ default: 'append', lg: 'append' }"
		 data-kt-swapper-parent="{ default: '#kt_app_body', lg: '#kt_app_header' }">
		<!--begin::Header secondary wrapper-->
		<div class="d-flex flex-column flex-grow-1 overflow-hidden">
			<div class="app-header-secondary-menu-main d-flex flex-grow-lg-1 align-items-end pt-3 pt-lg-2 px-3 px-lg-0 w-auto overflow-auto flex-nowrap">
				<div class="app-container container-fluid">
                    {include file="partials/_body_app_header_menu_main.tpl"}
				</div>
			</div>
			<div class="app-header-secondary-menu-sub d-flex align-items-stretch flex-grow-1">
				<div class="app-container d-flex flex-column flex-lg-row align-items-stretch justify-content-lg-between container-fluid">
					<!--begin::Main menu-->
					<div class="menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-700 menu-state-gray-900 menu-icon-gray-500 menu-arrow-gray-500 menu-state-icon-primary menu-state-bullet-primary fw-semibold fs-base align-items-stretch my-2 my-lg-0 px-2 px-lg-0"
						 id="#kt_app_header_tertiary_menu"
						 data-kt-menu="true">
						<!-- this menu if left intentionally blank -->
					</div>
					<!--end::Menu-->

                    {include file="partials/_body_app_header_mobilemenu.tpl"}

				</div>
			</div>
		</div>
		<!--end::Header secondary wrapper-->
	</div>
	<!--end::Header secondary-->
</div>