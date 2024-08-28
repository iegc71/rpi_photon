<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://github.com/iegc71
 * @since             1.0.0
 * @package           Rpi_photon
 *
 * @wordpress-plugin
 * Plugin Name:       rpi_photon
 * Plugin URI:        https://https://codelisto.com/
 * Description:       Optimizes server inodes by uploading only original images to the site. These images are set to display at a proper size using jetpack photon. Depends on Jetpack Boost plugin.
 * Version:           1.0.0
 * Author:            Ivan Garcia
 * Author URI:        https://https://github.com/iegc71/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rpi_photon
 * Domain Path:       /languages
 * Requires Plugins:  jetpack-boost
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
define( 'RPI_PHOTON_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rpi_photon-activator.php
 */
function activate_rpi_photon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpi_photon-activator.php';
	Rpi_photon_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rpi_photon-deactivator.php
 */
function deactivate_rpi_photon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpi_photon-deactivator.php';
	Rpi_photon_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rpi_photon' );
register_deactivation_hook( __FILE__, 'deactivate_rpi_photon' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rpi_photon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rpi_photon() {

	$plugin = new Rpi_photon();
	$plugin->run();

}
run_rpi_photon();
