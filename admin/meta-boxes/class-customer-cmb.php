<?php

class Customer_CMB
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
	
	protected $post_type = 'obcal_customer';

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
			"{$this->post_type}_details",												// Unique ID
			__('Customer details', 'open-booking-calendar'),		// Box title
			[$this, 'details_html'],  													// Content callback, must be of type callable
			$this->post_type             												// Post type
		);

	}

	/**
	 * HTML content of the Details Meta Box.
	 */
	public function details_html($post)
	{
		$email = get_post_meta($post->ID, "_{$this->post_type}_email", true);
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="<?= esc_attr($this->post_type . '_email') ?>"><?=__('Email', 'open-booking-calendar')?></label>
				</th>
				<td>
					<input type="email" name="<?= esc_attr($this->post_type . '_email') ?>" id="<?= esc_attr($this->post_type . '_email') ?>" value="<?= esc_attr($email) ?>">
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
		$keys_to_save_directly = ['email'];

		/** 
		 * Sanitize POST values
		 */

		// Sanitize values for 'Save values in array directly'
		foreach ($keys_to_save_directly as $key_to_save) {
			if (array_key_exists("{$this->post_type}_{$key_to_save}", $_POST)) {
				if ($key_to_save == 'email') {
					$_POST["{$this->post_type}_{$key_to_save}"] = sanitize_email($_POST["{$this->post_type}_{$key_to_save}"]);
				}
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

				// Validate 'email'

			}
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

	}

	/**
	 * Add Custom Columns to the Post Type Table.
	 */
	public function add_table_custom_columns($columns) {
		$new_columns = [
			'cb' => $columns['cb'],
			'title' => $columns['title'],
			'email' => esc_html__('Email', 'open-booking-calendar'),
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
			echo esc_html( get_post_meta( $post_id , "_{$this->post_type}_email" , true ) );
			break;
		}
	}

	/**
	 * Register the columns as sortable.
	 */
	public function register_table_sortable_columns( $columns ) {
		$columns['email'] = 'Email';
		return $columns;
	}
}