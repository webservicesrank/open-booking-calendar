(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	// execute when the DOM is ready
	$(document).ready(function () {

		/**
		 * obc_booking_calendar
		 */

		var date_diff_indays = function(date1, date2) {
			var dt1 = new Date(date1);
			var dt2 = new Date(date2);
			return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
		}

		// flatpickr calendar in 'obc_booking_calendar' shortcode.
		if ($(".obcal-booking-calendar-csc .flatpickr-input").length) {
			$(".flatpickr-input").flatpickr({
				locale: flatpickr_l10n.locale,
				inline: $("#obc_cal_inline").val(),
				mode: $("#obc_cal_mode").val(),
				dateFormat: $("#obc_cal_dateFormat").val(),
				ariaDateFormat: $("#obc_cal_dateFormat").val(),
				minDate: $("#obc_cal_minDate").val(),
				maxDate: $("#obc_cal_maxDate").val(),
				enable: $("#obc_cal_enable").val().split(','),
				disable: JSON.parse($("#obc_cal_disable").val()),
				onReady: function(selectedDates, dateStr, instance) {
					$('.flatpickr-day.disabled').attr('title', flatpickr_l10n.out_season);
					$.each($("#obc_cal_reserved").val().split(','), function( index, value ){
						$('.flatpickr-day[aria-label="' + value + '"]').addClass('flatpickr-reserved-dates').attr('title', flatpickr_l10n.date_reserved);
					});
				},
				onChange: function(selectedDates, dateStr, instance) {
					$.each($("#obc_cal_reserved").val().split(','), function( index, value ){
						$('.flatpickr-day[aria-label="' + value + '"]').addClass('flatpickr-reserved-dates');
					});

					/**
					 * Show an error message if the number of selected nights is less than the minimum allowed
					 */

					var num_nights = date_diff_indays(selectedDates[0], selectedDates[1]);
					
					if (!isNaN(num_nights) && num_nights < parseInt($("#obc_cal_minNumNights").val())) {
						// Show message
						$('.obcal-booking-calendar-csc .min-num-nights-error').css('display', 'block');
					} else {
						// Hide message
						$('.obcal-booking-calendar-csc .min-num-nights-error').css('display', 'none');
					}

				},
			});
		}

		$(".obcal-booking-calendar-csc .availability-calendar-input").change(function(){
			$(".obcal-booking-calendar-csc .form-selected-date").val($(this).val());
		});

		$(".obcal-booking-calendar-csc .obcal-form #selected_date").click(function(){
			alert(flatpickr_l10n.select_in_calendar);
		});


		/**
		 * obc_search_accommodations
		 */

		// flatpickr calendar in 'obc_search_accommodations' shortcode.
		if ($(".obcal-search-accommodations-csc .flatpickr-input").length) {
			$(".obcal-search-accommodations-csc .flatpickr-input").flatpickr({
				locale: flatpickr_l10n.locale,
				inline: $("#obc_cal_inline").val(),
				static: true,
				mode: $("#obc_cal_mode").val(),
				dateFormat: $("#obc_cal_dateFormat").val(),
				ariaDateFormat: $("#obc_cal_dateFormat").val(),
				minDate: $("#obc_cal_minDate").val(),
				//maxDate: $("#obc_cal_maxDate").val(),
			});
		}

	});

})( jQuery );
