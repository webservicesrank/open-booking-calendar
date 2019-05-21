<?php

namespace OBCal;

class AccommodationType_CT
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

	public static $mainAdminMenuCapability = 'edit_posts';
	public static $mainAdminMenuSlug = 'open-booking-calendar';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $open_booking_calendar       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($open_booking_calendar, $version)
    {

        $this->open_booking_calendar = $open_booking_calendar;
        $this->version = $version;
    }
    
    public function register()
    {

        $labels = [
            'name'              => _x('Accommodation Types', 'taxonomy general name', 'open-booking-calendar'),
            'singular_name'     => _x('Accommodation Type', 'taxonomy singular name', 'open-booking-calendar'),
            'search_items'      => __('Search Accommodation Types', 'open-booking-calendar'),
            'all_items'         => __('All Types', 'open-booking-calendar'),
            'parent_item'       => __('Parent Accommodation Type', 'open-booking-calendar'),
            'parent_item_colon' => __('Parent Accommodation Type:', 'open-booking-calendar'),
            'edit_item'         => __('Edit Accommodation Type', 'open-booking-calendar'),
            'update_item'       => __('Update Accommodation Type', 'open-booking-calendar'),
            'add_new_item'      => __('Add New Accommodation Type', 'open-booking-calendar'),
            'new_item_name'     => __('New Accommodation Type Name', 'open-booking-calendar'),
            'menu_name'         => __('Accommodation Type', 'open-booking-calendar'),
        ];
        $args = [
            'hierarchical'      => true, // make it hierarchical (like categories)
            'labels'            => $labels,
            'show_ui'           => true,
            'show_in_menu'	    => self::$mainAdminMenuSlug,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [
                'slug' => 'types',
                'with_front'	 => false,
				'hierarchical'	 => true
            ],
            'show_in_rest'       => true
        ];
        register_taxonomy('obcal_accommodation_type', 'obcal_accommodation', $args);
                
    }
}
