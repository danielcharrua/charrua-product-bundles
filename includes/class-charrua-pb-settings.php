<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Charrua_PB_Settings {
    
    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'init_settings' ] );
    }
    
    /**
     * Agregar página de configuración al menú de admin
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Product Bundles Settings', 'charrua-pb' ),
            __( 'Product Bundles', 'charrua-pb' ),
            'manage_woocommerce',
            'charrua-pb-settings',
            [ __CLASS__, 'settings_page' ]
        );
    }
    
    /**
     * Inicializar configuraciones
     */
    public static function init_settings() {
        register_setting(
            'charrua_pb_settings',
            'charrua_pb_settings',
            [ __CLASS__, 'sanitize_settings' ]
        );
        
        add_settings_section(
            'charrua_pb_display_section',
            __( 'Display Settings', 'charrua-pb' ),
            [ __CLASS__, 'display_section_callback' ],
            'charrua_pb_settings'
        );
        
        // Campos de configuración futuros pueden ir aquí
    }
    
    /**
     * Sanitizar configuraciones
     */
    public static function sanitize_settings( $input ) {
        $sanitized = array();
        
        // Configuraciones futuras pueden ir aquí
        
        return $sanitized;
    }
    
    /**
     * Callback para la sección de display
     */
    public static function display_section_callback() {
        echo '<p>' . __( 'Configure general settings for Product Bundles.', 'charrua-pb' ) . '</p>';
    }
    

    
    /**
     * Página de configuraciones
     */
    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'charrua_pb_settings' );
                do_settings_sections( 'charrua_pb_settings' );
                submit_button();
                ?>
            </form>
            
            <div class="charrua-pb-help-section" style="margin-top: 2rem; padding: 1rem; background: #f9f9f9; border-left: 4px solid #0073aa;">
                <h3><?php _e( 'How it works', 'charrua-pb' ); ?></h3>
                <ul>
                    <li><strong><?php _e( 'Bundle Groups:', 'charrua-pb' ); ?></strong> <?php _e( 'Created in Product Bundles → Add New. Apply to products by category or individual product selection.', 'charrua-pb' ); ?></li>
                    <li><strong><?php _e( 'Selection Types:', 'charrua-pb' ); ?></strong> <?php _e( 'Use "Unique" for single-choice add-ons (radio buttons), or "Multiple" to let customers build bundles (checkboxes).', 'charrua-pb' ); ?></li>
                    <li><strong><?php _e( 'Use Cases:', 'charrua-pb' ); ?></strong> <?php _e( 'Perfect for add-ons like warranties, protection, accessories, and increasing average order value.', 'charrua-pb' ); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
    

}