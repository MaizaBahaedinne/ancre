import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
	const sidebar = document.getElementById('app-sidebar');
	const toggleBtn = document.querySelector('.modern-sidebar-toggle');
	const layout = document.querySelector('.modern-admin-layout');

	if (toggleBtn && sidebar) {
		toggleBtn.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			sidebar.classList.toggle('sidebar-visible');
		});

		// Close sidebar when clicking outside
		document.addEventListener('click', function(e) {
			if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
				sidebar.classList.remove('sidebar-visible');
			}
		});

		// Close sidebar on navigation
		const navLinks = sidebar.querySelectorAll('a');
		navLinks.forEach(link => {
			link.addEventListener('click', function() {
				// Only close on actual navigation (not on route highlighting)
				setTimeout(() => {
					if (window.innerWidth <= 991.98) {
						sidebar.classList.remove('sidebar-visible');
					}
				}, 100);
			});
		});
	}
});

