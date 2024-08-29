window.onload = function () {
    
    /**
     * CONFIGURAR MANUALMENTE 
     */
    // const IMAGE_CONFIGS = [
    //     ['.home .lightbox-gallery-wrap', 850, 80],
    //     // Puedes agregar más configuraciones aquí
    // ];
    
    console.log(IMAGE_CONFIGS);
    
    function formatSrcWithPhoton(src, width, quality) {
        const siteDomain = window.location.hostname; 
        const photonDomain = getPhotonDomain(); 
        const cleanSrc = src.split('?')[0];
    
        // Formatear el nuevo src con Photon
        let newSrc = `https://${photonDomain}/${siteDomain}${cleanSrc.replace(/https?:\/\/[^\/]+/, '')}`;
    
        if (width) {
            newSrc += `?w=${width}`;
            if (quality) {
                newSrc += `&quality=${quality}`;
            }
        }
    
        return newSrc; 
    }
    
    function updateImageSources() {
        IMAGE_CONFIGS.forEach(([selector, width, quality]) => {
            // Seleccionar todas las imágenes dentro del elemento con el selector dado
            const elements = document.querySelectorAll(`${selector} img`);
    
            elements.forEach(img => {
                // Verifica si la imagen ya ha sido formateada
                if (img.classList.contains('rpi-checked')) {
                    return; // Salta esta imagen
                }
    
                const originalSrc = img.getAttribute('src');
                if (originalSrc) {
                    // Formatear el src usando Photon
                    const newSrc = formatSrcWithPhoton(originalSrc, width, quality);
                    img.setAttribute('src', newSrc);
                    img.classList.add('rpi-checked'); // Marca la imagen como formateada
                }
            });
            
            // Seleccionar los elementos que coincidan con el selector directamente
            const elementsWithoutImg = document.querySelectorAll(selector);
            elementsWithoutImg.forEach(element => {
                if (element.tagName.toLowerCase() === 'img') {
                    // Verifica si la imagen ya ha sido formateada
                    if (element.classList.contains('rpi-checked')) {
                        return; // Salta esta imagen
                    }
    
                    const originalSrc = element.getAttribute('src');
                    if (originalSrc) {
                        // Formatear el src usando Photon
                        const newSrc = formatSrcWithPhoton(originalSrc, width, quality);
                        element.setAttribute('src', newSrc);
                        element.classList.add('rpi-checked'); // Marca la imagen como formateada
                    }
                }
            });
        });
    }
    
    // Ejecuta la función para actualizar las imágenes
    updateImageSources();
    
    /**
     * END Configurar Manualmente
     */
    
    // Utilizar requestAnimationFrame para garantizar que el DOM esté completamente cargado
    requestAnimationFrame(observeNewImages);

    function getPhotonDomain() {
        const photonDomains = ['i0.wp.com', 'i1.wp.com', 'i2.wp.com'];
        return photonDomains[Math.floor(Math.random() * photonDomains.length)];
    }

    function hasClassRpi(classes) {
        if (typeof classes === 'string' || classes instanceof DOMTokenList) {
            const classArray = typeof classes === 'string' ? classes.split(' ') : Array.from(classes);
            if (classArray.includes('rpi-checked')) {
                return false;
            }
            return classArray.some(clase => clase === 'rpi' || clase.startsWith('rpi-'));
        }
        console.error('El parámetro classes debe ser una cadena o un DOMTokenList');
        return false;
    }

    function checkForRpiClass(element) {
        if (hasClassRpi(element.classList)) {
            element.classList.add('rpi-checked');
            return element;
        }
        let parent = element.parentElement;
        while (parent) {
            if (hasClassRpi(parent.classList)) {
                element.classList.add('rpi-checked');
                return parent;
            }
            parent = parent.parentElement;
        }
        return null;
    }

    function adjustImageSrc(img, rpiElement) {
        const src = img.getAttribute('src');
        if (!src) return;

        const siteDomain = window.location.hostname;
        const photonDomain = getPhotonDomain();
        const cleanSrc = src.split('?')[0];

        let finalSrc = cleanSrc.includes(photonDomain)
            ? cleanSrc
            : `https://${photonDomain}/${siteDomain}${cleanSrc.replace(/https?:\/\/[^\/]+/, '')}`;

        const rpiClass = Array.from(rpiElement.classList).find(cls => cls.startsWith('rpi-'));

        if (rpiClass) {
            const [_, width, quality] = rpiClass.split('-');
            finalSrc += `?w=${width}`;
            if (quality) {
                finalSrc += `&quality=${quality}`;
            }
        }

        fetch(finalSrc, { method: 'HEAD' }).then(response => {
            if (response.ok) {
                img.setAttribute('src', finalSrc);
            }
        });
    }

    function observeNewImages() {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeName === 'IMG') {
                            handleImageNode(node);
                        } else if (node.nodeType === Node.ELEMENT_NODE) {
                            node.querySelectorAll('img').forEach(img => {
                                handleImageNode(img);
                                updateImageSources();
                            });
                        }
                    });
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function handleImageNode(node) {
        const rpiElement = checkForRpiClass(node);
        if (rpiElement) {
            adjustImageSrc(node, rpiElement);
        }
    }
};
