# Carpeta de Compatibilidades

Esta carpeta contiene todas las compatibilidades con otros plugins de terceros.

## Estructura

```
compatibility/
├── class-charrua-pb-compatibility-loader.php # Cargador automático de compatibilidades
├── class-charrua-pb-debug.php                # Herramientas de debug de precios
├── class-charrua-pb-price-utils.php          # Utilidades generales para precios
├── class-charrua-pb-wdr-compatibility.php    # Compatibilidad con Discount Rules for WooCommerce
├── class-charrua-pb-yith-compatibility.php   # Compatibilidad con YITH Dynamic Pricing
└── README.md                                 # Este archivo
```

## Cómo funciona

### Arquitectura del sistema

```
Frontend (class-charrua-pb-frontend.php)
   ↓
Charrua_PB_Price_Utils::get_compatible_price()  ← API centralizada
   ↓
   ├─→ Si WDR activo → Charrua_PB_WDR_Compatibility::get_compatible_price()
   ├─→ Si YITH activo → Charrua_PB_YITH_Compatibility::get_compatible_price()
   ├─→ Si Subscriptions activo → (futuro)
   └─→ Fallback → wc_get_price_to_display()
```

### Principios clave

1. **Carga automática**: El archivo `class-charrua-pb-compatibility-loader.php` carga todas las clases de compatibilidad al inicio. Cada clase verifica internamente si su plugin está activo.

2. **API unificada**: `Charrua_PB_Price_Utils::get_compatible_price()` es el único punto de entrada. Aplica las compatibilidades en orden de prioridad y devuelve el precio correcto.

3. **Detección en runtime**: Las compatibilidades se detectan cuando se llaman los métodos, no cuando se carga el plugin. Esto asegura que siempre funcionan correctamente.

4. **Sin acoplamiento**: El frontend solo conoce `Charrua_PB_Price_Utils`, no las clases de compatibilidad específicas. Esto facilita añadir nuevas compatibilidades sin modificar el frontend.

## Compatibilidades actuales

- **Discount Rules for WooCommerce (by Flycart)**: Aplica descuentos dinámicos basados en reglas (por cantidad, porcentaje, precio fijo, BOGO, etc.)
- **YITH Dynamic Pricing & Discounts**: Aplica automáticamente descuentos de YITH a los precios de bundles

### Orden de prioridad

1. **Discount Rules for WooCommerce** - Se verifica primero por ser muy popular y tener reglas complejas
2. **YITH Dynamic Pricing** - Se verifica segundo
3. **WooCommerce (fallback)** - Precio normal si ningún plugin de descuentos está activo

## Añadir nuevas compatibilidades

Para añadir compatibilidad con un nuevo plugin:

1. Crear archivo `class-charrua-pb-[nombre]-compatibility.php`
2. Añadir detección en `class-charrua-pb-compatibility-loader.php`
3. Actualizar `class-charrua-pb-price-utils.php` si es necesario
4. Documentar aquí

## Ejemplo de uso

```php
// CORRECTO - Usar siempre la API centralizada
$precio_con_descuentos = Charrua_PB_Price_Utils::get_compatible_price( $producto );

// INCORRECTO - No llamar directamente a las compatibilidades específicas
$precio = Charrua_PB_YITH_Compatibility::get_compatible_price( $producto );
```

### ¿Por qué usar la API centralizada?

**Ventajas:**
- Un solo punto de entrada para obtener precios
- Fácil añadir nuevas compatibilidades sin tocar el frontend
- Prioridades claras y configurables
- Código más mantenible y escalable
- Evita duplicación y errores

**Si añades una nueva compatibilidad**, solo necesitas:
1. Crear `class-charrua-pb-[nombre]-compatibility.php`
2. Cargarla en `class-charrua-pb-compatibility-loader.php`
3. Añadir la lógica en `Charrua_PB_Price_Utils::get_compatible_price()`

El resto del código (frontend, cart, etc.) **no necesita cambiar**.

## Debug de Precios

El plugin incluye un panel de debug visual para diagnosticar problemas de precios y compatibilidades.

### Activar/Desactivar

En `class-charrua-pb-debug.php`, línea 22:

```php
const DEBUG_ENABLED = true;  // Cambiar a false para desactivar
```

### Qué muestra el panel

El panel aparece en la página de producto singular (solo para administradores):

| Sección | Información |
|---------|-------------|
| **Producto** | ID, tipo, nombre |
| **Plugins Detectados** | Estado de WooCommerce, WDR y YITH (versión, activo/inactivo) |
| **Precios WooCommerce** | Regular Price, Sale Price, get_price(), wc_get_price_to_display() |
| **Discount Rules (WDR)** | Si está activo, precio con descuento aplicado |
| **YITH Dynamic Pricing** | Si está activo, precio con descuento aplicado |
| **Resultado Final** | Precio final usado y fuente del precio |

### Alternativas para activar

También puedes activar el debug de estas formas:

```php
// En wp-config.php
define( 'CHARRUA_PB_DEBUG_PRICES', true );

// O con un filtro en functions.php
add_filter( 'charrua_pb_debug_prices', '__return_true' );
```

### Seguridad

- Solo visible para usuarios con capacidad `manage_options` (administradores)
- Se desactiva fácilmente cambiando la constante
- No afecta el rendimiento cuando está desactivado
