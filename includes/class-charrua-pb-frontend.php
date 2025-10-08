<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Frontend {
    public static function render_groups_on_product() {
        if ( ! function_exists( 'wc' ) ) return;

        global $product;
        if ( ! $product instanceof WC_Product ) return;

        $groups = self::get_matching_groups_for_product( $product->get_id() );
        if ( empty( $groups ) ) return;

        static $nonce_printed = false;
        if ( ! $nonce_printed ) {
            wp_nonce_field( Charrua_PB_Helper::NONCE_ACTION, Charrua_PB_Helper::NONCE_FIELD );
            $nonce_printed = true;
        }

        foreach ( $groups as $group_post ) {
            $m = Charrua_PB_Helper::get_meta( $group_post->ID );
            if ( ! Charrua_PB_Helper::is_enabled( $m ) ) continue;

            $label      = $m['title'] ?: get_the_title( $group_post );
            $allow_none = Charrua_PB_Helper::allow_none( $m );
            $none_label = trim( $m['none_label'] ) !== '' ? $m['none_label'] : 'None';

            $addon_ids = array_filter( $m['addons'] );
            if ( empty( $addon_ids ) ) continue;

            $addon_products = [];
            foreach ( $addon_ids as $pid ) {
                $p = wc_get_product( $pid );
                if ( $p && $p->is_purchasable() ) $addon_products[] = $p;
            }
            if ( empty( $addon_products ) ) continue;

            $group_id = (int) $group_post->ID;

            echo '<input type="hidden" name="charrua_pb_groups_present[]" value="' . esc_attr( $group_id ) . '">';

            echo '<div class="charrua-pb-group" style="margin:.85rem 0;border:1px solid #eee;padding:.75rem;border-radius:.5rem">';
            echo '<strong style="display:block;margin-bottom:.5rem">' . esc_html( $label ) . '</strong>';

            $description = $m['description'];
            if ( $description ) {
                echo '<div class="charrua-pb-group-description" style="color:#888;margin-top:8px;margin-bottom:8px;font-size:0.97em;">' . esc_html( $description ) . '</div>';
            }

            $name = 'charrua_pb_group_' . $group_id;

            if ( $allow_none ) {
                echo '<label style="display:flex;gap:.5rem;align-items:center;margin-bottom:.35rem">';
                echo '<input type="radio" name="' . esc_attr( $name ) . '" value="" checked> ' . esc_html( $none_label );
                echo '</label>';
            }

            foreach ( $addon_products as $ap ) {
                $title = $ap->get_name();
                $price = wc_price( wc_get_price_to_display( $ap ) );
                $url   = get_permalink( $ap->get_id() );
                printf(
                    '<label style="display:flex;gap:.5rem;align-items:center;margin-bottom:.35rem">
                        <input type="radio" name="%1$s" value="%2$d">
                        <span>%3$s — %4$s</span>
                        <a href="%5$s" target="_blank" style="margin-left:auto;font-size:.9em" aria-label="%3$s">
                            <img alt="%3$s" src="%6$s" style="width:20px">
                        </a>
                    </label>',
                    esc_attr( $name ),
                    (int) $ap->get_id(),
                    esc_html( $title ),
                    wp_kses_post( $price ),
                    esc_url( $url ),
                    esc_url( CHARRUA_PB_URL . 'assets/img/link.svg' )
                );
            }

            echo '<small style="display:block;color:#777;margin-top:.25rem"></small>';
            echo '</div>';
        }
    }

    private static function get_matching_groups_for_product( $product_id ) : array {
        $groups = get_posts( [
            'post_type'      => Charrua_PB_CPT::POST_TYPE,
            'post_status'    => 'publish',
            'numberposts'    => -1,
            'meta_query'     => [
                [
                    'key'     => Charrua_PB_Helper::MK_ENABLED,
                    'value'   => 'no',
                    'compare' => '!=',
                ]
            ],
        ] );
        if ( empty( $groups ) ) return [];

        $product_terms = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );
        $product_terms = array_map( 'intval', (array) $product_terms );

        $matched = [];
        foreach ( $groups as $g ) {
            $m = Charrua_PB_Helper::get_meta( $g->ID );
            if ( ! Charrua_PB_Helper::is_enabled( $m ) ) continue;

            $cats  = array_map( 'intval', (array) $m['cats'] );
            $prods = array_map( 'intval', (array) $m['products_cond'] );

            $cat_match  = ! empty( $cats )  && count( array_intersect( $cats, $product_terms ) ) > 0;
            $prod_match = ! empty( $prods ) && in_array( (int) $product_id, $prods, true );

            if ( $cat_match || $prod_match ) {
                $matched[] = $g;
            }
        }
        return $matched;
    }
}
