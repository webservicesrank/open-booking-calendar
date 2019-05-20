(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

		// Horizontal separators in the main menu
		$("#toplevel_page_open-booking-calendar ul li")
		.parent().find("li:nth-child(4)").addClass("menu-top-separator")
		.parent().find("li:nth-child(6)").addClass("menu-top-separator");

		$(".season-start-date, .season-end-date").flatpickr({
			dateFormat: $("#obc_cal_dateFormat").val(),
		});

		$(".booking-check-in-date, .booking-check-out-date").flatpickr({
			dateFormat: $("#obc_cal_dateFormat").val(),
		});

	});

})( jQuery );
