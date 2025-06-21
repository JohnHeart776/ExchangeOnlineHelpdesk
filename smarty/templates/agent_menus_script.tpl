<script>
	$(document).ready(function () {
		$('.btnAddMenu').on('click', function (e) {
			e.preventDefault();

			Swal.fire({
				title: 'Add New Menu Item',
				html: `
                <input type="text" id="menuTitle" class="swal2-input" placeholder="Menu Title">
                <input type="text" id="menuLink" class="swal2-input" placeholder="Menu Link">
            `,
				showCancelButton: true,
				confirmButtonText: 'Add',
				showLoaderOnConfirm: true,
				preConfirm: () => {
					const title = document.getElementById('menuTitle').value;
					const link = document.getElementById('menuLink').value;

					return $.ajax({
						url: '/api/agent/menuitem/add.json',
						type: 'POST',
						data: {
							title: title,
							link: link
						}
					});
				}
			}).then((result) => {
				if (result.isConfirmed) {
					if (result.value.success) {
						Swal.fire('Success', 'Menu item added successfully', 'success')
							.then(() => location.reload());
					} else {
						Swal.fire('Error', result.value.message || 'Failed to add menu item', 'error');
					}
				}
			});
		});

		$('.btnAddMenuItem').on('click', function (e) {
			e.preventDefault();
			const menu = $(this).data('menu');
			const parent = $(this).data('parent');

			Swal.fire({
				title: 'Add New Sub-Menu Item',
				html: `
                <input type="text" id="menuTitle" class="swal2-input" placeholder="Menu Title">
                <input type="text" id="menuLink" class="swal2-input" placeholder="Menu Link">
            `,
				showCancelButton: true,
				confirmButtonText: 'Add',
				showLoaderOnConfirm: true,
				preConfirm: () => {
					const title = document.getElementById('menuTitle').value;
					const link = document.getElementById('menuLink').value;

					return $.ajax({
						url: '/api/agent/menuitem/add.json',
						type: 'POST',
						data: {
							title: title,
							link: link,
							menu: menu,
							parent: parent
						}
					});
				}
			}).then((result) => {
				if (result.isConfirmed) {
					if (result.value.status) {
						Swal.fire('Success', 'Sub-menu item added successfully', 'success')
							.then(() => location.reload());
					} else {
						Swal.fire('Error', result.value.message || 'Failed to add sub-menu item', 'error');
					}
				}
			});
		});

		$('.btnDeleteMenuItem').on('click', function (e) {
			e.preventDefault();
			const menuItem = $(this).data('menuitem');

			Swal.fire({
				title: 'Delete Menu Item',
				text: 'Are you sure you want to delete this menu item?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Delete',
				showLoaderOnConfirm: true,
				preConfirm: () => {
					return $.ajax({
						url: '/api/agent/menuitem/delete.json',
						type: 'POST',
						data: {
							menuitem: menuItem
						}
					});
				}
			}).then((result) => {
				if (result.isConfirmed) {
					if (result.value.status) {
						Swal.fire('Success', 'Menu item deleted successfully', 'success')
							.then(() => location.reload());
					} else {
						Swal.fire('Error', result.value.message || 'Failed to delete menu item', 'error');
					}
				}
			});
		});
	});
	
	
</script>