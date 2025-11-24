<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Cargador de compatibilidades
 * 
 * Este archivo maneja automáticamente la carga de todas las compatibilidades
 * con otros plugins según estén disponibles.
 */
class Charrua_PB_Compatibility_Loader {
    
    /**
     * Inicializa todas las compatibilidades disponibles
     */
    public static function init() {
        // Cargar utilidades de precios (siempre disponible)
        require_once dirname( __FILE__ ) . '/class-charrua-pb-price-utils.php';
        
        // Compatibilidad con YITH Dynamic Pricing
        if ( self::is_yith_dynamic_pricing_active() ) {
            require_once dirname( __FILE__ ) . '/class-charrua-pb-yith-compatibility.php';
        }
        
        // Aquí se pueden añadir más compatibilidades en el futuro:
        
        // if ( self::is_woocommerce_subscriptions_active() ) {
        //     require_once dirname( __FILE__ ) . '/class-charrua-pb-subscriptions-compatibility.php';
        // }
        
        // if ( self::is_wpml_active() ) {
        //     require_once dirname( __FILE__ ) . '/class-charrua-pb-wpml-compatibility.php';
        // }
    }
    
    /**
     * Detecta si YITH Dynamic Pricing está activo
     */
    private static function is_yith_dynamic_pricing_active() {
        return class_exists( 'YWDPD_Frontend' ) || 
               class_exists( 'YITH_WC_Dynamic_Pricing_Discounts' );
    }
    
    /**
     * Detecta si WooCommerce Subscriptions está activo
     */
    private static function is_woocommerce_subscriptions_active() {
        return class_exists( 'WC_Subscriptions' );
    }
    
    /**
     * Detecta si WPML está activo
     */
    private static function is_wpml_active() {
        return defined( 'ICL_SITEPRESS_VERSION' );
    }
    
    /**
     * Obtiene información de todas las compatibilidades detectadas
     */
    public static function get_active_compatibilities() {
        return array(
            'yith_dynamic_pricing' => self::is_yith_dynamic_pricing_active(),
            'woocommerce_subscriptions' => self::is_woocommerce_subscriptions_active(),
            'wpml' => self::is_wpml_active(),
        );
    }
}

// Inicializar compatibilidades automáticamente
Charrua_PB_Compatibility_Loader::init();
