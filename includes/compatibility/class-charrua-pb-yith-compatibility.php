<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Compatibilidad simple y directa con YITH Dynamic Pricing
 */
class Charrua_PB_YITH_Compatibility {
    
    /**
     * Obtiene el precio con descuentos de YITH aplicados
     */
    public static function get_yith_price( $product ) {
        if ( ! $product instanceof WC_Product ) {
            return 0;
        }
        
        // Verificar si YITH está activo
        if ( ! class_exists( 'YWDPD_Frontend' ) ) {
            return wc_get_price_to_display( $product );
        }
        
        // Obtener la instancia de YITH Frontend
        $yith_frontend = YWDPD_Frontend::get_instance();
        if ( ! $yith_frontend ) {
            return wc_get_price_to_display( $product );
        }
        
        // Usar directamente la función de YITH para obtener precio con descuentos
        $original_price = floatval( $product->get_price() );
        $discounted_price = $yith_frontend->get_quantity_price( $original_price, $product );
        
        // Convertir a precio mostrable
        return wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
    }
    
    /**
     * Función simple para usar en tu plugin
     */
    public static function get_compatible_price( $product ) {
        return self::get_yith_price( $product );
    }
}
