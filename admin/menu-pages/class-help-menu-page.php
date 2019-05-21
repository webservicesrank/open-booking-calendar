<?php

namespace OBCal;

class HelpMenuPage
{

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

	public static $mainAdminMenuCapability = 'manage_options';
	public static $mainAdminMenuSlug = 'open-booking-calendar';

    /**
     * Initialize the class and set its properties.
     *
     * @since      1.0.0
     * @param      string    $open_booking_calendar     The name of the plugin.
     * @param      string    $version                   The version of this plugin.
     */
    public function __construct($open_booking_calendar, $version)
    {

        $this->open_booking_calendar = $open_booking_calendar;
        $this->version = $version;
    }

    /**
     * Create Help page submenu.
     *
     * @since    1.0.0
     */
    public function create_submenu() {

		add_submenu_page(
			self::$mainAdminMenuSlug, // parent_slug
			__( 'Get started and Help', 'open-booking-calendar' ), // page_title
			__( 'Get Started & Help', 'open-booking-calendar' ), // menu_title
			self::$mainAdminMenuCapability, // capability
			'obc-help', // menu_slug
			[$this, 'html'] // function
        );
        
    }

    /**
     * HTML content for Help page.
     *
     * @since    1.0.0
     */
    public function html() {

        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/menu-page-help-display.php';

    }

}