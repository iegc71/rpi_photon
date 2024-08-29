<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://github.com/iegc71
 * @since      1.0.0
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rpi_photon
 * @subpackage Rpi_photon/includes
 * @author     Ivan Garcia <iegc71@gmail.com>
 */
class Rpi_photon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rpi_photon_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'RPI_PHOTON_VERSION' ) ) {
			$this->version = RPI_PHOTON_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'rpi_photon';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->load_snippets(); // Snippets de php
		$this->load_js_snippets(); // Snippets de javascript
	}
    
    // Para desactivar un snippet empezar el nombre con "off-"
    private function load_snippets() {
        $php_snippets_dir = plugin_dir_path(__FILE__) . 'snippets/php/';
    
        foreach ( glob( $php_snippets_dir . '*.php' ) as $file ) {
            $filename = basename($file);
    
            // Cargar solo archivos que no comiencen con "off-"
            if ( strpos($filename, 'off-') !== 0 ) {
                require_once $file;
            }
        }
    }
    
    private function load_js_snippets() {
        $js_snippets_dir = plugin_dir_url(__FILE__) . 'snippets/js/';
        $js_snippets_path = plugin_dir_path(__FILE__) . 'snippets/js/';
    
        foreach ( glob( $js_snippets_path . '*.js' ) as $file ) {
            $filename = basename($file);
    
            // Cargar solo archivos que no comiencen con "off-"
            if ( strpos($filename, 'off-') !== 0 ) {
                wp_enqueue_script(
                    $filename,                     // Handle
                    $js_snippets_dir . $filename,  // URL del script
                    array(),                      // Dependencias
                    $this->version,               // Versión del script
                    true                          // Cargar en el pie de página
                );
            }
        }
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rpi_photon_Loader. Orchestrates the hooks of the plugin.
	 * - Rpi_photon_i18n. Defines internationalization functionality.
	 * - Rpi_photon_Admin. Defines all hooks for the admin area.
	 * - Rpi_photon_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rpi_photon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rpi_photon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rpi_photon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rpi_photon-public.php';

		$this->loader = new Rpi_photon_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rpi_photon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rpi_photon_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rpi_photon_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        // Añadimos plugin menú principal
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rpi_photon_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context ofdefine_admin_hooks()
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rpi_photon_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
