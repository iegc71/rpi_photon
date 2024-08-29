<?php
/**
	* INSTRUCCIONES
	* -------------
	* 	por cada elemento con clase "rpi",
	* 		buscar la clase que comienza con "rpw-" seguido de ancho y calidad "rpw-600-80"
	*		ver si el elemento encontrado es una imagen, o buscar las imágenes contenidas en dicho elemento
	*		por cada imagen encontrada,
	*			si solo tiene la clase "rpi" crear los srcset para cada breakpoint
	*			si tiene ancho y calidad crear los srcset y sizes correspondientes al ancho establecido
	*			Validar si el src es una imagen con el dominio del sitio. 
	*				si ya está usando photon, solo revisar ancho y calidad
	*			el src y los srcset hay que estructurarlos con photon y dominios aleatorios de 0 a 3
	*			si photon da error probar con otros dominios, si todos fallan usar ImgIx
	*/

function rpi_dynamic_images($content) {
	// Puntos de ruptura para diferentes dispositivos
	$breakpoints = [320, 480, 768, 1024, 1200, 1440, 1920, 2560];
	
	// Usar DOMDocument para analizar el contenido
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $elementos_rpi = $xpath->query("//*[contains(@class, 'rpi')]");
	foreach ($elementos_rpi as $elemento) {
		// Obtener todas las clases del elemento para filtrar "rpi-ancho-calidad"
		$clases = explode(' ', $elemento->getAttribute('class'));

		foreach ($clases as $class) {
			// Verificar si la clase comienza con "rpi" seguido por ancho-calidad
			if (preg_match('/^rpi(?:-(\d+))?(?:-(\d+))?$/', $class, $matches)) {
				$ancho = isset($matches[1]) ? $matches[1] : null;
				$calidad = isset($matches[2]) ? $matches[2] : null;
			}

		}
		// Encontar las imágenes que estén dentro del elemento con la clase "rpi"
		$imagenes = $elemento->getElementsByTagName('img');
		foreach ($imagenes as $imagen) {
			$img_src = $imagen->getAttribute('src');
			// Si tiene formato de photon, solo ajustar ancho y calidad
			// Usar un cdn (photon o imgIx)
			$img_src = get_cdn_url($img_src, $ancho, $calidad);
			if ($ancho) {
				$sizes = "(max-width: {$ancho}px) 100vw, {$ancho}px";
				$imagen->setAttribute('sizes', $sizes);
				$srcset = [];
				foreach ($breakpoints as $breakpoint) {
					if ($breakpoint <= $ancho) {
						$photon_url = get_cdn_url($img_src, $breakpoint, $calidad);
						$srcset[] = $photon_url . ' ' . $breakpoint . 'w';
					} elseif ($breakpoint > $ancho) {
						$photon_url = get_cdn_url($img_src, $breakpoint, $calidad);
						$srcset[] = $photon_url . ' ' . $breakpoint . 'w';
						break;
					}
				}
				$srcset_str = implode(', ', $srcset);
				$imagen->setAttribute('srcset', $srcset_str);
				$imagen->setAttribute('src', $img_src);
			} else {
				// Si no tiene ancho definido, debe ser a pantalla completa, poner todos los breakpoints sin sizes
				$srcset = [];
				foreach ($breakpoints as $breakpoint) {
					if ($breakpoint <= $ancho_original) {
						$photon_url = get_cdn_url($img_src, $breakpoint, $calidad);
						$srcset[] = $photon_url . ' ' . $breakpoint . 'w';
					}
				}
				$srcset_str = implode(', ', $srcset);
                $imagen->setAttribute('srcset', $srcset_str);
			}
			// Agregar la clase "rpi-checked" a la imagen
			$current_class = trim($imagen->getAttribute('class'));
			// Agregar "rpi-checked" a las clases existentes
			$new_class = $current_class ? $current_class . ' rpi-checked' : 'rpi-checked';
			$imagen->setAttribute('class', $new_class);
		}
	}
	
	return $dom->saveHTML();
}
add_filter('the_content', 'rpi_dynamic_images');


// Verifica si el url está usando photon
function is_photon_url($url) {
    // Verifica si la URL está usando Photon
    return preg_match('/^https:\/\/i[0-3]\.wp\.com\//', $url) === 1;
}

// Verifica si es una imagen local del sitio
function is_original_url($url) {
    // Verifica si la URL es de una imagen local
    return preg_match('/^https:\/\/i[0-3]\.wp\.com\//', $url) === 1;
}

// Formatea la url para usar photon (solo para url de imagenes locales originales)
function format_url_photon($image_url, $opt = ['ancho' => null, 'calidad' => null, 'ssl' => true]) {
	$ancho = $opt['ancho'] ?? null;
    $calidad = $opt['calidad'] ?? null;
    $ssl = $opt['ssl'] ?? true;
	
	// Verifica si la URL está usando Photon
    if (is_photon_url($image_url)) {
        // Limpia cualquier filtro existente en la URL
        $image_url = preg_replace('/\?.*/', '', $image_url);
    } else if (is_original_url($image_url)) { // es una imagen local original
        // Formatea la URL para que use Photon
        // Escoge dominio aleatorio de photon de 0 a 3
		$domain_number = rand(0, 3);
		$image_url = preg_replace('/^https:\/\/(?:www\.)?' . preg_quote($_SERVER['SERVER_NAME'], '/') . '\//', "https://i$domain_number.wp.com/", $image_url);
    }
	// Crear los filtros de photon usando ancho y calidad
	$params = [];
	if ($ancho) {
        $params[] = "w=$ancho";
    }
	if ($calidad) {
        $params[] = "quality=$calidad";
    }
	if ($ssl) {
        $params[] = "ssl=1";
    }
	// Si hay parámetros, agrégalos a la URL
    if (!empty($params)) {
        $image_url .= '?' . implode('&', $params);
    }
	return $image_url;
}

// Obtiene la url del cdn
function get_cdn_url($image_url, $ancho = null, $calidad = null) {
    $image_url = format_url_photon($image_url, ['ancho' => $ancho, 'calidad' => $calidad]);
    return $image_url;
}
