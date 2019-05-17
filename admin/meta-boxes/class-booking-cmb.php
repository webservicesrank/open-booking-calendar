<?php

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
		$check_in_date = new DateTime(get_post_meta($post->ID, "_{$this->post_type}_check_in_date", true));
		$check_out_date = new DateTime(get_post_meta($post->ID, "_{$this->post_type}_check_out_date", true));
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
					<label for="<?=$this->post_type?>_customer_id"><?=__('Customer', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_customer_id" id="<?=$this->post_type?>_customer_id" >
						<?php
						foreach ($customers as $customer){
							$customer_email = get_post_meta($customer->ID, "_obcal_customer_email", true);
						?>
						<option value="<?=$customer->ID?>" <?php selected( $customer->ID, $customer_id, true ); ?>>
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
					<label for="<?=$this->post_type?>_accommodation_id"><?=__('Accommodation', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_accommodation_id" id="<?=$this->post_type?>_accommodation_id" >
						<?php
						foreach ($accommodations as $accommodation){
						?>
						<option value="<?=$accommodation->ID?>" <?php selected( $accommodation->ID, $accommodation_id, true ); ?>>
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
					<label for="<?=$this->post_type?>_num_adults"><?=__('Number of adults', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="number" name="<?=$this->post_type?>_num_adults" id="<?=$this->post_type?>_num_adults" value="<?=$num_adults?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_num_children"><?=__('Number of children', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="number" name="<?=$this->post_type?>_num_children" id="<?=$this->post_type?>_num_children" value="<?=$num_children?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_check_in_date"><?=__('Check-in date', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="hidden" id="obc_cal_dateFormat" value="<?=$options_date_format?>">
					<input type="text" name="<?=$this->post_type?>_check_in_date" id="<?=$this->post_type?>_check_in_date" class="booking-check-in-date" placeholder="<?=__('Select Date..', 'open-booking-calendar')?>" value="<?=$check_in_date->format($options_date_format)?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_check_out_date"><?=__('Check-out date', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="text" name="<?=$this->post_type?>_check_out_date" id="<?=$this->post_type?>_check_out_date" class="booking-check-out-date" placeholder="<?=__('Select Date..', 'open-booking-calendar')?>" value="<?=$check_out_date->format($options_date_format)?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_status"><?=__('Status', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_status" id="<?=$this->post_type?>_status" >
						<?php
						foreach ($statuses as $status_key => $status_value){
						?>
						<option value="<?=$status_key?>" <?php selected( $status_key, $status, true ); ?>>
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
		$booking_season_start_date = new DateTime(get_post_meta($booking_season->ID, "_obcal_season_start_date", true));
		$booking_season_end_date = new DateTime(get_post_meta($booking_season->ID, "_obcal_season_end_date", true));

		$booking_total_price = get_post_meta($post->ID, "_{$this->post_type}_total_price", true);

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_num_nights"><?=__('Number of nights', 'open-booking-calendar')?></label>
				</th>
				<td>
					<?php echo empty($booking_num_nights) ? '-' : $booking_num_nights?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_season"><?=__('Season', 'open-booking-calendar')?></label>
				</th>
				<td>
					<?php echo empty($booking_season_id) ? '-' : ($booking_season->post_title . " ({$booking_season_start_date->format($options_date_format)} - {$booking_season_end_date->format($options_date_format)})" )?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_applies"><?=__('Applies', 'open-booking-calendar')?></label>
				</th>
				<td>
					<?php
					if(empty($booking_total_price)){

						echo '-';

					}else{

						$promotion_id = get_post_meta($post->ID, "_{$this->post_type}_promotion_id", true);

						if (empty($promotion_id)) {

							echo __('Normal nights without promotion', 'open-booking-calendar') . '.';

						} else {

							$promotion = get_post($promotion_id);
							$promotion_num_nights = get_post_meta($promotion_id, "_obcal_promotion_num_nights", true);

							echo __('Promotion', 'open-booking-calendar') . ": " . $promotion->post_title;

							$extra_normal_nights = (int)$booking_num_nights - (int)$promotion_num_nights;

							if ($extra_normal_nights > 0) {

								echo '<br>' . __('Normal nights', 'open-booking-calendar') . ': ' . $extra_normal_nights;

							}

						}

					}
					?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_total_price"><?=__('Booking total price', 'open-booking-calendar')?></label>
				</th>
				<td>
					<?php
					if(empty($booking_total_price)){

						echo __('When saving the changes the calculation will be made automatically', 'open-booking-calendar');

					}else{

						$promotion_id = get_post_meta($post->ID, "_{$this->post_type}_promotion_id", true);						

						echo "$" . $booking_total_price;

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

		// Check if exists input dates keys
		if (array_key_exists("{$this->post_type}_accommodation_id", $_POST) && array_key_exists("{$this->post_type}_check_in_date", $_POST) && array_key_exists("{$this->post_type}_check_out_date", $_POST)) {

			$accommodation_id = $_POST["{$this->post_type}_accommodation_id"];
			$check_in_date =  new DateTime($_POST["{$this->post_type}_check_in_date"]);
			$check_out_date = new DateTime($_POST["{$this->post_type}_check_out_date"]);

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
			$promotion_id = $this->booking_cpt->get_promotion_id($accommodation_id, $season_id, $num_nights);

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

			$keys_to_save_directly = ['customer_id', 'accommodation_id', 'num_children', 'num_adults', 'check_in_date', 'check_out_date', 'status'];

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
			'accommodation' => __('Accommodation', 'open-booking-calendar'),
			'customer' => __('Customer', 'open-booking-calendar'),
			'check_in_date' => __('Check-in date', 'open-booking-calendar'),
			'num_nights' => __('Num. of nights', 'open-booking-calendar'),
			'total_price' => __('Price', 'open-booking-calendar'),
			'status' => __('Status', 'open-booking-calendar'),
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
			$check_in_date = new DateTime(get_post_meta($post_id, "_{$this->post_type}_check_in_date", true));
			echo $check_in_date->format($options_date_format);
			break;
		case 'num_nights':
			echo get_post_meta( $post_id , "_{$this->post_type}_num_nights" , true );
			break;
		case 'total_price':
			echo "$" . get_post_meta( $post_id , "_{$this->post_type}_total_price" , true );
			break;
		case 'status':
			echo $this->statuses[get_post_meta( $post_id , "_{$this->post_type}_status" , true )];
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
				$html .=	'<p>' . __('The selected date does not belong to an active season.', 'open-booking-calendar') . '</p>';
				$html .= '</div>';
												
			}
			
			if( $_REQUEST['wsr_notice'] === "availability_error") {

				$html .=	'<div class="notice notice-warning is-dismissible">';
				$html .=	'<p>' . __('The selected date is not available for reservations.', 'open-booking-calendar') . '</p>';
				$html .= '</div>';

			}

			echo $html;

		}
	}

}