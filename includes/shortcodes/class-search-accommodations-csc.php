<?php

/**
 * Search Accommodations Custom Shortcode
 */
class SearchAccommodations_CSC
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
        add_shortcode('obc_search_accommodations', [ $this, 'content' ]);
    }

    public function content($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $obcal_atts = shortcode_atts([
            'show_title' => true,
            'show_num_adults_field' => true,
            'show_num_children_field' => true,
            'show_name_email_fields' => true,
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
        $o .= '<div class="obcal-search-accommodations-csc">';

        // Show search accommodation form
        $o .= $this->content_search_form($obcal_atts);

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
     * Show search accommodations form in the shortcode content
     */
    private function content_search_form($obcal_atts) {

        // start output
        $o = '';

        // Get the options
        $options = get_option('obcal_options');

        // Get date format
        $options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        // Get search results page id
        $search_results_page_id = isset($options['obcal_field_search_results_page_id']) ? $options['obcal_field_search_results_page_id'] : '';

        // Calendar settings: limit min and max dates in the calendar
        $flatpickr_min_date = 'today';
        //$flatpickr_max_date = $this->max_date->format($options_date_format);

        // Title
        if ($obcal_atts['show_title'] == true) {
            $o .= '<h3>' . esc_html__('Search accommodations', 'open-booking-calendar') . '</h3>';
        }

        // form

        $o .= '<div class="obcal-form">';

        $o .= '<form method="POST" action="' . esc_url(get_permalink($search_results_page_id)) . '">';

        $o .= wp_nonce_field('search_accommodations_form_nonce', '_wp_search_accommodations_form_nonce', true, false);

        $o .= '<input type="hidden" name="available" value="1">';

        $o .= '<table class="obcal-form-table">';

        $o .= '<tr><th scope="row">';
        $o .= '<label for="selected_date">' . esc_html__('Select Date..', 'open-booking-calendar') . '</label>';
        $o .= '</th><td>';
        // calendar
        $o .= '<input type="hidden" id="obc_cal_inline" value="false">';
        $o .= '<input type="hidden" id="obc_cal_mode" value="range">';
        $o .= '<input type="hidden" id="obc_cal_dateFormat" value="' . esc_attr($options_date_format) . '">';
        $o .= '<input type="hidden" id="obc_cal_minDate" value="' . esc_attr($flatpickr_min_date) . '">';
        //$o .= '<input type="hidden" id="obc_cal_maxDate" value="' . esc_attr($flatpickr_max_date) . '">';
        $o .= '<input class="flatpickr flatpickr-input search-calendar-input" type="text" id="selected_date" name="selected_date" placeholder="' . esc_html__('Select Date..', 'open-booking-calendar') . '" readonly="readonly" required="required">';
        $o .= '</td></tr>';

        if ($obcal_atts['show_num_adults_field'] == true) {
            $o .= '<tr><th scope="row">';
            $o .= '<label for="num_adults">' . esc_html__('Number of adults', 'open-booking-calendar') . '</label>';
            $o .= '</th><td>';
            $o .= '<div><input type="number" min="1" id="num_adults" name="num_adults" placeholder="' . esc_html__('Number of adults', 'open-booking-calendar') . '" required="required"></div>';
            $o .= '</td></tr>';
        }

        if ($obcal_atts['show_num_children_field'] == true) {
            $o .= '<tr><th scope="row">';
            $o .= '<label for="num_children">' . esc_html__('Number of children', 'open-booking-calendar') . '</label>';
            $o .= '</th><td>';
            $o .= '<div><input type="number" min="0" id="num_children" name="num_children" placeholder="' . esc_html__('Number of children', 'open-booking-calendar') . '" required="required" ></div>';
            $o .= '</td></tr>';
        }

        if ( $obcal_atts['show_name_email_fields'] == true ) {
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
        }

        $o .= '</table>';

        $o .= '<div><input type="submit" value="' . esc_html__('Search', 'open-booking-calendar') . '" ></div>';

        $o .= '</form>';
        $o .= '</div>';

        return $o;
    }

}
