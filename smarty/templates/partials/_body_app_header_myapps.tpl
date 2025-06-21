<!--begin::My apps-->
{assign usermenu MenuController::searchOneBy("Name", "UserMenu")}
<div class="app-navbar-item ms-1">
	<!--begin::Menu- wrapper-->
	<div class="btn btn-sm btn-icon btn-custom h-35px w-35px"
		 data-kt-menu-trigger="{ default: 'click', lg: 'hover' }"
		 data-kt-menu-attach="parent"
		 data-kt-menu-placement="bottom-end">
		<i class="fas fa-compass fs-3"></i>
	</div>
	<!--begin::My apps-->
	<div class="menu menu-sub menu-sub-dropdown menu-column w-100 w-sm-350px" data-kt-menu="true">
		<!--begin::Card-->
		<div class="card">
			<!--begin::Card header-->
			<div class="card-header">
				<!--begin::Card title-->
				<div class="card-title">Benutzer-Apps</div>
				<!--end::Card title-->
				<!--begin::Card toolbar-->
				<div class="card-toolbar">

				</div>
				<!--end::Card toolbar-->
			</div>
			<!--end::Card header-->
			<!--begin::Card body-->
			<div class="card-body py-5">
				<!--begin::Scroll-->
				<div class="mh-450px scroll-y me-n5 pe-5">
					<!--begin::Row-->
					<div class="row g-2">
                        {foreach from=$usermenu->getMenuItems() item=menuitem}


							{if !$menuitem->isEnabled()}
								{continue}
							{/if}
							<!--begin::Col-->
							<div class="col-4">
								<a href="{$menuitem->getLink()}" class="d-flex flex-column flex-center text-center text-gray-800 text-hover-primary bg-hover-light rounded py-4 px-3 mb-3">

                                    {if $menuitem->hasImage()}
										<img src="{$menuitem->getImageAsFile()->getLink()}" class="w-25px h-25px mb-2" alt="image"/>
                                    {elseif $menuitem->hasIcon()}
										<i class="{$menuitem->getIcon()} w-25px h-25px mb-2"></i>
                                    {else}
										<i class="fas fa-bars w-25px h-25px mb-2"></i>
									{/if}

									<span class="fw-semibold">{$menuitem->getTitle()}</span>
								</a>
							</div>
							<!--end::Col-->

                        {/foreach}

					</div>
					<!--end::Row-->
				</div>
				<!--end::Scroll-->
			</div>
			<!--end::Card body-->
		</div>
		<!--end::Card-->
	</div>
	<!--end::My apps-->
	<!--end::Menu wrapper-->
</div>
<!--end::My apps-->