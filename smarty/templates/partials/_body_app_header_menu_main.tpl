<!--begin::Main menu-->
<div class="menu menu-rounded menu-nowrap flex-shrink-0 menu-row menu-active-bg menu-title-gray-700 menu-state-gray-900 menu-icon-gray-500 menu-arrow-gray-500 menu-state-icon-primary menu-state-bullet-primary fw-semibold fs-base align-items-stretch"
	 id="#kt_app_header_secondary_menu" data-kt-menu="true">

	{if isset($menu)}
    {foreach from=$menu->getItems() item=menuItem}

		{if (!$menuItem->isEnabled())}
			{continue}
		{/if}

		{if !$menuItem->canSee(login::getUser())}
			{continue}
        {/if}

        {assign isActive $menuItem->isActive()}
        {assign hasChildren $menuItem->hasEnabledChildren()}

		<!--begin:Menu item - top link-->
		<div
                {if $hasChildren}data-kt-menu-trigger="{ default: 'click', lg: 'hover' }"{/if}
				data-kt-menu-placement="bottom-start"
				class="menu-item me-6 {if $isActive}here{/if} show"
		>
			<!--begin:Menu links child-->
			<a href="{$menuItem->getLink()}" class="menu-link">
				<span class="menu-title">
					{if $menuItem->hasIcon()}<span class="me-3"><i class="{$menuItem->getIcon()} fs-4"></i></span>{/if}
					{$menuItem->getTitle()}
				</span>
				{if $hasChildren}<span class="menu-arrow"></span>{/if}
			</a>
			<!--end:Menu link-->
            {if $hasChildren}
				<!--begin:Menu sub-->
				<div class="menu-sub menu-sub-dropdown px-lg-2 py-lg-4 w-150px w-lg-175px">
                    {foreach from=$menuItem->getChildren() item=$menuItemChild}

						{if (!$menuItemChild->isEnabled())}
                            {continue}
                        {/if}

                        {if !$menuItemChild->canSee(login::getUser())}
                            {continue}
                        {/if}

						{assign isChildActive $menuItemChild->isActive()}
						<!--begin:Menu item-->
						<div class="menu-item">
							<!--begin:Menu link-->
							<a class="menu-link {if $isChildActive}here active{/if}" href="{$menuItemChild->getLink()}">
                                {if $menuItemChild->hasIcon()}
									<span class="menu-icon">
										<i class="{$menuItemChild->getIcon()} fs-3"></i>
									</span>
                                {/if}
								<span class="menu-title">{$menuItemChild->getTitle()}</span>
							</a>
							<!--end:Menu link-->
						</div>
						<!--end:Menu item-->
                    {/foreach}

				</div>
				<!--end:Menu sub-->
            {/if}
		</div>
		<!--end:Menu item-->

    {/foreach}
    {/if}

	<!--begin:Menu item-->
	<div class="menu-item">
		<!--begin:Menu content-->
		<div class="menu-content">
			<div class="menu-separator"></div>
		</div>
		<!--end:Menu content-->
	</div>
	<!--end:Menu item-->

</div>
<!--end::Menu-->