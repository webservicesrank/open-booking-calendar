<?php

namespace OBCal;

/**
 * Booking preview
 */
class BookingPreview_CSC
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
        add_shortcode('obc_booking_preview', [$this, 'content']);
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
        $o .= '<div class="obcal-booking-preview-csc">';

        /**
         * Verify form nonce
         */
        if ( ! isset( $_POST['_wp_booking_form_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_booking_form_nonce'], 'booking_form_nonce' ) ) {

            $o .= 'Sorry, your nonce did not verify.';
           
        } else {
        
            /**
             * Register Contact in the Log
             */

            if (array_key_exists("us_name", $_POST) && array_key_exists("us_email", $_POST)) {

                // Register Contact in the Log
                $contact_id = apply_filters('obcal_contact_log_insert_contact', sanitize_text_field($_POST['us_name']), sanitize_email($_POST['us_email']));

                if (!empty($contact_id)) {

                    // Register Query in the Log
                    do_action('obcal_contact_log_insert_query_log', $contact_id, 'booking_preview', $_POST);

                }

            }

            /**
             * Show booking preview content
             */

            $o .= $this->content_booking_preview($obcal_atts);

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
     * Show booking preview in the shortcode content
     */
    private function content_booking_preview($obcal_atts)
    {

        // start output
        $o = '';

        if (array_key_exists("accommodation_id", $_POST) && array_key_exists("selected_date", $_POST) && array_key_exists("num_adults", $_POST) && array_key_exists("num_children", $_POST) && array_key_exists("us_name", $_POST) && array_key_exists("us_email", $_POST)) {

            /**
             * Get POST values
             */

            $accommodation_id = sanitize_key($_POST['accommodation_id']);
            $selected_date = sanitize_text_field($_POST['selected_date']);
            $num_adults = sanitize_key($_POST['num_adults']);
            $num_children = sanitize_key($_POST['num_children']);
            $us_name = sanitize_text_field($_POST['us_name']);
            $us_email = sanitize_email($_POST['us_email']);

            /**
             * Get check-in and check-out dates from $selected_date, regardless of the date format.
             * It is separated by detecting the blank spaces in the center, for example in "date_any_format to date_any_format".
             */

            // Get the position of the first and second blank space in $selected_date
            $selected_date_pos_first_bsp = strpos($selected_date, ' ');
            $selected_date_pos_second_bsp = strpos($selected_date, ' ', $selected_date_pos_first_bsp + 1);

            // Get check-in and check-out dates from $selected_date
            $check_in_date = substr($selected_date, 0, $selected_date_pos_first_bsp);
            $check_out_date = substr($selected_date, $selected_date_pos_second_bsp + 1, strlen($check_in_date));
            
            /**
             * Print Summary
             */

            // Get the options
            $options = get_option('obcal_options');
            // Get date format
            $options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

            $check_in_date = new \DateTime($check_in_date);
            $check_out_date = new \DateTime($check_out_date);

            $accommodation = get_post($accommodation_id);

            $o .= '<h3>' . esc_html__('Booking details', 'open-booking-calendar') . '</h3>';

            $o .= '<table class="obcal-form-table">';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Accommodation', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= esc_html($accommodation->post_title);
            $o .= '</td></tr>';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Check-in date', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= esc_html($check_in_date->format($options_date_format));
            $o .= '</td></tr>';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Check-out date', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= esc_html($check_out_date->format($options_date_format));
            $o .= '</td></tr>';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Number of adults', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= esc_html($num_adults);
            $o .= '</td></tr>';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Number of children', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= esc_html($num_children);
            $o .= '</td></tr>';

            $o .= '</table>';

            /**
             * Billing details
             */

			// Get booking number of nights
            $num_nights = $this->booking_cpt->get_number_of_nights($check_in_date, $check_out_date);

			// Get booking season id
			$season_id = $this->booking_cpt->get_season_id($check_in_date, $check_out_date);

            // Get season
            $season = get_post($season_id);

            // Get season meta data
            $season_price_per_night = get_post_meta($accommodation_id, "_obcal_accommodation_s{$season->ID}_price_per_night", true);
    
			// Get booking promotion id
			$promotion_id = defined( 'OPEN_BOOKING_CALENDAR_PLUS_VERSION' ) ? apply_filters('obcal_promotion_get_promotion_id', $accommodation_id, $season_id, $num_nights) : '';

            // Get promotion
            if (!empty($promotion_id)) {
                $promotion = get_post($promotion_id);
            }
            
			// Get booking total price
			$total_price = $this->booking_cpt->get_total_price($accommodation_id, $num_nights, $season_id, $promotion_id);
            
            $o .= '<h3>' . esc_html__('Price details', 'open-booking-calendar') . '</h3>';

            $o .= '<table class="obcal-form-table">';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Season', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= esc_html($season->post_title . ', $' . $season_price_per_night . ' ' . __('per night', 'open-booking-calendar') );
            $o .= '</td></tr>';

            if (!empty($promotion_id)) {

                // Get promotion meta data
                $promotion_num_nights = get_post_meta($promotion_id, "_obcal_promotion_num_nights", true);
                $promotion_total_price = get_post_meta($promotion_id, "_obcal_promotion_total_price", true);    

                $o .= '<tr><th scope="row">';
                $o .= esc_html__('Promotion', 'open-booking-calendar');
                $o .= '</th><td>';
                $o .= esc_html($promotion->post_title . ', ' . $promotion_num_nights . ' ' . __('nights', 'open-booking-calendar') . ', $' . $promotion_total_price);
                $o .= '</td></tr>';

                $num_nights = (int)$num_nights - (int)$promotion_num_nights;
            }

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Applies', 'open-booking-calendar');
            $o .= '</th><td>';
            if (!empty($promotion_id)) {
                $o .= esc_html($promotion->post_title);
            }
            if (!empty($promotion_id) && $num_nights > 0) {
                $o .= '<span class="obcal-plus-csc-booking-preview">+</span>';
            }
            if ($num_nights > 0) {
                $o .= esc_html( sprintf( _n( '%s night', '%s nights', $num_nights, 'open-booking-calendar' ), $num_nights ) );
            }

            $o .= '</td></tr>';

            $o .= '<tr><th scope="row">';
            $o .= esc_html__('Price', 'open-booking-calendar');
            $o .= '</th><td>';
            $o .= '$' . esc_html($total_price);
            $o .= '</td></tr>';

            $o .= '</table>';

            /**
             * Confirm booking form
             */

            $booking_received_page_id = get_post_meta($accommodation->ID, "_obcal_accommodation_booking_received_page_id", true);

            $o .= '<div class="obcal-booking-confirm-form">';
            $o .= '<form method="POST" action="' . esc_url(get_permalink($booking_received_page_id)) . '">';
            $o .= wp_nonce_field('booking_preview_form_nonce', '_wp_booking_preview_form_nonce', true, false);
            $o .= '<input type="hidden" name="confirm_accommodation_id" value="' . esc_attr($accommodation_id) . '">';
            $o .= '<input type="hidden" name="confirm_check_in_date" value="' . esc_attr($check_in_date->format($options_date_format)) . '" >';
            $o .= '<input type="hidden" name="confirm_check_out_date" value="' . esc_attr($check_out_date->format($options_date_format)) . '" >';
            $o .= '<input type="hidden" name="confirm_num_adults" value="' . esc_attr($num_adults) . '" >';
            $o .= '<input type="hidden" name="confirm_num_children" value="' . esc_attr($num_children) . '" >';
            $o .= '<input type="hidden" name="confirm_us_name" value="' . esc_html($us_name) . '" >';
            $o .= '<input type="hidden" name="confirm_us_email" value="' . esc_attr($us_email) . '" >';
            $o .= '<input type="submit" value="' . esc_html__('Confirm booking', 'open-booking-calendar') . '" >';
            $o .= '</form>';
            $o .= '</div>';            
            
        } else {
            $o .= esc_html__('The sent data is not valid', 'open-booking-calendar');
        }
        return $o;
    }

}
