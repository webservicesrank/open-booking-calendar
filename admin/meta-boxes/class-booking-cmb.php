<?php

namespace OBCal;

class Booking_CMB
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

	/**
	 * The booking custom post type object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $booking_cpt    The booking custom post type object.
	 */	
	private $booking_cpt;

	protected $post_type = 'obcal_booking';
	protected $statuses;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $open_booking_calendar       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($open_booking_calendar, $version, $booking_cpt)
	{

		$this->open_booking_calendar = $open_booking_calendar;
		$this->version = $version;

		$this->booking_cpt = $booking_cpt;

		$this->statuses = [
			'pending' => __('Pending', 'open-booking-calendar'),
			'confirmed' => __('Confirmed', 'open-booking-calendar'),
			'cancelled' => __('Cancelled', 'open-booking-calendar'),
		];

	}

	/**
	 * Register Meta Boxes.
	 */
	public function register(){

		add_meta_box(
			"{$this->post_type}_details",											// Unique ID
			__('Booking details', 'open-booking-calendar'),		// Box title
			[$this, 'details_html'],  												// Content callback, must be of type callable
			$this->post_type             											// Post type
		);

		add_meta_box(
			"{$this->post_type}_billing_details",							// Unique ID
			__('Billing details', 'open-booking-calendar'),		// Box title
			[$this, 'billing_html'],  												// Content callback, must be of type callable
			$this->post_type             											// Post type
		);

	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function details_html($post)
	{
		$customer_id = get_post_meta($post->ID, "_{$this->post_type}_customer_id", true);
		$accommodation_id = get_post_meta($post->ID, "_{$this->post_type}_accommodation_id", true);
		$num_adults = get_post_meta($post->ID, "_{$this->post_type}_num_adults", true);
		$num_children = get_post_meta($post->ID, "_{$this->post_type}_num_children", true);
		$check_in_date = new \DateTime(get_post_meta($post->ID, "_{$this->post_type}_check_in_date", true));
		$check_out_date = new \DateTime(get_post_meta($post->ID, "_{$this->post_type}_check_out_date", true));
		$status = get_post_meta($post->ID, "_{$this->post_type}_status", true);

		$customers = get_posts(['post_type' => 'obcal_customer', 'numberposts' => -1]);

		$accommodations = get_posts(['post_type' => 'obcal_accommodation', 'numberposts' => -1]);

		$statuses = $this->statuses;

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_customer_id') ?>"><?php esc_html_e('Customer', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_customer_id') ?>" id="<?= esc_attr($this->post_type . '_customer_id') ?>" >
						<?php
						foreach ($customers as $customer){
							$customer_email = get_post_meta($customer->ID, "_obcal_customer_email", true);
						?>
						<option value="<?= esc_attr($customer->ID) ?>" <?php selected( $customer->ID, $customer_id, true ); ?>>
							<?php echo esc_html( $customer->ID . "- " . $customer->post_title . " (" . $customer_email . ")" ); ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_accommodation_id') ?>"><?php esc_html_e('Accommodation', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_accommodation_id') ?>" id="<?= esc_attr($this->post_type . '_accommodation_id') ?>" >
						<?php
						foreach ($accommodations as $accommodation){
						?>
						<option value="<?= esc_attr($accommodation->ID) ?>" <?php selected( $accommodation->ID, $accommodation_id, true ); ?>>
							<?php echo esc_html( $accommodation->post_title ); ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_num_adults') ?>"><?php esc_html_e('Number of adults', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="number" name="<?= esc_attr($this->post_type . '_num_adults') ?>" id="<?= esc_attr($this->post_type . '_num_adults') ?>" value="<?= esc_attr($num_adults) ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_num_children') ?>"><?php esc_html_e('Number of children', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="number" name="<?= esc_attr($this->post_type . '_num_children') ?>" id="<?= esc_attr($this->post_type . '_num_children') ?>" value="<?= esc_attr($num_children) ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_check_in_date') ?>"><?php esc_html_e('Check-in date', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="hidden" id="obc_cal_dateFormat" value="<?= esc_attr($options_date_format) ?>">
					<input type="text" name="<?= esc_attr($this->post_type . '_check_in_date') ?>" id="<?= esc_attr($this->post_type . '_check_in_date') ?>" class="booking-check-in-date" placeholder="<?=__('Select Date..', 'open-booking-calendar')?>" value="<?= esc_attr( $check_in_date->format($options_date_format) ) ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_check_out_date') ?>"><?php esc_html_e('Check-out date', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="text" name="<?= esc_attr($this->post_type . '_check_out_date') ?>" id="<?= esc_attr($this->post_type . '_check_out_date') ?>" class="booking-check-out-date" placeholder="<?=__('Select Date..', 'open-booking-calendar')?>" value="<?= esc_attr( $check_out_date->format($options_date_format) ) ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_status') ?>"><?php esc_html_e('Status', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_status') ?>" id="<?= esc_attr($this->post_type . '_status') ?>" >
						<?php
						foreach ($statuses as $status_key => $status_value){
						?>
						<option value="<?= esc_attr($status_key) ?>" <?php selected( $status_key, $status, true ); ?>>
							<?php echo esc_html( $status_value ); ?>
						</option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
		</table>			
		<?php
	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function billing_html($post)
	{

		$booking_num_nights = get_post_meta($post->ID, "_{$this->post_type}_num_nights", true);

		$booking_season_id = get_post_meta($post->ID, "_{$this->post_type}_season_id", true);
		$booking_season = get_post($booking_season_id);
		$booking_season_start_date = new \DateTime(get_post_meta($booking_season->ID, "_obcal_season_start_date", true));
		$booking_season_end_date = new \DateTime(get_post_meta($booking_season->ID, "_obcal_season_end_date", true));

		$booking_total_price = get_post_meta($post->ID, "_{$this->post_type}_total_price", true);

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Number of nights', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<?= esc_html( empty($booking_num_nights) ? '-' : $booking_num_nights) ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Season', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<?= esc_html( empty($booking_season_id) ? '-' : ($booking_season->post_title . " ({$booking_season_start_date->format($options_date_format)} - {$booking_season_end_date->format($options_date_format)})" ) ) ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Applies', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<?php
					if(empty($booking_total_price)){

						echo '-';

					}else{

						$promotion_id = defined( 'OPEN_BOOKING_CALENDAR_PLUS_VERSION' ) ? get_post_meta($post->ID, "_{$this->post_type}_promotion_id", true) : '';

						if (empty($promotion_id)) {

							esc_html_e('Normal nights without promotion.', 'open-booking-calendar');

						} else {

							$promotion = get_post($promotion_id);
							$promotion_num_nights = get_post_meta($promotion_id, "_obcal_promotion_num_nights", true);

							echo esc_html( __('Promotion', 'open-booking-calendar') . ": " . $promotion->post_title );

							$extra_normal_nights = (int)$booking_num_nights - (int)$promotion_num_nights;

							if ($extra_normal_nights > 0) {

								echo esc_html( '<br>' . __('Normal nights', 'open-booking-calendar') . ': ' . $extra_normal_nights );

							}

						}

					}
					?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Booking total price', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<?php
					if(empty($booking_total_price)){

						esc_html_e('When saving the changes the calculation will be made automatically.', 'open-booking-calendar');

					}else{

						echo esc_html( "$" . $booking_total_price );

					}
					?>
				</td>
			</tr>
		</table>

		<?php

	}

	/**
	 * Save data of Meta Boxes.
	 */
	public function save($post_id)
	{
		// Keys of the values to save directly
		$keys_to_save_directly = ['customer_id', 'accommodation_id', 'num_children', 'num_adults', 'check_in_date', 'check_out_date', 'status'];

		/** 
		 * Sanitize POST values
		 */

		// Sanitize values for 'Save values in array directly'
		foreach ($keys_to_save_directly as $key_to_save) {
			if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {
				if ($key_to_save == 'check_in_date' || $key_to_save == 'check_out_date'){
					$_POST["{$this->post_type}_{$key_to_save}"] = sanitize_text_field($_POST["{$this->post_type}_{$key_to_save}"]);
				} else {
					$_POST["{$this->post_type}_{$key_to_save}"] = sanitize_key($_POST["{$this->post_type}_{$key_to_save}"]);
				}
			}
		}


		/**
		 * Validate POST values
		 */

		// Validate values for 'Save values in array directly'
		foreach ($keys_to_save_directly as $key_to_save) {
			if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {

				// Get POST value
				$value = $_POST["{$this->post_type}_{$key_to_save}"];

				// Validate 'customer_id'

				// Validate 'accommodation_id'

				// Validate 'num_adults'
				if ($key_to_save == 'num_adults' && (!is_numeric($value) || $value < 1)) {
					$_POST["{$this->post_type}_{$key_to_save}"] = 1;
				}

				// Validate 'num_children'
				if ($key_to_save == 'num_children' && (!is_numeric($value) || $value < 0)) {
					$_POST["{$this->post_type}_{$key_to_save}"] = 0;
				}

				// Validate 'check_in_date', 'check_out_date'

				// Validate 'status'
				
			}
		}
		

		/**
		 * 
		 */

		// Check if exists input dates keys
		if (array_key_exists("{$this->post_type}_accommodation_id", $_POST) && array_key_exists("{$this->post_type}_check_in_date", $_POST) && array_key_exists("{$this->post_type}_check_out_date", $_POST)) {

			$accommodation_id = $_POST["{$this->post_type}_accommodation_id"];
			$check_in_date =  new \DateTime($_POST["{$this->post_type}_check_in_date"]);
			$check_out_date = new \DateTime($_POST["{$this->post_type}_check_out_date"]);

			/**
			 * Get indirect (calculated) values
			 */

			// Get booking number of nights
			$num_nights = $this->booking_cpt->get_number_of_nights($check_in_date, $check_out_date);

			// Get booking season id
			$season_id = $this->booking_cpt->get_season_id($check_in_date, $check_out_date);

			if (empty($season_id)) {

				// server response
				$notice_id = 'season_error';
				$notice_data = [];
        $this->admin_notice_redirect( $post_id, $notice_id, $notice_data );
				exit;

			}

			// Get availability (bool)
			$available_dates = $this->booking_cpt->get_availability($post_id, $accommodation_id, $season_id, $check_in_date, $check_out_date);

			if (!$available_dates) {

				// server response
				$notice_id = 'availability_error';
				$notice_data = [];
        $this->admin_notice_redirect( $post_id, $notice_id, $notice_data );
				exit;

			}

			// Get booking promotion id
			$promotion_id = defined( 'OPEN_BOOKING_CALENDAR_PLUS_VERSION' ) ? apply_filters('obcal_promotion_get_promotion_id', $accommodation_id, $season_id, $num_nights) : '';

			// Get booking total price
			$total_price = $this->booking_cpt->get_total_price($accommodation_id, $num_nights, $season_id, $promotion_id);

	
			/**
			 * Save indirect values
			 */
		
			if (!empty($season_id) && $available_dates){

				// Save indirect (calculated) values
				$this->booking_cpt->save_indirect_post_meta(
					$post_id, $num_nights, $season_id, $promotion_id, $total_price
				);

			}

			/** 
			 * Save values in array directly
			 */

			// Get booking status before save, this is useful for send the email when the status change
			$prev_booking_status = get_post_meta($post_id, "_{$this->post_type}_status", true);

			foreach ($keys_to_save_directly as $key_to_save) {

				if ( $key_to_save == 'check_in_date' ) {
					$meta_value = $check_in_date->format('Y-m-d');
				} else if ( $key_to_save == 'check_out_date' ) {
					$meta_value = $check_out_date->format('Y-m-d');
				} else {
					$meta_value = $_POST["{$this->post_type}_{$key_to_save}"]; 
				}

				if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {
					update_post_meta(
						$post_id,
						"_{$this->post_type}_{$key_to_save}",
						$meta_value
					);
				}
			}

			/**
			 * Send emails
			 */
			
			$new_booking_status = $_POST["{$this->post_type}_status"];

			if ($prev_booking_status == 'pending' && ($new_booking_status == 'confirmed' || $new_booking_status == 'cancelled')) {

				$email_meta_data = [
					'accommodation_id' => $_POST["{$this->post_type}_accommodation_id"],
				];

				// Send email to customer
				$this->booking_cpt->send_email('booking_' . $new_booking_status . '_to_customer', $_POST["{$this->post_type}_customer_id"], $email_meta_data);			

			}
		}

	}

	/**
	 * Add Custom Columns to the Post Type Table.
	 */
	public function add_table_custom_columns($columns) {
		$new_columns = [
			'cb' => $columns['cb'],
			'title' => $columns['title'],
			'accommodation' => esc_html__('Accommodation', 'open-booking-calendar'),
			'customer' => esc_html__('Customer', 'open-booking-calendar'),
			'check_in_date' => esc_html__('Check-in date', 'open-booking-calendar'),
			'num_nights' => esc_html__('Num. of nights', 'open-booking-calendar'),
			'total_price' => esc_html__('Price', 'open-booking-calendar'),
			'status' => esc_html__('Status', 'open-booking-calendar'),
			'date' => $columns['date'],
		];
		return $new_columns;
	}

	/**
	 * Add Custom Columns Data to the Post Type Table.
	 */
	public function add_table_custom_values( $column, $post_id ) {
		switch ( $column ) {
	  case 'accommodation':
			$accommodation_id = get_post_meta( $post_id , "_{$this->post_type}_accommodation_id" , true );
			$accommodation = get_post($accommodation_id);
			echo esc_html($accommodation->post_title);
			break;
	  case 'customer':
			$customer_id = get_post_meta( $post_id , "_{$this->post_type}_customer_id" , true );
			$customer = get_post($customer_id);
			echo esc_html($customer->post_title);
			break;
		case 'check_in_date':
			// Get the options
			$options = get_option('obcal_options');
			// Get date format
			$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';
			// Get and print check-in date
			$check_in_date = new \DateTime(get_post_meta($post_id, "_{$this->post_type}_check_in_date", true));
			echo esc_html($check_in_date->format($options_date_format));
			break;
		case 'num_nights':
			echo esc_html(get_post_meta( $post_id , "_{$this->post_type}_num_nights" , true ));
			break;
		case 'total_price':
			echo esc_html("$" . get_post_meta( $post_id , "_{$this->post_type}_total_price" , true ));
			break;
		case 'status':
			echo esc_html($this->statuses[get_post_meta( $post_id , "_{$this->post_type}_status" , true )]);
			break;

		}
	}

	/**
	 * Register the columns as sortable.
	 */
	public function register_table_sortable_columns( $columns ) {
		$columns['accommodation'] = 'Accommodation';
		$columns['customer'] = 'Customer';
		$columns['check_in_date'] = 'Check-in date';
		$columns['num_nights'] = 'Num Nights';
		$columns['total_price'] = 'Price';
		$columns['status'] = 'Status';
		return $columns;
	}

	/**
	 * Redirect
	 * 
	 * @since    1.0.0
	 */
	public function admin_notice_redirect( $post_id, $wsr_notice, $wsr_notice_data ) {

		wp_redirect( 
	    esc_url_raw( 
        add_query_arg( 
          [
            'wsr_notice' => $wsr_notice,
            'wsr_data' => $wsr_notice_data,
					],
          admin_url(
            'post.php?post=' . $post_id . '&action=edit'
          ) 
        ) 
      ) 
		);
		
  }
    
	/**
	 * Print Admin Notices
	 * 
	 * @since    1.0.0
	 * 
	 * 
	 * Use notice-success (green), notice-info (blue), notice-warning (yellow/orange), and notice-error (red) for change the left border.
	 * Optionally use is-dismissible to apply a closing icon.
	 */
	public function print_plugin_admin_notices() {

		if ( isset( $_REQUEST['wsr_notice'] ) ) {

			$html = '';

			if( $_REQUEST['wsr_notice'] === "season_error") {

				$html .=	'<div class="notice notice-warning is-dismissible">';
				$html .=	'<p>' . esc_html__('The selected date does not belong to an active season.', 'open-booking-calendar') . '</p>';
				$html .= '</div>';
												
			}
			
			if( $_REQUEST['wsr_notice'] === "availability_error") {

				$html .=	'<div class="notice notice-warning is-dismissible">';
				$html .=	'<p>' . esc_html__('The selected date is not available for reservations.', 'open-booking-calendar') . '</p>';
				$html .= '</div>';

			}

			echo $html;

		}
	}

}