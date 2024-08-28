<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://github.com/iegc71
 * @since      1.0.0
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/admin
 * @author     Ivan Garcia <iegc71@gmail.com>
 */
class Rpi_photon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rpi_photon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rpi_photon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rpi_photon-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rpi_photon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rpi_photon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rpi_photon-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function add_menu() {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(
            "rpi_photon", // Título de la página
            "rpi_photon", // Literal de la opción
            "manage_options", // Dejadlo tal cual
            'rpi_photon-index', // Slug
            array( $this, 'rpi_photon_index' ), // Función que llama al pulsar
            plugins_url( 'rpi_photon/icon.png' ) // Icono del menú
            //'dashicons-format-gallery'
            //'dashicons-cover-image'
        );
        // los iconos también se pueden poner: 'dashicons-admin-generic'
    }
    
    public function rpi_photon_index() {
       include plugin_dir_path(__FILE__) . 'partials/main-admin-display.php';
    }
}
