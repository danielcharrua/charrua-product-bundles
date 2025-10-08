<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_AJAX {
    public static function check_caps() {
        if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error();
        if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], Charrua_PB_Helper::ADMIN_NONCE ) ) wp_send_json_error();
    }

    public static function search_product_cats() {
        self::check_caps();

        $q    = isset( $_REQUEST['q'] )    ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
        $page = max( 1, intval( $_REQUEST['page'] ?? 1 ) );
        $per  = max( 5, min( 50, intval( $_REQUEST['per'] ?? 30 ) ) );

        $args = [
            'taxonomy'   => 'product_cat',
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
            'number'     => $per,
            'offset'     => ( $page - 1 ) * $per,
        ];
        if ( $q !== '' ) $args['search'] = $q;

        $terms   = get_terms( $args );
        $results = [];
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $results[] = [ 'id' => (int) $t->term_id, 'text' => $t->name . ' [#' . $t->term_id . ']' ];
            }
        }

        $more = is_array( $terms ) && count( $terms ) === $per;
        wp_send_json( [ 'results' => $results, 'more' => $more ] );
    }

    public static function search_products() {
        self::check_caps();

        $q    = isset( $_REQUEST['q'] )    ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
        $page = max( 1, intval( $_REQUEST['page'] ?? 1 ) );
        $per  = max( 5, min( 50, intval( $_REQUEST['per'] ?? 30 ) ) );

        $ids  = [];
        $more = false;

        // 1) Título (paginado)
        $title_q = new WP_Query( [
            'post_type'      => [ 'product' ],
            'post_status'    => 'any',
            'posts_per_page' => $per,
            'paged'          => $page,
            's'              => $q,
            'fields'         => 'ids',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
        if ( $title_q->have_posts() ) {
            $ids  = array_merge( $ids, $title_q->posts );
            $more = ( $page < (int) $title_q->max_num_pages );
        }

        // 2) En página 1, añade coincidencias por SKU
        if ( $page === 1 && $q !== '' ) {
            $sku_q = new WP_Query( [
                'post_type'      => [ 'product' ],
                'post_status'    => 'any',
                'posts_per_page' => $per,
                'paged'          => 1,
                'fields'         => 'ids',
                'meta_query'     => [
                    [
                        'key'     => '_sku',
                        'value'   => $q,
                        'compare' => 'LIKE',
                    ]
                ],
            ] );
            if ( $sku_q->have_posts() ) {
                $ids = array_merge( $ids, $sku_q->posts );
                if ( count( $sku_q->posts ) === $per ) $more = true;
            }
        }

        $ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
        $ids_page = array_slice( $ids, 0, $per );

        $results = [];
        foreach ( $ids_page as $pid ) {
            $p = wc_get_product( $pid );
            if ( ! $p ) continue;
            $sku = $p->get_sku();
            $results[] = [
                'id'   => $pid,
                'text' => $p->get_name() . ( $sku ? ' (SKU: ' . $sku . ')' : '' ) . ' [#' . $pid . ']',
            ];
        }

        if ( count( $ids ) > $per ) $more = true;

        wp_send_json( [ 'results' => $results, 'more' => $more ] );
    }
}
