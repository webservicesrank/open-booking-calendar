<?php

namespace OBCal;

class Accommodation_CMB
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

	protected $post_type = 'obcal_accommodation';

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
	
	/**
	 * Register Meta Boxes.
	 */
	public function register(){

		add_meta_box(
			"{$this->post_type}_details",																					// Unique ID
			__('Capacity', 'open-booking-calendar'),															// Box title
			[$this, 'details_html'],  																						// Content callback, must be of type callable
			$this->post_type             																					// Post type
		);

		$seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);

		foreach ($seasons as $season){
			add_meta_box(
				"{$this->post_type}_season_{$season->ID}" ,													// Unique ID
				__('Season', 'open-booking-calendar') . ': ' . $season->post_title,	// Box title
				[$this, 'season_html'],  																						// Content callback, must be of type callable
				$this->post_type,             																			// Post type (screen)
				'advanced',																													// context
				'default',																													// priority
				[$season]																														// callback_args
			);
		}

		add_meta_box(
			"{$this->post_type}_pages",																						// Unique ID
			__('Pages', 'open-booking-calendar'),																	// Box title
			[$this, 'pages_html'],  																							// Content callback, must be of type callable
			$this->post_type             																					// Post type
		);

		add_meta_box(
			"{$this->post_type}_booking",																					// Unique ID
			__('Booking settings', 'open-booking-calendar'),											// Box title
			[$this, 'booking_html'],  																						// Content callback, must be of type callable
			$this->post_type             																					// Post type
		);

	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function details_html($post)
	{
		$max_adults = get_post_meta($post->ID, "_{$this->post_type}_max_adults", true);
		$max_children = get_post_meta($post->ID, "_{$this->post_type}_max_children", true);
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_max_adults') ?>"><?php esc_html_e('Maximum adults', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="number" min="1" name="<?= esc_attr($this->post_type . '_max_adults') ?>" id="<?= esc_attr($this->post_type . '_max_adults') ?>" value="<?= esc_attr($max_adults) ?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_max_children') ?>"><?php esc_html_e('Maximum children', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="number" min="0" name="<?= esc_attr($this->post_type . '_max_children') ?>" id="<?= esc_attr($this->post_type . '_max_children') ?>" value="<?= esc_attr($max_children) ?>">
				</td>
			</tr>		
		</table>			
		<?php
	}

	/**
	 * HTML content of the Seasons Meta Boxes.
	 */
	public function season_html($post, $args)
	{
		$season = $args['args'][0];

		$price_per_night = get_post_meta($post->ID, "_{$this->post_type}_s{$season->ID}_price_per_night", true);
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_season_' . $season->ID . '_price_per_night') ?>"><?php esc_html_e('Price per night', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					$<input type="number" min="0" name="<?= esc_attr($this->post_type . '_season_' . $season->ID . '_price_per_night') ?>" id="<?= esc_attr($this->post_type . '_season_' . $season->ID . '_price_per_night') ?>" value="<?= esc_attr($price_per_night) ?>">
				</td>
			</tr>
		</table>			
		<?php
	}

	/**
	 * HTML content of the Pages Meta Box.
	 */
	public function pages_html($post)
	{

		$info_page_id = get_post_meta($post->ID, "_{$this->post_type}_info_page_id", true);
		$booking_preview_page_id = get_post_meta($post->ID, "_{$this->post_type}_booking_preview_page_id", true);
		$booking_received_page_id = get_post_meta($post->ID, "_{$this->post_type}_booking_received_page_id", true);

		$pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_info_page_id') ?>"><?php esc_html_e('Accommodation page', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_info_page_id') ?>" id="<?= esc_attr($this->post_type . '_info_page_id') ?>" >
						<option value="0">
							<?php esc_html_e( '-- Create a new page for this accommodation --', 'open-booking-calendar' ) ?>
						</option>
						<?php
						foreach ($pages as $page){
						?>
						<option value="<?= esc_attr($page->ID) ?>" <?php selected( $page->ID, $info_page_id, true ); ?>>
							<?= esc_html( $page->post_title ) ?>
						</option>
						<?php
						}						
						?>
					</select>
					<p class="description">
			            <?php esc_html_e('Select the first item to create a new page for this accommodation or select an existing page.', 'open-booking-calendar'); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_booking_preview_page_id') ?>"><?php esc_html_e('Booking preview page', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_booking_preview_page_id') ?>" id="<?= esc_attr($this->post_type . '_booking_preview_page_id') ?>" >
						<?php
						foreach ($pages as $page){
						?>
						<option value="<?= esc_attr($page->ID) ?>" <?php selected( $page->ID, $booking_preview_page_id, true ); ?>>
							<?= esc_html( $page->post_title ) ?>
						</option>
						<?php
						}						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_booking_received_page_id') ?>"><?php esc_html_e('Booking received page', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_booking_received_page_id') ?>" id="<?= esc_attr($this->post_type . '_booking_received_page_id') ?>" >
						<?php
						foreach ($pages as $page){
						?>
						<option value="<?= esc_attr($page->ID) ?>" <?php selected( $page->ID, $booking_received_page_id, true ); ?>>
							<?= esc_html( $page->post_title ) ?>
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
	 * HTML content of the Booking Settings Meta Box.
	 */
	public function booking_html($post)
	{

		$exclusivity_last_day = get_post_meta($post->ID, "_{$this->post_type}_exclusivity_last_day", true);
		$min_num_nights = get_post_meta($post->ID, "_{$this->post_type}_min_num_nights", true);

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_exclusivity_last_day') ?>"><?php esc_html_e('Exclusivity of the last day', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<select name="<?= esc_attr($this->post_type . '_exclusivity_last_day') ?>" id="<?= esc_attr($this->post_type . '_exclusivity_last_day') ?>" >
						<option value="1" <?php selected( '1', $exclusivity_last_day, true ); ?>>
							<?php esc_html_e('Activated, the last day is not shared', 'open-booking-calendar')?>
						</option>
						<option value="0" <?php selected( '0', $exclusivity_last_day, true ); ?>>
							<?php esc_html_e('Deactivated, can be booked on the last day', 'open-booking-calendar')?>
						</option>
					</select>
					<p class="description">
            <?php esc_html_e('Ideal for accommodation where it is possible that the day of check-out of a client is also the day of check-in of a new client.', 'open-booking-calendar'); ?>
  	      </p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_min_num_nights') ?>"><?php esc_html_e('Minimum number of nights', 'open-booking-calendar'); ?></label>
				</th>
				<td>
					<input type="number" min="1" name="<?= esc_attr($this->post_type . '_min_num_nights') ?>" id="<?= esc_attr($this->post_type . '_min_num_nights') ?>" value="<?= esc_attr($min_num_nights) ?>">
					<p class="description">
			            <?php esc_html_e('Minimum number of nights that a visitor or client can select for a reservation.', 'open-booking-calendar'); ?>
	        		</p>
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
		$keys_to_save_directly = ['max_adults', 'max_children', 'info_page_id', 'booking_preview_page_id', 'booking_received_page_id', 'exclusivity_last_day', 'min_num_nights'];

		// Seasons to save their prices
		$seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);

		/** 
		 * Sanitize POST values
		 */

		// Sanitize values for 'Save values in array directly'
		foreach ($keys_to_save_directly as $key_to_save) {
			if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {
				$_POST["{$this->post_type}_{$key_to_save}"] = sanitize_key($_POST["{$this->post_type}_{$key_to_save}"]);
			}
		}

		// Sanitize prices per night of the seasons
		foreach ($seasons as $season){
			if (array_key_exists("{$this->post_type}_season_{$season->ID}_price_per_night", $_POST)) {
				$_POST["{$this->post_type}_season_{$season->ID}_price_per_night"] = sanitize_text_field($_POST["{$this->post_type}_season_{$season->ID}_price_per_night"]);
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

				// Validate 'max_adults', 'min_num_nights'
				if (($key_to_save == 'max_adults' || $key_to_save == 'min_num_nights' ) && (!is_numeric($value) || $value < 1)) {
					$_POST["{$this->post_type}_{$key_to_save}"] = 1;
				}

				// Validate 'info_page_id' (0 for create a new page), 'max_children'
				if (($key_to_save == 'info_page_id' || $key_to_save == 'max_children') && (!is_numeric($value) || $value < 0)) {
					$_POST["{$this->post_type}_{$key_to_save}"] = 0;
				}

				// Validate 'booking_preview_page_id', 'booking_received_page_id'
				if (($key_to_save == 'booking_preview_page_id' || $key_to_save == 'booking_received_page_id') && (!is_numeric($value) || $value < 1)) {
					$_POST["{$this->post_type}_{$key_to_save}"] = '';
				}

				// Validate 'exclusivity_last_day'
				if ($key_to_save == 'exclusivity_last_day' && (!is_numeric($value) || !in_array($value, ['0', '1']))) {
					$_POST["{$this->post_type}_{$key_to_save}"] = 0;
				}

			}
		}
		
		// Validate prices per night of the seasons
		foreach ($seasons as $season){
			if (array_key_exists("{$this->post_type}_season_{$season->ID}_price_per_night", $_POST)) {

				// Get POST value
				$value = $_POST["{$this->post_type}_season_{$season->ID}_price_per_night"];

				// Validate price per night
				if (!is_numeric($value) || $value < 0) {
					$_POST["{$this->post_type}_season_{$season->ID}_price_per_night"] = 0;
				}

			}
		}


		/**
		 * Create a new page for this accommodation (if the selected option is '0' [create])
		 */

		if (array_key_exists("{$this->post_type}_info_page_id", $_POST) && $_POST["{$this->post_type}_info_page_id"] == '0') { // '0' for create new page

			// Content for the new page
			$post_content = '<!-- wp:shortcode -->';
			$post_content .= '[obc_booking_calendar id="' . $post_id . '" show_seasons="true" show_seasons_dates="true" show_seasons_price="true" show_promotions="true"]';
			$post_content .= '<!-- /wp:shortcode -->';

			// Set meta values for the new page
			$meta_input = [];

			// Create the new page
			$info_page_id = wp_insert_post([
				'post_title'    => wp_strip_all_tags( isset($_POST["post_title"]) ? $_POST["post_title"] : $post_id ),
				'post_content' => $post_content,
				'post_status'   => 'publish',
				'comment_status'  => 'closed',
				'ping_status'   => 'closed',
				'post_type'   => 'page',
				'meta_input' => $meta_input
			]);
			
			// Set the new page ID for save in 'Save values in array directly'
			$_POST["{$this->post_type}_info_page_id"] = $info_page_id;

		}


		/** 
		 * Save values in array directly
		 */

		foreach ($keys_to_save_directly as $key_to_save) {
			if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {
				update_post_meta(
					$post_id,
					"_{$this->post_type}_{$key_to_save}",
					$_POST["{$this->post_type}_{$key_to_save}"]
				);
			}
		}

		/** 
		 * Save seasons values
		 */
		
		foreach ($seasons as $season){
			if (array_key_exists("{$this->post_type}_season_{$season->ID}_price_per_night", $_POST)) {
				update_post_meta(
					$post_id,
					"_{$this->post_type}_s{$season->ID}_price_per_night",
					$_POST["{$this->post_type}_season_{$season->ID}_price_per_night"]
				);
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
			'max_adults' => esc_html__('Maximum Adults', 'open-booking-calendar'),
			'max_children' => esc_html__('Maximum Children', 'open-booking-calendar'),
			'date' => $columns['date'],
		];
		return $new_columns;
	}

	/**
	 * Add Custom Columns Data to the Post Type Table.
	 */
	public function add_table_custom_values( $column, $post_id ) {
		switch ( $column ) {
		  case 'max_adults':
			echo esc_html( get_post_meta( $post_id , "_{$this->post_type}_max_adults" , true ) );
			break;
		case 'max_children':
			echo esc_html( get_post_meta( $post_id , "_{$this->post_type}_max_children" , true ) );
			break;
		}
	}

	/**
	 * Register the columns as sortable.
	 */
	public function register_table_sortable_columns( $columns ) {
		$columns['max_adults'] = 'Maximum Adults';
		$columns['max_children'] = 'Maximum Children';
		return $columns;
	}
}