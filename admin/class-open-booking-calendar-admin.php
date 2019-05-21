<?php

namespace OBCal;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://webservicesrank.com
 * @since      1.0.0
 *
 * @package    Open_Booking_Calendar
 * @subpackage Open_Booking_Calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Open_Booking_Calendar
 * @subpackage Open_Booking_Calendar/admin
 * @author     Web Services Rank <support@webservicesrank.com>
 */
class Open_Booking_Calendar_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $open_booking_calendar    The ID of this plugin.
	 */
	private $open_booking_calendar;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $open_booking_calendar       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $open_booking_calendar, $version ) {

		$this->open_booking_calendar = $open_booking_calendar;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Open_Booking_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Open_Booking_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->open_booking_calendar, plugin_dir_url( __FILE__ ) . 'css/open-booking-calendar-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'flatpickr', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/css/flatpickr.min.css', array(), $this->version, 'all' );
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Open_Booking_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Open_Booking_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->open_booking_calendar, plugin_dir_url( __FILE__ ) . 'js/open-booking-calendar-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'flatpickr', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/js/flatpickr.min.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the Main Menu.
	 * 
	 * @since	1.0.0
	 */
	public function create_main_menu() {

		add_menu_page(
			__( 'Bookings', 'open-booking-calendar' ),
			__( 'Bookings', 'open-booking-calendar' ),
			\OBCal\Booking_CPT::$mainAdminMenuCapability,
			\OBCal\Booking_CPT::$mainAdminMenuSlug,
			'__return_false',
			'dashicons-calendar-alt',
			\OBCal\Booking_CPT::$mainAdminMenuPosition
		);
	}

}
 