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

<?php
// Asegúrate de que el archivo se esté ejecutando en el contexto de administración
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Obtén el estado actual de la opción de Photon
$photon_enabled = get_option('rpi_photon_enable_media_library', 'no');
$image_config = get_option('rpi_photon_image_config', '');
?>
<div class="wrap">
    <h1><?php esc_html_e('Configuración de rpi_photon', 'rpi_photon'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('rpi_photon_options_group'); // Nombre del grupo de opciones
        do_settings_sections('rpi_photon'); // Página de opciones
        submit_button(); // Botón de guardar cambios
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Habilitar Photon en Biblioteca de Medios', 'rpi_photon'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="rpi_photon_enable_media_library" value="yes" <?php checked($photon_enabled, 'yes'); ?>>
                        <?php esc_html_e('Habilitar Photon', 'rpi_photon'); ?>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Configuraciones de Imágenes', 'rpi_photon'); ?></th>
                <td>
                    <p><?php esc_html_e('
                    Busca una clase que corresponda a la imagen que se quiere transformar, encuentre el ancho y calidad correcto, cree arreglos separados por coma con esos datos. Ej: [\'.clase\', ancho-máximo, calidad], [\'.home .img-wrap\', 800, 80]
                    ', 'rpi_photon'); ?></p>
                    <textarea name="rpi_photon_image_config" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($image_config); ?></textarea>
                </td>
            </tr>
        </table>
    </form>
    
    <h2>Eliminar todas las variaciones de las imágenes originales subidas</h2>
    <p>Asegúrate de hacer una salva antes, esta acción es irreversible.</p>
    
    <form id="delete_variations_form" method="post" action="">
        <?php submit_button('Eliminar Variaciones', 'delete', 'delete_variations'); ?>
    </form>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('delete_variations_form');
    if (form) {
        form.addEventListener('submit', function (event) {
            const confirmation = confirm('¿Estás seguro de que deseas eliminar todas las variaciones de imágenes registradas en la base de datos y físicamente? Esta acción es irreversible.');
            if (!confirmation) {
                event.preventDefault(); // Detiene el envío del formulario si el usuario cancela
            }
        });
    }
});
</script>
