/**
 * Frontend JavaScript
 *
 * @package SimpleHallBookingManager
 */

(function ($) {
	'use strict';

	var SHB = {
		selectedDates: [],
		dateSlots: {}, // Store slot_id for each date: {date: slot_id}
		availableSlotsByDate: {}, // Cache available slots per date

		init: function () {
			this.bindEvents();
			this.initCalendar();
		},

		bindEvents: function () {
			// Check availability when hall changes
			$('#shb_hall_id').on('change', this.onHallChange.bind(this));

			// Submit booking form
			$('#shb-booking-form').on('submit', this.submitBooking);
		},

		onHallChange: function () {
			// Clear previous selections when hall changes
			this.selectedDates = [];
			this.dateSlots = {};
			this.availableSlotsByDate = {};

			// Re-render calendar with new hall
			this.renderCalendar();
			this.updateSelectedDatesDisplay();
		},

		initCalendar: function () {
			// Initialize calendar
			this.selectedDates = [];
			this.dateSlots = {};
			this.availableSlotsByDate = {};
			this.renderCalendar();
		},

		// Auto-detect if booking is single or multi-day based on selection
		isMultiday: function () {
			return this.selectedDates.length > 1;
		},

		renderCalendar: function () {
			var self = this;
			var $calendar = $('#shb-calendar');

			// Check if hall is selected
			var hallId = $('#shb_hall_id').val();
			if (!hallId) {
				$calendar.html('<p class="shb-calendar-placeholder">Please select a hall first to choose dates.</p>');
				return;
			}

			// Clear availability data when hall changes
			this.availableSlotsByDate = {};

			var today = new Date();
			var currentMonth = today.getMonth();
			var currentYear = today.getFullYear();

			// Simple calendar HTML
			var html = '<div class="shb-calendar-header">';
			html += '<button type="button" class="shb-cal-prev" data-month="' + currentMonth + '" data-year="' + currentYear + '">«</button>';
			html += '<span class="shb-cal-month-year">' + self.getMonthName(currentMonth) + ' ' + currentYear + '</span>';
			html += '<button type="button" class="shb-cal-next" data-month="' + currentMonth + '" data-year="' + currentYear + '">»</button>';
			html += '</div>';
			html += '<div class="shb-calendar-grid" data-month="' + currentMonth + '" data-year="' + currentYear + '">';
			html += self.generateCalendarDays(currentYear, currentMonth);
			html += '</div>';

			$calendar.html(html);

			// Add legend below calendar
			this.renderCalendarLegend();

			// Bind calendar events using event delegation
			$calendar.off('click').on('click', '.shb-cal-day:not(.disabled):not(.empty)', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var $day = $(this);

				// Double-check if date is disabled (safety check)
				if ($day.hasClass('disabled') || $day.hasClass('fully-booked')) {
					console.log('SHB: Prevented click on disabled/fully-booked date');
					return false;
				}

				var dateStr = $day.data('date');
				console.log('SHB: Date clicked:', dateStr);
				self.toggleDate(dateStr, $day);
			});

			// Bind navigation buttons
			$calendar.off('click', '.shb-cal-prev, .shb-cal-next').on('click', '.shb-cal-prev, .shb-cal-next', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var $btn = $(this);
				var month = parseInt($btn.closest('.shb-calendar-header').find('.shb-cal-prev').data('month'));
				var year = parseInt($btn.closest('.shb-calendar-header').find('.shb-cal-prev').data('year'));

				console.log('SHB: Navigation clicked, current:', month, year);

				if ($(this).hasClass('shb-cal-next')) {
					month++;
					if (month > 11) {
						month = 0;
						year++;
					}
				} else {
					month--;
					if (month < 0) {
						month = 11;
						year--;
					}
				}

				console.log('SHB: Navigating to:', month, year);
				self.updateCalendar(year, month);
			});

			// Preload availability for the current month
			this.preloadMonthAvailability(currentYear, currentMonth);
		},

		generateCalendarDays: function (year, month) {
			var self = this;
			var html = '';
			var firstDay = new Date(year, month, 1).getDay();
			var daysInMonth = new Date(year, month + 1, 0).getDate();
			var today = new Date();
			today.setHours(0, 0, 0, 0);

			// Day headers
			var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
			days.forEach(function (day) {
				html += '<div class="shb-cal-day-header">' + day + '</div>';
			});

			// Empty cells before first day
			for (var i = 0; i < firstDay; i++) {
				html += '<div class="shb-cal-day empty"></div>';
			}

			// Days of month
			for (var day = 1; day <= daysInMonth; day++) {
				var date = new Date(year, month, day);
				var dateStr = this.formatDate(date);
				var isSelected = this.selectedDates.indexOf(dateStr) !== -1;
				var isPast = date < today;

				// Check booking status based on available slots
				var bookingStatus = '';
				var statusIndicator = '';
				var isFullyBooked = false;

				if (!isPast && self.availableSlotsByDate[dateStr]) {
					var slots = self.availableSlotsByDate[dateStr];
					if (slots.length === 0) {
						bookingStatus = 'fully-booked';
						statusIndicator = '<span class="shb-date-indicator shb-indicator-full" title="Fully Booked"></span>';
						isFullyBooked = true;
					} else if (slots.length > 0 && slots.length <= 2) {
						bookingStatus = 'partially-booked';
						statusIndicator = '<span class="shb-date-indicator shb-indicator-partial" title="Limited Availability"></span>';
					} else {
						bookingStatus = 'available';
						statusIndicator = '<span class="shb-date-indicator shb-indicator-available" title="Available"></span>';
					}
				}

				var classes = 'shb-cal-day';
				if (isPast || isFullyBooked) classes += ' disabled';
				if (isSelected) classes += ' selected';
				if (bookingStatus) classes += ' ' + bookingStatus;

				html += '<button type="button" class="' + classes + '" data-date="' + dateStr + '">';
				html += '<span class="shb-date-number">' + day + '</span>';
				html += statusIndicator;
				html += '</button>';
			}

			return html;
		},

		updateCalendar: function (year, month) {
			var $calendar = $('#shb-calendar');
			var $header = $calendar.find('.shb-calendar-header');
			var $grid = $calendar.find('.shb-calendar-grid');

			$header.find('.shb-cal-month-year').text(this.getMonthName(month) + ' ' + year);
			$header.find('.shb-cal-prev, .shb-cal-next').data({ month: month, year: year });
			$grid.data({ month: month, year: year });
			$grid.html(this.generateCalendarDays(year, month));

			console.log('SHB: Calendar updated to', this.getMonthName(month), year);
			console.log('SHB: Currently selected dates:', this.selectedDates);

			// Preload availability for visible dates
			this.preloadMonthAvailability(year, month);

			// No need to rebind click events - event delegation from renderCalendar handles it!
		},

		preloadMonthAvailability: function (year, month) {
			var self = this;
			var hallId = $('#shb_hall_id').val();

			if (!hallId) {
				return;
			}

			// Get all dates in the month
			var daysInMonth = new Date(year, month + 1, 0).getDate();
			var dates = [];
			var today = new Date();
			today.setHours(0, 0, 0, 0);

			for (var day = 1; day <= daysInMonth; day++) {
				var date = new Date(year, month, day);
				if (date >= today) {
					dates.push(this.formatDate(date));
				}
			}

			if (dates.length === 0) {
				return;
			}

			console.log('SHB: Preloading availability for', dates.length, 'dates in', this.getMonthName(month), year);

			// Show loading state
			var $calendar = $('#shb-calendar');
			$calendar.addClass('shb-loading-availability');

			// Fetch availability for all dates in the month
			$.ajax({
				url: shbFrontend.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'shb_check_availability',
					nonce: shbFrontend.nonce,
					hall_id: hallId,
					dates: dates
				},
				success: function (response) {
					console.log('SHB: Preload response:', response);

					if (response.success && response.data && response.data.dates) {
						// Store availability for each date
						$.each(response.data.dates, function (dateStr, dateData) {
							self.availableSlotsByDate[dateStr] = dateData.available_slots || [];
						});

						// Re-render calendar with availability data
						var $grid = $('#shb-calendar').find('.shb-calendar-grid');
						var currentMonth = parseInt($grid.data('month'));
						var currentYear = parseInt($grid.data('year'));
						$grid.html(self.generateCalendarDays(currentYear, currentMonth));

						console.log('SHB: Availability preloaded and calendar updated');
						console.log('SHB: Available slots by date:', self.availableSlotsByDate);
					}

					// Remove loading state
					$calendar.removeClass('shb-loading-availability');
				},
				error: function (xhr, status, error) {
					console.error('SHB: Preload availability error:', error);
					$calendar.removeClass('shb-loading-availability');
				}
			});
		},

		renderCalendarLegend: function () {
			var $calendar = $('#shb-calendar');
			var $existingLegend = $calendar.find('.shb-calendar-legend');

			// Remove existing legend if any
			if ($existingLegend.length) {
				$existingLegend.remove();
			}

			var legendHtml = '<div class="shb-calendar-legend">';
			legendHtml += '<div class="shb-legend-item">';
			legendHtml += '<span class="shb-legend-indicator shb-indicator-available"></span>';
			legendHtml += '<span class="shb-legend-label">Available</span>';
			legendHtml += '</div>';
			legendHtml += '<div class="shb-legend-item">';
			legendHtml += '<span class="shb-legend-indicator shb-indicator-partial"></span>';
			legendHtml += '<span class="shb-legend-label">Limited</span>';
			legendHtml += '</div>';
			legendHtml += '<div class="shb-legend-item">';
			legendHtml += '<span class="shb-legend-indicator shb-indicator-full"></span>';
			legendHtml += '<span class="shb-legend-label">Fully Booked</span>';
			legendHtml += '</div>';
			legendHtml += '<div class="shb-legend-item">';
			legendHtml += '<span class="shb-legend-indicator shb-indicator-selected"></span>';
			legendHtml += '<span class="shb-legend-label">Selected</span>';
			legendHtml += '</div>';
			legendHtml += '</div>';

			$calendar.append(legendHtml);
		},

		toggleDate: function (dateStr, $dayEl) {
			var self = this;
			var index = this.selectedDates.indexOf(dateStr);

			// Check if date is fully booked (prevent selection)
			if (index === -1) {
				// Only check when adding (not removing)
				if (this.availableSlotsByDate[dateStr] && this.availableSlotsByDate[dateStr].length === 0) {
					console.log('SHB: Date is fully booked, cannot select:', dateStr);
					alert('This date is fully booked. Please select a different date.');
					return;
				}
			}

			if (index !== -1) {
				// Remove date
				this.selectedDates.splice(index, 1);
				delete this.dateSlots[dateStr];
				// Don't delete availableSlotsByDate - keep it for indicator
				$dayEl.removeClass('selected');
			} else {
				// Add date
				this.selectedDates.push(dateStr);
				$dayEl.addClass('selected');

				// Fetch available slots for this date
				this.fetchSlotsForDate(dateStr);
			}

			// Sort dates
			this.selectedDates.sort();

			// Update display
			this.updateSelectedDatesDisplay();
		},

		fetchSlotsForDate: function (dateStr) {
			var self = this;
			var hallId = $('#shb_hall_id').val();

			if (!hallId) {
				return;
			}

			// Show loading for this date
			this.updateSelectedDatesDisplay();

			$.ajax({
				url: shbFrontend.ajaxUrl,
				type: 'POST',
				data: {
					action: 'shb_check_availability',
					nonce: shbFrontend.nonce,
					hall_id: hallId,
					date: dateStr
				},
				success: function (response) {
					if (response.success && response.data.slots) {
						self.availableSlotsByDate[dateStr] = response.data.slots;
					} else {
						self.availableSlotsByDate[dateStr] = [];
					}
					self.updateSelectedDatesDisplay();
				},
				error: function () {
					self.availableSlotsByDate[dateStr] = [];
					self.updateSelectedDatesDisplay();
				}
			});
		},

		updateSelectedDatesDisplay: function () {
			var self = this;
			var $displayContainer = $('#shb-selected-dates-display');
			var $container = $('#shb-selected-dates-with-slots');
			var $count = $('#shb-dates-count');

			if (this.selectedDates.length === 0) {
				// No dates selected - hide display
				$displayContainer.hide();
				$container.html('');
				$count.text('0');
				return;
			}

			// Show the selected dates display
			$displayContainer.show();

			// Single date - show radio buttons for slots
			if (this.selectedDates.length === 1) {
				var dateStr = this.selectedDates[0];
				var dateObj = new Date(dateStr + 'T00:00:00');
				var formatted = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

				var html = '<div class="shb-single-date-selection">';
				html += '<input type="hidden" name="booking_date" value="' + dateStr + '">';
				html += '<div class="shb-date-info">';
				html += '<strong>' + formatted + '</strong>';
				html += '<button type="button" class="shb-remove-date" data-date="' + dateStr + '" title="Remove date">×</button>';
				html += '</div>';

				// Show required notice at the top (only once, not in the grid)
				html += '<p class="shb-select-required">* Please select a time slot (Required)</p>';

				// Show slots if available
				if (self.availableSlotsByDate[dateStr]) {
					var slots = self.availableSlotsByDate[dateStr];
					if (slots.length > 0) {
						html += '<div class="shb-slot-options">';
						slots.forEach(function (slot) {
							var checked = self.dateSlots[dateStr] == slot.id ? ' checked' : '';
							var slotClass = 'shb-slot-option shb-slot-available';

							html += '<div class="' + slotClass + '">';
							html += '<input type="radio" name="slot_id" id="slot_' + slot.id + '" value="' + slot.id + '" data-date="' + dateStr + '" required' + checked + '>';
							html += '<label for="slot_' + slot.id + '">';
							html += '<span class="shb-availability-indicator shb-available"></span>';
							html += '<span class="shb-slot-label">' + slot.label + '</span>';
							html += '<span class="shb-slot-time">' + slot.start_time + ' - ' + slot.end_time + '</span>';
							html += '<span class="shb-slot-status">Available</span>';
							html += '</label>';
							html += '</div>';
						});
						html += '</div>';
					} else {
						html += '<p class="shb-no-slots"><strong>⚠️ No time slots available</strong><br><small>All slots are booked for this date</small></p>';
					}
				} else {
					html += '<p class="shb-loading-slots">⏳ Loading available time slots...</p>';
				}

				html += '</div>';
				$container.html(html);
				$count.text('1');

				// Bind events for slot selection
				$container.find('input[name="slot_id"]').on('change', function () {
					var date = $(this).data('date');
					var slotId = $(this).val();
					self.dateSlots[date] = slotId;
					console.log('SHB: Selected slot', slotId, 'for date', date);
				});

				// Bind remove button
				$container.find('.shb-remove-date').on('click', function (e) {
					e.preventDefault();
					var date = $(this).data('date');
					self.removeDateFromSelection(date);
				});

			} else {
				// Multiple dates - show dropdown for each date
				var html = '<div class="shb-multiday-selection">';

				// Add header row
				html += '<div class="shb-dates-header">';
				html += '<div>Date</div>';
				html += '<div>Time Slot</div>';
				html += '<div style="width: 36px;"></div>';
				html += '</div>';

				this.selectedDates.forEach(function (dateStr) {
					var dateObj = new Date(dateStr + 'T00:00:00');
					var formatted = dateObj.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });

					html += '<div class="shb-date-slot-row" data-date="' + dateStr + '">';

					// Date column
					html += '<div class="shb-date-label">';
					html += '<strong>' + formatted + '</strong>';
					html += '</div>';

					// Dropdown column
					html += '<div class="shb-date-slot-selector">';

					// Show slots if available
					if (self.availableSlotsByDate[dateStr]) {
						var slots = self.availableSlotsByDate[dateStr];
						if (slots.length > 0) {
							html += '<select name="date_slots[' + dateStr + ']" class="shb-slot-select shb-slot-required" data-date="' + dateStr + '" required>';
							html += '<option value="" disabled ' + (self.dateSlots[dateStr] ? '' : 'selected') + '>* Select time slot (Required)</option>';
							slots.forEach(function (slot) {
								var selected = self.dateSlots[dateStr] == slot.id ? ' selected' : '';
								html += '<option value="' + slot.id + '"' + selected + ' data-available="true">';
								html += '✓ ' + slot.label + ' (' + slot.start_time + ' - ' + slot.end_time + ')';
								html += '</option>';
							});
							html += '</select>';
						} else {
							html += '<span class="shb-no-slots">⚠️ All slots booked</span>';
						}
					} else {
						html += '<span class="shb-loading-slots">⏳ Loading...</span>';
					}

					html += '</div>';

					// Delete button column
					html += '<button type="button" class="shb-remove-date" data-date="' + dateStr + '" title="Remove date">×</button>';

					html += '</div>';
				});
				html += '</div>';
				$container.html(html);
				$count.text(this.selectedDates.length);

				// Bind events for slot selection and date removal
				$container.find('.shb-slot-select').on('change', function () {
					var date = $(this).data('date');
					var slotId = $(this).val();
					self.dateSlots[date] = slotId;
					console.log('SHB: Selected slot', slotId, 'for date', date);
				});

				$container.find('.shb-remove-date').on('click', function (e) {
					e.preventDefault();
					var date = $(this).data('date');
					self.removeDateFromSelection(date);
				});
			}
		},

		removeDateFromSelection: function (dateStr) {
			var index = this.selectedDates.indexOf(dateStr);
			if (index !== -1) {
				this.selectedDates.splice(index, 1);
				delete this.dateSlots[dateStr];
				delete this.availableSlotsByDate[dateStr];

				// Update calendar UI
				$('.shb-cal-day[data-date="' + dateStr + '"]').removeClass('selected');

				// Update display
				this.updateSelectedDatesDisplay();
			}
		},

		clearMultidayHiddenFields: function () {
			// Remove all hidden date fields when switching back to single-day
			var $form = $('#shb-booking-form');
			$form.find('input[name="booking_dates[]"]').remove();
			$form.find('select[name^="date_slots"]').remove();

			console.log('SHB: Cleared all multi-day date fields');
		},

		formatDate: function (date) {
			var year = date.getFullYear();
			var month = ('0' + (date.getMonth() + 1)).slice(-2);
			var day = ('0' + date.getDate()).slice(-2);
			return year + '-' + month + '-' + day;
		},

		getMonthName: function (month) {
			var months = ['January', 'February', 'March', 'April', 'May', 'June',
				'July', 'August', 'September', 'October', 'November', 'December'];
			return months[month];
		},

		checkAvailability: function (e) {
			// This function is kept for backward compatibility but is no longer used
			// Availability is now checked per-date when dates are selected in the calendar
			return;

			$.ajax({
				url: shbFrontend.ajaxUrl,
				type: 'POST',
				data: {
					action: 'shb_check_availability',
					nonce: shbFrontend.nonce,
					hall_id: hallId,
					date: date
				},
				success: function (response) {
					if (response.success && response.data.slots) {
						SHB.displaySlots(response.data.slots);
					} else {
						var message = response.data.message || shbFrontend.i18n.noSlotsAvailable;
						$container.html('<p class="shb-slots-placeholder">' + message + '</p>');
					}
				},
				error: function () {
					$container.html('<p class="shb-slots-placeholder" style="color: #d63638;">' + shbFrontend.i18n.error + '</p>');
				}
			});
		},

		displaySlots: function (slots) {
			var $container = $('#shb-slots-container');

			if (slots.length === 0) {
				$container.html('<p class="shb-slots-placeholder">' + shbFrontend.i18n.noSlotsAvailable + '</p>');
				return;
			}

			var html = '<div class="shb-slot-options">';

			$.each(slots, function (index, slot) {
				html += '<div class="shb-slot-option">';
				html += '<input type="radio" name="slot_id" id="slot_' + slot.id + '" value="' + slot.id + '" required>';
				html += '<label for="slot_' + slot.id + '">';
				html += '<span class="shb-slot-label">' + slot.label + '</span>';
				html += '<span class="shb-slot-time">' + slot.start_time + ' - ' + slot.end_time + '</span>';
				html += '</label>';
				html += '</div>';
			});

			html += '</div>';

			$container.html(html);
		},

		submitBooking: function (e) {
			e.preventDefault();

			var $form = $(this);
			var $button = $('#shb-submit-booking');
			var $messages = $('.shb-form-messages');

			var isMultiday = SHB.isMultiday();

			// Debug: Log form data
			console.log('SHB: Form submission started');
			console.log('SHB: Is multiday?', isMultiday);
			console.log('SHB: Selected dates:', SHB.selectedDates);

			// Check required fields
			var hallId = $('#shb_hall_id').val();
			var customerName = $('#shb_customer_name').val();
			var customerEmail = $('#shb_customer_email').val();

			console.log('SHB: Hall ID:', hallId, '(type:', typeof hallId + ')');
			console.log('SHB: Name:', customerName);
			console.log('SHB: Email:', customerEmail);

			if (!hallId) {
				SHB.showMessage('error', 'Please select a hall.');
				return false;
			}

			// Validate dates and slots
			if (SHB.selectedDates.length === 0) {
				SHB.showMessage('error', 'Please select at least one date from the calendar.');
				return false;
			}

			// Validate that all dates have slots selected
			var missingSlots = [];
			SHB.selectedDates.forEach(function (date) {
				if (!SHB.dateSlots[date]) {
					missingSlots.push(date);
				}
			});

			if (missingSlots.length > 0) {
				SHB.showMessage('error', 'Please select a time slot for all selected dates.');
				return false;
			}

			if (!customerName) {
				SHB.showMessage('error', 'Please enter your name.');
				return false;
			}

			if (!customerEmail) {
				SHB.showMessage('error', 'Please enter your email address.');
				return false;
			}

			// Disable button and show loading
			$button.prop('disabled', true).html('<span class="shb-spinner"></span> ' + shbFrontend.i18n.loading);
			$messages.empty();

			var formData = $form.serialize();
			formData += '&action=shb_submit_booking';

			console.log('SHB: Sending AJAX request...');
			console.log('SHB: URL:', shbFrontend.ajaxUrl);
			console.log('SHB: Form data:', formData);
			if (isMultiday) {
				console.log('SHB: Multiday booking with', SHB.selectedDates.length, 'dates');
				console.log('SHB: Date slots:', SHB.dateSlots);
				// Log what's actually in the form
				$form.find('select[name^="date_slots"]').each(function () {
					console.log('SHB: Found slot select:', $(this).attr('name'), '=', $(this).val());
				});
			} else {
				var slotId = $('input[name="slot_id"]:checked').val();
				console.log('SHB: Single-day booking - Slot ID:', slotId);
			}

			// Function to submit the form with optional reCAPTCHA token
			var submitForm = function (recaptchaToken) {
				var data = formData;
				if (recaptchaToken) {
					data += '&recaptcha_token=' + encodeURIComponent(recaptchaToken);
				}

				$.ajax({
					url: shbFrontend.ajaxUrl,
					type: 'POST',
					data: data,
					success: function (response) {
						console.log('SHB: AJAX Response:', response);

						if (response.success) {
							console.log('SHB: Booking successful!');

							if (response.data.redirect_url) {
								window.location.href = response.data.redirect_url;
								return;
							}

							SHB.showMessage('success', response.data.message);
							$form[0].reset();
							$('#shb-slots-container').html('<p class="shb-slots-placeholder">' + shbFrontend.i18n.selectSlot + '</p>');

							// Show access URL
							if (response.data.access_url) {
								var accessHtml = '<div class="shb-notice shb-notice-info" style="margin-top: 15px;">';
								accessHtml += '<p><strong>Booking Received!</strong></p>';
								if (response.data.remarks) {
									accessHtml += '<p><strong>Your Remarks:</strong><br><small>' + response.data.remarks.replace(/\n/g, '<br>') + '</small></p>';
								}
								accessHtml += '<p style="margin: 15px 0;"><a href="' + response.data.access_url + '" target="_blank" class="shb-btn shb-btn-primary" style="color: #fff; text-decoration: none;">View Your Booking</a></p>';
								accessHtml += '<p><small>Please save this link to view or manage your booking.</small></p>';
								accessHtml += '</div>';
								$messages.append(accessHtml);
							}

							// Scroll to messages
							$('html, body').animate({
								scrollTop: $messages.offset().top - 100
							}, 500);
						} else {
							console.log('SHB Error:', response.data);
							var errorMsg = response.data && response.data.message ? response.data.message : shbFrontend.i18n.error;
							SHB.showMessage('error', errorMsg);
						}
					},
					error: function (xhr, status, error) {
						console.error('SHB AJAX Error:', status, error);
						console.error('SHB Response:', xhr.responseText);

						var errorMsg = shbFrontend.i18n.error;

						// Try to parse error response
						try {
							var response = JSON.parse(xhr.responseText);
							if (response.data && response.data.message) {
								errorMsg = response.data.message;
							}
						} catch (e) {
							console.error('SHB: Could not parse error response');
						}

						SHB.showMessage('error', errorMsg);
					},
					complete: function () {
						console.log('SHB: AJAX request complete');
						$button.prop('disabled', false).text($button.data('original-text') || 'Submit Booking Request');
					}
				});
			};

			// Check if reCAPTCHA is enabled
			if (shbFrontend.recaptchaEnabled === 'yes' && shbFrontend.recaptchaSiteKey && typeof grecaptcha !== 'undefined') {
				console.log('SHB: Generating reCAPTCHA token...');
				grecaptcha.ready(function () {
					grecaptcha.execute(shbFrontend.recaptchaSiteKey, { action: 'booking_submit' })
						.then(function (token) {
							console.log('SHB: reCAPTCHA token generated');
							submitForm(token);
						})
						.catch(function (error) {
							console.error('SHB: reCAPTCHA error:', error);
							SHB.showMessage('error', 'reCAPTCHA verification failed. Please try again.');
							$button.prop('disabled', false).text($button.data('original-text') || 'Submit Booking Request');
						});
				});
			} else {
				// Submit without reCAPTCHA
				submitForm(null);
			}

			return false;
		},

		showMessage: function (type, message) {
			var $messages = $('.shb-form-messages');
			var html = '<div class="shb-notice shb-notice-' + type + '">';
			html += '<p>' + message + '</p>';
			html += '</div>';

			$messages.html(html);

			// Scroll to message
			$('html, body').animate({
				scrollTop: $messages.offset().top - 100
			}, 500);

			// Auto-hide success messages after 10 seconds
			if (type === 'success') {
				setTimeout(function () {
					$messages.fadeOut(300, function () {
						$(this).empty().show();
					});
				}, 10000);
			}
		}
	};

	// Initialize when DOM is ready
	$(document).ready(function () {
		// Store original button text
		$('#shb-submit-booking').data('original-text', $('#shb-submit-booking').text());

		SHB.init();
	});

})(jQuery);

