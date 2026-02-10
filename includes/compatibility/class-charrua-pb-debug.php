<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Herramientas de debug para compatibilidad de precios
 * 
 * Para activar el debug, añade esta línea en wp-config.php o en el plugin principal:
 * define( 'CHARRUA_PB_DEBUG_PRICES', true );
 * 
 * O usa el filtro:
 * add_filter( 'charrua_pb_debug_prices', '__return_true' );
 * 
 * El debug solo se muestra a administradores logueados.
 */
class Charrua_PB_Debug {
    
    /**
     * ═══════════════════════════════════════════════════════════
     *  ACTIVAR/DESACTIVAR DEBUG AQUÍ
     * ═══════════════════════════════════════════════════════════
     */
    const DEBUG_ENABLED = false;  // Cambiar a false para desactivar
    
    /**
     * Inicializa el debug
     */
    public static function init() {
        if ( self::is_debug_enabled() ) {
            add_action( 'woocommerce_single_product_summary', [ __CLASS__, 'render_price_debug' ], 6 );
            add_action( 'wp_head', [ __CLASS__, 'debug_styles' ] );
        }
    }
    
    /**
     * Verifica si el debug está activado
     */
    public static function is_debug_enabled() {
        // Primero verificar la constante de clase
        if ( ! self::DEBUG_ENABLED ) {
            return false;
        }
        
        // Solo para admins logueados
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }
        
        // Verificar constante global
        if ( defined( 'CHARRUA_PB_DEBUG_PRICES' ) && ! CHARRUA_PB_DEBUG_PRICES ) {
            return false;
        }
        
