<?php

class Season_CPT
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

	protected $post_type = 'obcal_season';

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
	
	public function register(){

		$labels = array(
			'name'					 => __( 'Seasons', 'open-booking-calendar' ),
			'singular_name'			 => __( 'Season', 'open-booking-calendar' ),
			'add_new'				 => _x( 'Add New', 'Add New Season', 'open-booking-calendar' ),
			'add_new_item'			 => __( 'Add New Season', 'open-booking-calendar' ),
			'edit_item'				 => __( 'Edit Season', 'open-booking-calendar' ),
			'new_item'				 => __( 'New Season', 'open-booking-calendar' ),
			'view_item'				 => __( 'View Season', 'open-booking-calendar' ),
			'search_items'			 => __( 'Search Season', 'open-booking-calendar' ),
			'not_found'				 => __( 'No seasons found', 'open-booking-calendar' ),
			'not_found_in_trash'	 => __( 'No seasons found in Trash', 'open-booking-calendar' ),
			'all_items'				 => __( 'Seasons', 'open-booking-calendar' ),
			'insert_into_item'		 => __( 'Insert into season description', 'open-booking-calendar' ),
			'uploaded_to_this_item'	 => __( 'Uploaded to this season', 'open-booking-calendar' )
		);

		$args = array(
			'labels'				 => $labels,
			'description'			 => __( 'This is where you can add new seasons.', 'open-booking-calendar' ),
			'public'				 => false,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'query_var'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'hierarchical'			 => false,
			'show_in_menu'			 => self::$mainAdminMenuSlug,
			'supports'				 => array( 'title' ),
			'hierarchical'			 => false,
		);

		register_post_type( $this->post_type, $args );
	}

}