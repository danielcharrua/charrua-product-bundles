<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Admin_Columns {
    public static function add( $columns ) {
        $new = [];
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'title' === $key ) {
                $new['charrua_pb_status'] = __( 'Status', 'charrua-pb' );
                $new['charrua_pb_selection_type'] = __( 'Selection Type', 'charrua-pb' );
            }
        }
        return $new;
    }

    public static function render( $column, $post_id ) {
        if ( 'charrua_pb_status' === $column ) {
            $enabled = get_post_meta( $post_id, Charrua_PB_Helper::MK_ENABLED, true );
            $enabled = ( $enabled === 'no' ) ? 'no' : 'yes';
            if ( 'yes' === $enabled ) {
                echo '<span class="charrua-pb-badge charrua-pb-badge--on"><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'charrua-pb' ) . '</span>';
            } else {
                echo '<span class="charrua-pb-badge charrua-pb-badge--off"><span class="dashicons dashicons-hidden"></span> ' . esc_html__( 'Inactive', 'charrua-pb' ) . '</span>';
            }
        }
        
        if ( 'charrua_pb_selection_type' === $column ) {
            $selection_type = get_post_meta( $post_id, Charrua_PB_Helper::MK_SELECTION_TYPE, true );
            $selection_type = ( $selection_type === 'multiple' ) ? 'multiple' : 'unique';
            if ( 'unique' === $selection_type ) {
                echo '<span class="charrua-pb-badge charrua-pb-badge--unique"><span class="dashicons dashicons-marker"></span> ' . esc_html__( 'Single', 'charrua-pb' ) . '</span>';
            } else {
                echo '<span class="charrua-pb-badge charrua-pb-badge--multiple"><span class="dashicons dashicons-yes-alt"></span> ' . esc_html__( 'Multiple', 'charrua-pb' ) . '</span>';
            }
        }
    }
}
