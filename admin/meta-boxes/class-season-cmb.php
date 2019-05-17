<?php

class Season_CMB
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

	protected $post_type = 'obcal_season';

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
			__('Season details', 'open-booking-calendar'),	  	// Box title
			[$this, 'details_html'],  							// Content callback, must be of type callable
			$this->post_type             						// Post type
		);

	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function details_html($post)
	{
		$start_date = new DateTime(get_post_meta($post->ID, "_{$this->post_type}_start_date", true));
		$end_date = new DateTime(get_post_meta($post->ID, "_{$this->post_type}_end_date", true));

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_start_date"><?=__('Start date', 'open-booking-calendar')?></label>
				</th>
				<td>
	        <input type="hidden" id="obc_cal_dateFormat" value="<?=$options_date_format?>">
					<input type="text" name="<?=$this->post_type?>_start_date" id="<?=$this->post_type?>_start_date" class="season-start-date" placeholder="<?=__('Select Date..', 'open-booking-calendar')?>" value="<?=$start_date->format($options_date_format)?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_end_date"><?=__('End date', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="text" name="<?=$this->post_type?>_end_date" id="<?=$this->post_type?>_end_date" class="season-end-date" placeholder="<?=__('Select Date..', 'open-booking-calendar')?>" value="<?=$end_date->format($options_date_format)?>">
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
		$start_date = new DateTime(isset($_POST["{$this->post_type}_start_date"]) ? $_POST["{$this->post_type}_start_date"] : '');
		$end_date = new DateTime(isset($_POST["{$this->post_type}_end_date"]) ? $_POST["{$this->post_type}_end_date"] : '');

		if (array_key_exists("{$this->post_type}_start_date", $_POST)) {
			update_post_meta(
				$post_id,
				"_{$this->post_type}_start_date",
				$start_date->format('Y-m-d')
			);
		}

		if (array_key_exists("{$this->post_type}_end_date", $_POST)) {
			update_post_meta(
				$post_id,
				"_{$this->post_type}_end_date",
				$end_date->format('Y-m-d')
			);
		}
	}

	/**
	 * Add Custom Columns to the Post Type Table.
	 */
	public function add_table_custom_columns($columns) {
		$new_columns = [
			'cb' => $columns['cb'],
			'title' => $columns['title'],
			'start_date' => __('Start date', 'open-booking-calendar'),
			'end_date' => __('End date', 'open-booking-calendar'),
			'date' => $columns['date'],
		];
		return $new_columns;
	}

	/**
	 * Add Custom Columns Data to the Post Type Table.
	 */
	public function add_table_custom_values( $column, $post_id ) {

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		switch ( $column ) {
			case 'start_date':
			$start_date = new DateTime(get_post_meta($post_id, "_{$this->post_type}_start_date", true));
			echo $start_date->format($options_date_format);
			break;
		case 'end_date':
			$end_date = new DateTime(get_post_meta($post_id, "_{$this->post_type}_end_date", true));
			echo $end_date->format($options_date_format);
			break;
		}
	}

	/**
	 * Register the columns as sortable.
	 */
	public function register_table_sortable_columns( $columns ) {
		$columns['start_date'] = 'Start date';
		$columns['end_date'] = 'End date';
		return $columns;
	}
}