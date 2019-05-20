<?php

/**
 * Availability calendar and booking form
 */
class BookingCalendar_CSC
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

    // Active seasons for this accommodation in the current date
    private $active_season_ids = [];

    // Active seasons for this accommodation in the current date
    private $active_season_ids3 = "a";

    // Active season dates for flatpickr
    private $active_season_dates = [];

    // Max date for flatpickr
    private $max_date = "";

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
        add_shortcode('obc_booking_calendar', [$this, 'content']);
    }

    public function content($atts = [], $content = null, $tag = '')
    {
        $this->active_season_ids3 = "c";

        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $obcal_atts = shortcode_atts([
            'id' => '',
            'show_seasons' => false,
            'show_seasons_dates' => false,
            'show_seasons_price' => false,
            'show_promotions' => false
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
        $o .= '<div class="obcal-booking-calendar-csc">';

        // Set default value
        $is_valid_accommodation = false;

        // Get Accommodation by id
        $accommodation = get_post($obcal_atts['id']);

        // Check accommodation
        if ($accommodation !== null && $accommodation->post_type == 'obcal_accommodation' && $accommodation->post_status == "publish") {

            // Is a valid accommodation
            $is_valid_accommodation = true;

            // Show seasons
            $o .= $this->content_seasons($obcal_atts, $accommodation);

            if( defined( 'OPEN_BOOKING_CALENDAR_PLUS_VERSION' ) ) {
                // Show promotions
                $o .= $this->content_promotions($obcal_atts, $accommodation);
            }

            // Show availability calendar
            $o .= $this->content_calendar($obcal_atts, $accommodation);

            // Show booking preview form
            $o .= $this->content_form($obcal_atts, $accommodation);

        }

        // Accommodation error message
        if (!$is_valid_accommodation) {
            $o .= "<p>" . esc_html__('Invalid accommodation', 'open-booking-calendar') . "</p>";
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
     * Show seasons in the shortcode content
     */
    private function content_seasons($obcal_atts, $accommodation)
    {

        // start output
        $o = '';

        // Get the options
        $options = get_option('obcal_options');

        // Get date format
        $options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        $now_date = new DateTime(date_i18n($options_date_format));

        $this->max_date = $now_date;

        $seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);
        $o_seasons = "";
        foreach ($seasons as $season) {
            $season_start_date = new DateTime(get_post_meta($season->ID, "_obcal_season_start_date", true));
            $season_end_date = new DateTime(get_post_meta($season->ID, "_obcal_season_end_date", true));

            $season_price_per_night = get_post_meta($accommodation->ID, "_obcal_accommodation_s{$season->ID}_price_per_night", true);

            if ($season->post_status == "publish" && $season_price_per_night > 0 && $season_end_date >= $now_date) {
                // Register season ID
                $this->active_season_ids[] = $season->ID;
                // Generate the season item to be printed
                $o_seasons .= "<li>" . esc_html($season->post_title) . ($obcal_atts['show_seasons_dates'] == true ? '<span class="season-dates">' . esc_html("({$season_start_date->format($options_date_format)} - {$season_end_date->format($options_date_format)})") . "</span>" : "") . ($obcal_atts['show_seasons_price'] == true ? '<span class="season-price">$' . esc_html($season_price_per_night . " " . __('per night', 'open-booking-calendar')) . "</span>" : "") . "</li>";

                // Register max_date for flatpickr
                if ($season_end_date > $this->max_date) {
                    $this->max_date = $season_end_date;
                }

                // Register active_season_date (array of all dates of the seasons) for flatpickr
                $interval = new DateInterval('P1D');
                $daterange = new DatePeriod($season_start_date, $interval ,$season_end_date->modify( '+1 day' ));
                foreach($daterange as $date){
                    $this->active_season_dates[] = $date->format($options_date_format);
                }

            }
        }

        if ($obcal_atts['show_seasons'] == true) {
            $o .= '<div class="obcal-seasons"><h3>' . esc_html__('Seasons', 'open-booking-calendar') . '</h3>';
            if (empty($o_seasons)) {
                $o .= '<p>' . esc_html__('No active seasons were found with available dates.', 'open-booking-calendar') . '</p>';
            } else {
                $o .= '<ul>';
                $o .= $o_seasons;
                $o .= '</ul>';
            }
            $o .= '</div>';
        }

        return $o;
    }

    /**
     * Show promotions in the shortcode content
     */
    private function content_promotions($obcal_atts, $accommodation)
    {

        // start output
        $o = '';

        $promotions = get_posts(['post_type' => 'obcal_promotion', 'numberposts' => -1]);
        $o_promotions = "";
        foreach ($promotions as $promotion) {

            $promotion_accommodation_id = get_post_meta($promotion->ID, "_obcal_promotion_accommodation_id", true);
            $promotion_season_id = get_post_meta($promotion->ID, "_obcal_promotion_season_id", true);

            if ($promotion->post_status == "publish" && $promotion_accommodation_id == $accommodation->ID && in_array($promotion_season_id, $this->active_season_ids)) {

                $promotion_season = get_post($promotion_season_id);

                $promotion_num_nights = get_post_meta($promotion->ID, "_obcal_promotion_num_nights", true);
                $promotion_total_price = get_post_meta($promotion->ID, "_obcal_promotion_total_price", true);

                $o_promotions .= "<li>";
                $o_promotions .= esc_html($promotion->post_title) . ':<span class="promotion-num-nights">' . esc_html($promotion_num_nights . ' ' . __('nights', 'open-booking-calendar')) . '</span>';
                $o_promotions .= '<span class="promotion-price"> ' . esc_html( __('for', 'open-booking-calendar') . ' $' . $promotion_total_price ) . '</span>';
                $o_promotions .= '<span class="promotion-season"> ' . esc_html( '(' . __('Season', 'open-booking-calendar') . ': ' . $promotion_season->post_title ) . ')</span>';
                $o_promotions .= "</li>";

            }
        }

        if ($obcal_atts['show_promotions'] == true) {
            $o .= '<div class="obcal-promotions"><h3>' . esc_html__('Promotions', 'open-booking-calendar') . '</h3>';
            if (empty($o_promotions)) {
                $o .= '<p>' . esc_html__('No active promotions were found with available dates.', 'open-booking-calendar') . '</p>';
            } else {
                $o .= '<ul>';
                $o .= $o_promotions;
                $o .= '</ul>';
            }
            $o .= '</div>';
        }

        return $o;
    }

    /**
     * Show availability calendar in the shortcode content
     */
    private function content_calendar($obcal_atts, $accommodation)
    {

        // start output
        $o = '';

        // Get the options
        $options = get_option('obcal_options');

        // Get date format
        $options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        /*
        $this->active_season_dates = [
            '10-05-2019',
            '11-05-2019',
            '12-05-2019',
            '13-05-2019',
            '14-05-2019',
            '15-05-2019',
            '16-05-2019',
            '17-05-2019',
        ];
        */

        /**
         * Search reserved booking dates
         */

        $reserved_dates = [];

        $bookings = get_posts(['post_type' => 'obcal_booking', 'numberposts' => -1]);
        foreach ($bookings as $booking) {

            $check_in_date = new DateTime(get_post_meta($booking->ID, "_obcal_booking_check_in_date", true));
            $check_out_date = new DateTime(get_post_meta($booking->ID, "_obcal_booking_check_out_date", true));
            $status = get_post_meta($booking->ID, "_obcal_booking_status", true);

            if ($status == 'pending' || $status == 'confirmed') {

                $accommodation_exclusivity_last_day = get_post_meta($accommodation->ID, "_obcal_accommodation_exclusivity_last_day", true);

                // Include as reserved the last day if the exclusivity of the last day is activated
                $check_out_date_for_range = clone $check_out_date;
                if ($accommodation_exclusivity_last_day) {
                    $check_out_date_for_range->modify( '+1 day' );
                }

                // Register reserved_dates (array of all booking dates) for flatpickr
                $interval_1d = new DateInterval('P1D');
                $daterange = new DatePeriod($check_in_date, $interval_1d ,$check_out_date_for_range);
                foreach($daterange as $date){
                    $reserved_dates[] = $date->format($options_date_format);
                }

            }

        }

        /*
        $reserved_dates = [
            '10-05-2019',
            //'11-05-2019',
            '12-05-2019',
            '13-05-2019',
            '14-05-2019',
            '15-05-2019',
            '16-05-2019',
            '17-05-2019',
        ];
        */

        /**
         * Calendar
         */

        // limit min and max dates in the calendar
        $flatpickr_min_date = 'today';
        $flatpickr_max_date = $this->max_date->format($options_date_format);

        // convert dates to comma separated strings
        $flatpickr_enable_dates = implode(",", array_diff($this->active_season_dates, $reserved_dates));
        $flatpickr_reserved_dates = implode(",", $reserved_dates);

        // if not available dates to enable in the calendar
        if (empty($flatpickr_enable_dates)) {

            // disable all calendar
            $disable_ranges = [
                [
                    'from' => '01-01-2000',
                    'to' => '31-12-2099',
                ],
            ];

        } else {

            // not disable calendar
            $disable_ranges = [];

        }

        // Get the minimum number of selectable nights
        $min_num_nights = get_post_meta($accommodation->ID, "_obcal_accommodation_min_num_nights", true);

        // Get the minimum number of selectable nights for flatpickr
        $flatpickr_min_num_nights = !empty($min_num_nights) ? $min_num_nights : 0;

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        // calendar
        $o .= '<div class="obcal-calendar"><h3>' . esc_html__('Availability calendar', 'open-booking-calendar') . '</h3>';
        $o .= '<input type="hidden" id="obc_cal_inline" value="true">';
        $o .= '<input type="hidden" id="obc_cal_mode" value="range">';
        $o .= '<input type="hidden" id="obc_cal_dateFormat" value="' . esc_attr($options_date_format) . '">';
        $o .= '<input type="hidden" id="obc_cal_minDate" value="' . esc_attr($flatpickr_min_date) . '">';
        $o .= '<input type="hidden" id="obc_cal_maxDate" value="' . esc_attr($flatpickr_max_date) . '">';
        $o .= '<input type="hidden" id="obc_cal_enable" value="' . esc_attr($flatpickr_enable_dates) . '">';
        $o .= '<input type="hidden" id="obc_cal_disable" value="' . esc_attr(wp_json_encode($disable_ranges)) . '">';
        $o .= '<input type="hidden" id="obc_cal_reserved" value="' . esc_attr($flatpickr_reserved_dates) . '">';
        $o .= '<input type="hidden" id="obc_cal_minNumNights" value="' . esc_attr($flatpickr_min_num_nights) . '">';
        $o .= '<input class="flatpickr flatpickr-input availability-calendar-input" type="text" placeholder="' . esc_html__('Select Date..', 'open-booking-calendar') . '" readonly="readonly">';
        $o .= '</div>';

        /**
         * Show an error message if the number of selected nights is less than the minimum allowed
         * Hidden by default
         */
        $o .= '<div class="obcal-notice obcal-notice-error min-num-nights-error">';
        $o .= sprintf( esc_html__( 'The minimum number of nights is %s (%s days).', 'open-booking-calendar' ), $flatpickr_min_num_nights, $flatpickr_min_num_nights + 1 );
        $o .= '</div>';

        return $o;
    }

    /**
     * Show booking preview form in the shortcode content
     */
    private function content_form($obcal_atts, $accommodation)
    {

        // start output
        $o = '';

        $booking_preview_page_id = get_post_meta($accommodation->ID, "_obcal_accommodation_booking_preview_page_id", true);

        $o .= '<div class="obcal-form">';
        $o .= '<h3>' . esc_html__('Booking form', 'open-booking-calendar') . '</h3>';
        $o .= '<form method="POST" action="' . esc_url(get_permalink($booking_preview_page_id)) . '">';
        $o .= wp_nonce_field('booking_form_nonce', '_wp_booking_form_nonce', true, false);
        $o .= '<input type="hidden" name="accommodation_id" value="' . esc_attr($accommodation->ID) . '">';

        $o .= '<table class="obcal-form-table">';

        $o .= '<tr><th scope="row">';
        $o .= '<label for="selected_date">' . esc_html__('Select Date..', 'open-booking-calendar') . '</label>';
        $o .= '</th><td>';
        $o .= '<div><input type="text" id="selected_date" name="selected_date" class="form-selected-date" readonly="readonly" required="required" placeholder="' . esc_html__('Select Date..', 'open-booking-calendar') . '" ></div>';
        $o .= '</td></tr>';

        $o .= '<tr><th scope="row">';
        $o .= '<label for="num_adults">' . esc_html__('Number of adults', 'open-booking-calendar') . '</label>';
        $o .= '</th><td>';
        $o .= '<div><input type="number" min="1" id="num_adults" name="num_adults" placeholder="' . esc_html__('Number of adults', 'open-booking-calendar') . '" required="required"></div>';
        $o .= '</td></tr>';

        $o .= '<tr><th scope="row">';
        $o .= '<label for="num_children">' . esc_html__('Number of children', 'open-booking-calendar') . '</label>';
        $o .= '</th><td>';
        $o .= '<div><input type="number" min="0" id="num_children" name="num_children" placeholder="' . esc_html__('Number of children', 'open-booking-calendar') . '" required="required" ></div>';
        $o .= '</td></tr>';

        $o .= '<tr><th scope="row">';
        $o .= '<label for="us_name">' . esc_html__('Your name', 'open-booking-calendar') . '</label>';
        $o .= '</th><td>';
        $o .= '<div><input type="text" id="us_name" name="us_name" placeholder="' . esc_html__('Your name', 'open-booking-calendar') . '" required="required" ></div>';
        $o .= '</td></tr>';

        $o .= '<tr><th scope="row">';
        $o .= '<label for="us_email">' . esc_html__('Your email', 'open-booking-calendar') . '</label>';
        $o .= '</th><td>';
        $o .= '<div><input type="email" id="us_email" name="us_email" placeholder="' . esc_html__('Your email', 'open-booking-calendar') . '" required="required" ></div>';
        $o .= '</td></tr>';

        $o .= '</table>';

        $o .= '<div><input type="submit" value="' . esc_html__('Preview booking', 'open-booking-calendar') . '" ></div>';

        $o .= '</form>';
        $o .= '</div>';


        return $o;
    }

    public function localize_script()
    {

        $lang = substr(get_option('WPLANG'), 0, 2);

        wp_enqueue_script('flatpickr-lang', 'https://npmcdn.com/flatpickr/dist/l10n/' . $lang . '.js', array('jquery', 'flatpickr'), $this->version, false);

        wp_localize_script(
            $this->open_booking_calendar,
            'flatpickr_l10n',
            [
                'locale' => $lang,
                'out_season' => esc_html__('Out of season', 'open-booking-calendar'),
                'date_reserved' => esc_html__('Date already reserved', 'open-booking-calendar'),
                'select_in_calendar' => esc_html__('Select your dates in the calendar', 'open-booking-calendar'),
            ]
        );
    }

    public function enqueue_flatpickr_styles()
    {
        // get the options
        $options = get_option('obcal_options');

        if (isset($options['obcal_field_flatpickr_theme']) && $options['obcal_field_flatpickr_theme'] != "default") {
            wp_enqueue_style( 'flatpickr-theme', 'https://npmcdn.com/flatpickr/dist/themes/' . $options['obcal_field_flatpickr_theme'] . '.css', array(), $this->version, 'all' );
        }
    }
}
