<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Helper {
    const NONCE_FIELD  = 'charrua_pb_addons_nonce';
    const NONCE_ACTION = 'charrua_pb_addons_action';
    const ADMIN_NONCE  = 'charrua_pb_admin_nonce';

    // Meta keys (todas nuevas, sin retrocompatibilidad).
    const MK_TITLE         = '_charrua_pb_group_title';
    const MK_DESCRIPTION   = '_charrua_pb_group_description';
    const MK_ALLOW_NONE    = '_charrua_pb_allow_none';     // 'yes' | 'no'
    const MK_ENABLED       = '_charrua_pb_is_enabled';     // 'yes' | 'no'
    const MK_NONE_LABEL    = '_charrua_pb_none_label';
    const MK_COND_CATS     = '_charrua_pb_cond_cats';      // int[]
    const MK_COND_PRODUCTS = '_charrua_pb_cond_products';  // int[]
    const MK_ADDONS        = '_charrua_pb_addons';         // int[]
    
    public static function get_meta( $post_id ) : array {
        return [
            'title'         => (string) get_post_meta( $post_id, self::MK_TITLE, true ),
            'description'   => (string) get_post_meta( $post_id, self::MK_DESCRIPTION, true ),
            'cats'          => array_map( 'intval', (array) get_post_meta( $post_id, self::MK_COND_CATS, true ) ),
            'products_cond' => array_map( 'intval', (array) get_post_meta( $post_id, self::MK_COND_PRODUCTS, true ) ),
            'addons'        => array_map( 'intval', (array) get_post_meta( $post_id, self::MK_ADDONS, true ) ),
            'allow_none'    => (string) get_post_meta( $post_id, self::MK_ALLOW_NONE, true ),
            'enabled'       => (string) get_post_meta( $post_id, self::MK_ENABLED, true ),
            'none_label'    => (string) get_post_meta( $post_id, self::MK_NONE_LABEL, true ),
        ];
    }

    public static function is_enabled( array $meta ) : bool {
        return ( $meta['enabled'] === 'no' ) ? false : true;
    }

    public static function allow_none( array $meta ) : bool {
        return ( $meta['allow_none'] === 'no' ) ? false : true;
    }
}
