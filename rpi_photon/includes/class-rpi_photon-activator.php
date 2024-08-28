<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://github.com/iegc71
 * @since      1.0.0
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rpi_photon
 * @subpackage Rpi_photon/includes
 * @author     Ivan Garcia <iegc71@gmail.com>
 */
class Rpi_photon_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        $snippets_dir = plugin_dir_path(__FILE__) . 'snippets';
        $php_dir = $snippets_dir . '/php';
        $js_dir = $snippets_dir . '/js';
    
        // Crear las carpetas si no existen
        if ( ! file_exists( $php_dir ) ) {
            mkdir( $php_dir, 0755, true );
        }
    
        if ( ! file_exists( $js_dir ) ) {
            mkdir( $js_dir, 0755, true );
        }
	}

}
