<?php

class Promotion_CMB
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

	protected $post_type = 'obcal_promotion';

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
			"{$this->post_type}_details",						// Unique ID
			__('Promotion details', 'open-booking-calendar'),	// Box title
			[$this, 'details_html'],  							// Content callback, must be of type callable
			$this->post_type             						// Post type
		);

	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function details_html($post)
	{
		$accommodation_id = get_post_meta($post->ID, "_{$this->post_type}_accommodation_id", true);
		$season_id = get_post_meta($post->ID, "_{$this->post_type}_season_id", true);
		$num_nights = get_post_meta($post->ID, "_{$this->post_type}_num_nights", true);
		$total_price = get_post_meta($post->ID, "_{$this->post_type}_total_price", true);

		$accommodations = get_posts(['post_type' => 'obcal_accommodation', 'numberposts' => -1]);
		 
		$seasons = get_posts(['post_type' => 'obcal_season', 'numberposts' => -1]);

		?>
		<table class="form-table">
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
					<label for="<?=$this->post_type?>_season_id"><?=__('Season', 'open-booking-calendar')?></label>
				</th>
				<td>
					<select name="<?=$this->post_type?>_season_id" id="<?=$this->post_type?>_season_id" >
						<?php
						foreach ($seasons as $season){
						?>
						<option value="<?=$season->ID?>" <?php selected( $season->ID, $season_id, true ); ?>>
							<?php echo esc_html( $season->post_title ); ?>
						</option>
						<?php
						}						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_num_nights"><?=__('Number of nights', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="number" name="<?=$this->post_type?>_num_nights" id="<?=$this->post_type?>_num_nights" value="<?=$num_nights?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_total_price"><?=__('Promotion price', 'open-booking-calendar')?></label>
				</th>
				<td>
					$<input type="number" name="<?=$this->post_type?>_total_price" id="<?=$this->post_type?>_total_price" value="<?=$total_price?>">
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

		$keys_to_save_directly = ['accommodation_id', 'season_id', 'num_nights', 'total_price'];

		foreach ($keys_to_save_directly as $key_to_save) {
			if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {
				update_post_meta(
					$post_id,
					"_{$this->post_type}_{$key_to_save}",
					$_POST["{$this->post_type}_{$key_to_save}"]
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
			'accommodation' => __('Accommodation', 'open-booking-calendar'),
			'season' => __('Season', 'open-booking-calendar'),
			'num_nights' => __('Num. of nights', 'open-booking-calendar'),
			'total_price' => __('Price', 'open-booking-calendar'),
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
		case 'season':
			$season_id = get_post_meta( $post_id , "_{$this->post_type}_season_id" , true );
			$season = get_post($season_id);
			echo esc_html($season->post_title);
			break;
		case 'num_nights':
			echo get_post_meta( $post_id , "_{$this->post_type}_num_nights" , true );
			break;
		case 'total_price':
			echo "$" . get_post_meta( $post_id , "_{$this->post_type}_total_price" , true );
			break;

		}
	}

	/**
	 * Register the columns as sortable.
	 */
	public function register_table_sortable_columns( $columns ) {
		$columns['accommodation'] = 'Accommodation';
		$columns['season'] = 'Season';
		$columns['num_nights'] = 'Num Nights';
		$columns['total_price'] = 'Price';
		return $columns;
	}
}