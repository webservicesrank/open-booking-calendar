<?php

namespace OBCal;

/**
 * Search Results Custom Shortcode
 */
class SearchResults_CSC
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
        add_shortcode('obc_search_results', [ $this, 'content' ]);
    }

    public function content($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $obcal_atts = shortcode_atts([
            'show_title' => false,
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
        $o .= '<div class="obcal-search-results-csc">';

        /**
         * Verify form nonce
         */
        if ( ! isset( $_POST['_wp_search_accommodations_form_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_search_accommodations_form_nonce'], 'search_accommodations_form_nonce' ) ) {

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
                    do_action('obcal_contact_log_insert_query_log', $contact_id, 'search_results', $_POST);

                }

            }

            // Show search results
            $o .= $this->content_search_results($obcal_atts);

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
     * Show search results in the shortcode content
     */
    private function content_search_results($obcal_atts) {

        // start output
        $o = '';

        // Get the options
        $options = get_option('obcal_options');

        // Get date format
        $options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        /**
         * Get POST values
         */

        $selected_date = sanitize_text_field($_POST['selected_date']);

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

        // Get dates as objects
        $search_check_in_date = new \DateTime($check_in_date);
        $search_check_out_date = new \DateTime($check_out_date);

        /**
         * Find active seasons in the search date period
         */

        $relevant_season_ids = [];

        $now_date = new \DateTime(date_i18n($options_date_format));

        $seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);
        foreach ($seasons as $season) {
            $season_start_date = new \DateTime(get_post_meta($season->ID, "_obcal_season_start_date", true));
            $season_end_date = new \DateTime(get_post_meta($season->ID, "_obcal_season_end_date", true));

            // If is a current an published season
            if ($season->post_status == "publish" && $season_end_date >= $now_date) {

                // If is a season with intersections with the search date period
                if ( ($search_check_in_date >= $season_start_date && $search_check_in_date <= $season_end_date) || ($search_check_out_date >= $season_start_date && $search_check_out_date <= $season_end_date) ) {

                    // Register relevant season
                    $relevant_season_ids[] = $season->ID;

                }

            }
        }

        /**
         * 
         */

        $num_accommodations = 0;

        $accommodations = get_posts(['post_type' => 'obcal_accommodation', 'numberposts' => -1]);

        // Title
        if ($obcal_atts['show_title'] == true) {
            $o .= '<h3>' . esc_html__('Search accommodations', 'open-booking-calendar') . '</h3>';
        }

        $o .= '<div class="obcal-form">';

        $o .= '<table class="obcal-form-table">';

        foreach ($accommodations as $accommodation) {

            if ($accommodation->post_status == "publish") {

                $has_active_seasons = false;

                foreach ($relevant_season_ids as $relevant_season_id) {

                    $season_price_per_night = get_post_meta($accommodation->ID, "_obcal_accommodation_s{$relevant_season_id}_price_per_night", true);

                    if (is_numeric($season_price_per_night) && $season_price_per_night > 0) {
                        $has_active_seasons = true;
                    }

                }

                if ($has_active_seasons) {

                    $accommodation_url = esc_url(get_permalink($accommodation->ID));

                    $o .= '<tr><th scope="row">';

                    $o .= '<a href="' . $accommodation_url . '">' . get_the_post_thumbnail( $accommodation->ID, 'post-thumbnail', array( 'class' => 'accommodation-img' ) ) . '</a>';

                    $o .= '</th><td>';

                    $o .= '<a href="' . $accommodation_url . '"><h3>' . esc_html($accommodation->post_title) . '</h3></a>';
                    $o .= '<p>' . esc_html($accommodation->post_excerpt) . '</p>';

                    $o .= '</td></tr>';

                    $num_accommodations++;

                }
            }

        }

        $o .= '</table>';

        if ($num_accommodations == 0) {

            $o .= '<div class="obcal-notice obcal-notice-info availability-error">';
            $o .= esc_html__('There are currently no accommodations available in the selected dates period.', 'open-booking-calendar');
            $o .= '</div>';

        }

        $o .= '</div>';

        return $o;
    }

}
