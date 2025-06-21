<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Artikel</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Verwalten Sie hier Ihre Artikel</span>
		</h3>
		<div class="card-toolbar">
			<button class="btn btnAddArticle btn-sm btn-primary hover-elevate-up">
				<i class="ki-outline ki-plus fs-2 me-1"></i>
				<span>Neuer Artikel</span>
			</button>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table-->
		<table class="table align-middle table-row-dashed fs-6 gy-5">
			<thead>
			<tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
				<th>Titel</th>
				<th>Zugriffsebene</th>
				<th>Link</th>
				<th>Aktionen</th>
			</tr>
			</thead>
			<tbody>
            {foreach from=ArticleController::getAll() item=article}
				<tr>
					<td>{$article->getTitle()}</td>
					<td>{$article->getAccessLevel()}</td>
					<td><a href="{$article->getLink()}" target="_blank">{$article->getLink()}</a></td>
					<td>
						<a href="/agent/article/{$article->getGuid()}/">
							<span class="btn btn-icon btn-light-primary btn-sm me-1">
								<i class="fas fa-pencil fs-2"></i>
							</span>
						</a>

                        {if login::getUser()->isAdmin()}
							<span class="btn btnDeleteArticle btn-icon btn-light-danger btn-sm" data-article="{$article->getGuid()}">
								<i class="fas fa-times fs-2"></i>
							</span>
                        {/if}
					</td>
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