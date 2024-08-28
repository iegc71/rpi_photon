<?php
function mostrar_mensaje_admin() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Snippet desde PHP', 'rpi_photon' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'mostrar_mensaje_admin' );
