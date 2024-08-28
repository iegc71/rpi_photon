<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://github.com/iegc71
 * @since      1.0.0
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<p>Hola desde un partials</p>

<div class="wrap">
    <h1><?php _e( 'RPI Photon Settings', 'rpi_photon' ); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'rpi_photon_options_group' );
        do_settings_sections( 'rpi_photon' );
        submit_button();
        ?>
    </form>
</div>