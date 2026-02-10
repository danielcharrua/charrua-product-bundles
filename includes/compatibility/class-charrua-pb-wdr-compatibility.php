<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Compatibilidad con Discount Rules for WooCommerce (by Flycart)
 * Plugin: woo-discount-rules
 * 
 * Este plugin aplica descuentos dinámicos basados en reglas configurables.
 * Los descuentos pueden ser por cantidad, porcentaje, precio fijo, etc.
 */
class Charrua_PB_WDR_Compatibility {
    
    /**
     * Verifica si el plugin woo-discount-rules está activo (v2)
     * 
     * @return bool
     */
    public static function is_active() {
        // El plugin v2 define la clase ManageDiscount
        return class_exists( '\Wdr\App\Controllers\ManageDiscount' ) || 
               class_exists( 'Wdr\App\Controllers\ManageDiscount' );
    }
    
    /**
     * Verifica si el Router está disponible (indica que el plugin está correctamente inicializado)
     * 
     * @return bool
     */
    public static function is_router_available() {
        return class_exists( '\Wdr\App\Router' ) || class_exists( 'Wdr\App\Router' );
    }
    
    /**
     * Obtiene el precio con descuentos de woo-discount-rules aplicados
     * 
     * @param WC_Product $product El producto
     * @param int $quantity Cantidad (por defecto 1)
     * @return float Precio con descuentos aplicados (para mostrar)
     */
    public static function get_wdr_price( $product, $quantity = 1 ) {
        if ( ! $product instanceof WC_Product ) {
            return 0;
        }
        
        // Si woo-discount-rules no está activo, devolver precio normal
        if ( ! self::is_active() ) {
            return wc_get_price_to_display( $product );
        }
        
        // Intentar obtener el precio con descuento usando el filtro del plugin
        // El filtro 'advanced_woo_discount_rules_get_product_discount_price' devuelve el precio con descuento
        $original_price = floatval( $product->get_price() );
        
        /**
         * El filtro advanced_woo_discount_rules_get_product_discount_price
         * Parámetros:
         * - $product_price (float|false): Precio inicial o false
         * - $product_or_id: El producto o su ID
         * - $quantity: Cantidad
         * - $custom_price: Precio personalizado (0 para usar el del producto)
         * 
         * Retorna: float con el precio con descuento, o el precio original si no hay descuento
         */
        $discounted_price = apply_filters( 
            'advanced_woo_discount_rules_get_product_discount_price', 
            false, 
            $product, 
            $quantity, 
            0 // custom_price = 0 para usar el precio del producto
        );
        
        // Si el filtro devuelve false, intentar método alternativo con detalles
        if ( $discounted_price === false ) {
            $discounted_price = self::get_price_from_details( $product, $quantity );
        }
        
        // Si aún es false, no hay descuento aplicado
        if ( $discounted_price === false || $discounted_price === null ) {
            return wc_get_price_to_display( $product );
        }
        
        // Convertir a precio mostrable (aplica impuestos si corresponde)
        return wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
    }
    
    /**
     * Método alternativo: obtiene precio desde los detalles de descuento
     * 
     * @param WC_Product $product
     * @param int $quantity
     * @return float|false
     */
    private static function get_price_from_details( $product, $quantity = 1 ) {
        /**
         * El filtro advanced_woo_discount_rules_get_product_discount_details
         * Devuelve un array con toda la información del descuento:
         * - initial_price
         * - discounted_price
         * - initial_price_with_tax
         * - discounted_price_with_tax
         * - total_discount_details
         */
        $details = apply_filters( 
            'advanced_woo_discount_rules_get_product_discount_details', 
            false, 
            $product, 
            $quantity, 
            0 
        );
        
        if ( $details !== false && is_array( $details ) && isset( $details['discounted_price'] ) ) {
            return floatval( $details['discounted_price'] );
        }
        
        return false;
    }
    
    /**
     * Obtiene información detallada del descuento aplicado
     * Útil para debug o para mostrar información adicional
     * 
     * @param WC_Product $product
     * @param int $quantity
     * @return array|false
     */
    public static function get_discount_details( $product, $quantity = 1 ) {
        if ( ! $product instanceof WC_Product || ! self::is_active() ) {
            return false;
        }
        
        return apply_filters( 
            'advanced_woo_discount_rules_get_product_discount_details', 
            false, 
            $product, 
            $quantity, 
            0 
        );
    }
    
    /**
     * Obtiene el porcentaje de descuento aplicado
     * 
     * @param WC_Product $product
     * @return float|false Porcentaje de descuento o false si no hay
     */
    public static function get_discount_percentage( $product ) {
        if ( ! $product instanceof WC_Product || ! self::is_active() ) {
            return false;
        }
        
        return apply_filters( 
            'advanced_woo_discount_rules_get_product_discount_percentage', 
            false, 
            $product 
        );
    }
    
    /**
     * Función principal para usar en el plugin
     * Mantiene consistencia con otras clases de compatibilidad
     * 
     * @param WC_Product $product
     * @param int $quantity
     * @return float
     */
    public static function get_compatible_price( $product, $quantity = 1 ) {
        return self::get_wdr_price( $product, $quantity );
    }
}
