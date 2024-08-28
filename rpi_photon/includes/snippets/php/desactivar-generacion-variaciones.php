<?php
// Evita la creación de nuevas variaciones de imágenes
function desactivar_generacion_variaciones( $sizes ) {
    return [];
}
add_filter( 'intermediate_image_sizes_advanced', 'desactivar_generacion_variaciones' );
