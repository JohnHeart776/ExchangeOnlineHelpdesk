<head>
	<title>{if isset($title)}{$title} | {/if}{Config::getConfigValueFor("site.title")}</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="shortcut icon" href="/favicon.ico"/>

	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="/assets/fonts/inter/inter.css"/>
	<!--end::Fonts-->

	<!--begin::Vendor Stylesheets(used for this page only)-->
	<link href="/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css"/>
	<!--end::Vendor Stylesheets-->

	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
	<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css"/>
	<!--end::Global Stylesheets Bundle-->

	<!--begin::font-awesome-->
	<link href="/assets/vendor/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" type="text/css"/>
	<!--end::font-awesome-->

	<!--begin::sweetalert-->
	<link href="/assets/vendor/sweetalert2/11.7.5/sweetalert2.min.css" rel="stylesheet" type="text/css"/>
	<!--end::sweetalert-->

	<link href="/assets/vendor/x-editable/1.5.1/jquery-editable/css/jquery-editable.css" rel="stylesheet" type="text/css"/>

	<link href="/assets/vendor/select2/4.0.13/css/select2.min.css" rel="stylesheet" type="text/css"/>

	<!-- dropzonejs -->
	<link href="/assets/vendor/dropzonejs/5.9.3/dropzone.min.css" rel="stylesheet" type="text/css" />

	<!-- fullcalendar -->
	<link href="/assets/vendor/fullcalendar/5.11.5/main.min.css" rel="stylesheet" type="text/css"/>

	<!--begin::custom css-->
	<link href="/assets/css/custom.css" rel="stylesheet" type="text/css"/>
	<!--end::custom css-->


</head>