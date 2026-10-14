/**
 * WPCalibrate Timeline Builder - Admin Scripts
 */

(function ($) {
	'use strict';

	$(document).ready(function () {
		initColorPickers();
		initShortcodeCopy();
		initItemRowEvents();
		initDragAndDrop();
		initFormSubmit();
		initLivePreview();
		initTimelineListActions();
	});

	/**
	 * Initialize WordPress Color Pickers.
	 */
	function initColorPickers() {
		if ($.fn.wpColorPicker) {
			$('.wpcal-color-picker').wpColorPicker();
		}
	}

	/**
	 * Initialize Shortcode Copy to Clipboard.
	 */
	function initShortcodeCopy() {
		$(document).on('click', '.wpcal-copy-btn, .wpcal-shortcode-pill', function (e) {
			e.preventDefault();
			var text = $(this).data('clipboard-text') || $(this).data('copy');
			if (!text && $(this).prev('.wpcal-shortcode-input').length) {
				text = $(this).prev('.wpcal-shortcode-input').val();
			}

			if (text) {
				navigator.clipboard.writeText(text).then(function () {
					alert((window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.copySuccess) || 'Shortcode copied to clipboard!');
				});
			}
		});
	}

	/**
	 * Initialize Collapsible and Form Controls inside Item Rows.
	 */
	function initItemRowEvents() {
		// Toggle collapse.
		$(document).on('click', '.wpcal-item-row-header', function (e) {
			if ($(e.target).closest('.wpcal-item-drag-handle, .wpcal-reorder-buttons, .wpcal-item-row-actions').length) {
				return;
			}
			var row = $(this).closest('.wpcal-item-row');
			row.toggleClass('is-collapsed');
			var isCollapsed = row.hasClass('is-collapsed');
			$(this).find('.wpcal-toggle-item-btn').text(isCollapsed ? '▸' : '▾').attr('aria-expanded', !isCollapsed);
		});

		$(document).on('click', '.wpcal-toggle-item-btn', function (e) {
			e.preventDefault();
			var row = $(this).closest('.wpcal-item-row');
			row.toggleClass('is-collapsed');
			var isCollapsed = row.hasClass('is-collapsed');
			$(this).text(isCollapsed ? '▸' : '▾').attr('aria-expanded', !isCollapsed);
		});

		// Live update summary header on title or date change.
		$(document).on('input', '.wpcal-item-title-input', function () {
			var val = $(this).val().trim();
			var row = $(this).closest('.wpcal-item-row');
			row.find('.wpcal-summary-title').text(val || 'Untitled Milestone');
		});

		$(document).on('input', '.wpcal-date-val-input', function () {
			var val = $(this).val().trim();
			var row = $(this).closest('.wpcal-item-row');
			row.find('.wpcal-summary-date').text(val || 'No Date');
		});

		// Date type change hints.
		$(document).on('change', '.wpcal-date-type-select', function () {
			var type = $(this).val();
			var row = $(this).closest('.wpcal-item-row');
			var input = row.find('.wpcal-date-val-input');
			var hint = row.find('.wpcal-date-hint');

			if (type === 'exact') {
				input.attr('placeholder', 'YYYY-MM-DD (e.g. 2024-05-18)');
				hint.text('Enter a standard calendar date (YYYY-MM-DD).');
			} else if (type === 'month_year') {
				input.attr('placeholder', 'YYYY-MM (e.g. 2024-05)');
				hint.text('Enter year and month (YYYY-MM).');
			} else if (type === 'year_only') {
				input.attr('placeholder', 'YYYY (e.g. 2024)');
				hint.text('Enter 4-digit calendar year.');
			} else {
				input.attr('placeholder', 'e.g. Founded, Phase One, Coming Soon');
				hint.text('Custom date text for non-standard milestones.');
			}
		});

		// Keyboard Accessible Move Up / Move Down buttons.
		$(document).on('click', '.wpcal-move-up-btn', function (e) {
			e.preventDefault();
			var row = $(this).closest('.wpcal-item-row');
			var prev = row.prev('.wpcal-item-row');
			if (prev.length) {
				row.insertBefore(prev);
				reindexItemOrders();
			}
		});

		$(document).on('click', '.wpcal-move-down-btn', function (e) {
			e.preventDefault();
			var row = $(this).closest('.wpcal-item-row');
			var next = row.next('.wpcal-item-row');
			if (next.length) {
				row.insertAfter(next);
				reindexItemOrders();
			}
		});

		// Media Library picker.
		$(document).on('click', '.wpcal-select-img-btn', function (e) {
			e.preventDefault();
			var row = $(this).closest('.wpcal-item-row');
			var idField = row.find('.wpcal-image-id-field');
			var imgPreview = row.find('.wpcal-image-preview');
			var removeBtn = row.find('.wpcal-remove-img-btn');

			var frame = wp.media({
				title: (window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.selectImage) || 'Select Item Image',
				button: {
					text: (window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.useImage) || 'Use This Image'
				},
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				idField.val(attachment.id);
				var thumbUrl = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
				imgPreview.attr('src', thumbUrl).show();
				removeBtn.show();
			});

			frame.open();
		});

		$(document).on('click', '.wpcal-remove-img-btn', function (e) {
			e.preventDefault();
			var row = $(this).closest('.wpcal-item-row');
			row.find('.wpcal-image-id-field').val('0');
			row.find('.wpcal-image-preview').attr('src', '').hide();
			$(this).hide();
		});

		// Add new item from template.
		$('#wpcal_add_item_btn').on('click', function (e) {
			e.preventDefault();
			var container = $('#wpcal-items-container');
			container.find('.wpcal-items-empty-state').remove();

			var templateHtml = $('#tmpl-wpcal-item-row').html();
			var newIndex = new Date().getTime();
			var rendered = templateHtml.replace(/\{\{data\.index\}\}/g, newIndex);

			var $newRow = $(rendered);
			container.append($newRow);
			$newRow.removeClass('is-collapsed');

			if ($.fn.wpColorPicker) {
				$newRow.find('.wpcal-color-picker').wpColorPicker();
			}

			reindexItemOrders();
			$newRow.find('.wpcal-item-title-input').focus();
		});

		// Duplicate Item.
		$(document).on('click', '.wpcal-duplicate-item-btn', function (e) {
			e.preventDefault();
			var row = $(this).closest('.wpcal-item-row');
			var clone = row.clone();

			var newIndex = new Date().getTime();
			// Re-index names in clone.
			clone.attr('data-index', newIndex);
			clone.find('.wpcal-item-id-field').val('0');
			clone.find('[name*="items["]').each(function () {
				var name = $(this).attr('name');
				var updated = name.replace(/items\[[^\]]+\]/, 'items[' + newIndex + ']');
				$(this).attr('name', updated);
			});

			var currentTitle = clone.find('.wpcal-item-title-input').val();
			clone.find('.wpcal-item-title-input').val(currentTitle + ' (Copy)');
			clone.find('.wpcal-summary-title').text(currentTitle + ' (Copy)');

			// Remove wpColorPicker wraps and re-init in clone.
			clone.find('.wp-picker-container').replaceWith(function () {
				return clone.find('.wpcal-color-picker');
			});

			row.after(clone);
			if ($.fn.wpColorPicker) {
				clone.find('.wpcal-color-picker').wpColorPicker();
			}

			reindexItemOrders();
		});

		// Delete Item.
		$(document).on('click', '.wpcal-delete-item-btn', function (e) {
			e.preventDefault();
			var confirmMsg = (window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.confirmDelete) || 'Are you sure you want to delete this item?';
			if (!confirm(confirmMsg)) {
				return;
			}

			var row = $(this).closest('.wpcal-item-row');
			row.fadeOut(200, function () {
				row.remove();
				reindexItemOrders();

				var container = $('#wpcal-items-container');
				if (container.children('.wpcal-item-row').length === 0) {
					var emptyText = container.data('empty-text') || 'No items yet. Click "+ Add Item" above to create your first milestone.';
					container.html('<div class="wpcal-items-empty-state"><p>' + emptyText + '</p></div>');
				}
			});
		});

		// Toggle vertical alignment style dropdown when layout changes.
		$('#wpcal_layout').on('change', function () {
			if ($(this).val() === 'horizontal') {
				$('#wpcal_vertical_style_group').slideUp(150);
			} else {
				$('#wpcal_vertical_style_group').slideDown(150);
			}
		});
	}

	/**
	 * Re-index item order hidden fields.
	 */
	function reindexItemOrders() {
		$('#wpcal-items-container .wpcal-item-row').each(function (idx) {
			$(this).find('.wpcal-item-order-field').val(idx);
		});
	}

	/**
	 * Drag and drop reordering using HTML5 native drag events.
	 */
	function initDragAndDrop() {
		var container = document.getElementById('wpcal-items-container');
		if (!container) {
			return;
		}

		var draggedRow = null;

		$(document).on('dragstart', '.wpcal-item-row', function (e) {
			draggedRow = this;
			e.originalEvent.dataTransfer.effectAllowed = 'move';
			$(this).addClass('is-dragging');
		});

		$(document).on('dragend', '.wpcal-item-row', function () {
			$(this).removeClass('is-dragging');
			$('.wpcal-item-row').removeClass('is-drag-over');
			draggedRow = null;
			reindexItemOrders();
		});

		$(document).on('dragover', '.wpcal-item-row', function (e) {
			e.preventDefault();
			e.originalEvent.dataTransfer.dropEffect = 'move';
			if (draggedRow && draggedRow !== this) {
				$(this).addClass('is-drag-over');
			}
		});

		$(document).on('dragleave', '.wpcal-item-row', function () {
			$(this).removeClass('is-drag-over');
		});

		$(document).on('drop', '.wpcal-item-row', function (e) {
			e.preventDefault();
			$(this).removeClass('is-drag-over');
			if (draggedRow && draggedRow !== this) {
				var allRows = Array.from(container.querySelectorAll('.wpcal-item-row'));
				var draggedIdx = allRows.indexOf(draggedRow);
				var targetIdx = allRows.indexOf(this);

				if (draggedIdx < targetIdx) {
					this.after(draggedRow);
				} else {
					this.before(draggedRow);
				}
				reindexItemOrders();
			}
		});

		// Make rows draggable via handle.
		$(document).on('mouseenter', '.wpcal-item-drag-handle', function () {
			$(this).closest('.wpcal-item-row').attr('draggable', 'true');
		});

		$(document).on('mouseleave', '.wpcal-item-drag-handle', function () {
			$(this).closest('.wpcal-item-row').removeAttr('draggable');
		});
	}

	/**
	 * AJAX Timeline Form Submit.
	 */
	function initFormSubmit() {
		$('#wpcal-timeline-form').on('submit', function (e) {
			e.preventDefault();
			var saveBtn = $('#wpcal_save_timeline_btn');
			var origText = saveBtn.text();

			saveBtn.prop('disabled', true).text((window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.saving) || 'Saving...');

			var formData = $(this).serialize();
			formData += '&action=wpcal_save_timeline&nonce=' + encodeURIComponent((window.wpcalAdminData && window.wpcalAdminData.nonce) || '');

			$.ajax({
				url: (window.wpcalAdminData && window.wpcalAdminData.ajaxUrl) || ajaxurl,
				type: 'POST',
				data: formData,
				dataType: 'json',
				success: function (res) {
					saveBtn.prop('disabled', false).text(origText);
					if (res.success) {
						if (res.data.redirect && window.location.href.indexOf('id=' + res.data.timeline_id) === -1) {
							window.location.href = res.data.redirect;
						} else {
							alert(res.data.message || 'Saved successfully!');
							if (res.data.shortcode && $('.wpcal-shortcode-pill').length === 0) {
								var pillHtml = '<span class="wpcal-shortcode-pill" title="Click to copy shortcode" data-copy="' + res.data.shortcode + '"><code>' + res.data.shortcode + '</code></span>';
								$('.wpcal-topbar-left').append(pillHtml);
							}
						}
					} else {
						alert(res.data.message || 'Error saving timeline.');
					}
				},
				error: function () {
					saveBtn.prop('disabled', false).text(origText);
					alert((window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.error) || 'An error occurred.');
				}
			});
		});
	}

	/**
	 * Live Preview Modal.
	 */
	function initLivePreview() {
		$('#wpcal_open_preview').on('click', function (e) {
			e.preventDefault();
			var timelineId = $('#wpcal_timeline_id').val();
			if (!timelineId || timelineId === '0') {
				alert('Please save the timeline first before previewing.');
				return;
			}

			var modal = $('#wpcal_preview_modal');
			var canvas = $('#wpcal_preview_canvas');
			canvas.html('<div class="wpcal-preview-loading">Generating preview...</div>');
			modal.fadeIn(200);

			$.ajax({
				url: (window.wpcalAdminData && window.wpcalAdminData.ajaxUrl) || ajaxurl,
				type: 'POST',
				data: {
					action: 'wpcal_preview_timeline',
					timeline_id: timelineId,
					nonce: (window.wpcalAdminData && window.wpcalAdminData.nonce) || ''
				},
				dataType: 'json',
				success: function (res) {
					if (res.success && res.data.html) {
						canvas.html(res.data.html);
						// Re-trigger frontend JS setup if available.
						if (window.wpcalTimelineInit) {
							window.wpcalTimelineInit();
						}
					} else {
						canvas.html('<div class="notice notice-error"><p>' + (res.data.message || 'Failed to generate preview.') + '</p></div>');
					}
				},
				error: function () {
					canvas.html('<div class="notice notice-error"><p>Server error during preview generation.</p></div>');
				}
			});
		});

		$('.wpcal-modal-close, .wpcal-modal-backdrop').on('click', function () {
			$('#wpcal_preview_modal').fadeOut(150);
		});
	}

	/**
	 * Timeline List Actions: Duplicate and Delete.
	 */
	function initTimelineListActions() {
		// Duplicate Timeline.
		$(document).on('click', '.wpcal-duplicate-timeline-btn', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			if (!id) return;

			$(this).text('Duplicating...');

			$.ajax({
				url: (window.wpcalAdminData && window.wpcalAdminData.ajaxUrl) || ajaxurl,
				type: 'POST',
				data: {
					action: 'wpcal_duplicate_timeline',
					id: id,
					nonce: (window.wpcalAdminData && window.wpcalAdminData.nonce) || ''
				},
				dataType: 'json',
				success: function (res) {
					if (res.success && res.data.redirect) {
						window.location.href = res.data.redirect;
					} else {
						alert(res.data.message || 'Duplication failed.');
					}
				},
				error: function () {
					alert('Duplication failed.');
				}
			});
		});

		// Delete Timeline.
		$(document).on('click', '.wpcal-delete-timeline-btn', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			if (!id) return;

			var confirmMsg = (window.wpcalAdminData && window.wpcalAdminData.i18n && window.wpcalAdminData.i18n.confirmDeleteTL) || 'Are you sure you want to delete this entire timeline and all its items?';
			if (!confirm(confirmMsg)) {
				return;
			}

			var row = $(this).closest('tr');

			$.ajax({
				url: (window.wpcalAdminData && window.wpcalAdminData.ajaxUrl) || ajaxurl,
				type: 'POST',
				data: {
					action: 'wpcal_delete_timeline',
					id: id,
					nonce: (window.wpcalAdminData && window.wpcalAdminData.nonce) || ''
				},
				dataType: 'json',
				success: function (res) {
					if (res.success) {
						row.fadeOut(200, function () {
							row.remove();
						});
					} else {
						alert(res.data.message || 'Delete failed.');
					}
				},
				error: function () {
					alert('Delete failed.');
				}
			});
		});
	}

})(jQuery);
