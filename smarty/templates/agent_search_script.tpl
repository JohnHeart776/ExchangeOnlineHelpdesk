<script>
	$(document).ready(function () {
		let searchTimeout;
		let currentRequest;

		$('#searchInput').focus();


		$('#searchInput').on('input', function () {
			const searchTerm = $(this).val();

			if (currentRequest) {
				currentRequest.abort();
			}

			clearTimeout(searchTimeout);

			if (searchTerm.length < 3) {
				$('#searchResults').html('Bitte einen Suchbegriff eingeben');
				return;
			}

			searchTimeout = setTimeout(function () {
				currentRequest = $.post('/search.html?limit=250', {
					query: searchTerm,
				}, function (response) {
					$('#searchResults').html(response);
				});
			}, 125);
		});
	});
</script>
