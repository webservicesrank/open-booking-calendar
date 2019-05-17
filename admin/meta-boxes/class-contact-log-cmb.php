<?php

class ContactLog_CMB
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

	protected $post_type = 'obcal_contact_log';

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
			"{$this->post_type}_details",										// Unique ID
			__('Contact details', 'open-booking-calendar'),	// Box title
			[$this, 'details_html'],  											// Content callback, must be of type callable
			$this->post_type             										// Post type
		);

		add_meta_box(
			"{$this->post_type}_queries_details",						// Unique ID
			__('Queries details', 'open-booking-calendar'),	// Box title
			[$this, 'queries_details_html'],								// Content callback, must be of type callable
			$this->post_type             										// Post type
		);

	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function details_html($post)
	{
		$email = get_post_meta($post->ID, "_{$this->post_type}_email", true);
		$last_query_date = new DateTime(get_post_meta($post->ID, "_{$this->post_type}_last_query_date", true));
		$num_queries = count(get_post_meta( $post->ID , "_{$this->post_type}_query" , false ));

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_email"><?=__('Email', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="text" name="<?=$this->post_type?>_email" id="<?=$this->post_type?>_email" class="contact-log-email" placeholder="<?=__('Email', 'open-booking-calendar')?>" value="<?=$email?>">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_last_query_date"><?=__('Last query date', 'open-booking-calendar')?></label>
				</th>
				<td>
					<?=$last_query_date->format($options_date_format . ' H:i:s')?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?=$this->post_type?>_num_queries"><?=__('Number of queries', 'open-booking-calendar')?></label>
				</th>
				<td>
					<?=$num_queries?>
				</td>
			</tr>
		</table>			
		<?php
	}

	/**
	 * HTML content of the Queries Details Meta Box.
	 */
	public function queries_details_html($post)
	{

		// Get the options
		$options = get_option('obcal_options');

		// Get date format
		$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';

		$query_types = [
			'booking_preview' => __('Booking preview', 'open-booking-calendar'),
			'search_results' => __('Accommodation search', 'open-booking-calendar'),
		];

		$queries = get_post_meta($post->ID, "_{$this->post_type}_query", false);

		?>
		<table class="form-table">
			<?php
				$num = 1;
				foreach ($queries as $query) {
			?>
			<tr>
				<th scope="row">
					#<?=$num?><br>
					<?=__('Type', 'open-booking-calendar') . ': ' . $query_types[$query['query_type']]?>
				</th>
				<td>
					<?php
					
					if ($query['query_type'] == 'booking_preview') {

						echo $this->show_booking_preview_query_details($query, $options_date_format);

					} else if ($query['query_type'] == 'search_results') {

						echo $this->show_search_results_query_details($query, $options_date_format);

					}
					
					?>
				</td>
			</tr>
			<?php
					$num++;
				}
			?>
		</table>			
		<?php
	}

	/**
	 * Show the query details of 'booking_preview' query type
	 */
	public function show_booking_preview_query_details($query, $options_date_format) {

		$o = '<table class="inner-form-table">';

		if (array_key_exists("query_date", $query)) {

			$query_date = new DateTime($query['query_date']);
			$o .= '<tr><th scope="row">';
			$o .= __('Query date', 'open-booking-calendar');
			$o .= '</th><td>';
			$o .= esc_html($query_date->format($options_date_format . ' H:i:s'));
			$o .= '</td></tr>';

		}

		if (array_key_exists("post_array", $query)) {

			if (array_key_exists("accommodation_id", $query['post_array'])) {

				$accommodation = get_post($query['post_array']['accommodation_id']);
				$o .= '<tr><th scope="row">';
				$o .= __('Accommodation', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($accommodation->post_title);
				$o .= '</td></tr>';

			}

			if (array_key_exists("selected_date", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Selected date', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['selected_date']);
				$o .= '';

			}

			if (array_key_exists("num_adults", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Number of adults', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['num_adults']);
				$o .= '</td></tr>';

			}

			if (array_key_exists("num_children", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Number of children', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['num_children']);
				$o .= '</td></tr>';

			}

			if (array_key_exists("us_name", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Name', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['us_name']);
				$o .= '</td></tr>';

			}

		}

		$o .= '</table>';

		return $o;

	}

	/**
	 * Show the query details of 'search_results' query type
	 */
	public function show_search_results_query_details($query, $options_date_format) {

		$o = '<table class="inner-form-table">';

		if (array_key_exists("query_date", $query)) {

			$query_date = new DateTime($query['query_date']);
			$o .= '<tr><th scope="row">';
			$o .= __('Query date', 'open-booking-calendar');
			$o .= '</th><td>';
			$o .= esc_html($query_date->format($options_date_format . ' H:i:s'));
			$o .= '</td></tr>';

		}

		if (array_key_exists("post_array", $query)) {

			if (array_key_exists("selected_date", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Selected date', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['selected_date']);
				$o .= '';

			}

			if (array_key_exists("num_adults", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Number of adults', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['num_adults']);
				$o .= '</td></tr>';

			}

			if (array_key_exists("num_children", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Number of children', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['num_children']);
				$o .= '</td></tr>';

			}

			if (array_key_exists("us_name", $query['post_array'])) {

				$o .= '<tr><th scope="row">';
				$o .= __('Name', 'open-booking-calendar');
				$o .= '</th><td>';
				$o .= esc_html($query['post_array']['us_name']);
				$o .= '</td></tr>';

			}

		}

		$o .= '</table>';

		return $o;

	}

	/**
	 * Save data of Meta Boxes.
	 */
	public function save($post_id)
	{

		/** 
		 * Save values in array directly
		 */

		$keys_to_save_directly = ['email'];

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
			'email' => __('Email', 'open-booking-calendar'),
			'last_query_date' => __('Last query date', 'open-booking-calendar'),
			'num_queries' => __('Queries', 'open-booking-calendar'),
			'date' => $columns['date'],
		];
		return $new_columns;
	}

	/**
	 * Add Custom Columns Data to the Post Type Table.
	 */
	public function add_table_custom_values( $column, $post_id ) {
		switch ( $column ) {
			case 'email':
				echo get_post_meta( $post_id , "_{$this->post_type}_email" , true );
				break;
			case 'last_query_date':
				// Get the options
				$options = get_option('obcal_options');
				// Get date format
				$options_date_format = isset($options['obcal_field_date_format']) ? $options['obcal_field_date_format'] : 'Y-m-d';
				//
				$last_query_date = new DateTime(get_post_meta( $post_id , "_{$this->post_type}_last_query_date" , true ));
				echo $last_query_date->format($options_date_format . ' H:i:s');
				break;
			case 'num_queries':
				$num_queries = count(get_post_meta( $post_id , "_{$this->post_type}_query" , false ));
				echo $num_queries;
				break;	
		}
	}

	/**
	 * Register the columns as sortable.
	 */
	public function register_table_sortable_columns( $columns ) {
		$columns['email'] = 'Email';
		$columns['last_query_date'] = 'Last query date';
		$columns['num_queries'] = 'Queries';
		return $columns;
	}
}