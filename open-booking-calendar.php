<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://webservicesrank.com
 * @since             1.0.0
 * @package           Open_Booking_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Open Booking Calendar
 * Plugin URI:        https://webservicesrank.com/wp-plugins/open-booking-calendar/
 * Description:       Manage your hotel booking services.
 * Version:           1.0.0
 * Author:            WebServicesRank.com, Fabián Karaben
 * Author URI:        https://webservicesrank.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       open-booking-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OPEN_BOOKING_CALENDAR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-open-booking-calendar-activator.php
 */
function activate_open_booking_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-open-booking-calendar-activator.php';
	Open_Booking_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-open-booking-calendar-deactivator.php
 */
function deactivate_open_booking_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-open-booking-calendar-deactivator.php';
	Open_Booking_Calendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_open_booking_calendar' );
register_deactivation_hook( __FILE__, 'deactivate_open_booking_calendar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-open-booking-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_open_booking_calendar() {

	$plugin = new Open_Booking_Calendar();
	$plugin->run();

}
run_open_booking_calendar();
