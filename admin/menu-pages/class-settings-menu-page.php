<?php

class SettingsMenuPage
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

	public static $mainAdminMenuCapability = 'manage_options';
	public static $mainAdminMenuSlug = 'open-booking-calendar';

    /**
     * Initialize the class and set its properties.
     *
     * @since      1.0.0
     * @param      string    $open_booking_calendar     The name of the plugin.
     * @param      string    $version                   The version of this plugin.
     */
    public function __construct($open_booking_calendar, $version)
    {

        $this->open_booking_calendar = $open_booking_calendar;
        $this->version = $version;
    }

    /**
     * add submenu page
     */
    public function create_submenu() {

		add_submenu_page(
			self::$mainAdminMenuSlug, // parent_slug
			__( 'Settings', 'open-booking-calendar' ), // page_title
			__( 'Settings', 'open-booking-calendar' ), // menu_title
			self::$mainAdminMenuCapability, // capability
			'obc-settings', // menu_slug
			[$this, 'page_html'] // function
        );
        
    }

    /*

    public function html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/menu-page-settings-display.php';
    }
    */

    /**
     * submenu:
     * callback functions
     */
    public function page_html()
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // wordpress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // add settings saved message with the class of "updated"
            add_settings_error('obcal_messages', 'obcal_message', __('Settings Saved', 'open-booking-calendar'), 'updated');
        }

        // show error/update messages
        settings_errors('obcal_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "obcal"
                settings_fields('obcal');
                // output setting sections and their fields
                // (sections are registered for "obcal", each field is registered to a specific section)
                do_settings_sections('obcal');
                // output save settings button
                submit_button(__('Save Settings', 'open-booking-calendar'));
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * custom option and settings
     */
    public function settings_init()
    {
        // register a new setting for "obcal" page
        register_setting('obcal', 'obcal_options');

        // register a new section in the "obcal" page
        add_settings_section(
            'section_developers',
            __('Open Booking Calendar configuration options.', 'open-booking-calendar'),
            [$this, 'section_developers_cb'],
            'obcal'
        );

        // register a new field in the "section_developers" section, inside the "obcal" page
        add_settings_field(
            'obcal_field_flatpickr_theme', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Calendar theme', 'open-booking-calendar'),
            [$this, 'field_flatpickr_theme_cb'],
            'obcal',
            'section_developers',
            [
                'label_for' => 'obcal_field_flatpickr_theme',
                'class' => 'obcal-flatpickr_theme',
            ]
        );

        // register a new field in the "section_developers" section, inside the "obcal" page
        add_settings_field(
            'obcal_field_date_format', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Date format', 'open-booking-calendar'),
            [$this, 'field_date_format_cb'],
            'obcal',
            'section_developers',
            [
                'label_for' => 'obcal_field_date_format',
                'class' => 'obcal-date-format',
            ]
        );

        // register a new field in the "section_developers" section, inside the "obcal" page
        add_settings_field(
            'obcal_field_search_results_page_id', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Search results page', 'open-booking-calendar'),
            [$this, 'field_search_results_page_cb'],
            'obcal',
            'section_developers',
            [
                'label_for' => 'obcal_field_search_results_page_id',
                'class' => 'obcal-search-results-page',
            ]
        );

        // register a new field in the "section_developers" section, inside the "obcal" page
        add_settings_field(
            'obcal_field_email_notifications_user_id', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('User to send email notifications', 'open-booking-calendar'),
            [$this, 'field_email_notifications_user_cb'],
            'obcal',
            'section_developers',
            [
                'label_for' => 'obcal_field_email_notifications_user_id',
                'class' => 'obcal-email-notifications-user',
            ]
        );

    }

    /**
     * custom option and settings:
     * callback functions
     */

    // developers section cb

    // section callbacks can accept an $args parameter, which is an array.
    // $args have the following keys defined: title, id, callback.
    // the values are defined at the add_settings_section() function.
    public function section_developers_cb($args)
    {
        ?>
        <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('From this section you can customize the behavior of the plugin.', 'open-booking-calendar'); ?></p>
    <?php
    }

    // pill field cb

    // field callbacks can accept an $args parameter, which is an array.
    // $args is defined at the add_settings_field() function.
    // wordpress has magic interaction with the following keys: label_for, class.
    // the "label_for" key value is used for the "for" attribute of the <label>.
    // the "class" key value is used for the "class" attribute of the <tr> containing the field.
    // you can add custom key value pairs to be used inside your callbacks.
    public function field_flatpickr_theme_cb($args)
    {
        // get the value of the setting we've registered with register_setting()
        $options = get_option('obcal_options');

        $themes = [
            'default' => __('Default', 'open-booking-calendar'),
            'dark' => __('Dark', 'open-booking-calendar'),
            'material_blue' => __('Material Blue', 'open-booking-calendar'), 
            'material_green' => __('Material Green', 'open-booking-calendar'), 
            'material_red' => __('Material Red', 'open-booking-calendar'), 
            'material_orange' => __('Material Orange', 'open-booking-calendar'), 
            'airbnb' => 'Airbnb'
        ];

        // output the field
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="obcal_options[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            foreach($themes as $theme_key => $theme_value) {
            ?>
            <option value="<?=$theme_key?>" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], $theme_key, false)) : (''); ?>>
                <?php echo esc_html($theme_value); ?>
            </option>
            <?php
            }
            ?>
        </select>
        <p class="description">
            <?php esc_html_e('The theme for the date picker (calendar).', 'open-booking-calendar'); ?>
        </p>
    <?php
    }

    // pill field cb

    // field callbacks can accept an $args parameter, which is an array.
    // $args is defined at the add_settings_field() function.
    // wordpress has magic interaction with the following keys: label_for, class.
    // the "label_for" key value is used for the "for" attribute of the <label>.
    // the "class" key value is used for the "class" attribute of the <tr> containing the field.
    // you can add custom key value pairs to be used inside your callbacks.
    public function field_date_format_cb($args)
    {
        // get the value of the setting we've registered with register_setting()
        $options = get_option('obcal_options');

        $now_date = new DateTime(date_i18n('Y-m-d'));

        $date_formats = [
            'Y-m-d' => $now_date->format('Y-m-d') . ' &nbsp;&nbsp;&nbsp; [Y-m-d]',
            'd-m-Y' => $now_date->format('d-m-Y') . ' &nbsp;&nbsp;&nbsp; [d-m-Y]',
        ];

        // output the field
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="obcal_options[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            foreach($date_formats as $format_key => $format_value) {
            ?>
            <option value="<?=$format_key?>" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], $format_key, false)) : (''); ?>>
                <?php echo esc_html($format_value); ?>
            </option>
            <?php
            }
            ?>
        </select>
        <p class="description">
            <?php esc_html_e('Date format for all the plugin.', 'open-booking-calendar'); ?>
        </p>
        <p class="description">
            <?php esc_html_e('This only modifies the way in which the dates are displayed, that is, there will be no problems with the dates previously saved.', 'open-booking-calendar'); ?>
        </p>
    <?php
    }

    // pill field cb

    // field callbacks can accept an $args parameter, which is an array.
    // $args is defined at the add_settings_field() function.
    // wordpress has magic interaction with the following keys: label_for, class.
    // the "label_for" key value is used for the "for" attribute of the <label>.
    // the "class" key value is used for the "class" attribute of the <tr> containing the field.
    // you can add custom key value pairs to be used inside your callbacks.
    public function field_search_results_page_cb($args)
    {
        // get the value of the setting we've registered with register_setting()
        $options = get_option('obcal_options');

		$pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);

        // output the field
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="obcal_options[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            foreach($pages as $page) {
            ?>
            <option value="<?=$page->ID?>" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], $page->ID, false)) : (''); ?>>
                <?php echo esc_html($page->post_title); ?>
            </option>
            <?php
            }
            ?>
        </select>
        <p class="description">
            <?php esc_html_e('The page where the search results will be displayed.', 'open-booking-calendar'); ?>
        </p>
    <?php
    }

    // pill field cb

    // field callbacks can accept an $args parameter, which is an array.
    // $args is defined at the add_settings_field() function.
    // wordpress has magic interaction with the following keys: label_for, class.
    // the "label_for" key value is used for the "for" attribute of the <label>.
    // the "class" key value is used for the "class" attribute of the <tr> containing the field.
    // you can add custom key value pairs to be used inside your callbacks.
    public function field_email_notifications_user_cb($args)
    {
        // get the value of the setting we've registered with register_setting()
        $options = get_option('obcal_options');

        // Get user list
		$users = get_users();

        // output the field
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="obcal_options[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            foreach($users as $user) {
            ?>
            <option value="<?=$user->ID?>" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], $user->ID, false)) : (''); ?>>
                <?php echo esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')'; ?>
            </option>
            <?php
            }
            ?>
        </select>
        <p class="description">
            <?php esc_html_e('User to send email notifications when internal events occur such as the creation of a reservation.', 'open-booking-calendar'); ?>
        </p>
    <?php
    }

}
