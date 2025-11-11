<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Frontend_Assets {
    public static function enqueue() {
        // Solo cargar en páginas de productos de WooCommerce
        if ( ! function_exists( 'is_product' ) || ! is_product() ) {
            return;
        }

        wp_enqueue_style(
            'charrua-pb-frontend',
            CHARRUA_PB_URL . 'assets/frontend.css',
            [],
            CHARRUA_PB_VERSION
        );

        wp_enqueue_script(
            'charrua-pb-frontend',
            CHARRUA_PB_URL . 'assets/frontend.js',
            [],
            CHARRUA_PB_VERSION,
            true
        );
    }
}