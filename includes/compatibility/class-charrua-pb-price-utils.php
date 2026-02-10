<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Utilidades para manejo de precios compatibles
 */
class Charrua_PB_Price_Utils {
    
    /**
     * Obtiene el precio de un producto con todas las compatibilidades aplicadas
     * 
     * Orden de prioridad:
     * 1. Discount Rules for WooCommerce (woo-discount-rules by Flycart)
     * 2. YITH Dynamic Pricing & Discounts
     * 3. Precio normal de WooCommerce
     * 
     * @param WC_Product $product El producto
     * @param int $quantity Cantidad (por defecto 1)
     * @return float Precio con descuentos/compatibilidades aplicadas
     */
    public static function get_compatible_price( $product, $quantity = 1 ) {
        if ( ! $product instanceof WC_Product ) {
            return 0;
        }
        
        // Prioridad 1: Discount Rules for WooCommerce (woo-discount-rules)
        // Este plugin es muy popular y aplica descuentos dinámicos basados en reglas
        if ( class_exists( 'Charrua_PB_WDR_Compatibility' ) && Charrua_PB_WDR_Compatibility::is_active() ) {
            $wdr_price = Charrua_PB_WDR_Compatibility::get_compatible_price( $product, $quantity );
            // Solo retornar si realmente hay un descuento aplicado
            $normal_price = wc_get_price_to_display( $product );
            if ( $wdr_price > 0 && $wdr_price < $normal_price ) {
                return $wdr_price;
            }
            // Si WDR está activo pero no hay descuento, el precio puede ser igual
            // Continuamos para ver si YITH tiene algún descuento
        }
        
        // Prioridad 2: YITH Dynamic Pricing
        if ( class_exists( 'Charrua_PB_YITH_Compatibility' ) && class_exists( 'YWDPD_Frontend' ) ) {
            $yith_price = Charrua_PB_YITH_Compatibility::get_compatible_price( $product );
            $normal_price = wc_get_price_to_display( $product );
            if ( $yith_price > 0 && $yith_price < $normal_price ) {
                return $yith_price;
            }
        }
        
        // Prioridad 3: Otras compatibilidades futuras
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
        
        if ( class_exists( 'Charrua_PB_WDR_Compatibility' ) && Charrua_PB_WDR_Compatibility::is_active() ) {
            $handlers[] = 'Discount Rules for WooCommerce';
        }
        
        if ( class_exists( 'Charrua_PB_YITH_Compatibility' ) && class_exists( 'YWDPD_Frontend' ) ) {
            $handlers[] = 'YITH Dynamic Pricing';
        }
        
        return $handlers;
    }
}
