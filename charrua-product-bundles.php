<?php
/**
 * Plugin Name: Bundled Products for WooCommerce – Charrúa
 * Plugin URI: https://charrua.es
 * Description: Añade extras a productos y permite seleccionarlos desde la propia página de producto.
 * Version: 1.0.0
 * Author: Daniel Pereyra Costas
 * Author URI: https://charrua.es
 * Text Domain: charrua-pb
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CHARRUA_PB_VERSION', '1.0.0' );
define( 'CHARRUA_PB_FILE', __FILE__ );
define( 'CHARRUA_PB_DIR', plugin_dir_path( __FILE__ ) );
define( 'CHARRUA_PB_URL', plugin_dir_url( __FILE__ ) );

// Carga de clases.
require_once CHARRUA_PB_DIR . 'includes/helpers-charrua-pb.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-cpt.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-admin-assets.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-admin-columns.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-admin-metaboxes.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-ajax.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-frontend.php';
require_once CHARRUA_PB_DIR . 'includes/class-charrua-pb-cart.php';

register_activation_hook( __FILE__, function() {
    Charrua_PB_CPT::register();
    flush_rewrite_rules();
});

add_action( 'init', function() {
    Charrua_PB_CPT::register();
});

// Admin
add_action( 'admin_enqueue_scripts', [ 'Charrua_PB_Admin_Assets', 'enqueue' ] );
add_action( 'add_meta_boxes',        [ 'Charrua_PB_Admin_Metaboxes', 'add' ] );
add_action( 'save_post_' . Charrua_PB_CPT::POST_TYPE, [ 'Charrua_PB_Admin_Metaboxes', 'save' ] );
add_filter( 'manage_edit-' . Charrua_PB_CPT::POST_TYPE . '_columns', [ 'Charrua_PB_Admin_Columns', 'add' ] );
add_action( 'manage_' . Charrua_PB_CPT::POST_TYPE . '_posts_custom_column', [ 'Charrua_PB_Admin_Columns', 'render' ], 10, 2 );

// AJAX (admin)
add_action( 'wp_ajax_charrua_pb_search_product_cats', [ 'Charrua_PB_AJAX', 'search_product_cats' ] );
add_action( 'wp_ajax_charrua_pb_search_products',     [ 'Charrua_PB_AJAX', 'search_products' ] );

// Frontend (render + cart)
add_action( 'woocommerce_before_add_to_cart_button', [ 'Charrua_PB_Frontend', 'render_groups_on_product' ], 9 );
add_action( 'woocommerce_add_to_cart',              [ 'Charrua_PB_Cart', 'maybe_add_selected_addons' ], 20, 6 );
