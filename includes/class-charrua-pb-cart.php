<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Cart {
    public static function maybe_add_selected_addons( $parent_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
        // Evita loops si ya es add-on
        if ( ! empty( $cart_item_data['_charrua_pb_is_addon'] ) ) return;

        // Verifica nonce
        if ( empty( $_POST[ Charrua_PB_Helper::NONCE_FIELD ] ) || ! wp_verify_nonce( $_POST[ Charrua_PB_Helper::NONCE_FIELD ], Charrua_PB_Helper::NONCE_ACTION ) ) return;

        $groups_present = isset( $_POST['charrua_pb_groups_present'] ) ? (array) $_POST['charrua_pb_groups_present'] : [];
        if ( empty( $groups_present ) ) return;

        foreach ( $groups_present as $gid_raw ) {
            $gid = (int) $gid_raw;
            if ( $gid <= 0 ) continue;

            $meta_group = Charrua_PB_Helper::get_meta( $gid );
            if ( ! Charrua_PB_Helper::is_enabled( $meta_group ) ) continue;

            $field  = 'charrua_pb_group_' . $gid;
            $choice = isset( $_POST[ $field ] ) ? (int) $_POST[ $field ] : 0;
            if ( $choice <= 0 ) continue;

            $allowed_addons = array_map( 'intval', (array) $meta_group['addons'] );
            if ( ! in_array( $choice, $allowed_addons, true ) ) continue;

            $addon = wc_get_product( $choice );
            if ( ! $addon || ! $addon->is_purchasable() || ! $addon->is_in_stock() ) continue;
            if ( (int) $choice === (int) $product_id ) continue;

            WC()->cart->add_to_cart(
                $choice,
                max( 1, (int) $quantity ),
                0,
                [],
                [
                    '_charrua_pb_is_addon'   => true,
                    '_charrua_pb_parent_key' => $parent_key,
                    '_charrua_pb_group_id'   => $gid,
                ]
            );
        }
    }
}
