<?php

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
	 * The contact log custom post type object.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $contact_log_cpt    The contact log custom post type object.
	 */
    private $contact_log_cpt;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $open_booking_calendar       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($open_booking_calendar, $version, $contact_log_cpt)
    {

        $this->open_booking_calendar = $open_booking_calendar;
        $this->version = $version;

        $this->contact_log_cpt = $contact_log_cpt;

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
                $contact_id = $this->contact_log_cpt->insert_contact($_POST['us_name'], $_POST['us_email']);

                if (!empty($contact_id)) {

                    // Register Query in the Log 
                    $this->contact_log_cpt->insert_query_log($contact_id, 'search_results', $_POST);

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

        $accommodations = get_posts(['post_type' => 'obcal_accommodation', 'numberposts' => -1]);

        // Title
        if ($obcal_atts['show_title'] == true) {
            $o .= '<h3>' . __('Search accommodations', 'open-booking-calendar') . '</h3>';
        }

        $o .= '<div class="obcal-form">';

        $o .= '<table class="obcal-form-table">';

        foreach ($accommodations as $accommodation) {

            if ($accommodation->post_status == "publish") {

                $accommodation_url = esc_url(get_permalink($accommodation->ID));

                $o .= '<tr><th scope="row">';

                $o .= '<a href="' . $accommodation_url . '">' . get_the_post_thumbnail( $accommodation->ID, 'post-thumbnail', array( 'class' => 'accommodation-img' ) ) . '</a>';

                $o .= '</th><td>';

                $o .= '<a href="' . $accommodation_url . '"><h3>' . esc_html($accommodation->post_title) . '</h3></a>';
                $o .= '<p>' . esc_html($accommodation->post_excerpt) . '</p>';

                $o .= '</td></tr>';

            }

        }

        $o .= '</table>';

        $o .= '</div>';

        return $o;
    }

}
