<?php

class Booking_CPT
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

	protected $post_type = 'obcal_booking';

	public static $mainAdminMenuCapability = 'edit_posts';
	public static $mainAdminMenuSlug = 'open-booking-calendar';
	public static $mainAdminMenuPosition = 7;

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
			'name'					 => __('Bookings', 'open-booking-calendar'),
			'singular_name'			 => __('Booking', 'open-booking-calendar'),
			'add_new'				 => _x('Add New', 'Add New Booking', 'open-booking-calendar'),
			'add_new_item'			 => __('Add New Booking', 'open-booking-calendar'),
			'edit_item'				 => __('Edit Booking', 'open-booking-calendar'),
			'new_item'				 => __('New Booking', 'open-booking-calendar'),
			'view_item'				 => __('View Booking', 'open-booking-calendar'),
			'search_items'			 => __('Search Booking', 'open-booking-calendar'),
			'not_found'				 => __('No bookings found', 'open-booking-calendar'),
			'not_found_in_trash'	 => __('No bookings found in Trash', 'open-booking-calendar'),
			'all_items'				 => __('All Bookings', 'open-booking-calendar'),
			'insert_into_item'		 => __('Insert into booking description', 'open-booking-calendar'),
			'uploaded_to_this_item'	 => __('Uploaded to this booking', 'open-booking-calendar')
		);

		$args = array(
			'labels'				 => $labels,
			'map_meta_cap'			 => true,
			'public'				 => false,
			'exclude_from_search'	 => true,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'query_var'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'hierarchical'			 => false,
			'show_in_menu'			 => self::$mainAdminMenuSlug,
			'supports'				 => ['title'],
			//'register_meta_box_cb'	 => array( $this, 'registerMetaBoxes' ),
			//'capabilities'			 => array(
			//'create_posts' => 'do_not_allow',
			//),
		);

		register_post_type($this->post_type, $args);
	}

	/**
	 * Calculate and return the number of nights
	 */
	public function get_number_of_nights($check_in_date, $check_out_date)
	{

		$interval = $check_in_date->diff($check_out_date);

		return $interval->format('%d');
	}

	/**
	 * Determine and return the corresponding season id
	 */
	public function get_season_id($booking_check_in_date, $booking_check_out_date)
	{

		$season_id = ""; // empty by default

		$seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);

		foreach ($seasons as $season) {

			$season_start_date = new DateTime(get_post_meta($season->ID, "_obcal_season_start_date", true));
			$season_end_date = new DateTime(get_post_meta($season->ID, "_obcal_season_end_date", true));

			if ($booking_check_in_date >= $season_start_date && $booking_check_out_date <= $season_end_date) { // if is true is a valid season for this booking

				// set booking season id
				$season_id = $season->ID;
			}
		}

		return $season_id;
	}

	/**
	 * Determine and return the availability of a date range
	 */
	public function get_availability($booking_being_saved_id, $accommodation_id, $season_id, $check_in_date, $check_out_date)
	{

		// Return variable
		$availability = false; // by default

		// Interval of one day
		$interval_1d = new DateInterval('P1D');

		/**
		 * Get an array of relevant reserved days
		 */

		$reserved_dates = [];

		// Find all booking for the accommodation $accommodation_id with season equal to $season_id and status 'pending' or 'confirmed'
		$args = [
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => '_obcal_booking_accommodation_id',
					'value' => $accommodation_id
				],
				[
					'key' => '_obcal_booking_season_id',
					'value' => $season_id
				],
				[
					'key' => '_obcal_booking_status',
					'value' => ['pending', 'confirmed'],
					'compare' => 'IN'
				],
			],
			'post_type' => 'obcal_booking',
		];
		
		$query_result = new WP_Query($args);

		// The Loop
		if ($query_result->have_posts()) {

			while ($query_result->have_posts()) {

				$query_result->the_post();
				$other_booking = $query_result->post;

				// Only if not is the currently being saved booking. $booking_being_saved_id = '' for new bookings in csc
				if ($other_booking->ID != $booking_being_saved_id) {

					$other_check_in_date = new DateTime(get_post_meta($other_booking->ID, "_obcal_booking_check_in_date", true));
					$other_check_out_date = new DateTime(get_post_meta($other_booking->ID, "_obcal_booking_check_out_date", true));
		
					// Same accommodation for all compared bookings
					$other_accommodation_exclusivity_last_day = get_post_meta($accommodation_id, "_obcal_accommodation_exclusivity_last_day", true);

					// Include as reserved the last day if the exclusivity of the last day is activated
					if ($other_accommodation_exclusivity_last_day) {
						$other_check_out_date = $other_check_out_date->modify( '+1 day' );
					}

					$other_daterange = new DatePeriod($other_check_in_date, $interval_1d ,$other_check_out_date);
					foreach($other_daterange as $other_date){
						$reserved_dates[] = $other_date->format('Y-m-d');
					}

				}

			}

			/* Restore original Post Data */
			wp_reset_postdata();

		}

		/**
		 * Get an array of reserved days for this booking
		 */

		$this_booking_dates = [];

		$accommodation_exclusivity_last_day = get_post_meta($accommodation_id, "_obcal_accommodation_exclusivity_last_day", true);

		// Include as reserved the last day if the exclusivity of the last day is activated
		$check_out_date_for_range = clone $check_out_date;
		if ($accommodation_exclusivity_last_day) {
			$check_out_date_for_range->modify( '+1 day' );
		}

		$daterange = new DatePeriod($check_in_date, $interval_1d ,$check_out_date_for_range);
		foreach($daterange as $date){
			$this_booking_dates[] = $date->format('Y-m-d');
		}

		/**
		 * Compare both array and verify its intersection
		 */

		$intersection = array_intersect($reserved_dates, $this_booking_dates);

		/**
		 * Return
		 */

		// If there are no intersections all dates are available
		if (empty($intersection)) {
			$availability = true;
		}
		
		return $availability;
	}

	/**
	 * Determine and return the corresponding promotion id
	 */
	public function get_promotion_id($accommodation_id, $season_id, $num_nights)
	{

		$promotion_id = ""; // empty by default

		// 
		$args = [
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => '_obcal_promotion_accommodation_id',
					'value' => $accommodation_id
				],
				[
					'key' => '_obcal_promotion_season_id',
					'value' => $season_id
				],
				[
					'key' => '_obcal_promotion_num_nights',
					'value' => $num_nights,
					'type' => 'NUMERIC', // specify it for numeric values
					'compare' => '<='
				],
			],
			'post_type' => 'obcal_promotion',
		];

		$query_result = new WP_Query($args);

		// The Loop
		if ($query_result->have_posts()) {

			$query_result->the_post();
			$post = $query_result->post;

			// Set promotion_id
			$promotion_id = $post->ID;

			/* Restore original Post Data */
			wp_reset_postdata();
		}

		return $promotion_id;
	}

	/**
	 * Calculate and return booking total price
	 */
	public function get_total_price($accommodation_id, $num_nights, $season_id, $promotion_id)
	{

		$total_price = 0; // by default

		if (!empty($promotion_id)) { // with promotion

			// Get promotion total price
			$promotion_total_price = get_post_meta($promotion_id, "_obcal_promotion_total_price", true);
			// Get promotion number of nights
			$promotion_num_nights = get_post_meta($promotion_id, "_obcal_promotion_num_nights", true);

			// booking total price is equal to promotion total price
			$total_price += $promotion_total_price;

			// Subtract the nights that are included in the promotion
			$num_nights = (int)$num_nights - (int)$promotion_num_nights;

		}

		// Get the season price per night for the accommodation of this booking
		$season_price_per_night = get_post_meta($accommodation_id, "_obcal_accommodation_s{$season_id}_price_per_night", true);

		if (!empty($season_price_per_night) && $season_price_per_night > 0) {

			// set booking total price
			$total_price += $season_price_per_night * $num_nights;
		}

		return $total_price;
	}

	/**
	 * Save indirect (calculated) values
	 */
	public function save_indirect_post_meta($post_id, $num_nights, $season_id, $promotion_id, $total_price)
	{

		// Save booking number of nights
		update_post_meta(
			$post_id,
			"_{$this->post_type}_num_nights",
			$num_nights
		);

		// Save booking season id
		update_post_meta(
			$post_id,
			"_{$this->post_type}_season_id",
			$season_id
		);

		// Save booking promotion id
		update_post_meta(
			$post_id,
			"_{$this->post_type}_promotion_id",
			$promotion_id
		);

		// Save booking total price
		update_post_meta(
			$post_id,
			"_{$this->post_type}_total_price",
			$total_price
		);

	}

	public function send_email( $email_id, $customer_id = '', $meta_data = [] ) {

		if ( !empty($customer_id) ) {

			// Get customer email
			$customer_email = get_post_meta($customer_id, "_obcal_customer_email", true);

			// Get customer
			$customer = get_post($customer_id);

		}

		if ( substr($email_id, strlen($email_id) - 9, 9) == '_to_admin' ) {

			// get the value of the setting we've registered with register_setting()
			$options = get_option('obcal_options');

			// Get the ID of the user to send email notifications
			$admin_id = $options['obcal_field_email_notifications_user_id'];

			// Get admin
			$admin = get_userdata($admin_id);

			// Get admin display_name
			$admin_name = $admin->data->display_name;

			// Get admin email
			$admin_email = $admin->data->user_email;

		}

		// Default values
		$to = '';
		$subject = '';
		$message = '';	
		$headers = '';
		$attachments = '';

		if ( $email_id == 'booking_created_to_customer' || $email_id == 'booking_created_to_admin' ) {

			$message_booking_details = '';

			/*
			$meta_data keys:
                'booking_id'
                'accommodation_id'
                'check_in_date'
                'check_out_date'
                'num_adults'
                'num_children'
                'num_nights'
                'season_id'
                'promotion_id'
                'total_price'
			*/

			$message_booking_details .= "\n--- Detalles de la reserva: ---\n\n";

			$accommodation = get_post($meta_data['accommodation_id']);
			$message_booking_details .= 'Alojamiento: ' . $accommodation->post_title . "\n";

			$message_booking_details .= 'Fecha de check-in: ' . $meta_data['check_in_date'] . "\n";

			$message_booking_details .= 'Fecha de check-out: ' . $meta_data['check_out_date'] . "\n";

			$message_booking_details .= 'Número de adultos: ' . $meta_data['num_adults'] . "\n";

			$message_booking_details .= 'Número de niños: ' . $meta_data['num_children'] . "\n";

			$message_booking_details .= 'Número de noches: ' . $meta_data['num_nights'] . "\n";

			$season = get_post($meta_data['season_id']);
			$message_booking_details .= 'Temporada: ' . $season->post_title . "\n";

			if ( !empty($meta_data['promotion_id']) ) {

				$promotion = get_post($meta_data['promotion_id']);
				$promotion_num_nights = get_post_meta($meta_data['promotion_id'], "_obcal_promotion_num_nights", true);
				$message_booking_details .= 'Promoción: ' . $promotion->post_title . ' (' . $promotion_num_nights . " noches)\n";

			}

			$message_booking_details .= 'Precio: $' . $meta_data['total_price'] . "\n";

			$message_booking_details .= "\n--- --- --- ---\n\n";

		}

		if ( $email_id == 'booking_created_to_customer' && !empty($customer_email) ) {

			$to = $customer_email;
			$subject = 'Hemos recibido su solicitud de reserva';
			$message .= "Hola {$customer->post_title}.\n\n";
			$message .= "Hemos recibido su solicitud de reserva, la misma será verificada y le enviaremos un nuevo email cuando sea confirmada.\n";
			$message .= $message_booking_details;
			$message .= "Cualquier duda no deje de consultarnos, saludos!\n";
			$message .= get_site_url();
	
		} else if ( $email_id == 'booking_created_to_admin' && !empty($admin_email) ) {

			$to = $admin_email;
			$subject = 'Nueva solicitud de reserva';
			$message .= "Hola {$admin_name}.\n\n";
			$message .= "Se ha solicitado una nueva reserva para un alojamiento.\n";
			$message .= $message_booking_details;
			$message .= "Puede confirmar o cancelar la solicitud desde el siguiente enlace:\n";
			$message .= get_edit_post_link($meta_data['booking_id'], '&') . "\n";

		} else if ( $email_id == 'booking_received_to_customer' && !empty($customer_email) ) {

			$accommodation = get_post($meta_data['accommodation_id']);

			$to = $customer_email;
			$subject = 'Su reserva ha sido confirmada';
			$message .= "Hola {$customer->post_title}.\n\n";
			$message .= "Hemos verificado y confirmado su reserva para el alojamiento: " . $accommodation->post_title . ".\n\n";
			$message .= "Cualquier duda no deje de consultarnos, saludos!\n";
			$message .= get_site_url();

		} else if ( $email_id == 'booking_cancelled_to_customer' && !empty($customer_email) ) {

			$accommodation = get_post($meta_data['accommodation_id']);

			$to = $customer_email;
			$subject = 'Su reserva ha sido cancelada';
			$message .= "Hola {$customer->post_title}.\n\n";
			$message .= "Hemos verificado y cancelado su reserva para el alojamiento: " . $accommodation->post_title . ".\n\n";
			$message .= "Cualquier duda no deje de consultarnos, saludos!\n";
			$message .= get_site_url();
			
		}

		/**
		 * Send the email
		 */

		if ( !empty($to) && !empty($subject) && !empty($message) ) {

			wp_mail( 
				$to,				// to
				$subject,			// subject
				$message,			// message
				$headers,			// headers
				$attachments		// attachments
			);

		}

	}
}
