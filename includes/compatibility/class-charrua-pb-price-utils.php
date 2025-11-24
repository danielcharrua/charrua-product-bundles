<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Utilidades para manejo de precios compatibles
 */
class Charrua_PB_Price_Utils {
    
    /**
     * Obtiene el precio de un producto con todas las compatibilidades aplicadas
     * 
     * @param WC_Product $product El producto
     * @return float Precio con descuentos/compatibilidades aplicadas
     */
    public static function get_compatible_price( $product ) {
        if ( ! $product instanceof WC_Product ) {
            return 0;
        }
        
        // Prioridad 1: YITH Dynamic Pricing
        if ( class_exists( 'Charrua_PB_YITH_Compatibility' ) ) {
            return Charrua_PB_YITH_Compatibility::get_compatible_price( $product );
        }
        
        // Prioridad 2: Otras compatibilidades futuras
        // if ( class_exists( 'Charrua_PB_Subscriptions_Compatibility' ) ) {
        //     return Charrua_PB_Subscriptions_Compatibility::get_compatible_price( $product );
        // }
        
        // Fallback: Precio normal de WooCommerce
        return wc_get_price_to_display( $product );
    }
    
    /**
     * Obtiene información sobre qué compatibilidades están activas
     */
    public static function get_active_price_handlers() {
        $handlers = array();
        
        if ( class_exists( 'Charrua_PB_YITH_Compatibility' ) ) {
            $handlers[] = 'YITH Dynamic Pricing';
        }
        
        return $handlers;
    }
}
