# Charrua Product Bundles - Testing Checklist

Esta lista de verificación debe completarse cada vez que se actualiza el plugin para asegurar retrocompatibilidad y correcto funcionamiento.

## ✅ Testing Checklist

### 1. Compatibilidad con WooCommerce - Manejadores de Cantidad

#### 1.1 Manejadores Estándar de WooCommerce
- [ ] Verificar que el campo de cantidad estándar funciona correctamente
- [ ] Cambiar cantidad escribiendo directamente en el input
- [ ] Cambiar cantidad usando las flechas del input number (↑↓)
- [ ] Verificar que el total se actualiza en tiempo real (máximo 200ms de delay)

#### 1.2 Manejadores Personalizados (Kadence y otros temas)
- [ ] Verificar con tema Kadence (botones +/- personalizados)
- [ ] Usar botón "+" para incrementar cantidad
- [ ] Usar botón "-" para decrementar cantidad
- [ ] Verificar que el total se actualiza automáticamente
- [ ] Probar con otros temas populares si es posible (Astra, GeneratePress, etc.)

### 2. Productos con Selección Única (Unique)

#### 2.1 Layout Lista
- [ ] Crear grupo con `selection_type: unique` y `layout_type: list`
- [ ] Verificar que se muestran radio buttons (ocultos visualmente)
- [ ] Click en una opción la selecciona
- [ ] Click en la misma opción la deselecciona
- [ ] Seleccionar otra opción deselecciona la anterior automáticamente
- [ ] Verificar estilos visuales (clase `charrua-pb-selected`)
- [ ] Verificar que el precio del addon se suma al precio base

#### 2.2 Layout Grid
- [ ] Crear grupo con `selection_type: unique` y `layout_type: grid`
- [ ] Probar con 1, 2, 3 y 4 columnas
- [ ] Verificar que grid se visualiza correctamente
- [ ] Mismo comportamiento de selección que lista
- [ ] Verificar imágenes de productos se muestran correctamente
- [ ] Verificar enlaces a productos funcionan (no interfieren con selección)

### 3. Productos con Selección Múltiple (Multiple)

#### 3.1 Layout Lista
- [ ] Crear grupo con `selection_type: multiple` y `layout_type: list`
- [ ] Verificar que se muestran checkboxes
- [ ] Poder seleccionar varias opciones simultáneamente
- [ ] Poder deseleccionar cualquier opción
- [ ] Verificar que todos los precios seleccionados se suman correctamente

#### 3.2 Layout Grid
- [ ] Crear grupo con `selection_type: multiple` y `layout_type: grid`
- [ ] Probar con diferentes números de columnas
- [ ] Mismo comportamiento de selección múltiple
- [ ] Verificar diseño responsive en móvil/tablet

### 4. Cálculo de Total

#### 4.1 Selección Única (Unique)
- [ ] Producto base: $10, Addon: $5, Cantidad: 1 = Total: $15
- [ ] Cambiar cantidad a 2 = Total: $30
- [ ] Cambiar cantidad a 5 = Total: $75
- [ ] Deseleccionar addon = Total vuelve al precio base × cantidad
- [ ] Cambiar de addon A ($5) a addon B ($8) calcula correctamente
- [ ] Verificar formato de moneda se mantiene (símbolo, decimales)

#### 4.2 Selección Múltiple (Multiple)
- [ ] Producto base: $10, Sin addons, Cantidad: 1 = Total: $10
- [ ] Seleccionar Addon A ($5), Cantidad: 1 = Total: $15
- [ ] Seleccionar Addon B ($3), Cantidad: 1 = Total: $18
- [ ] Cambiar cantidad a 3 = Total: $54 (10+5+3)×3
- [ ] Deseleccionar Addon A = Total: $39 (10+3)×3
- [ ] Seleccionar/deseleccionar múltiples addons verifica cálculo correcto

#### 4.3 Múltiples Grupos
- [ ] Producto con 2 grupos de addons
- [ ] Grupo 1 (unique): seleccionar opción de $5
- [ ] Grupo 2 (multiple): seleccionar 2 opciones de $3 y $2
- [ ] Base: $10, Cantidad: 2 = Total: (10+5+3+2)×2 = $40
- [ ] Cambiar selecciones en ambos grupos y verificar cálculo

#### 4.4 Sincronización con Manejadores de Cantidad
- [ ] Con addon seleccionado, cambiar cantidad con manejadores personalizados
- [ ] Verificar que total se actualiza sin necesidad de reseleccionar addon
- [ ] Cambiar cantidad rápidamente (múltiples clicks) y verificar cálculo correcto
- [ ] Verificar que no hay "saltos" o valores incorrectos temporales

### 5. Retrocompatibilidad y Migraciones

