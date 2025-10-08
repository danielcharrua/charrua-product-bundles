<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Admin_Columns {
    public static function add( $columns ) {
        $new = [];
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'title' === $key ) {
                $new['charrua_pb_status'] = __( 'Status', 'charrua-pb' );
            }
        }
        return $new;
    }

    public static function render( $column, $post_id ) {
        if ( 'charrua_pb_status' !== $column ) return;
        $enabled = get_post_meta( $post_id, Charrua_PB_Helper::MK_ENABLED, true );
        $enabled = ( $enabled === 'no' ) ? 'no' : 'yes';
        if ( 'yes' === $enabled ) {
            echo '<span class="charrua-pb-badge charrua-pb-badge--on"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'charrua-pb' ) . '</span>';
        } else {
            echo '<span class="charrua-pb-badge charrua-pb-badge--off"><span class="dashicons dashicons-hidden"></span> ' . esc_html__( 'Inactive', 'charrua-pb' ) . '</span>';
        }
    }
}
