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
    <h1><?php esc_html(get_admin_page_title()); ?></h1>
    <h1>Ajustes</h1>
    <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields('wporg_options');
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections('wporg');
        // output save settings button
        submit_button('Save Settings');
        ?>
    </form>
</div>