        // Permitir override con filtro
        return apply_filters( 'charrua_pb_debug_prices', true );
    }
    
    /**
     * Estilos para el panel de debug
     */
    public static function debug_styles() {
        ?>
        <style>
            .charrua-pb-debug {
                background: #1e1e1e;
                color: #d4d4d4;
                font-family: 'Monaco', 'Consolas', monospace;
                font-size: 12px;
                padding: 15px;
                margin: 15px 0;
                border-radius: 8px;
                border-left: 4px solid #007acc;
                max-width: 100%;
                overflow-x: auto;
            }
            .charrua-pb-debug summary {
                cursor: pointer;
                font-weight: bold;
                color: #569cd6;
                font-size: 14px;
                padding: 5px 0;
            }
            .charrua-pb-debug summary:hover {
                color: #9cdcfe;
            }
            .charrua-pb-debug table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            .charrua-pb-debug th,
            .charrua-pb-debug td {
                text-align: left;
                padding: 8px 12px;
                border-bottom: 1px solid #3c3c3c;
            }
            .charrua-pb-debug th {
                color: #c586c0;
                font-weight: normal;
                width: 200px;
            }
            .charrua-pb-debug td {
                color: #ce9178;
            }
            .charrua-pb-debug .price-value {
                color: #b5cea8;
                font-weight: bold;
            }
            .charrua-pb-debug .final-price {
                background: #264f78;
                color: #4ec9b0;
                font-size: 14px;
            }
            .charrua-pb-debug .active {
                color: #4ec9b0;
            }
            .charrua-pb-debug .inactive {
                color: #6a6a6a;
            }
            .charrua-pb-debug .has-discount {
                color: #4ec9b0;
            }
            .charrua-pb-debug .no-discount {
                color: #f14c4c;
            }
            .charrua-pb-debug .section-title {
                color: #dcdcaa;
                font-size: 11px;
                text-transform: uppercase;
                padding-top: 15px;
                border-bottom: 1px solid #569cd6;
            }
            .charrua-pb-debug .note {
                color: #6a9955;
                font-style: italic;
                font-size: 11px;
            }
        </style>
        <?php
    }
    
    /**
     * Renderiza el panel de debug de precios
     */
    public static function render_price_debug() {
        global $product;
        
        if ( ! $product instanceof WC_Product ) {
            return;
        }
        
        $debug_data = self::get_all_prices( $product );
        $plugins_status = self::get_plugins_status();
        
        ?>
        <details class="charrua-pb-debug">
            <summary>🔧 Debug Precios - Charrua PB</summary>
            
            <table>
                <!-- Info del producto -->
                <tr class="section-title">
                    <th colspan="2">📦 Producto</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <td><?php echo esc_html( $debug_data['product_id'] ); ?></td>
                </tr>
                <tr>
                    <th>Tipo</th>
                    <td><?php echo esc_html( $debug_data['product_type'] ); ?></td>
                </tr>
                <tr>
                    <th>Nombre</th>
                    <td><?php echo esc_html( $debug_data['product_name'] ); ?></td>
                </tr>
                
                <!-- Estado de plugins -->
                <tr class="section-title">
                    <th colspan="2">🔌 Plugins de Descuento Detectados</th>
                </tr>
                <tr>
                    <th>WooCommerce</th>
                    <td class="<?php echo $plugins_status['woocommerce']['active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $plugins_status['woocommerce']['active'] ? '✅ Activo' : '❌ Inactivo'; ?>
                        <?php if ( $plugins_status['woocommerce']['version'] ) : ?>
                            <span class="note">(v<?php echo esc_html( $plugins_status['woocommerce']['version'] ); ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Discount Rules (WDR)</th>
                    <td class="<?php echo $plugins_status['wdr']['active'] ? 'active' : 'inactive'; ?>">
                        <?php if ( $plugins_status['wdr']['installed'] ) : ?>
                            <?php echo $plugins_status['wdr']['active'] ? '✅ Activo' : '⚠️ Instalado pero inactivo'; ?>
                            <?php if ( $plugins_status['wdr']['version'] ) : ?>
                                <span class="note">(v<?php echo esc_html( $plugins_status['wdr']['version'] ); ?>)</span>
                            <?php endif; ?>
                        <?php else : ?>
                            ❌ No instalado
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>YITH Dynamic Pricing</th>
                    <td class="<?php echo $plugins_status['yith']['active'] ? 'active' : 'inactive'; ?>">
                        <?php if ( $plugins_status['yith']['installed'] ) : ?>
                            <?php echo $plugins_status['yith']['active'] ? '✅ Activo' : '⚠️ Instalado pero inactivo'; ?>
                            <?php if ( $plugins_status['yith']['version'] ) : ?>
                                <span class="note">(v<?php echo esc_html( $plugins_status['yith']['version'] ); ?>)</span>
                            <?php endif; ?>
                        <?php else : ?>
                            ❌ No instalado
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Compatibilidad activa</th>
                    <td>
                        <?php 
                        $handlers = Charrua_PB_Price_Utils::get_active_price_handlers();
                        if ( $handlers ) {
                            echo '<span class="active">' . esc_html( implode( ', ', $handlers ) ) . '</span>';
                        } else {
                            echo '<span class="inactive">Ninguna (usando precios WooCommerce)</span>';
                        }
                        ?>
                    </td>
                </tr>
                
                <!-- Precios base WooCommerce -->
                <tr class="section-title">
                    <th colspan="2">💰 Precios WooCommerce (raw)</th>
                </tr>
                <tr>
                    <th>Regular Price</th>
                    <td class="price-value"><?php echo esc_html( $debug_data['wc_regular_price'] ); ?> €</td>
                </tr>
                <tr>
                    <th>Sale Price</th>
                    <td class="price-value">
                        <?php echo $debug_data['wc_sale_price'] ? esc_html( $debug_data['wc_sale_price'] ) . ' €' : '<span class="inactive">—</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <th>get_price()</th>
                    <td class="price-value"><?php echo esc_html( $debug_data['wc_price'] ); ?> €</td>
                </tr>
                <tr>
                    <th>wc_get_price_to_display()</th>
                    <td class="price-value"><?php echo esc_html( $debug_data['wc_display_price'] ); ?> €</td>
                </tr>
                
                <!-- WDR -->
                <tr class="section-title">
                    <th colspan="2">🏷️ Discount Rules for WooCommerce</th>
                </tr>
                <tr>
                    <th>Clase disponible</th>
                    <td class="<?php echo $debug_data['wdr_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $debug_data['wdr_active'] ? '✅ Sí' : '❌ No'; ?>
                    </td>
                </tr>
                <?php if ( $debug_data['wdr_active'] ) : ?>
                <tr>
                    <th>Precio con descuento</th>
                    <td class="price-value">
                        <?php echo esc_html( $debug_data['wdr_price'] ); ?> €
                        <?php if ( $debug_data['wdr_has_discount'] ) : ?>
                            <span class="has-discount">(descuento aplicado: -<?php echo esc_html( round( $debug_data['wc_display_price'] - $debug_data['wdr_price'], 2 ) ); ?> €)</span>
                        <?php else : ?>
                            <span class="no-discount">(sin descuento para este producto)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ( $debug_data['wdr_details'] ) : ?>
                <tr>
                    <th>Detalles WDR</th>
                    <td>
                        <pre style="margin:0;color:#9cdcfe;font-size:10px;"><?php echo esc_html( print_r( $debug_data['wdr_details'], true ) ); ?></pre>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endif; ?>
                
                <!-- YITH -->
                <tr class="section-title">
                    <th colspan="2">🟡 YITH Dynamic Pricing</th>
                </tr>
                <tr>
                    <th>Clase disponible</th>
                    <td class="<?php echo $debug_data['yith_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $debug_data['yith_active'] ? '✅ Sí' : '❌ No'; ?>
                    </td>
                </tr>
                <?php if ( $debug_data['yith_active'] ) : ?>
                <tr>
                    <th>Precio con descuento</th>
                    <td class="price-value">
                        <?php echo esc_html( $debug_data['yith_price'] ); ?> €
                        <?php if ( $debug_data['yith_has_discount'] ) : ?>
                            <span class="has-discount">(descuento aplicado: -<?php echo esc_html( round( $debug_data['wc_display_price'] - $debug_data['yith_price'], 2 ) ); ?> €)</span>
                        <?php else : ?>
                            <span class="no-discount">(sin descuento para este producto)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
                
                <!-- Resultado final -->
                <tr class="section-title">
                    <th colspan="2">🎯 Resultado Charrua PB</th>
                </tr>
                <tr class="final-price">
                    <th>Precio Final Usado</th>
                    <td class="price-value" style="font-size:16px;">
                        <?php echo esc_html( $debug_data['final_price'] ); ?> €
                    </td>
                </tr>
                <tr>
                    <th>Fuente del precio</th>
                    <td class="active"><?php echo esc_html( $debug_data['price_source'] ); ?></td>
                </tr>
            </table>
            
            <p class="note">
                ℹ️ Este panel solo es visible para administradores. 
                Para desactivar, cambia DEBUG_ENABLED a false en class-charrua-pb-debug.php
            </p>
        </details>
        <?php
    }
    
    /**
     * Obtiene el estado de los plugins de descuento
     */
    public static function get_plugins_status() {
        // WooCommerce
        $wc_active = class_exists( 'WooCommerce' );
        $wc_version = $wc_active && defined( 'WC_VERSION' ) ? WC_VERSION : null;
        
        // Discount Rules for WooCommerce (WDR)
        $wdr_active = class_exists( 'Charrua_PB_WDR_Compatibility' ) && Charrua_PB_WDR_Compatibility::is_active();
        $wdr_installed = defined( 'WDR_VERSION' ) || $wdr_active;
        $wdr_version = defined( 'WDR_VERSION' ) ? WDR_VERSION : null;
        
        // YITH Dynamic Pricing
        $yith_active = class_exists( 'YWDPD_Frontend' );
        $yith_installed = defined( 'YWDPD_VERSION' ) || defined( 'YITH_YWDPD_VERSION' ) || $yith_active;
        $yith_version = null;
        if ( defined( 'YWDPD_VERSION' ) ) {
            $yith_version = YWDPD_VERSION;
        } elseif ( defined( 'YITH_YWDPD_VERSION' ) ) {
            $yith_version = YITH_YWDPD_VERSION;
        }
        
        return [
            'woocommerce' => [
                'installed' => true,
                'active'    => $wc_active,
                'version'   => $wc_version,
            ],
            'wdr' => [
                'installed' => $wdr_installed,
                'active'    => $wdr_active,
                'version'   => $wdr_version,
            ],
            'yith' => [
                'installed' => $yith_installed,
                'active'    => $yith_active,
                'version'   => $yith_version,
            ],
        ];
    }
    
    /**
     * Obtiene todos los precios para debug
     */
    public static function get_all_prices( $product ) {
        $normal_price = wc_get_price_to_display( $product );
        
        // WDR
        $wdr_active = class_exists( 'Charrua_PB_WDR_Compatibility' ) && Charrua_PB_WDR_Compatibility::is_active();
        $wdr_price = $wdr_active ? Charrua_PB_WDR_Compatibility::get_compatible_price( $product ) : null;
        $wdr_details = $wdr_active ? Charrua_PB_WDR_Compatibility::get_discount_details( $product ) : null;
        
        // YITH
        $yith_active = class_exists( 'YWDPD_Frontend' );
        $yith_price = ( $yith_active && class_exists( 'Charrua_PB_YITH_Compatibility' ) ) 
            ? Charrua_PB_YITH_Compatibility::get_compatible_price( $product ) 
            : null;
        
        // Precio final
        $final_price = Charrua_PB_Price_Utils::get_compatible_price( $product );
        
        // Determinar fuente
        $price_source = 'WooCommerce (sin descuentos)';
        if ( $wdr_active && $wdr_price && $wdr_price < $normal_price ) {
            $price_source = 'Discount Rules for WooCommerce';
        } elseif ( $yith_active && $yith_price && $yith_price < $normal_price ) {
            $price_source = 'YITH Dynamic Pricing';
        }
        
        return [
            'product_id'       => $product->get_id(),
            'product_type'     => $product->get_type(),
            'product_name'     => $product->get_name(),
            
            'wc_regular_price' => $product->get_regular_price(),
            'wc_sale_price'    => $product->get_sale_price(),
            'wc_price'         => $product->get_price(),
            'wc_display_price' => $normal_price,
            
            'wdr_active'       => $wdr_active,
            'wdr_price'        => $wdr_price,
            'wdr_has_discount' => $wdr_price && $wdr_price < $normal_price,
            'wdr_details'      => $wdr_details,
            
            'yith_active'      => $yith_active,
            'yith_price'       => $yith_price,
            'yith_has_discount'=> $yith_price && $yith_price < $normal_price,
            
            'final_price'      => $final_price,
            'price_source'     => $price_source,
        ];
    }
}
