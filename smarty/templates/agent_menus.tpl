<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Menüs</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Verwalten Sie Ihre Menüs</span>
		</h3>
		<div class="card-toolbar">
            {if login::isAdmin()}
				<button class="btn btnAddMenu btn-sm btn-primary hover-elevate-up">
					<i class="ki-outline ki-plus fs-2 me-1"></i>
					<span>Neues Menü</span>
				</button>
            {/if}
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
        {foreach from=MenuController::getAll() item=menu}
			<div class="card mb-5">
				<div class="card-header border-0 py-5 bg-light-primary bg-opacity-50 shadow-sm">
					<div class="card-title d-flex align-items-center">
						<h3 class="m-0"><a href="#" class="editable text-hover-primary text-gray-800 text-decoration-none"
										   data-type="text"
										   data-pk="{$menu->getGuid()}"
										   data-name="Name"
										   data-url="/api/menu/update.json"
							>{$menu->getName()}</a></h3>
					</div>
					<div class="card-toolbar">
						<button class="btn btnAddMenuItem btn-sm btn-primary hover-elevate-up me-2" data-menu="{$menu->getGuid()}" data-parent="">
							<i class="ki-outline ki-plus fs-2 me-1"></i>
							<span>Neues Menüelement hinzufügen</span>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="menu-items-container">
                        {foreach from=$menu->getMenuItems() item=item}
							<div class="menu-item p-3 border-bottom">
								<div class="d-flex flex-column">
									<div class="d-flex align-items-center mb-2">
										<div class="d-flex align-items-center flex-grow-1">
											<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 {if $item->isEnabled()}btn-success{/if}"
												  data-url="/api/agent/menuitem/update.json"
												  data-uiaction="reload"
												  data-prompt=""
											data-success=""
											data-pk="{$item->getGuid()}"
											data-action="toggle"
											data-name="Enabled"
											data-value="Enabled"
											>
												<i class="ki-outline ki-check fs-2"></i>
											</span>
											<span class="btn btn-icon btn-bg-light btn-sm me-1">
												<i class="{if $item->hasIcon()}{$item->getIcon()}{else}ki-outline ki-abstract-24{/if}"></i>
											</span>

											<span class="me-3">Sort: #</span>
											<span href="#" class="editable"
												  data-type="number"
												  data-pk="{$item->getGuid()}"
												  data-name="SortOrder"
												  data-url="/api/agent/menuitem/update.json"
											>{$item->getSortOrder()}</span>

											<span class="ms-4 me-2 fw-bold me-3">Title:</span>
											<a href="#" class="editable"
											   data-type="text"
											   data-pk="{$item->getGuid()}"
											   data-name="Title"
											   data-url="/api/agent/menuitem/update.json"
											>{$item->getTitle()}</a>
											<i class="ms-2 me-2 {if $item->hasIcon()}{$item->getIcon()}{else}ki-outline ki-abstract-24{/if}"></i>
										</div>
										<div class="menu-item-controls">
											<span 
													class="btn btnAddMenuItem btn-sm btn-light hover-elevate-up me-2"
													title="Sub-Item hinzufügen"
													data-menu="{$menu->getGuid()}"
													data-parent="{$item->getGuid()}"
											>
												<i class="ki-outline ki-plus fs-2"></i>
											</span>

											<a href="/agent/menuitem/{$item->getGuid()}" class="btn btn-icon btn-light-primary btn-sm me-2">
												<i class="ki-outline ki-notepad-edit fs-2"></i>
											</a>
											<a href="{$item->getLink()}" class="btn btn-icon btn-light-info btn-sm me-2" target="_blank">
												<i class="ki-outline ki-arrow-right fs-2"></i>
											</a>
											<span class="btn btnDeleteMenuItem btn-icon btn-light-danger btn-sm" data-menuitem="{$item->getGuid()}">
												<i class="ki-outline ki-trash fs-2"></i>
											</span>
										</div>
									</div>
                                    {if $item->hasChildren()}
										<div class="ms-5">
                                            {foreach from=$item->getChildren() item=child}
												<div class="menu-item p-3 border-bottom">
													<div class="d-flex align-items-center">
														<span class="actionPostButton btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 {if $child->isEnabled()}btn-success{/if}"
															  data-url="/api/agent/menuitem/update.json"
															  data-uiaction="reload"
															  data-prompt=""
															  data-success=""
															  data-pk="{$child->getGuid()}"
															  data-action="toggle"
															  data-name="Enabled"
															  data-value="Enabled"
														>
															<i class="ki-outline ki-check fs-2"></i>
														</span>
														<span class="btn btn-icon btn-bg-light btn-sm me-1">
															<i class="{if $child->hasIcon()}{$child->getIcon()}{else}ki-outline ki-abstract-24{/if}"></i>
														</span>

														<span class="me-3">Sort: #</span>
														<span href="#" class="editable"
															  data-type="number"
															  data-pk="{$child->getGuid()}"
															  data-name="SortOrder"
															  data-url="/api/agent/menuitem/update.json"
														>{$child->getSortOrder()}</span>

														<span class="ms-4 me-2 fw-bold me-3">Title:</span>
														<a href="#" class="editable"
														   data-type="text"
														   data-pk="{$child->getGuid()}"
														   data-name="Title"
														   data-url="/api/agent/menuitem/update.json"
														>{$child->getTitle()}</a>


														<a href="/agent/menuitem/{$child->getGuid()}" class="btn btn-icon btn-light-primary btn-sm ms-auto me-2">
															<i class="ki-outline ki-notepad-edit fs-2"></i>
														</a>
														<a href="{$child->getLink()}" class="btn btn-icon btn-light-info btn-sm me-2" target="_blank">
															<i class="ki-outline ki-arrow-right fs-2"></i>
														</a>
														<span class="btn btnDeleteMenuItem btn-icon btn-light-danger btn-sm" data-menuitem="{$child->getGuid()}">
        															<i class="ki-outline ki-trash fs-2"></i>
        														</span>
													</div>
												</div>
                                            {/foreach}
										</div>
                                    {/if}
								</div>
							</div>
                        {/foreach}
					</div>
				</div>
			</div>
        {/foreach}
		<!--end::Table-->
	</div>
	<!--end::Table container-->
</div>
<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->