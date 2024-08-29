<?php
add_filter('wp_handle_upload_prefilter', 'convert_heic_to_jpg');

function convert_heic_to_jpg($file) {
    if ($file['type'] == 'image/heic') {
        $image = new Imagick();
        $image->readImage($file['tmp_name']);
        $image->setImageFormat('jpg');
        
        $new_file_path = preg_replace('/\.[^.\s]{3,4}$/', '.jpg', $file['tmp_name']);
        $image->writeImage($new_file_path);

        $file['name'] = preg_replace('/\.[^.\s]{3,4}$/', '.jpg', $file['name']);
        $file['type'] = 'image/jpeg';
        $file['tmp_name'] = $new_file_path;
    }

    return $file;
}

function redimensionar_imagen($file) {
    // Requerir la librería de WordPress para el manejo de imágenes
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Obtener las dimensiones de la imagen
    $size = getimagesize($file['file']);
    $width = $size[0];
    $height = $size[1];

    // Establecer el límite de peso de archivo en bytes
    $peso_maximo = 500 * 1024; // 500 KB

    // Establecer el tamaño máximo permitido en píxeles
    $tamano_maximo = 2560;

    // Cargar la imagen
    $image = wp_get_image_editor($file['file']);
    if (!is_wp_error($image)) {
        // Redimensionar si es necesario
        if ($width > $tamano_maximo || $height > $tamano_maximo) {
            $image->resize($tamano_maximo, $tamano_maximo, false);
            $image->save($file['file']);
        }

        // Comprobar el peso del archivo y comprimir si es necesario
        if (filesize($file['file']) > $peso_maximo) {
            $quality = 90; // Comenzar con una calidad del 90%

            // Reducir la calidad hasta que el archivo esté dentro del límite de peso
            while (filesize($file['file']) > $peso_maximo && $quality > 10) {
                $image->set_quality($quality);
                $image->save($file['file']);
                $quality -= 10;
            }
        }
    }

    // Devolver el archivo modificado
    return $file;
}

// Añadir el filtro para procesar el archivo antes de subirlo
add_filter('wp_handle_upload', 'redimensionar_imagen');
