<?php

namespace OBCal;

/**
 * Booking preview
 */
class BookingReceived_CSC
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
    }

    public function register()
    {
        add_shortcode('obc_booking_received', [$this, 'content']);
    }

    public function content($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $obcal_atts = shortcode_atts([
        ], $atts, $tag);

        // Convert values to bool if applicable
        foreach ($obcal_atts as $key_atts => $value_atts) {
            if ( in_array( $value_atts, [ 'true', '1', 'false', '0' ], true ) ) {
                $obcal_atts[$key_atts] = rest_sanitize_boolean( $value_atts );
            }
        }

        // start output
        $o = '';

        // start box
        $o .= '<div class="obcal-booking-received-csc">';

        /**
         * Verify form nonce
         */
        if ( ! isset( $_POST['_wp_booking_preview_form_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_booking_preview_form_nonce'], 'booking_preview_form_nonce' ) ) {

            $o .= 'Sorry, your nonce did not verify.';
           
        } else {

            // Show booking received
            $o .= $this->content_booking_received($obcal_atts);

        }

        // enclosing tags
        if (!is_null($content)) {
            // secure output by executing the_content filter hook on $content
            $o .= apply_filters('the_content', $content);

            // run shortcode parser recursively
            $o .= do_shortcode($content);
        }

        // end box
        $o .= '</div>';

        // return output
        return $o;
    }

    /**
     * Show booking received in the shortcode content
     */
    private function content_booking_received($obcal_atts)
    {

        // start output
        $o = '';

        if (array_key_exists("confirm_accommodation_id", $_POST) && array_key_exists("confirm_check_in_date", $_POST) && array_key_exists("confirm_check_out_date", $_POST) && array_key_exists("confirm_num_adults", $_POST) && array_key_exists("confirm_num_children", $_POST) && array_key_exists("confirm_us_name", $_POST) && array_key_exists("confirm_us_email", $_POST)) {

            $accommodation_id = sanitize_key($_POST['confirm_accommodation_id']);
            $check_in_date = new \DateTime(sanitize_text_field($_POST['confirm_check_in_date']));
            $check_out_date = new \DateTime(sanitize_text_field($_POST['confirm_check_out_date']));
            $num_adults = sanitize_key($_POST['confirm_num_adults']);
            $num_children = sanitize_key($_POST['confirm_num_children']);
            $us_name = sanitize_text_field($_POST['confirm_us_name']);
            $us_email = sanitize_email($_POST['confirm_us_email']);

            // Add customer and/or get your ID
            $customer_id = $this->add_customer($us_email, $us_name);

            // Add the booking in 'pending' status
            $o .= $this->add_booking($customer_id, $accommodation_id, $check_in_date, $check_out_date, $num_adults, $num_children);

        } else {

            $o .= esc_html__('The sent data is not valid', 'open-booking-calendar');

        }

        return $o;
    }

    /**
     * 
     */
    private function add_customer($email, $name)
    {

        $query_args = [
            'meta_key' => '_obcal_customer_email',
            'meta_value' => $email,
            'post_type' => 'obcal_customer'
        ];

        $query_result = new \WP_Query($query_args);

        if (!$query_result->have_posts()) {

            $customer_id = wp_insert_post([
                'post_title'    => ucwords(wp_strip_all_tags($name)),
                'post_status'   => 'publish',
                'comment_status'  => 'closed',
                'ping_status'   => 'closed',
                'post_type'   => 'obcal_customer',
                'meta_input' => [
                    '_obcal_customer_email' => $email,
                ]
            ]);
        } else {

            $query_result->the_post();
            $customer = $query_result->post;

            $customer_id = $customer->ID;
        }

        return $customer_id;
    }

    /**
     * 
     */
    private function add_booking($customer_id, $accommodation_id, $check_in_date, $check_out_date, $num_adults, $num_children)
    {

        // start output
        $o = '';

        // Default booking_id for a new booking
        $booking_id = '';

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        // Get if show de currency code
        $show_currency_code = isset($options['obcal_field_show_currency_code']) ? $options['obcal_field_show_currency_code'] : '1';

        // Get accommodation currency
		$accommodation_currency_code = get_post_meta($accommodation_id, "_obcal_accommodation_currency_code", true);
		$accommodation_currency_symbol = get_post_meta($accommodation_id, "_obcal_accommodation_currency_symbol", true);


        /**
         * Get indirect (calculated) values
         */

        // Get booking number of nights
        $num_nights = $this->booking_cpt->get_number_of_nights($check_in_date, $check_out_date);

        // Get booking season id
        $season_id = $this->booking_cpt->get_season_id($check_in_date, $check_out_date);

        if (empty($season_id)) {

            $o .= '<div class="obcal-notice obcal-notice-error availability-error">' . esc_html__('Sorry, the dates were edited internally and now the dates you selected no longer belong to an active season.', 'open-booking-calendar') . '</div>';

        }

        // Get availability (bool)
        $available_dates = $this->booking_cpt->get_availability($booking_id, $accommodation_id, $season_id, $check_in_date, $check_out_date);

        if (!$available_dates) {

            $o .= '<div class="obcal-notice obcal-notice-error availability-error">' . esc_html__('Sorry, another reservation was made seconds before this and now the dates you selected are no longer available.', 'open-booking-calendar') . '</div>';

        }

        // Get booking promotion id
        $promotion_id = defined( 'OPEN_BOOKING_CALENDAR_PLUS_VERSION' ) ? apply_filters('obcal_promotion_get_promotion_id', $accommodation_id, $season_id, $num_nights) : '';

        // Get booking total price
        $total_price = $this->booking_cpt->get_total_price($accommodation_id, $num_nights, $season_id, $promotion_id);

        // Print more error messages
        if (empty($season_id) || !$available_dates) {

            $o .= '<div class="obcal-notice obcal-notice-info availability-error">' . esc_html__('You can go back to the calendar and select a different date or accommodation.', 'open-booking-calendar') . '</div>';

        }

        /**
         * Save indirect values
         */

        if (!empty($season_id) && $available_dates) {

            $booking_id = wp_insert_post([
                'comment_status'  => 'closed',
                'ping_status'   => 'closed',
                'post_type'   => 'obcal_booking',
                'meta_input' => [
                    '_obcal_booking_customer_id' => $customer_id,
                    '_obcal_booking_accommodation_id' => $accommodation_id,
                    '_obcal_booking_check_in_date' => $check_in_date->format('Y-m-d'),
                    '_obcal_booking_check_out_date' => $check_out_date->format('Y-m-d'),
                    '_obcal_booking_num_adults' => $num_adults,
                    '_obcal_booking_num_children' => $num_children,
                    '_obcal_booking_promotion_id' => $promotion_id,
                    '_obcal_booking_currency_code' => $accommodation_currency_code,
                    '_obcal_booking_currency_symbol' => $accommodation_currency_symbol,
                    '_obcal_booking_status' => 'pending',
                ]
            ]);
    
            wp_update_post([
                'ID'  => $booking_id,
                'post_title'    => wp_strip_all_tags(__('Booking', 'open-booking-calendar') . ' #' . $booking_id),
                'post_status'   => 'publish'
            ]);
    

            // Save indirect (calculated) values
            $this->booking_cpt->save_indirect_post_meta(
                $booking_id,
                $num_nights,
                $season_id,
                $promotion_id,
                $total_price
            );

            $o .= '<div class="obcal-notice obcal-notice-success booking-received">';
            $o .= '<h3>' . esc_html__('Your reservation was received successfully!', 'open-booking-calendar') . '</h3>';
            $o .= '</div>';

            $o .= '<div class="obcal-notice obcal-notice-info booking-received">';
            $o .= esc_html__('We will send you an email with the details of the booking.', 'open-booking-calendar');
            $o .= '</div>';

            /**
             * Send emails
             */

            $email_meta_data = [
                'booking_id' => $booking_id,
                'accommodation_id' => $accommodation_id,
                'check_in_date' => $check_in_date->format($options_date_format),
                'check_out_date' => $check_out_date->format($options_date_format),
                'num_adults' => $num_adults,
                'num_children' => $num_children,
                'num_nights' => $num_nights,
                'season_id' => $season_id,
                'promotion_id' => $promotion_id,
                'total_price' => $total_price,
                'currency_code' => rest_sanitize_boolean($show_currency_code) ? strtoupper($accommodation_currency_code) : '',
                'currency_symbol' => $accommodation_currency_symbol,              
            ];

            // Send email to admin
    		$this->booking_cpt->send_email('booking_created_to_admin', '', $email_meta_data);

            // Send email to customer
            $this->booking_cpt->send_email('booking_created_to_customer', $customer_id, $email_meta_data);

        }

        return $o;
    }
}
