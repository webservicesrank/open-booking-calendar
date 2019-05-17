<?php

class ContactLog_CPT
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

	protected $post_type = 'obcal_contact_log';

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

		$labels = array(
			'name'					 => __('Contact Log', 'open-booking-calendar'),
			'singular_name'			 => __('Contact Log', 'open-booking-calendar'),
			'add_new'				 => _x('Add New', 'Add New Contact Log', 'open-booking-calendar'),
			'add_new_item'			 => __('Add New Contact', 'open-booking-calendar'),
			'edit_item'				 => __('Edit Contact', 'open-booking-calendar'),
			'new_item'				 => __('New Contact', 'open-booking-calendar'),
			'view_item'				 => __('View Contact', 'open-booking-calendar'),
			'search_items'			 => __('Search Contact', 'open-booking-calendar'),
			'not_found'				 => __('No contacts found', 'open-booking-calendar'),
			'not_found_in_trash'	 => __('No contacts found in Trash', 'open-booking-calendar'),
			'all_items'				 => __('Contact Log', 'open-booking-calendar'),
			'insert_into_item'		 => __('Insert into contact description', 'open-booking-calendar'),
			'uploaded_to_this_item'	 => __('Uploaded to this contact', 'open-booking-calendar')
		);

		$args = array(
			'labels'				 => $labels,
			'description'			 => __('This is where you can add new contacts.', 'open-booking-calendar'),
			'public'				 => false,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'query_var'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'hierarchical'			 => false,
			'show_in_menu'			 => self::$mainAdminMenuSlug,
			'supports'				 => array('title'),
			'hierarchical'			 => false,
			//'register_meta_box_cb'	 => array( $this, 'registerMetaBoxes' ),
		);

		register_post_type($this->post_type, $args);
	}

	/**
	 * Register Contact in the Log
	 */
	public function insert_contact($name, $email)
	{
		// Return value
		$contact_id = '';

		if (!empty($name) && !empty($email)) {

			$email = sanitize_email($email);

			$query_args = [
				'meta_key' => '_obcal_contact_log_email',
				'meta_value' => $email,
				'post_type' => 'obcal_contact_log'
			];

			$query_result = new WP_Query($query_args);

			if (!$query_result->have_posts()) {

				$meta_input = [];

				$meta_input["_obcal_contact_log_email"] = $email;

				$contact_id = wp_insert_post([
					'post_title'    => ucwords(wp_strip_all_tags($_POST['us_name'])),
					'post_status'   => 'publish',
					'comment_status'  => 'closed',
					'ping_status'   => 'closed',
					'post_type'   => 'obcal_contact_log',
					'meta_input' => $meta_input
				]);

			} else {

				$query_result->the_post();
				$post = $query_result->post;

				$contact_id = $post->ID;

			}
		}

		return $contact_id;

	}

	/**
	 * Register Query in the Log
	 */
	public function insert_query_log($contact_id, $query_type, $query_data)
	{

		if (!empty($contact_id) && !empty($query_type) && !empty($query_data)) {

			$query_date = date_i18n('Y-m-d H:i:s');

			add_post_meta(
				$contact_id,
				"_obcal_contact_log_query",
				['query_type' => $query_type, 'query_date' => $query_date, 'post_array' => $query_data],
				false
			);

			update_post_meta(
				$contact_id,
				"_obcal_contact_log_last_query_date",
				$query_date
			);

		}
	}

}
