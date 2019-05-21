<?php

namespace OBCal;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webservicesrank.com
 * @since      1.0.0
 *
 * @package    Open_Booking_Calendar
 * @subpackage Open_Booking_Calendar/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Open_Booking_Calendar
 * @subpackage Open_Booking_Calendar/includes
 * @author     Web Services Rank <support@webservicesrank.com>
 */
class Open_Booking_Calendar {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Open_Booking_Calendar_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $open_booking_calendar    The string used to uniquely identify this plugin.
	 */
	protected $open_booking_calendar;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The booking custom post type object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $booking_cpt    The booking custom post type object.
	 */
	private $booking_cpt;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'OPEN_BOOKING_CALENDAR_VERSION' ) ) {
			$this->version = OPEN_BOOKING_CALENDAR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->open_booking_calendar = 'open-booking-calendar';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_custom_post_types();
		$this->register_custom_taxonomies();
		$this->register_custom_meta_boxes();
		$this->register_custom_shortcodes();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Open_Booking_Calendar_Loader. Orchestrates the hooks of the plugin.
	 * - Open_Booking_Calendar_i18n. Defines internationalization functionality.
	 * - Open_Booking_Calendar_Admin. Defines all hooks for the admin area.
	 * - Open_Booking_Calendar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-open-booking-calendar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-open-booking-calendar-i18n.php';

		/**
		 * The classes responsible for register Custom Post Types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-customer-cpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-booking-cpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-accommodation-cpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-season-cpt.php';

		/**
		 * The classes responsible for register Custom Taxonomies.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/taxonomies/class-accommodation-type-ct.php';

		/**
		 * The classes responsible for register Custom Meta Boxes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-customer-cmb.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-season-cmb.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-accommodation-cmb.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-booking-cmb.php';

		/**
		 * The classes responsible for register Custom Shortcodes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/class-booking-calendar-csc.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/class-booking-preview-csc.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/class-booking-received-csc.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/class-search-accommodations-csc.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/class-search-results-csc.php';

		/**
		 * The classes responsible for register Menu Pages.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/menu-pages/class-help-menu-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/menu-pages/class-settings-menu-page.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-open-booking-calendar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-open-booking-calendar-public.php';

		$this->loader = new \OBCal\Open_Booking_Calendar_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Open_Booking_Calendar_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new \OBCal\Open_Booking_Calendar_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Add Custom Post Types.
	 * 
	 * @since	1.0.0
	 * @access	private
	 */
	private function register_custom_post_types() {

		/**
		 * Add 'Booking custom post type' actions an filters.
		 */
		$this->booking_cpt = new \OBCal\Booking_CPT( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $this->booking_cpt, 'register');

		/**
		 * Add 'Customer custom post type' actions an filters.
		 */
		$customer_cpt = new \OBCal\Customer_CPT( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $customer_cpt, 'register');

		/**
		 * Add 'Accommodation custom post type' actions an filters.
		 */
		$accommodation_cpt = new \OBCal\Accommodation_CPT( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $accommodation_cpt, 'register');

		/**
		 * Add 'Season custom post type' actions an filters.
		 */
		$season_cpt = new \OBCal\Season_CPT( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $season_cpt, 'register');

	}

	/**
	 * Add Custom Taxonomies.
	 * 
	 * @since	1.0.0
	 * @access	private
	 */
	private function register_custom_taxonomies() {

		/**
		 * Add 'Accommodation custom taxonomy' actions an filters.
		 */
		$accommodation_type_ct = new \OBCal\AccommodationType_CT( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $accommodation_type_ct, 'register');

	}

	/**
	 * Add Custom Meta Boxes.
	 * 
	 * @since	1.0.0
	 * @access	private
	 */
	private function register_custom_meta_boxes() {

		/**
		 * Add 'Customer custom meta boxes' actions an filters.
		 */
		$customer_cmb = new \OBCal\Customer_CMB( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'add_meta_boxes', $customer_cmb, 'register');
		$this->loader->add_action( 'save_post_obcal_customer', $customer_cmb, 'save');
		$this->loader->add_filter( 'manage_obcal_customer_posts_columns', $customer_cmb, 'add_table_custom_columns');
		$this->loader->add_action( 'manage_obcal_customer_posts_custom_column', $customer_cmb, 'add_table_custom_values', 10, 2);
		$this->loader->add_filter( 'manage_edit-obcal_customer_sortable_columns', $customer_cmb, 'register_table_sortable_columns');

		/**
		 * Add 'Season custom meta boxes' actions an filters.
		 */
		$season_cmb = new \OBCal\Season_CMB( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'add_meta_boxes', $season_cmb, 'register');
		$this->loader->add_action( 'save_post_obcal_season', $season_cmb, 'save');
		$this->loader->add_filter( 'manage_obcal_season_posts_columns', $season_cmb, 'add_table_custom_columns');
		$this->loader->add_action( 'manage_obcal_season_posts_custom_column', $season_cmb, 'add_table_custom_values', 10, 2);
		$this->loader->add_filter( 'manage_edit-obcal_season_sortable_columns', $season_cmb, 'register_table_sortable_columns');

		/**
		 * Add 'Accommodation custom meta boxes' actions an filters.
		 */
		$accommodation_cmb = new \OBCal\Accommodation_CMB( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'add_meta_boxes', $accommodation_cmb, 'register');
		$this->loader->add_action( 'save_post_obcal_accommodation', $accommodation_cmb, 'save');
		$this->loader->add_filter( 'manage_obcal_accommodation_posts_columns', $accommodation_cmb, 'add_table_custom_columns');
		$this->loader->add_action( 'manage_obcal_accommodation_posts_custom_column', $accommodation_cmb, 'add_table_custom_values', 10, 2);
		$this->loader->add_filter( 'manage_edit-obcal_accommodation_sortable_columns', $accommodation_cmb, 'register_table_sortable_columns');

		/**
		 * Add 'Booking custom meta boxes' actions an filters.
		 */
		$booking_cmb = new \OBCal\Booking_CMB( $this->get_open_booking_calendar(), $this->get_version(), $this->booking_cpt );
		$this->loader->add_action( 'add_meta_boxes', $booking_cmb, 'register');
		$this->loader->add_action( 'save_post_obcal_booking', $booking_cmb, 'save');
		$this->loader->add_action( 'admin_notices', $booking_cmb, 'print_plugin_admin_notices');
		$this->loader->add_filter( 'manage_obcal_booking_posts_columns', $booking_cmb, 'add_table_custom_columns');
		$this->loader->add_action( 'manage_obcal_booking_posts_custom_column', $booking_cmb, 'add_table_custom_values', 10, 2);
		$this->loader->add_filter( 'manage_edit-obcal_booking_sortable_columns', $booking_cmb, 'register_table_sortable_columns');

	}

	/**
	 * Add Custom Shortcodes.
	 * 
	 * @since	1.0.0
	 * @access	private
	 */
	private function register_custom_shortcodes() {

		/**
		 * Add 'Availability calendar and booking form custom shortcode' actions an filters.
		 */
		$booking_calendar_csc = new \OBCal\BookingCalendar_CSC( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $booking_calendar_csc, 'register');
		$this->loader->add_action( 'wp_enqueue_scripts', $booking_calendar_csc, 'localize_script', 11);
		$this->loader->add_action( 'wp_enqueue_scripts', $booking_calendar_csc, 'enqueue_flatpickr_styles', 11 );

		/**
		 * Add 'Booking preview custom shortcode' actions an filters.
		 */
		$booking_preview_csc = new \OBCal\BookingPreview_CSC( $this->get_open_booking_calendar(), $this->get_version(), $this->booking_cpt );
		$this->loader->add_action( 'init', $booking_preview_csc, 'register');

		/**
		 * Add 'Booking received custom shortcode' actions an filters.
		 */
		$booking_received_csc = new \OBCal\BookingReceived_CSC( $this->get_open_booking_calendar(), $this->get_version(), $this->booking_cpt );
		$this->loader->add_action( 'init', $booking_received_csc, 'register');

		/**
		 * Add 'Search Accommodations custom shortcode' actions an filters.
		 */
		$search_accommodations_csc = new \OBCal\SearchAccommodations_CSC( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $search_accommodations_csc, 'register');

		/**
		 * Add 'Search Results custom shortcode' actions an filters.
		 */
		$search_results_csc = new \OBCal\SearchResults_CSC( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'init', $search_results_csc, 'register');

	}

	/**
	 * Add all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new \OBCal\Open_Booking_Calendar_Admin( $this->get_open_booking_calendar(), $this->get_version() );

		/**
		 * Add 'Admin Javascript and CSS styles' actions an filters.
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * Add 'Plugin main menu' action.
		 */
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'create_main_menu');

		/**
		 * Add 'Help page' actions an filters.
		 */
		$help_admin = new \OBCal\HelpMenuPage( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $help_admin, 'create_submenu' );

		/**
		 * Add 'Settings page' actions an filters.
		 */
		$settings_admin = new \OBCal\SettingsMenuPage( $this->get_open_booking_calendar(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $settings_admin, 'create_submenu' );
		$this->loader->add_action( 'admin_init', $settings_admin, 'settings_init' );

	}

	/**
	 * Add all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new \OBCal\Open_Booking_Calendar_Public( $this->get_open_booking_calendar(), $this->get_version() );

		/**
		 * Add 'Public Javascript and CSS styles' actions an filters.
		 */
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_open_booking_calendar() {
		return $this->open_booking_calendar;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Open_Booking_Calendar_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
