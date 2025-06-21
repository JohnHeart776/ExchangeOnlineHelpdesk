<!DOCTYPE html>
<html lang="de">
<!--begin::Head-->
{include file="partials/_header.tpl"}
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body"
	  data-kt-app-header-fixed="true"
	  data-kt-app-header-fixed-mobile="true"
	  data-kt-app-header-stacked="true"
	  data-kt-app-header-primary-enabled="true"
	  data-kt-app-header-secondary-enabled="false"
	  data-kt-app-sidebar-enabled="false"
	  data-kt-app-sidebar-fixed="false"
	  data-kt-app-sidebar-push-toolbar="false"
	  data-kt-app-sidebar-push-footer="false"
	  class="app-default">
{include file='partials/_body_thememode.tpl'}

<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
	<!--begin::Page-->
	<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
		<!--begin::Header-->
        {include file='partials/_body_app_header.tpl'}
		<!--end::Header-->

		<!--begin::Wrapper-->
		<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
			<!--begin::Main-->
			<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
				<!--begin::Content wrapper-->
				<div class="d-flex flex-column flex-column-fluid">
					<!--begin::Content-->
					<div id="kt_app_content" class="app-content app-content-stretch">
						<!--begin::Content container-->
						<div id="kt_app_content_container" class="app-container container-fluid">
							<!--begin::Content Main-->
							<div class="card card-flush">
								<!--begin::Card header-->
								<div class="card-header align-items-center gap-12 gap-md-3">

								</div>
								<!--end::Card header-->
								<!--begin::Card body-->
								<div class="card-body pt-0 px-3">
									<!-- content area -->
                                    {if isset($content)}
                                        {include file="`$content`.tpl"}
                                    {/if}
								</div>
								<!--end::Card body-->
							</div>
							<!--end::Content Main-->
						</div>
						<!--end::Content container-->
					</div>
					<!--end::Content-->
				</div>
				<!--end::Content wrapper-->

			</div>
			<!--end:::Main-->
		</div>
		<!--end::Wrapper-->
	</div>
	<!--end::Page-->
</div>
<!--end::App-->
{include file="partials/_body_end.tpl"}
{if isset($content)}
    {assign var="content_script" value="`$content`_script.tpl"}
    {include file=$content_script}
{/if}

</body>
<!--end::Body-->
</html>