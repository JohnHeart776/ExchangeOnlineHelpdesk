<!--begin::Tables Widget 10-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">
		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Artikel</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Manage article content</span>
		</h3>
		<div class="card-toolbar">
			<span type="button" id="generateArticle" class="btn btn-sm btn-primary">
				<i class="fas fa-lightbulb"></i> Artikel generieren
			</span>
			<a href="{$article->getLink()}" target="_blank" class="btn btn-sm btn-light-info ms-2">
				<i class="fas fa-eye"></i> Artikel Vorschau
			</a>
		</div>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Title Frame-->
		<div class="mb-5">
			<h1 class="editable"
				data-url="/api/agent/article/update.json"
				data-pk="{$article->getGuid()}"
				data-name="Title"
				data-value="{$article->getTitleForTinyMce()}"
				>{$article->getTitle()}</h1>
		</div>
		<!--end::Title Frame-->

		<!--begin::Content Editor-->
		<div class="mb-5">
			<textarea id="articleContent" style="height: 800px;" class="tinymce">{$article->getContentForTinyMce()}</textarea>
			<div class="text-end mt-3">
				<button id="saveContent" class="btn btn-primary">Speichern</button>
			</div>
		</div>
		<!--end::Content Editor-->

	</div>
	<!--begin::Body-->
</div>
<!--end::Tables Widget 10-->