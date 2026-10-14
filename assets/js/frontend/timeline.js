/**
 * WPCalibrate Timeline Builder - Frontend Scripts
 * Modular Vanilla JavaScript for filters, horizontal track navigation, and viewport animations.
 */

(function () {
	'use strict';

	/**
	 * Initialize all timelines present on the page.
	 */
	function initTimelines() {
		var timelines = document.querySelectorAll('.wpcal-timeline-wrap');
		if (!timelines.length) {
			return;
		}

		timelines.forEach(function (timeline) {
			setupFilters(timeline);
			setupHorizontalNavigation(timeline);
			setupScrollAnimation(timeline);
		});
	}

	/**
	 * Setup Category & Year Filtering.
	 *
	 * @param {HTMLElement} timeline The timeline wrapper element.
	 */
	function setupFilters(timeline) {
		var filterContainer = timeline.querySelector('.wpcal-timeline-filters');
		if (!filterContainer) {
			return;
		}

		var items = timeline.querySelectorAll('.wpcal-timeline-item');
		var activeFilters = {
			category: 'all',
			year: 'all'
		};

		var buttons = filterContainer.querySelectorAll('.wpcal-filter-btn');
		buttons.forEach(function (button) {
			button.addEventListener('click', function () {
				var filterType = button.getAttribute('data-filter-type');
				var filterVal = button.getAttribute('data-filter-value');

				// Update active button state within its group.
				var group = button.closest('.wpcal-filter-group');
				if (group) {
					group.querySelectorAll('.wpcal-filter-btn').forEach(function (btn) {
						btn.classList.remove('is-active');
						btn.setAttribute('aria-selected', 'false');
					});
				}

				button.classList.add('is-active');
				button.setAttribute('aria-selected', 'true');

				activeFilters[filterType] = filterVal;
				applyFilters();
			});
		});

		function applyFilters() {
			var visibleCount = 0;

			items.forEach(function (item) {
				var itemCats = (item.getAttribute('data-categories') || '').split(',').map(function (s) { return s.trim(); });
				var itemYear = (item.getAttribute('data-year') || '').trim();

				var matchesCat = (activeFilters.category === 'all') || (itemCats.indexOf(activeFilters.category) !== -1);
				var matchesYear = (activeFilters.year === 'all') || (itemYear === activeFilters.year);

				if (matchesCat && matchesYear) {
					item.classList.remove('is-filtered-out');
					item.setAttribute('aria-hidden', 'false');
					visibleCount++;
				} else {
					item.classList.add('is-filtered-out');
					item.setAttribute('aria-hidden', 'true');
				}
			});

			// Toggle filtered empty notice.
			var emptyNotice = timeline.querySelector('.wpcal-filter-empty-notice');
			if (visibleCount === 0) {
				if (!emptyNotice) {
					emptyNotice = document.createElement('div');
					emptyNotice.className = 'wpcal-timeline-empty wpcal-filter-empty-notice';
					emptyNotice.setAttribute('role', 'status');
					var msg = (window.wpcalTimelineData && window.wpcalTimelineData.i18n && window.wpcalTimelineData.i18n.empty)
						? window.wpcalTimelineData.i18n.empty
						: 'No items found matching the selected filter.';
					emptyNotice.innerHTML = '<p>' + msg + '</p>';
					var container = timeline.querySelector('.wpcal-vertical-container') || timeline.querySelector('.wpcal-horizontal-container');
					if (container) {
						container.appendChild(emptyNotice);
					}
				}
				emptyNotice.style.display = 'block';
			} else if (emptyNotice) {
				emptyNotice.style.display = 'none';
			}
		}
	}

	/**
	 * Setup Horizontal Track Scroll Buttons.
	 *
	 * @param {HTMLElement} timeline The timeline wrapper element.
	 */
	function setupHorizontalNavigation(timeline) {
		if (!timeline.classList.contains('wpcal-layout-horizontal')) {
			return;
		}

		var track = timeline.querySelector('.wpcal-horizontal-track');
		var prevBtn = timeline.querySelector('.wpcal-nav-prev');
		var nextBtn = timeline.querySelector('.wpcal-nav-next');

		if (!track || !prevBtn || !nextBtn) {
			return;
		}

		var scrollAmount = 340;

		prevBtn.addEventListener('click', function () {
			track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
		});

		nextBtn.addEventListener('click', function () {
			track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
		});

		// Keyboard arrow accessibility on track focus.
		track.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') {
				track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
			} else if (e.key === 'ArrowRight') {
				track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
			}
		});
	}

	/**
	 * Setup Viewport Scroll Animation using IntersectionObserver.
	 *
	 * @param {HTMLElement} timeline The timeline wrapper element.
	 */
	function setupScrollAnimation(timeline) {
		if (!timeline.classList.contains('wpcal-has-animation')) {
			return;
		}

		// Honor prefers-reduced-motion.
		var mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
		if (mediaQuery.matches) {
			timeline.querySelectorAll('.wpcal-animated-item').forEach(function (item) {
				item.classList.add('is-visible');
			});
			return;
		}

		var animatedItems = timeline.querySelectorAll('.wpcal-animated-item');
		if (!animatedItems.length) {
			return;
		}

		if ('IntersectionObserver' in window) {
			var observer = new IntersectionObserver(
				function (entries, obs) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							entry.target.classList.add('is-visible');
							obs.unobserve(entry.target);
						}
					});
				},
				{
					threshold: 0.15,
					rootMargin: '0px 0px -50px 0px'
				}
			);

			animatedItems.forEach(function (item) {
				observer.observe(item);
			});
		} else {
			// Fallback: make all items visible immediately.
			animatedItems.forEach(function (item) {
				item.classList.add('is-visible');
			});
		}
	}

	// Initialize when DOM is ready.
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initTimelines);
	} else {
		initTimelines();
	}
})();
