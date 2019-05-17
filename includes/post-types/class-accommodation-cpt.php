<?php

class Accommodation_CPT
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

	protected $post_type = 'obcal_accommodation';

	public static $mainAdminMenuCapability = 'edit_posts';
	public static $mainAdminMenuSlug = 'open-booking-calendar';
	public static $mainAdminMenuPosition = 3.5;

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
	
	public function register(){

		$labels = array(
			'name'					 => __( 'Accommodations', 'open-booking-calendar' ),
			'singular_name'			 => __( 'Accommodation', 'open-booking-calendar' ),
			'add_new'				 => _x( 'Add New', 'Add New Accommodation', 'open-booking-calendar' ),
			'add_new_item'			 => __( 'Add New', 'open-booking-calendar' ),
			'edit_item'				 => __( 'Edit Accommodation', 'open-booking-calendar' ),
			'new_item'				 => __( 'New Accommodation', 'open-booking-calendar' ),
			'view_item'				 => __( 'View Accommodation', 'open-booking-calendar' ),
			'search_items'			 => __( 'Search Accommodation', 'open-booking-calendar' ),
			'not_found'				 => __( 'No accommodations found', 'open-booking-calendar' ),
			'not_found_in_trash'	 => __( 'No accommodations found in Trash', 'open-booking-calendar' ),
			'all_items'				 => __( 'Accommodations', 'open-booking-calendar' ),
			'insert_into_item'		 => __( 'Insert into accommodation description', 'open-booking-calendar' ),
			'uploaded_to_this_item'	 => __( 'Uploaded to this accommodation', 'open-booking-calendar' )
		);

		$args = array(
			'labels'				 => $labels,
			'description'			 => __( 'This is where you can add new accommodations to your hotel.', 'open-booking-calendar' ),
			'public'				 => true,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'query_var'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => true,
			'hierarchical'			 => false,
			'show_in_menu'			 => self::$mainAdminMenuSlug, //'edit.php?post_type=obcal_accomm_type', //MPHB()->post_types()->roomType()->getMenuSlug(),
			'supports'				 => [ 'title', 'excerpt', 'thumbnail' ],
			//'register_meta_box_cb'	 => array( $this, 'registerMetaBoxes' ),
		);

		register_post_type( $this->post_type, $args );
	}

}