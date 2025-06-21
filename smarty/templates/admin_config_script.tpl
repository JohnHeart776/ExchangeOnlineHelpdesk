<script>
	$(document).ready(function () {
		// Add click event to toggle icons
		$('.toggle-icon').on('click', function () {
			// Toggle the icon between right and down arrows
			const iconElement = $(this).find('i');
			if (iconElement.hasClass('fa-chevron-right')) {
				iconElement.removeClass('fa-chevron-right').addClass('fa-chevron-down');
			} else {
				iconElement.removeClass('fa-chevron-down').addClass('fa-chevron-right');
			}
		});

		// Config search functionality
		$('#config-search').on('input', function() {
			const searchTerm = $(this).val().toLowerCase().trim();

			if (searchTerm === '') {
				// If search is empty, show all items
				resetSearch();
				return;
			}

			// Hide all root configs by default
			$('tbody > tr').not('.group-header').hide();

			// Hide all group headers by default
			$('.group-header').hide();

			// Process root configs
			$('tbody > tr').not('.group-header').each(function() {
				const configName = $(this).find('td:first').text().trim().toLowerCase();
				if (configName.includes(searchTerm)) {
					$(this).show();
				}
			});

			// Process grouped configs
			$('.group-header').each(function() {
				const groupName = $(this).find('td').text().trim().toLowerCase();
				const groupId = $(this).find('[data-bs-target]').attr('data-bs-target');
				let hasMatch = groupName.includes(searchTerm);

				// Check if any config in this group matches
				$(groupId).find('tr').each(function() {
					const configName = $(this).find('td:first').text().trim().toLowerCase();
					if (configName.includes(searchTerm)) {
						hasMatch = true;
						$(this).show();
					} else {
						$(this).hide();
					}
				});

				if (hasMatch) {
					// Show this group header
					$(this).show();
					// Show the next row which contains the collapsible content
					$(this).next('tr').show();

					// Expand the group if it's not already expanded
					const toggleIcon = $(this).find('.toggle-icon i');
					if (toggleIcon.hasClass('fa-chevron-right')) {
						toggleIcon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
						$(groupId).addClass('show');
					}
				}
			});
		});

		// Reset search function
		function resetSearch() {
			// Show all root configs
			$('tbody > tr').not('.group-header').show();

			// Show all group headers
			$('.group-header').show();
			$('.group-header').next('tr').show();

			// Show all configs within groups
			$('.collapse table tr').show();

			// Collapse all groups (optional, can be removed if you want to keep expanded state)
			$('.collapse').removeClass('show');
			$('.toggle-icon i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
		}

		// Reset button click event
		$('#reset-search').on('click', function() {
			$('#config-search').val('');
			resetSearch();
		});
	});
</script>
