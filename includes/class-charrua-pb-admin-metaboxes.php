<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Admin_Metaboxes {
    public static function add() {
        add_meta_box(
            'charrua_pb_options',
            __( 'Display options', 'charrua-pb' ),
            [ __CLASS__, 'mb_options' ],
            Charrua_PB_CPT::POST_TYPE,
            'normal',
            'high'
        );

        add_meta_box(
            'charrua_pb_conditions',
            __( 'Conditions (where to show)', 'charrua-pb' ),
            [ __CLASS__, 'mb_conditions' ],
            Charrua_PB_CPT::POST_TYPE,
            'normal',
            'default'
        );

        add_meta_box(
            'charrua_pb_addons',
            __( 'Products to offer as add-ons', 'charrua-pb' ),
            [ __CLASS__, 'mb_addons' ],
            Charrua_PB_CPT::POST_TYPE,
            'normal',
            'default'
        );
    }

    public static function mb_conditions( $post ) {
        wp_nonce_field( 'charrua_pb_save_group', 'charrua_pb_save_group_nonce' );
        $m = Charrua_PB_Helper::get_meta( $post->ID );

        echo '<p><strong>' . esc_html__( 'Select categories and/or products where this group should be shown.', 'charrua-pb' ) . '</strong></p>';

        echo '<h4 style="margin:8px 0">' . esc_html__( 'Product categories', 'charrua-pb' ) . '</h4>';
        echo '<select id="charrua_pb_cond_cats" name="charrua_pb_cond_cats[]" multiple="multiple" style="width:100%" data-placeholder="' . esc_attr__( 'Search categories...', 'charrua-pb' ) . '">';
        if ( ! empty( $m['cats'] ) ) {
            foreach ( $m['cats'] as $term_id ) {
                $term = get_term( $term_id, 'product_cat' );
                if ( $term && ! is_wp_error( $term ) ) {
                    printf( '<option value="%1$d" selected>%2$s [#%1$d]</option>', (int) $term_id, esc_html( $term->name ) );
                }
            }
        }
        echo '</select>';

        echo '<h4 style="margin:12px 0 6px">' . esc_html__( 'Specific products', 'charrua-pb' ) . '</h4>';
        echo '<select id="charrua_pb_cond_products" name="charrua_pb_cond_products[]" multiple="multiple" style="width:100%" data-placeholder="' . esc_attr__( 'Search products by name or SKU...', 'charrua-pb' ) . '">';
        if ( ! empty( $m['products_cond'] ) ) {
            foreach ( $m['products_cond'] as $pid ) {
                $p = wc_get_product( $pid );
                if ( $p ) {
                    $sku = $p->get_sku();
                    $label = $p->get_name() . ( $sku ? ' (SKU: ' . $sku . ')' : '' ) . ' [#' . $pid . ']';
                    printf( '<option value="%1$d" selected>%2$s</option>', (int) $pid, esc_html( $label ) );
                }
            }
        }
        echo '</select>';

        echo '<p style="margin-top:10px;color:#555">' . wp_kses_post( __( 'Logic: the group is shown if the product belongs to <u>any</u> of the selected categories <strong>or</strong> matches any of the specific products.', 'charrua-pb' ) ) . '</p>';
    }

    public static function mb_addons( $post ) {
        $m = Charrua_PB_Helper::get_meta( $post->ID );

        echo '<p><strong>' . esc_html__( 'Select the products that will be offered as add-ons in this group.', 'charrua-pb' ) . '</strong></p>';

        // Buscador para añadir
        echo '<select id="charrua_pb_addons_select" style="width:100%" data-placeholder="' . esc_attr__( 'Search products (name or SKU)...', 'charrua-pb' ) . '"></select>';

        // Lista sortable con los seleccionados (en orden)
        echo '<ul id="charrua_pb_addons_list" class="charrua-pb-sortable" style="margin-top:10px;padding-left:0;list-style:none;">';

        if ( ! empty( $m['addons'] ) ) {
            foreach ( $m['addons'] as $pid ) {
                $p = wc_get_product( $pid );
                if ( ! $p ) continue;
                $label = $p->get_name();
                printf(
                    '<li class="charrua-pb-item" data-id="%1$d" style="display:flex;align-items:center;gap:.5rem;margin:.35rem 0;padding:.35rem .5rem;border:1px solid #ddd;border-radius:.35rem;cursor:move;">
                        <span class="dashicons dashicons-move" aria-hidden="true"></span>
                        <span class="charrua-pb-item-label" style="flex:1">%2$s [#%1$d]</span>
                        <button type="button" class="button-link charrua-pb-remove" aria-label="%3$s" style="color:#b32d2e;">&times;</button>
                    </li>',
                    (int) $pid,
                    esc_html( $label ),
                    esc_attr__( 'Remove', 'charrua-pb' )
                );
            }
        }
        echo '</ul>';

        // Aquí guardamos el orden (CSV de IDs)
        $csv = ! empty( $m['addons'] ) ? implode( ',', array_map( 'intval', $m['addons'] ) ) : '';
        echo '<input type="hidden" id="charrua_pb_addons_field" name="charrua_pb_addons_field" value="' . esc_attr( $csv ) . '">';

        echo '<p style="margin-top:8px;color:#777">' . esc_html__( 'Drag to reorder. Click × to remove.', 'charrua-pb' ) . '</p>';
    }


    public static function mb_options( $post ) {
        $m             = Charrua_PB_Helper::get_meta( $post->ID );
        $title         = $m['title'] ?: '';
        $enabled       = Charrua_PB_Helper::is_enabled( $m )  ? 'yes' : 'no';
        $layout_type   = $m['layout_type'] ?: 'list';
        $grid_columns  = $m['grid_columns'] ?: 2;
        $selection_type = $m['selection_type'] ?: 'unique';

        echo '<p><label for="charrua_pb_group_title"><strong>' . esc_html__( 'Visible title', 'charrua-pb' ) . '</strong></label>';
        echo '<input type="text" id="charrua_pb_group_title" name="charrua_pb_group_title" value="' . esc_attr( $title ) . '" placeholder="e.g. SIMs for GPS" style="width:100%"></p>';



        echo '<p><label for="charrua_pb_group_description"><strong>' . esc_html__( 'Description', 'charrua-pb' ) . '</strong></label>';
        echo '<textarea id="charrua_pb_group_description" name="charrua_pb_group_description" rows="2" style="width:100%" placeholder="' . esc_attr__( 'Optional description...', 'charrua-pb' ) . '">' . esc_textarea( $m['description'] ?? '' ) . '</textarea></p>';

        echo '<hr><h4>' . esc_html__( 'Selection Options', 'charrua-pb' ) . '</h4>';
        
        echo '<p><label for="charrua_pb_selection_type"><strong>' . esc_html__( 'Selection Type', 'charrua-pb' ) . '</strong></label><br>';
        echo '<select id="charrua_pb_selection_type" name="charrua_pb_selection_type" style="width:100%">';
        echo '<option value="unique" ' . selected( $selection_type, 'unique', false ) . '>' . esc_html__( 'Single choice', 'charrua-pb' ) . '</option>';
        echo '<option value="multiple" ' . selected( $selection_type, 'multiple', false ) . '>' . esc_html__( 'Multiple choice', 'charrua-pb' ) . '</option>';
        echo '</select>';
        echo '<small style="color:#777;display:block;margin-top:5px;">';
        echo '<strong>' . esc_html__( 'Single choice:', 'charrua-pb' ) . '</strong> ' . esc_html__( 'Customer can select only ONE option from this group (e.g., choose one case, one warranty).', 'charrua-pb' ) . '<br>';
        echo '<strong>' . esc_html__( 'Multiple choice:', 'charrua-pb' ) . '</strong> ' . esc_html__( 'Customer can select SEVERAL options to build their own bundle (e.g., lens + tripod + memory card).', 'charrua-pb' );
        echo '</small></p>';

        echo '<hr><h4>' . esc_html__( 'Layout Options', 'charrua-pb' ) . '</h4>';
        
        echo '<p><label for="charrua_pb_layout_type"><strong>' . esc_html__( 'Display Layout', 'charrua-pb' ) . '</strong></label><br>';
        echo '<select id="charrua_pb_layout_type" name="charrua_pb_layout_type" style="width:100%">';
        echo '<option value="list" ' . selected( $layout_type, 'list', false ) . '>' . esc_html__( 'List (default)', 'charrua-pb' ) . '</option>';
        echo '<option value="grid" ' . selected( $layout_type, 'grid', false ) . '>' . esc_html__( 'Grid', 'charrua-pb' ) . '</option>';
        echo '</select></p>';

        echo '<p id="charrua_pb_grid_columns_wrapper" style="' . ( $layout_type !== 'grid' ? 'display:none;' : '' ) . '"><label for="charrua_pb_grid_columns"><strong>' . esc_html__( 'Grid Columns', 'charrua-pb' ) . '</strong></label>';
        echo '<select id="charrua_pb_grid_columns" name="charrua_pb_grid_columns" style="width:100%">';
        for ( $i = 1; $i <= 4; $i++ ) {
            echo '<option value="' . $i . '" ' . selected( $grid_columns, $i, false ) . '>' . $i . ' ' . esc_html__( 'column(s)', 'charrua-pb' ) . '</option>';
        }
        echo '</select>';
        echo '<small style="color:#777">' . esc_html__( 'Used when layout is set to Grid.', 'charrua-pb' ) . '</small></p>';
        
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('#charrua_pb_layout_type').on('change', function() {
                if ($(this).val() === 'grid') {
                    $('#charrua_pb_grid_columns_wrapper').show();
                } else {
                    $('#charrua_pb_grid_columns_wrapper').hide();
                }
            });
        });
        </script>
        <?php

        echo '<hr><p><label for="charrua_pb_is_enabled"><strong>' . esc_html__( 'Status', 'charrua-pb' ) . '</strong></label><br>';
        echo '<label style="display:inline-flex;align-items:center;gap:.5rem">';
        echo '<input type="checkbox" id="charrua_pb_is_enabled" name="charrua_pb_is_enabled" value="yes" ' . checked( $enabled, 'yes', false ) . ' />';
        echo '<span>' . ( $enabled === 'yes' ? esc_html__( 'Active', 'charrua-pb' ) : esc_html__( 'Inactive', 'charrua-pb' ) ) . '</span>';
        echo '</label></p>';
    }

    public static function save( $post_id ) {
        if ( ! isset( $_POST['charrua_pb_save_group_nonce'] ) || ! wp_verify_nonce( $_POST['charrua_pb_save_group_nonce'], 'charrua_pb_save_group' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        update_post_meta( $post_id, Charrua_PB_Helper::MK_TITLE,       sanitize_text_field( $_POST['charrua_pb_group_title'] ?? '' ) );
        update_post_meta( $post_id, Charrua_PB_Helper::MK_DESCRIPTION, sanitize_textarea_field( $_POST['charrua_pb_group_description'] ?? '' ) );
        update_post_meta( $post_id, Charrua_PB_Helper::MK_ENABLED,     isset( $_POST['charrua_pb_is_enabled'] ) ? 'yes' : 'no' );

        // Guardar opciones de selección
        $selection_type = sanitize_text_field( $_POST['charrua_pb_selection_type'] ?? 'unique' );
        $selection_type = in_array( $selection_type, [ 'unique', 'multiple' ] ) ? $selection_type : 'unique';
        update_post_meta( $post_id, Charrua_PB_Helper::MK_SELECTION_TYPE, $selection_type );

        // Guardar opciones de layout
        $layout_type = sanitize_text_field( $_POST['charrua_pb_layout_type'] ?? 'list' );
        $layout_type = in_array( $layout_type, [ 'list', 'grid' ] ) ? $layout_type : 'list';
        update_post_meta( $post_id, Charrua_PB_Helper::MK_LAYOUT_TYPE, $layout_type );
        
        $grid_columns = (int) ( $_POST['charrua_pb_grid_columns'] ?? 2 );
        $grid_columns = max( 1, min( 4, $grid_columns ) ); // Entre 1 y 4 columnas
        update_post_meta( $post_id, Charrua_PB_Helper::MK_GRID_COLUMNS, $grid_columns );

        $cats = isset( $_POST['charrua_pb_cond_cats'] ) ? array_map( 'intval', (array) $_POST['charrua_pb_cond_cats'] ) : [];
        update_post_meta( $post_id, Charrua_PB_Helper::MK_COND_CATS, $cats );

        $cond_products = isset( $_POST['charrua_pb_cond_products'] ) ? array_map( 'intval', (array) $_POST['charrua_pb_cond_products'] ) : [];
        update_post_meta( $post_id, Charrua_PB_Helper::MK_COND_PRODUCTS, $cond_products );

        // Guardar add-ons respetando el orden (CSV → array de ints)
        $addons_csv = isset( $_POST['charrua_pb_addons_field'] ) ? sanitize_text_field( wp_unslash( $_POST['charrua_pb_addons_field'] ) ) : '';
        $addons_ids = array_filter( array_map( 'intval', array_map( 'trim', explode( ',', $addons_csv ) ) ) );
        update_post_meta( $post_id, Charrua_PB_Helper::MK_ADDONS, $addons_ids );
    }
}
