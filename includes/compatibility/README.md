# Carpeta de Compatibilidades

Esta carpeta contiene todas las compatibilidades con otros plugins de terceros.

## Estructura

```
compatibility/
├── class-charrua-pb-compatibility-loader.php # Cargador automático de compatibilidades
├── class-charrua-pb-price-utils.php          # Utilidades generales para precios
├── class-charrua-pb-yith-compatibility.php   # Compatibilidad con YITH Dynamic Pricing
└── README.md                                 # Este archivo
```

## Cómo funciona

1. **Carga automática**: El archivo `class-charrua-pb-compatibility-loader.php` detecta automáticamente qué plugins están activos y carga solo las compatibilidades necesarias.

2. **API unificada**: `Charrua_PB_Price_Utils::get_compatible_price()` proporciona una interfaz única para obtener precios con todas las compatibilidades aplicadas.

3. **Sin impacto**: Si no hay plugins compatibles instalados, no se carga código innecesario.

## Compatibilidades actuales

- ✅ **YITH Dynamic Pricing & Discounts**: Aplica automáticamente descuentos de YITH a los precios de bundles

## Añadir nuevas compatibilidades

Para añadir compatibilidad con un nuevo plugin:

1. Crear archivo `class-charrua-pb-[nombre]-compatibility.php`
2. Añadir detección en `class-charrua-pb-compatibility-loader.php`
3. Actualizar `class-charrua-pb-price-utils.php` si es necesario
4. Documentar aquí

## Ejemplo de uso

```php
// En cualquier parte del plugin:
$precio_con_descuentos = Charrua_PB_Price_Utils::get_compatible_price( $producto );
```
