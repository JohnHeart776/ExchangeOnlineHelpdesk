<!--begin::Mobile menu-->
<div class="d-flex d-lg-none">
	<div class="menu menu-rounded menu-column menu-title-gray-700 menu-arrow-gray-500" style="scroll-behavior: smooth; height: 100vh; overflow: scroll; scrollbar-width: thin;">
        {if $menu}
            {foreach from=$menu->getItems() item=menuItem}
                {if (!$menuItem->isEnabled())}
                    {continue}
                {/if}
                {assign isActive $menuItem->isActive()}
                {assign hasChildren $menuItem->hasEnabledChildren()}

				<!--begin::Menu item-->
				<div class="menu-item {if $isActive}here{/if}">
					<a href="{$menuItem->getLink()}" class="menu-link">
                        {if $menuItem->hasIcon()}
							<span class="menu-icon"><i class="{$menuItem->getIcon()}"></i></span>
                        {/if}
						<span class="menu-title">{$menuItem->getTitle()}</span>
                        {if $hasChildren}<span class="menu-arrow"></span>{/if}
					</a>
                    {if $hasChildren}
						<div class="ps-5">
                            {foreach from=$menuItem->getChildren() item=$menuItemChild}
                                {if (!$menuItemChild->isEnabled())}
                                    {continue}
                                {/if}
                                {assign isChildActive $menuItemChild->isActive()}
								<div class="menu-item">
									<a class="menu-link ps-0 {if $isChildActive}active{/if}" href="{$menuItemChild->getLink()}">
                                        {if $menuItemChild->hasIcon()}
											<span class="menu-bullet">
											<i class="{$menuItemChild->getIcon()}"></i>
										</span>
                                        {/if}
										<span class="menu-title">{$menuItemChild->getTitle()}</span>
									</a>
								</div>
                            {/foreach}
						</div>
                    {/if}
				</div>
				<!--end::Menu item-->
            {/foreach}
        {/if}
	</div>
</div>
<!--end::Mobile menu-->