#### 5.1 Grupos Existentes Sin selection_type
- [ ] Crear grupo en versión anterior (sin campo `selection_type`)
- [ ] Actualizar plugin
- [ ] Verificar que grupo funciona con comportamiento por defecto (unique)
- [ ] Verificar que no hay errores en consola
- [ ] Verificar que se puede editar y guardar el grupo

#### 5.2 Grupos Existentes Sin layout_type
- [ ] Crear grupo en versión anterior (sin campo `layout_type`)
- [ ] Actualizar plugin
- [ ] Verificar que grupo funciona con comportamiento por defecto (list)
- [ ] Verificar visualización correcta

#### 5.3 Grupos con Addons Eliminados
- [ ] Crear grupo con 3 addons
- [ ] Eliminar uno de los productos addon de WooCommerce
- [ ] Verificar que grupo muestra solo addons disponibles
- [ ] Verificar que no hay errores en frontend
- [ ] Verificar que se puede guardar el grupo sin problemas

#### 5.4 Productos con Categorías Cambiadas
- [ ] Crear grupo asignado a categoría A
- [ ] Crear producto en categoría A (debe mostrar el grupo)
- [ ] Cambiar producto a categoría B
- [ ] Verificar que grupo ya no aparece en el producto
- [ ] Volver a poner en categoría A
- [ ] Verificar que grupo vuelve a aparecer

### 6. Funcionalidad del Carrito

#### 6.1 Añadir al Carrito - Selección Única
- [ ] Seleccionar addon y añadir al carrito
- [ ] Verificar que producto base se añade
- [ ] Verificar que addon se añade como producto separado
- [ ] Verificar cantidades correctas en carrito
- [ ] Sin addon seleccionado, añadir al carrito (solo producto base)

#### 6.2 Añadir al Carrito - Selección Múltiple
- [ ] Seleccionar múltiples addons y añadir al carrito
- [ ] Verificar que todos los addons seleccionados se añaden
- [ ] Verificar cantidades correctas para cada item
- [ ] Sin addons seleccionados, añadir al carrito (solo producto base)

#### 6.3 Validaciones
- [ ] Verificar que nonce se valida correctamente
- [ ] Verificar que solo se pueden añadir addons definidos en el grupo
- [ ] Intentar manipular POST data con addons no válidos (debe rechazar)

### 7. Admin - Listado de Grupos

#### 7.1 Columnas Personalizadas
- [ ] Verificar columna "Status" muestra Active/Inactive correctamente
- [ ] Badge verde para activos, rojo para inactivos
- [ ] Verificar columna "Selection Type" muestra Single/Multiple
- [ ] Badge azul para Single, gris para Multiple
- [ ] Iconos dashicons se muestran correctamente

#### 7.2 Edición de Grupos
- [ ] Campo "Selection Type" muestra opciones correctas
- [ ] Texto de ayuda explica claramente la diferencia
- [ ] Campo "Layout Type" funciona correctamente
- [ ] Campo "Grid Columns" solo aparece cuando Layout Type = Grid
- [ ] Guardar y verificar que valores se almacenan correctamente

### 8. Tests de Rendimiento

#### 8.1 Polling de Cantidad
- [ ] Abrir consola y verificar logs cada 2 segundos
- [ ] Verificar que no hay lag en la página
- [ ] Con múltiples productos en página, verificar que solo se monitorea el correcto
- [ ] Verificar que no hay memory leaks (dejar página abierta 5 minutos)

#### 8.2 Grupos Múltiples
- [ ] Producto con 5+ grupos de addons
- [ ] Verificar que todos los grupos se renderizan correctamente
- [ ] Cambiar selecciones en diferentes grupos
- [ ] Verificar que cálculo de total sigue siendo instantáneo

### 9. Tests Cross-Browser

- [ ] Chrome (última versión)
- [ ] Firefox (última versión)
- [ ] Safari (última versión)
- [ ] Edge (última versión)
- [ ] Móvil Safari (iOS)
- [ ] Móvil Chrome (Android)

### 10. Tests Responsive

- [ ] Desktop (1920px+)
- [ ] Laptop (1366px)
- [ ] Tablet horizontal (1024px)
- [ ] Tablet vertical (768px)
- [ ] Móvil (375px)
- [ ] Grid columns se adaptan correctamente en cada breakpoint

---

## 📋 Registro de Tests

### Versión: __________ | Fecha: __________

**Testeador:** __________

**Resultados:**
- Tests Pasados: __ / __
- Tests Fallidos: __
- Bugs Encontrados: __

**Notas:**
```
(Anotar aquí cualquier observación, bug encontrado o comportamiento inesperado)
```

---

## 🐛 Template para Reportar Bugs

**Descripción del Bug:**


**Pasos para Reproducir:**
1.
2.
3.

**Comportamiento Esperado:**


**Comportamiento Actual:**


**Entorno:**
- Plugin Version:
- WordPress Version:
- WooCommerce Version:
- Theme:
- Browser:

**Screenshots/Logs:**
```
(Pegar aquí logs de consola o capturas de pantalla)
```
