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

            $label                = $m['title'] ?: get_the_title( $group_post );
            $layout_type          = $m['layout_type'] ?: 'list';
            $grid_columns         = $m['grid_columns'] ?: 2;
            $grid_columns_mobile  = $m['grid_columns_mobile'] ?: 1;
            $selection_type       = $m['selection_type'] ?: 'unique';

            $addon_ids = array_filter( $m['addons'] );
            if ( empty( $addon_ids ) ) continue;

            $addon_products = [];
            foreach ( $addon_ids as $pid ) {
                $p = wc_get_product( $pid );
                if ( $p && $p->is_purchasable() ) $addon_products[] = $p;
            }
            if ( empty( $addon_products ) ) continue;

            $group_id = (int) $group_post->ID;

            echo '<div class="charrua-pb-group">';
            echo '<strong class="charrua-pb-group-title">' . esc_html( $label ) . '</strong>';

            $description = $m['description'];
            if ( $description ) {
                echo '<div class="charrua-pb-group-description">' . esc_html( $description ) . '</div>';
            }

            $name = 'charrua_pb_group_' . $group_id;

            // Contenedor para las opciones con clase según el layout
            $container_class = 'charrua-pb-addons-container';
            $container_style = '';
            
            if ( $layout_type === 'grid' ) {
                $container_class .= ' charrua-pb-grid-layout charrua-pb-grid-columns-mobile-' . $grid_columns_mobile;
                $container_style = 'grid-template-columns:repeat(' . $grid_columns . ',1fr);';
            } else {
                $container_class .= ' charrua-pb-list-layout';
            }

            // Agregar clase según el tipo de selección
            if ( $selection_type === 'multiple' ) {
                $container_class .= ' charrua-pb-multiple-selection';
            } else {
                $container_class .= ' charrua-pb-unique-selection';
            }

            echo '<div class="' . esc_attr( $container_class ) . '"' . ($container_style ? ' style="' . esc_attr( $container_style ) . '"' : '') . '>';

            // Campo hidden solo para selección única (radio buttons)
            if ( $selection_type === 'unique' ) {
                echo '<input type="radio" name="' . esc_attr( $name ) . '" value="" checked class="charrua-pb-none-option">';
            }

            foreach ( $addon_products as $ap ) {
                $title = $ap->get_name();
                
                // Obtener precio con todas las compatibilidades aplicadas
                $numeric_price = Charrua_PB_Price_Utils::get_compatible_price( $ap );
                $price = wc_price( $numeric_price );
                $url   = get_permalink( $ap->get_id() );
                $product_image = wp_get_attachment_image_src( $ap->get_image_id(), 'thumbnail' );
                $product_image_url = $product_image ? $product_image[0] : wc_placeholder_img_src( 'thumbnail' );
                
                // Determinar tipo de input y nombre
                if ( $selection_type === 'multiple' ) {
                    $input_type = 'checkbox';
                    $input_name = $name . '[]'; // Array para múltiples selecciones
                } else {
                    $input_type = 'radio';
                    $input_name = $name;
                }
                                
                if ( $layout_type === 'grid' ) {
                    printf(
                        '<label>
                            <input type="%9$s" name="%1$s" value="%2$d">
                            <div class="charrua-pb-product-image">
                                <img src="%7$s" alt="%3$s">
                            </div>
                            <span class="product-title">%3$s</span>
                            <span class="product-price charrua-pb-price" data-price="%8$s">%4$s</span>
                            <a href="%5$s" target="_blank" aria-label="Ver %3$s" onclick="event.stopPropagation();">
                                <img alt="Ver producto" src="%6$s">
                            </a>
                        </label>',
                        esc_attr( $input_name ),
                        (int) $ap->get_id(),
                        esc_html( $title ),
                        wp_kses_post( $price ),
                        esc_url( $url ),
                        esc_url( CHARRUA_PB_URL . 'assets/img/link.svg' ),
                        esc_url( $product_image_url ),
                        esc_attr( $numeric_price ),
                        esc_attr( $input_type )
                    );
                } else {
                    printf(
                        '<label>
                            <input type="%8$s" name="%1$s" value="%2$d">
                            <span class="product-title">%3$s</span>
                            <span class="product-price charrua-pb-price" data-price="%7$s">%4$s</span>
                            <a href="%5$s" target="_blank" aria-label="%3$s">
                                <img alt="%3$s" src="%6$s">
                            </a>
                        </label>',
                        esc_attr( $input_name ),
                        (int) $ap->get_id(),
                        esc_html( $title ),
                        wp_kses_post( $price ),
                        esc_url( $url ),
                        esc_url( CHARRUA_PB_URL . 'assets/img/link.svg' ),
                        esc_attr( $numeric_price ),
                        esc_attr( $input_type )
                    );
                }
            }

            echo '</div>'; // Cerrar el contenedor de addons

            echo '</div>';
        }
        
        // Agregar calculador de total si hay grupos
        if ( ! empty( $groups ) ) {
            self::render_total_calculator( $product );
        }
    }
    
    private static function render_total_calculator( $product ) {
        // Obtener precio base con todas las compatibilidades aplicadas
        $base_price = Charrua_PB_Price_Utils::get_compatible_price( $product );
        
        echo '<div class="charrua-pb-total-calculator">';
        echo '<div class="charrua-pb-total">';
        echo '<span class="label">' . __( 'Total:', 'charrua-pb' ) . '</span>';
        echo '<span class="price" data-base-price="' . esc_attr( $base_price ) . '">' . wc_price( $base_price ) . '</span>';
        echo '</div>';
        echo '</div>';
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
