/**
 * Admin JavaScript
 *
 * @package SimpleHallBookingManager
 */

(function ($) {
	'use strict';

	$(document).ready(function () {
		// Slot modal handlers are in the view template for now
		// Future enhancements can add more dynamic functionality here

		// Confirm delete actions
		$('.delete-link').on('click', function (e) {
			if (!confirm(shbAdmin.i18n.confirmDelete)) {
				e.preventDefault();
				return false;
			}
		});

		// Auto-dismiss notices after 5 seconds
		setTimeout(function () {
			$('.notice.is-dismissible').fadeOut(300);
		}, 5000);
	});

	/**
	 * Global function to fetch slot details via AJAX
	 * 
	 * @param {number} slotId   The slot ID (0 for new)
	 * @param {number} hallId   The hall ID
	 * @param {string} slotType The slot type (optional)
	 * @param {function} success Callback on success
	 * @param {function} error   Callback on error
	 */
	window.shb_get_slot = function (slotId, hallId, slotType, success, error) {
		$.ajax({
			url: shbAdmin.ajaxUrl,
			type: 'POST',
			data: {
				action: 'shb_get_slot',
				nonce: shbAdmin.nonce,
				slot_id: slotId,
				hall_id: hallId,
				slot_type: slotType
			},
			success: function (response) {
				if (response.success) {
					if (typeof success === 'function') {
						success(response.data);
					}
				} else {
					if (typeof error === 'function') {
						error(response.data.message || 'Error fetching slot details');
					} else {
						alert(response.data.message || 'Error fetching slot details');
					}
				}
			},
			error: function () {
				if (typeof error === 'function') {
					error('Connection error while fetching slot details');
				} else {
					alert('Connection error. Please try again.');
				}
			}
		});
	};

	// Settings Page: Email Template Handlers
	$(document).ready(function () {
		// Insert Placeholder
		$('.shb-insert-placeholder').on('click', function (e) {
			e.preventDefault();
			var targetId = $(this).data('target');
			var placeholder = $(this).data('value');

			// Check if TinyMCE is defined and the instance exists (Visual mode)
			if (typeof tinyMCE !== 'undefined' && tinyMCE.get(targetId) && !tinyMCE.get(targetId).isHidden()) {
				tinyMCE.get(targetId).execCommand('mceInsertContent', false, placeholder);
			} else {
				// Text mode or TinyMCE disabled
				var textarea = $('#' + targetId);
				if (textarea.length) {
					var start = textarea[0].selectionStart;
					var end = textarea[0].selectionEnd;
					var text = textarea.val();
					textarea.val(text.substring(0, start) + placeholder + text.substring(end));

					// Refocus
					textarea[0].selectionStart = textarea[0].selectionEnd = start + placeholder.length;
					textarea.focus();
				}
			}
		});

		// Reset Template
		$('.shb-reset-template').on('click', function (e) {
			e.preventDefault();
			if (!confirm(shbAdmin.i18n.confirmReset || 'Are you sure you want to reset this template to default?')) {
				return;
			}

			var targetId = $(this).data('target');
			// We store the default content in a hidden div or data attribute. 
			// Since HTML can be large, it's better to fetch it from a hidden script tag or similar container 
			// referenced by data-default-container.
			var defaultContainerId = $(this).data('default-container');
			var defaultContent = $('#' + defaultContainerId).html();

			if (typeof tinyMCE !== 'undefined' && tinyMCE.get(targetId) && !tinyMCE.get(targetId).isHidden()) {
				tinyMCE.get(targetId).setContent(defaultContent);
			} else {
				$('#' + targetId).val(defaultContent);
			}
		});
	});

})(jQuery);

