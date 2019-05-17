<?php

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
			"{$this->post_type}_details",							// Unique ID
			__('Capacity', 'open-booking-calendar'),	// Box title
			[$this, 'details_html'],  								// Content callback, must be of type callable
			$this->post_type             							// Post type
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
			"{$this->post_type}_pages",								// Unique ID
			__('Pages', 'open-booking-calendar'),			// Box title
			[$this, 'pages_html'],  									// Content callback, must be of type callable
			$this->post_type             							// Post type
		);

		add_meta_box(
			"{$this->post_type}_booking",											// Unique ID
			__('Booking settings', 'open-booking-calendar'),	// Box title
			[$this, 'booking_html'],  												// Content callback, must be of type callable
			$this->post_type             											// Post type
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
					<label for="<?=$this->post_type?>_max_adults"><?=__('Maximum adults', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="number" name="<?=$this->post_type?>_max_adults" id="<?=$this->post_type?>_max_adults" value="<?=$max_adults?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_max_children"><?=__('Maximum children', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="number" name="<?=$this->post_type?>_max_children" id="<?=$this->post_type?>_max_children" value="<?=$max_children?>">
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
					<label for="<?=$this->post_type?>_season_<?=$season->ID?>_price_per_night"><?=__('Price per night', 'open-booking-calendar')?></label>
				</th>
				<td>
					$<input type="number" name="<?=$this->post_type?>_season_<?=$season->ID?>_price_per_night" id="<?=$this->post_type?>_season_<?=$season->ID?>_price_per_night" value="<?=$price_per_night?>">
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
					<label for="<?=$this->post_type?>_info_page_id"><?=__('Accommodation page', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_info_page_id" id="<?=$this->post_type?>_info_page_id" >
						<?php
						foreach ($pages as $page){
						?>
						<option value="<?=$page->ID?>" <?php selected( $page->ID, $info_page_id, true ); ?>>
							<?php echo esc_html( $page->post_title ); ?>
						</option>
						<?php
						}						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_booking_preview_page_id"><?=__('Booking preview page', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_booking_preview_page_id" id="<?=$this->post_type?>_booking_preview_page_id" >
						<?php
						foreach ($pages as $page){
						?>
						<option value="<?=$page->ID?>" <?php selected( $page->ID, $booking_preview_page_id, true ); ?>>
							<?php echo esc_html( $page->post_title ); ?>
						</option>
						<?php
						}						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_booking_received_page_id"><?=__('Booking received page', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_booking_received_page_id" id="<?=$this->post_type?>_booking_received_page_id" >
						<?php
						foreach ($pages as $page){
						?>
						<option value="<?=$page->ID?>" <?php selected( $page->ID, $booking_received_page_id, true ); ?>>
							<?php echo esc_html( $page->post_title ); ?>
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
					<label for="<?=$this->post_type?>_exclusivity_last_day"><?=__('Exclusivity of the last day', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_exclusivity_last_day" id="<?=$this->post_type?>_exclusivity_last_day" >
						<option value="1" <?php selected( '1', $exclusivity_last_day, true ); ?>>
							<?=__('Activated, the last day is not shared', 'open-booking-calendar')?>
						</option>
						<option value="0" <?php selected( '0', $exclusivity_last_day, true ); ?>>
							<?=__('Deactivated, can be booked on the last day', 'open-booking-calendar')?>
						</option>
					</select>
					<p class="description">
            <?php esc_html_e('Ideal for accommodation where it is possible that the day of check-out of a client is also the day of check-in of a new client.', 'open-booking-calendar'); ?>
  	      </p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_min_num_nights"><?=__('Minimum number of nights', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="number" name="<?=$this->post_type?>_min_num_nights" id="<?=$this->post_type?>_min_num_nights" value="<?=$min_num_nights?>">
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

		/** 
		 * Save values in array directly
		 */

		$keys_to_save_directly = ['max_adults', 'max_children', 'info_page_id', 'booking_preview_page_id', 'booking_received_page_id', 'exclusivity_last_day', 'min_num_nights'];

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
		
		$seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);

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
			'max_adults' => __('Maximum Adults', 'open-booking-calendar'),
			'max_children' => __('Maximum Children', 'open-booking-calendar'),
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
			echo get_post_meta( $post_id , "_{$this->post_type}_max_adults" , true );
			break;
		case 'max_children':
			echo get_post_meta( $post_id , "_{$this->post_type}_max_children" , true );
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