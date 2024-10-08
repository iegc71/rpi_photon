<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://github.com/iegc71
 * @since      1.0.0
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rpi_photon
 * @subpackage Rpi_photon/public
 * @author     Ivan Garcia <iegc71@gmail.com>
 */
class Rpi_photon_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rpi_photon-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rpi_photon-public.js', array( 'jquery' ), $this->version, false );
		
		// Encolar el script en el frontend
        wp_enqueue_script('rpi-frontend', plugin_dir_url(__FILE__) . 'js/rpi-frontend.js', array('jquery'), $this->version, true);
        
        // Pasar la configuración de imágenes al script JS
        $image_config = get_option('rpi_photon_image_config', '[]');
        // CONVERTIR A JSON
        // Reemplazar comillas simples por comillas dobles
        $image_config = str_replace("'", '"', $image_config);
        // Convertir la cadena a un array PHP válido
        $image_config = '[' . $image_config . ']';
        // Parsear la cadena como un array PHP
        $image_config = json_decode($image_config, true);
        // Convertir el array PHP a formato JSON
        $image_config = json_encode($image_config);
        
        // debug_message($image_config);
        wp_localize_script('rpi-frontend', 'rpiPhotonConfig', array(
            'imageConfigs' => $image_config
        ));

	}

}
