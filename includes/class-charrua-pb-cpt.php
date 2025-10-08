<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_CPT {
    // Renombrado: charrua_group → bundle_product_group
    const POST_TYPE = 'bundle_product_group';

    public static function register() {
        register_post_type( self::POST_TYPE, [
            'labels' => [
                'name'               => __( 'Bundle Product Groups', 'charrua-pb' ),
                'singular_name'      => __( 'Bundle Product Group', 'charrua-pb' ),
                'add_new'            => __( 'Add Group', 'charrua-pb' ),
                'add_new_item'       => __( 'Add New Group', 'charrua-pb' ),
                'edit_item'          => __( 'Edit Group', 'charrua-pb' ),
                'new_item'           => __( 'New Group', 'charrua-pb' ),
                'view_item'          => __( 'View Group', 'charrua-pb' ),
                'search_items'       => __( 'Search Groups', 'charrua-pb' ),
                'not_found'          => __( 'No groups found', 'charrua-pb' ),
                'not_found_in_trash' => __( 'No groups found in Trash', 'charrua-pb' ),
                'menu_name'          => __( 'Product Bundles', 'charrua-pb' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-image-filter',
            'supports'     => [ 'title' ],
        ] );
    }
}
