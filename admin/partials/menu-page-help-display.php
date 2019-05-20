<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://webservicesrank.com
 * @since      1.0.0
 *
 * @package    Open_Booking_Calendar
 * @subpackage Open_Booking_Calendar/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1><?php esc_html_e('Get started and Help', 'open-booking-calendar'); ?></h1>


    <h2><?php esc_html_e('Introduction', 'open-booking-calendar'); ?></h2>

    <p><?php esc_html_e('To make a reservation a visitor or client must enter the page of a accommodation, choose the dates of check-in and check-out, complete the form and press the "Preview booking" button.', 'open-booking-calendar'); ?></p>

    <p>
        <?php esc_html_e('The next screen is "Preview of the booking" where the visitor or client can see details of the reservation, such as the price and if a Promotion applies.', 'open-booking-calendar'); ?>
        <?php esc_html_e('Finally, clicking on the "Confirm booking" button will go to the screen where a "Reservation received" message will be displayed.', 'open-booking-calendar'); ?>
        <?php esc_html_e('At that moment a new reservation is generated in the "Pending" status and two emails will be sent, one to the client and another to the reservation system administrator.', 'open-booking-calendar'); ?>
    </p>
    
    <p><?php esc_html_e('When the reservation manager changes the status of the reservation from "Pending" to "Confirmed" or to "Canceled" a notification email will also be sent to the client.', 'open-booking-calendar'); ?></p>


    <h2><?php esc_html_e('Optional step:', 'open-booking-calendar'); ?></h2>

    <p><?php _e('Optionally you can install <strong>Open Booking Calendar Plus</strong>, our <strong>free plugin</strong> that adds interesting features to this plugin.', 'open-booking-calendar'); ?></p>

    <p><?php echo sprintf( esc_html__('You can download Open Booking Calendar Plus 100%% free from [%s], and then from the "Plugins" section of WordPress, upload the file open-booking-calendar-plus.zip for installation.', 'open-booking-calendar'), __('<a href="https://webservicesrank.com/wp-plugins/open-booking-calendar-plus" target="_blank">Download</a>', 'open-booking-calendar') ); ?></p>

    <p><?php echo '<strong>' . esc_html__('Notice:', 'open-booking-calendar') . '</strong> ' . esc_html__('it is necessary to provide a valid email to download Open Booking Calendar Plus, this way we will send you notifications when a new version of the plugin Plus is available.', 'open-booking-calendar'); ?></p>

    <!--
    <h2><?php esc_html_e('Steps to follow', 'open-booking-calendar'); ?></h2>

    <ol>
        <li><?php esc_html_e('Add the general pages', 'open-booking-calendar'); ?></li>
        <li><?php esc_html_e('Add the seasons', 'open-booking-calendar'); ?></li>
        <li><?php esc_html_e('Add the accommodations', 'open-booking-calendar'); ?></li>
        <li><?php esc_html_e('Add the promotions', 'open-booking-calendar'); ?></li>
    </ol>
    -->

    <h2><?php esc_html_e('Step 1: Add the general pages', 'open-booking-calendar'); ?></h2>

    <p><?php esc_html_e('It is necessary to create 4 special pages for the reservation system to work. Doing this is very simple as described below.', 'open-booking-calendar'); ?></p>

    <ol>
        <li><?php esc_html_e('Create a new "Booking preview" page with the shortcode: [obc_booking_preview]', 'open-booking-calendar'); ?></li>
        <li><?php esc_html_e('Create a new "Booking received" page with the shortcode: [obc_booking_received]', 'open-booking-calendar'); ?></li>
        <li><?php esc_html_e('Create a new "Search accommodations" page with the shortcode: [obc_search_accommodations]', 'open-booking-calendar'); ?></li>
        <li><?php esc_html_e('Create a new page of "Search results" with the shortcode: [obc_search_results]', 'open-booking-calendar'); ?></li>
    </ol>

    <p><?php esc_html_e('In all cases the title of the page is your choice, and you can add content before and after the shortcode.', 'open-booking-calendar'); ?></p>

    
    <h2><?php esc_html_e('Step 2: Add the seasons', 'open-booking-calendar'); ?></h2>
    
    <p><?php esc_html_e('The availability calendar will only show available the dates that belong to a season and that do not have previous reservations "Pending" or "Confirmed".', 'open-booking-calendar'); ?></p>

    <p><?php esc_html_e('To add or update a season, from the menu click on "Bookings » Seasons" and on the screen that will be displayed, click on the "Add new" button.', 'open-booking-calendar'); ?></p>

    <p><?php esc_html_e('The data to select are simply the opening and closing dates of the season.', 'open-booking-calendar'); ?></p>

    <p><?php esc_html_e('Click on the blue "Publish" button to save the changes.', 'open-booking-calendar'); ?></p>

    
    <h2><?php esc_html_e('Step 3: Add the accommodations', 'open-booking-calendar'); ?></h2>

    <p><?php esc_html_e('To add or update a accommodation, from the menu click on "Bookings » Accommodations" and on the screen that will be displayed, click on the "Add new" button.', 'open-booking-calendar'); ?></p>

    <p><?php esc_html_e('The data to enter are:', 'open-booking-calendar'); ?></p>

    <ol>
        <li><?php esc_html_e('General', 'open-booking-calendar'); ?></li>
        <ul>
            <li>- <?php esc_html_e('The title for the accommodation.', 'open-booking-calendar'); ?></li>
            <li>- <?php esc_html_e('Extract: that will be displayed in listings as in the search results.', 'open-booking-calendar'); ?></li>
        </ul>
        <li><?php esc_html_e('Capacity', 'open-booking-calendar'); ?></li>
        <ul>
            <li>- <?php esc_html_e('The maximum capacity of people who admit the accommodation.', 'open-booking-calendar'); ?></li>
        </ul>
        <li><?php esc_html_e('Seasons', 'open-booking-calendar'); ?></li>
        <ul>
            <li>- <?php esc_html_e('One price per night for each season set in Step 2. To disable a season for only this accommodation, assign a price of 0.', 'open-booking-calendar'); ?></li>
        </ul>
        <li><?php esc_html_e('Pages', 'open-booking-calendar'); ?></li>
        <ul>
            <li>- <?php esc_html_e('The information page of the accommodation, that is, where the visitor or client initiates the process of requesting a booking.', 'open-booking-calendar'); ?></li>
            <li>- <?php esc_html_e('The "Booking preview" page for this accommodation. This page may be the same for all accommodations.', 'open-booking-calendar'); ?></li>
            <li>- <?php esc_html_e('The "Booking received" page for this accommodation. This page may be the same for all accommodations.', 'open-booking-calendar'); ?></li>
        </ul>
        <li><?php esc_html_e('Booking settings', 'open-booking-calendar'); ?></li>
        <ul>
            <li>- <?php esc_html_e('Exclusivity of the last day: Deactivating this option is useful for accommodation in which two different reservations are allowed to have their check-out and check-in on the same day, for example: check-out at 10:00 AM and the next check-in at 11:30 AM. If this option is activated, a visitor or client will not be allowed to make a reservation that starts the same day that another reservation ends.', 'open-booking-calendar'); ?></li>
            <li>- <?php esc_html_e('The minimum number of nights that a visitor or client can select in the calendar to make a booking.', 'open-booking-calendar'); ?></li>
        </ul>
    </ol>

    <p><?php esc_html_e('Click on the blue "Publish" button to save the changes.', 'open-booking-calendar'); ?></p>

    
    <h2><?php esc_html_e('Step 4: Add the promotions (Free Plus feature)', 'open-booking-calendar'); ?></h2>

    <p><?php esc_html_e('To add or update a promotion, from the menu click on "Bookings » Promotions" and on the screen that will be displayed, click on the "Add new" button.', 'open-booking-calendar'); ?></p>

    <p><?php esc_html_e('Enter the corresponding data including a name for the promotion and then click on the blue "Publish" button.', 'open-booking-calendar'); ?></p>

    
    <h2><?php esc_html_e('Other items in the "Bookings" menu:', 'open-booking-calendar'); ?></h2>

    <h4><?php esc_html_e('All Bookings', 'open-booking-calendar'); ?></h4>

    <p><?php esc_html_e('In "Bookings » All Bookings" you will see a table with all the reservations and it is also where you must enter to confirm or cancel a new pending reservation request.', 'open-booking-calendar'); ?></p>

    <h4><?php esc_html_e('Customers', 'open-booking-calendar'); ?></h4>
    
    <p><?php esc_html_e('In "Bookings » Customers" you will see a table with the data of the people who have completed the request for a booking.', 'open-booking-calendar'); ?></p>

    <h4><?php esc_html_e('Contact Log (Free Plus feature)', 'open-booking-calendar'); ?></h4>
    
    <p><?php esc_html_e('In "Bookings » Contact Log" you will see a table with the data of the people who have preview a booking and the people who have made a search for accommodation.', 'open-booking-calendar'); ?></p>

    <h4><?php esc_html_e('Get Started & Help', 'open-booking-calendar'); ?></h4>
    
    <p><?php esc_html_e('In "Bookings » Get Started & Help" is where this text is located.', 'open-booking-calendar'); ?></p>

    <h4><?php esc_html_e('Settings', 'open-booking-calendar'); ?></h4>
    
    <p><?php esc_html_e('In "Bookings » Settings" you can see and customize some options for the reservation system.', 'open-booking-calendar'); ?></p>

    <hr>

    <p><?php _e('For more information visit the <a href="https://webservicesrank.com/wp-plugins/open-booking-calendar">Open Booking Calendar</a> page on our website.', 'open-booking-calendar'); ?></p>

</div>