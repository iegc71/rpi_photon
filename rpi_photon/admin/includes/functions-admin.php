<?php
// Función para reconstruir metadatos faltantes
function reconstruir_metadato_file($adjunto_id, $metadata) {
    // Obtener la ruta completa del archivo adjunto
    $file_path = get_attached_file($adjunto_id);
    
    if ($file_path && file_exists($file_path)) {
        // Obtener la ruta relativa a partir de la ruta completa
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);

        // Reconstruir el valor 'file' si falta
        if (!isset($metadata['file']) || !$metadata['file']) {
            $metadata['file'] = $relative_path;
        }

        // Reconstruir el valor 'filesize' si falta
        if (!isset($metadata['filesize']) || !$metadata['filesize']) {
            $metadata['filesize'] = filesize($file_path);
        }

        // Obtener dimensiones de la imagen
        $image_info = getimagesize($file_path);

        // Reconstruir el valor 'width' si falta
        if (!isset($metadata['width']) || !$metadata['width']) {
            if ($image_info) {
                $metadata['width'] = $image_info[0];
            }
        }

        // Reconstruir el valor 'height' si falta
        if (!isset($metadata['height']) || !$metadata['height']) {
            if ($image_info) {
                $metadata['height'] = $image_info[1];
            }
        }
    }

    return $metadata;
}

// Función para eliminar variaciones de imágenes y limpiar la base de datos
function eliminar_variaciones_imagenes() {
    global $wpdb;

    // Obtener todas las imágenes adjuntas
    $adjuntos = $wpdb->get_results("
        SELECT ID 
        FROM {$wpdb->posts} 
        WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%'
    ");

    // Recorrer cada adjunto
    foreach ($adjuntos as $adjunto) {
        // Obtener metadatos de la imagen
        $metadata = wp_get_attachment_metadata($adjunto->ID);

        // Verificar y reconstruir metadatos faltantes
        if (
            !isset($metadata['file']) || 
            !$metadata['file'] || 
            !isset($metadata['width']) || 
            !$metadata['width'] || 
            !isset($metadata['height']) || 
            !$metadata['height'] || 
            !isset($metadata['filesize']) || 
            !$metadata['filesize']
        ) {
            $metadata = reconstruir_metadato_file($adjunto->ID, $metadata);
        }
        
        // Verifica si hay variaciones de imagen registradas dentro de $metadata['sizes']
        if (isset($metadata['sizes']) && !empty($metadata['sizes'])) {
            // Eliminar variaciones de imagen
            foreach ($metadata['sizes'] as $size => $info) {
                // Verifica si el archivo es una imagen
                $filepath = wp_upload_dir()['basedir'] . '/' . dirname($metadata['file']) . '/' . $info['file'];
                $fileinfo = pathinfo($filepath);
                // Solo elimina archivos con extensiones de imagen
                if (file_exists($filepath) && in_array(strtolower($fileinfo['extension']), array('jpg', 'jpeg', 'png', 'gif', 'webp'))) {
                    /********************/
                    //unlink($filepath);
                }
            }

            // Elimina las entradas de las variaciones en los metadatos
            /**************************/
            //unset($metadata['sizes']);

            // Verifica si hay otros datos válidos en los metadatos
            $metadata_to_update = array_filter($metadata, function($value) {
                // Devuelve true si hay datos en la entrada, excluyendo arrays vacíos
                return !empty($value) || (is_array($value) && !empty(array_filter($value)));
            });

            // Actualiza los metadatos en la base de datos sin las variaciones vacías
            /******************************************************************/
            //wp_update_attachment_metadata($adjunto->ID, $metadata_to_update);
        }
    }
}

// Función para eliminar carpetas vacías
function eliminar_carpetas_vacias() {
    $upload_dir = wp_upload_dir()['basedir'];
    $directories = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($upload_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($directories as $fileinfo) {
        if ($fileinfo->isDir() && !iterator_count(new FilesystemIterator($fileinfo->getPathname(), FilesystemIterator::SKIP_DOTS))) {
            // Descomentar la siguiente línea si deseas eliminar las carpetas vacías
            /**********************************/
            //rmdir($fileinfo->getPathname());
        }
    }
}