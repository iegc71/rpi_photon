<?php

function aplicar_photon_en_biblioteca_medios($url) {
    // Verifica si estamos en el área de administración y en la biblioteca de medios
    if (is_admin()) {
        $current_screen = get_current_screen();
        if ($current_screen && $current_screen->base === 'upload') {
            // Verifica si Photon está habilitado
            if (get_option('rpi_photon_enable_media_library') === 'yes') {
                $url = aplicar_photon($url);
            }
        }
    }
    return $url; // Devuelve la URL original si no se está en la biblioteca de medios o ya está usando Photon
}
add_filter('wp_get_attachment_url', 'aplicar_photon_en_biblioteca_medios');

function aplicar_photon_en_biblioteca_medios_js($response, $attachment, $meta) {
    if (is_admin() && get_option('rpi_photon_enable_media_library') === 'yes') {
        $image_url = wp_get_attachment_url($attachment->ID);
        $response['sizes']['thumbnail']['url'] = aplicar_photon($image_url);
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'aplicar_photon_en_biblioteca_medios_js', 10, 3);

function aplicar_photon($url){
    // Verifica si la URL ya está usando Photon
    if (!preg_match('#https://i[0-3]\.wp\.com/#', $url)) {
        // Convierte la URL original para usar Photon
        $parsed_url = parse_url($url);
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $domain = parse_url(home_url(), PHP_URL_HOST); // Obtiene el dominio del sitio

        // Selecciona un dominio aleatorio para Photon (i0.wp.com, i1.wp.com, etc.)
        $photon_domain = rand(0, 3);
        $new_url = 'https://i' . $photon_domain . '.wp.com/' . $domain . $path;

        // Agrega los parámetros de ancho y calidad
        $ancho = 200; // Cambia el valor según el tamaño deseado
        $calidad = 80; // Cambia el valor según la calidad deseada
        $new_url .= '?w=' . $ancho . '&quality=' . $calidad . '&ssl=1';

        // Devuelve la nueva URL modificada
        return $new_url;
    }
    return $url;
}
