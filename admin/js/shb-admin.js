/**
 * Admin JavaScript
 *
 * @package SimpleHallBookingManager
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Slot modal handlers are in the view template for now
		// Future enhancements can add more dynamic functionality here

		// Confirm delete actions
		$('.delete-link').on('click', function(e) {
			if (!confirm(shbAdmin.i18n.confirmDelete)) {
				e.preventDefault();
				return false;
			}
		});

		// Auto-dismiss notices after 5 seconds
		setTimeout(function() {
			$('.notice.is-dismissible').fadeOut(300);
		}, 5000);
	});

})(jQuery);

