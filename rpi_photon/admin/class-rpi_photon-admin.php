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
    
    // Propiedad para almacenar errores
    private $parse_errors = [];

    /**
     * Parses a JavaScript-like array string to a PHP array.
     *
     * @param string $string The JavaScript-like array string.
     * @return array|null The parsed PHP array or null if the input is invalid.
     */
    private function parseJavaScriptArray($string) {
        // Elimina los comentarios de la cadena
        $string = preg_replace('/\/\/.*|\/\*[\s\S]*?\*\/|#.*$/m', '', $string);
        
        // Reemplaza los corchetes de array de JS por corchetes de JSON
        $string = str_replace(['[', ']'], ['[', ']'], $string);

        // Reemplaza las comas entre arrays por comas de JSON
        $string = str_replace('),array(', '),(', $string);

        // Reemplaza comillas simples por comillas dobles
        $string = str_replace("'", '"', $string);

        // Envuelve la cadena en corchetes para JSON
        $jsonString = '[' . $string . ']';

        // Decodifica la cadena JSON
        $phpArray = json_decode($jsonString, true);

        // Verifica errores de JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->parse_errors[] = 'Error en la estructura de la configuración. Ejemplo correcto: [\'.clases-texto\', ancho-número, calidad-número]';
            return null;
        }

        // Valida la estructura del array
        foreach ($phpArray as $item) {
            if (!is_array($item) || count($item) !== 3) {
                $this->parse_errors[] = 'Error: Debe contener los 3 datos: [\'.clase\', ancho, calidad]';
                return null;
            }
            list($class, $width, $quality) = $item;
            if (!is_string($class) || !is_int($width) || !is_int($quality)) {
                $this->parse_errors[] = 'Error: Los tipos de datos deben ser [\'texto\', número, número]';
                return null;
            }
        }

        return $phpArray;
    }

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
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_delete_variations'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rpi_photon-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rpi_photon-admin.js', array( 'jquery' ), $this->version, false );
        
        // Encolar el script en el frontend
        // wp_enqueue_script('rpi-frontend', plugin_dir_url(__FILE__) . 'public/js/rpi-frontend.js', array('jquery'), $this->version, true);

        // Pasar la configuración de imágenes al script JS
        // $image_config = get_option('rpi_photon_image_config', '[]');
        // wp_localize_script('rpi-frontend', 'rpiPhotonConfig', array(
        //     'imageConfigs' => $image_config
        // ));
    }
    
    // Accion para el botón del menu Borrar variaciones
    public $delete_message = '';
    public function handle_delete_variations() {
        require_once 'includes/functions-admin.php';
        if (isset($_POST['delete_variations'])) {
            eliminar_variaciones_imagenes();
            eliminar_carpetas_vacias();
            // Guardar el mensaje
            $this->delete_message = '¡Las variaciones de imágenes han sido eliminadas con éxito!';
        }
    }

    public function add_menu() {
        add_menu_page(
            "rpi_photon", // Título de la página
            "rpi_photon", // Literal de la opción
            "manage_options", // Dejadlo tal cual
            'rpi_photon-index', // Slug
            array( $this, 'rpi_photon_index' ), // Función que llama al pulsar
            plugins_url( 'rpi_photon/icon.png' ) // Icono del menú
        );
    }
    
    public function register_settings() {
        register_setting('rpi_photon_options_group', 'rpi_photon_enable_media_library');
        // Nuevo campo para IMAGE_CONFIGS
        register_setting('rpi_photon_options_group', 'rpi_photon_image_config'); 
    }
    
    public function rpi_photon_index() {
        if ($this->delete_message) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . esc_html($this->delete_message) . '</p>';
            echo '</div>';
        }
        
        // Convierte el texto del campo en un arreglo de configuraciones
        $image_config_raw = get_option('rpi_photon_image_config', '');
        $image_config = $this->parseJavaScriptArray($image_config_raw);
        // debug_message($image_config);

        // Muestra errores de parseo, si los hay
        if (!empty($this->parse_errors)) {
            foreach ($this->parse_errors as $error) {
                add_settings_error(
                    'rpi_photon_image_config', // ID del grupo de opciones
                    'parse_error', // ID del error
                    $error, // Mensaje del error
                    'error' // Tipo de mensaje
                );
            }
        }
        
        // Muestra los errores en la pantalla de configuración
        settings_errors('rpi_photon_image_config');
       
        include plugin_dir_path(__FILE__) . 'partials/rpi_photon-admin-display.php';
    }

}
