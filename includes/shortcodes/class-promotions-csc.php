<?php

/**
 * Promotion list
 */
class Promotions_CSC
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
        add_shortcode('obc_promotions', [ $this, 'content' ]);
    }

    public function content($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // override default attributes with user attributes
        $obcal_atts = shortcode_atts([
            'title' => __('Our current promotions', 'open-booking-calendar')
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
        $o .= '<div class="obcal-promotions-csc">';

        // Show promotions
        $o .= $this->content_promotions($obcal_atts);

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
     * Show promotions in the shortcode content
     */
    private function content_promotions($obcal_atts) {

        // start output
        $o = '';

        // Get the options
        $options = get_option('obcal_options');

        // Get date format
        $options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

        /**
         * Find IDs of active seasons
         */

        $active_season_ids = [];

        $seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);
        foreach ($seasons as $season) {
            $season_end_date = new DateTime(get_post_meta($season->ID, "_obcal_season_end_date", true));

            $now_date = new DateTime(date_i18n($options_date_format));

            if ($season->post_status == "publish" && $season_end_date >= $now_date) {
                // Register season ID
                $active_season_ids[] = $season->ID;
            }
        }

        /**
         * Get and return promotions for published accommodations with active seasons
         */

        $promotions = get_posts(['post_type' => 'obcal_promotion', 'numberposts' => -1]);
        $o_promotions = "";
        foreach ($promotions as $promotion) {

            $promotion_accommodation_id = get_post_meta($promotion->ID, "_obcal_promotion_accommodation_id", true);
            $promotion_season_id = get_post_meta($promotion->ID, "_obcal_promotion_season_id", true);

            $promotion_accommodation = get_post($promotion_accommodation_id);

            if ($promotion->post_status == "publish" && $promotion_accommodation->post_status == "publish" && in_array($promotion_season_id, $active_season_ids)) {

                $promotion_num_nights = get_post_meta($promotion->ID, "_obcal_promotion_num_nights", true);
                $promotion_total_price = get_post_meta($promotion->ID, "_obcal_promotion_total_price", true);

                $promotion_accommodation_page_id = get_post_meta($promotion_accommodation_id, "_obcal_accommodation_info_page_id", true);
                $promotion_booking_preview_page_id = get_post_meta($promotion_accommodation->ID, "_obcal_accommodation_booking_preview_page_id", true);
           
                $o_promotions .= "<li>";
                $o_promotions .= "<h3>{$promotion->post_title}</h3>";
                $o_promotions .= '<div class="promotion-num-nights">' . __('Accommodation', 'open-booking-calendar') . ': ' . $promotion_accommodation->post_title . ' ' . __('nights', 'open-booking-calendar') . '</div>';
                $o_promotions .= '<div class="promotion-num-nights">' . __('Number of nights', 'open-booking-calendar') . ': ' . $promotion_num_nights . ' ' . __('nights', 'open-booking-calendar') . '</div>';
                $o_promotions .= '<div class="promotion-price">' . __('Price', 'open-booking-calendar') . ': $' . $promotion_total_price . '</div>';

                //$o_promotions .= '<form method="POST" action="' . esc_url(get_permalink($promotion_booking_preview_page_id)) . '">';
                //$o_promotions .= '<input type="hidden" name="accommodation_id" value="' . $promotion_accommodation_id . '">';
                //$o_promotions .= '<input type="submit" value="' . __('Preview booking', 'open-booking-calendar') . '" >';
                //$o_promotions .= '</form>';

                $o_promotions .= '<a href="' . esc_url(get_permalink($promotion_accommodation_page_id)) . '">' . __('More about this accommodation', 'open-booking-calendar') . '</a>';

                $o_promotions .= '</li>';

            }
        }

        $o .= '<div class="obcal-promotions">';

        if (!empty($obcal_atts['title'])){
            $o .= '<h2>' . esc_html($obcal_atts['title']) . '</h2>';
        }

        if (empty($o_promotions)){
            $o .= '<p>' . __('No active promotions were found with available dates.', 'open-booking-calendar') . '</p>';
        } else {
            $o .= '<ul>';
            $o .= $o_promotions;
            $o .= '</ul>';
        }
        $o .= '</div>';

        return $o;
    }

}
