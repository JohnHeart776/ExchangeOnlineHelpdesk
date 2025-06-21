<!--begin::Content container-->
<div id="kt_app_content_container" class="app-container container-fluid">
	<!--begin::Card-->
	<div class="card card-xl-stretch mb-xl-10">
		<!--begin::Card header-->
		<div class="card-header">
			<h3 class="card-title align-items-start flex-column">
				<span class="card-label fw-bold text-dark">Tickets by Category</span>
			</h3>
		</div>
		<!--end::Card header-->

		<!--begin::Card body-->
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
					<thead>
					<tr class="fw-bold text-muted">
						<th>Week</th>
                        {assign var="categories" value=$report[0].stats}
                        {foreach from=$categories item=category}
							<th>{$category->getCategory()->getPublicName()}</th>
                        {/foreach}
					</tr>
					</thead>
					<tbody>
                    {foreach from=$report item=weekData}
						<tr>
							<td>{$weekData.week->format('Y-W')}</td>
                            {foreach from=$categories item=category}
                                {assign var="found" value=false}
                                {foreach from=$weekData.stats item=stat}
                                    {if $stat->getCategory()->getCategoryId() == $category->getCategory()->getCategoryId()}
										<td>{$stat->getCount()}</td>
                                        {assign var="found" value=true}
                                    {/if}
                                {/foreach}
                                {if !$found}
									<td>0</td>
                                {/if}
                            {/foreach}
						</tr>
                    {/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<!--end::Card body-->
	</div>
	<!--end::Card-->
</div>
<!--end::Content container-->