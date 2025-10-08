<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Admin_Assets {
    public static function enqueue( $hook ) {
        $screen = get_current_screen();
        if ( ! $screen || $screen->post_type !== Charrua_PB_CPT::POST_TYPE ) return;

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'selectWoo' );
        wp_enqueue_style( 'select2' );
        wp_enqueue_script( 'jquery-ui-sortable' ); // <-- añadido

        wp_enqueue_style( 'charrua-pb-admin', CHARRUA_PB_URL . 'assets/admin.css', [], CHARRUA_PB_VERSION );
        wp_enqueue_script( 'charrua-pb-admin', CHARRUA_PB_URL . 'assets/admin.js', [ 'jquery', 'selectWoo' ], CHARRUA_PB_VERSION, true );

        wp_localize_script( 'charrua-pb-admin', 'CHARRUA_PB_ADMIN', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( Charrua_PB_Helper::ADMIN_NONCE ),
            'per'     => 30,
        ] );

        add_action( 'admin_head', function() {
            echo '<style>.column-charrua_pb_status{width:110px}.charrua-pb-badge{display:inline-flex;gap:.3rem;align-items:center;padding:.15rem .45rem;border-radius:.4rem;border:1px solid #ccd0d4}.charrua-pb-badge--on{background:#e7f7ed;color:#1a7f37;border-color:#a6d5b6}.charrua-pb-badge--off{background:#f8e9e9;color:#8a1f1f;border-color:#e2bcbc}</style>';
        } );
    }
}
