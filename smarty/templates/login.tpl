<!DOCTYPE html>
<html lang="de">
<!--begin::Head-->
<head>
	<title>{Config::getConfigValueFor("site.title")}</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<link rel="shortcut icon" href="/favicon.ico"/>
	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="/assets/fonts/inter/inter.css"/>
	<!--end::Fonts-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
	<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css"/>
	<!--end::Global Stylesheets Bundle-->
	<!-- font-awesome -->
	<link href="/assets/vendor/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" type="text/css"/>

	<!-- Custom styles for login page -->
	<style>
		@media (max-width: 991.98px) {
			/* Adjust background image height on mobile */
			.bgi-size-cover.bgi-position-center {
				min-height: 180px !important;
			}

			/* Ensure login form has proper spacing on mobile */
			.app-blank .flex-root {
				padding: 0 !important;
			}

			/* Make logo larger on mobile */
			.aside-logo {
				width: 100% !important;
				max-height: none !important;
			}
		}

		/* Improve button appearance on very small screens */
		@media (max-width: 575.98px) {
			.btn-lg {
				padding: 0.65rem 1rem;
			}

			.fs-6 {
				font-size: 0.95rem !important;
			}

			/* Ensure logo is properly sized on very small screens */
			.h-sm-60px {
				height: 60px !important;
			}

			/* Adjust padding for better spacing on very small screens */
			.p-5 {
				padding: 0.75rem !important;
			}
		}
	</style>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="app-blank">
<!--begin::Theme mode setup on page load-->
<script>
	var defaultThemeMode = "light";
	var themeMode;
	if (document.documentElement) {
		if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
			themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
		} else {
			if (localStorage.getItem("data-bs-theme") !== null) {
				themeMode = localStorage.getItem("data-bs-theme");
			} else {
				themeMode = defaultThemeMode;
			}
		}
		if (themeMode === "system") {
			themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
		}
		document.documentElement.setAttribute("data-bs-theme", themeMode);
	}
</script>
<!--end::Theme mode setup on page load-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root" id="kt_app_root">
	<!--begin::Authentication - Sign-in -->
	<div class="d-flex flex-column flex-lg-row flex-column-fluid">
		<!--begin::Body-->
		<div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-5 p-lg-10 order-2 order-lg-1">
			<!--begin::Form-->
			<div class="d-flex flex-center flex-column flex-lg-row-fluid">
				<!--begin::Wrapper-->
				<div class="w-100 w-md-75 w-lg-500px p-5 p-lg-10">
					<!--begin::Header-->
					<div class="text-center mb-7 mb-lg-10">
						<!--begin::Title-->
						<h1 class="text-dark fw-bolder mb-3 fs-2x">Login</h1>
						<!--end::Title-->
						<div class="text-gray-500 fw-semibold fs-6">
							Please sign in with your Microsoft account
						</div>
					</div>
					<!--end::Header-->

					{if isset($smarty.get.message)}
						<!--begin::Message Alert-->
						<div class="alert alert-primary d-flex align-items-center p-4 mb-7">
							<i class="ki-duotone ki-information-5 fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
							<div class="d-flex flex-column">
								<span>{$smarty.get.message}</span>
							</div>
						</div>
						<!--end::Message Alert-->
					{/if}

					<!--begin::Microsoft Sign-In-->
					<div class="d-flex justify-content-center align-items-center">
						<a href="/login/auth/microsoft/" class="btn btn-flex btn-lg w-100 mb-0">
							<span class="btn btn-default border border-info rounded py-2 py-md-3 px-3 px-md-4 w-100 d-flex align-items-center justify-content-center">
								<img alt="Microsoft Logo" src="/assets/media/svg/social-logos/microsoft.svg" class="h-20px me-2 me-md-3"/>
								<span class="fs-6">Sign in with Microsoft</span>
							</span>
						</a>
					</div>
					<!--end::Microsoft Sign-In-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Form-->
		</div>
		<!--end::Body-->
		<!--begin::Aside-->
		<div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
			 style="background-image: url('/assets/media/misc/auth-bg.png'); min-height: 200px;">
			<!--begin::Content-->
			<div class="d-flex flex-column flex-center py-5 py-lg-15 px-5 px-md-15 w-100">
				<!--begin::Logo-->
				<a href="/" class="mb-5 mb-lg-12">
					<img alt="Logo" src="/logo/big-light.svg" class="h-50px h-sm-60px h-lg-75px aside-logo"/>
				</a>
				<!--end::Logo-->
				<!--begin::Text-->
				<div class="text-white fs-base fs-lg-lg">
					{Config::get("text.login")}
				</div>
				<!--end::Text-->
			</div>
			<!--end::Content-->
		</div>
		<!--end::Aside-->
	</div>
	<!--end::Authentication - Sign-in-->
</div>
<!--end::Root-->

<!--begin::Javascript-->
<script>
	var hostUrl = "/assets/";
	// Check if the "from" parameter is present in the URL and is a valid server-absolute path (not "/")
	(function () {
		const params = new URLSearchParams(window.location.search);
		const from = params.get("from");

		// Only process if "from" is a server-absolute path and not equal to "/"
		if (from && from !== "/" && from.startsWith("/")) {
 		// Save the URL with timestamp (valid for max. 5 minutes)
			const redirectData = {
				url: from,
				timestamp: Date.now(),
			};

			try {
				console.log(redirectData);
				localStorage.setItem("redirectData", JSON.stringify(redirectData));
 			// Show a subtle toast notification
				showToast("You will be redirected to your target page after login");
			} catch (error) {
				console.error("Error saving redirect URL:", error);
			}
		}
	})();

	/**
	 * Shows a toast notification. Position: top-right on desktop, bottom-right on mobile.
	 * Styling is done with Tailwind CSS.
	 */
	function showToast(message) {
		// Create the toast element
		const toast = document.createElement("div");
		// Determine position: Mobile (Screen < 640px) → bottom-right, otherwise top-right
		const positionClasses =
			window.innerWidth < 640 ? "fixed bottom-4 right-4" : "fixed top-4 right-4";
		toast.className = positionClasses + " bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50";
		toast.textContent = message;
		document.body.appendChild(toast);

		// Remove the toast after 3 seconds
		setTimeout(() => {
			toast.remove();
		}, 3000);
	}

</script>

</body>
<!--end::Body-->
</html>
