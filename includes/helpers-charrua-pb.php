<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Helper {
    const NONCE_FIELD  = 'charrua_pb_addons_nonce';
    const NONCE_ACTION = 'charrua_pb_addons_action';
    const ADMIN_NONCE  = 'charrua_pb_admin_nonce';

    // Meta keys (todas nuevas, sin retrocompatibilidad).
    const MK_TITLE          = '_charrua_pb_group_title';
    const MK_DESCRIPTION    = '_charrua_pb_group_description';
    const MK_ENABLED        = '_charrua_pb_is_enabled';     // 'yes' | 'no'
    const MK_COND_CATS      = '_charrua_pb_cond_cats';      // int[]
    const MK_COND_PRODUCTS  = '_charrua_pb_cond_products';  // int[]
    const MK_ADDONS         = '_charrua_pb_addons';         // int[]
    const MK_LAYOUT_TYPE    = '_charrua_pb_layout_type';    // 'list' | 'grid'
    const MK_GRID_COLUMNS   = '_charrua_pb_grid_columns';   // int
    const MK_SELECTION_TYPE = '_charrua_pb_selection_type'; // 'unique' | 'multiple'
    
    public static function get_meta( $post_id ) : array {
        return [
            'title'          => (string) get_post_meta( $post_id, self::MK_TITLE, true ),
            'description'    => (string) get_post_meta( $post_id, self::MK_DESCRIPTION, true ),
            'cats'           => array_map( 'intval', (array) get_post_meta( $post_id, self::MK_COND_CATS, true ) ),
            'products_cond'  => array_map( 'intval', (array) get_post_meta( $post_id, self::MK_COND_PRODUCTS, true ) ),
            'addons'         => array_map( 'intval', (array) get_post_meta( $post_id, self::MK_ADDONS, true ) ),
            'enabled'        => (string) get_post_meta( $post_id, self::MK_ENABLED, true ),
            'layout_type'    => (string) get_post_meta( $post_id, self::MK_LAYOUT_TYPE, true ) ?: 'list',
            'grid_columns'   => (int) get_post_meta( $post_id, self::MK_GRID_COLUMNS, true ) ?: 2,
            'selection_type' => (string) get_post_meta( $post_id, self::MK_SELECTION_TYPE, true ) ?: 'unique',
        ];
    }

    public static function is_enabled( array $meta ) : bool {
        return ( $meta['enabled'] === 'no' ) ? false : true;
    }
}
