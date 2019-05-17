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
    <h1>Inicio rápido y Ayuda</h1>

    <ul>
        <li>Las "Temporadas" definen el inicio y final de un período, sin más datos.</li>
        <li>En los "Alojamientos" se cargan todos los datos, asignando un precio a cada temporada.</li>
        <li>Para las "Promociones" se selecciona un alojamiento y se introduce el número de días, el precio total y el precio por día extra.</li>
        <li>Para las "Reservas" se selecciona un alojamiento, un cliente, cantidad de adultos y de niños y los días de entrada y salida.</li>
        <li>Los "Clientes" se van registrando cuando se realiza una reserva desde la interfaz pública o desde la sección Clientes de la interfaz admin.</li>
        <li>El Calendario mostrará disponibles los días que pertenezcan a una temporada y que no tengan reservas confirmadas.</li>
    </ul>
</div>