/* Charrúa Product Bundles - Frontend JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar containers únicos (radio buttons)
    function handleUniqueSelection(container) {
        const labels = container.querySelectorAll('label');
        const noneOption = container.querySelector('.charrua-pb-none-option');
        
        labels.forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            
            if (radio) {
                // Manejar clics en el label completo
                label.addEventListener('click', function(e) {
                    // Prevenir el comportamiento por defecto del label
                    e.preventDefault();
                    
                    const groupName = radio.name;
                    const allRadiosInGroup = container.querySelectorAll(`input[name="${groupName}"]`);
                    const wasChecked = radio.checked;
                    
                    // Deseleccionar todos los radios del grupo primero
                    allRadiosInGroup.forEach(r => {
                        r.checked = false;
                        r.closest('label')?.classList.remove('charrua-pb-selected');
                    });
                    
                    // Si NO estaba seleccionado, seleccionarlo
                    if (!wasChecked) {
                        radio.checked = true;
                        label.classList.add('charrua-pb-selected');
                    } else {
                        // Si estaba seleccionado, lo dejamos deseleccionado
                        // y marcamos la opción "None" si existe
                        if (noneOption) {
                            noneOption.checked = true;
                        }
                    }
                    
                    // Actualizar calculador después del cambio
                    updateTotalCalculator();
                });
                
                // Inicializar el estado visual para radios ya seleccionados
                if (radio.checked) {
                    label.classList.add('charrua-pb-selected');
                }
            }
        });
    }

    // Función para manejar containers múltiples (checkboxes)
    function handleMultipleSelection(container) {
        const labels = container.querySelectorAll('label');
        
        labels.forEach(label => {
            const checkbox = label.querySelector('input[type="checkbox"]');
            
            if (checkbox) {
                // Manejar cambios en el checkbox
                checkbox.addEventListener('change', function(e) {
                    // Actualizar clase visual
                    if (this.checked) {
                        label.classList.add('charrua-pb-selected');
                    } else {
                        label.classList.remove('charrua-pb-selected');
                    }
                    
                    // Actualizar calculador después del cambio
                    updateTotalCalculator();
                });
                
                // Inicializar el estado visual para checkboxes ya seleccionados
                if (checkbox.checked) {
                    label.classList.add('charrua-pb-selected');
                }
            }
        });
    }

    // Manejar containers de selección única (radio buttons)
    const uniqueContainers = document.querySelectorAll('.charrua-pb-unique-selection');
    uniqueContainers.forEach(handleUniqueSelection);

    // Manejar containers de selección múltiple (checkboxes)
    const multipleContainers = document.querySelectorAll('.charrua-pb-multiple-selection');
    multipleContainers.forEach(handleMultipleSelection);
    
    // Inicializar calculador de total
    initializeTotalCalculator();
});

// Variable global para guardar referencia al input de cantidad
let cachedQuantityInput = null;

// Función para inicializar el calculador de total
function initializeTotalCalculator() {
    const calculator = document.querySelector('.charrua-pb-total-calculator');
    if (!calculator) {
        return;
    }
    
    // Guardar el último valor conocido
    let lastValue = null;
    
    // Polling directo del valor del campo cada 200ms (más frecuente)
    setInterval(function() {
        // Buscar el campo de cantidad en cada iteración (por si cambia)
        const quantityInput = document.querySelector('input.qty, input[name="quantity"], .quantity input[type="number"]');
        
        if (!quantityInput) {
            return;
        }
        
        // Guardar la referencia al input encontrado
        cachedQuantityInput = quantityInput;
        
        const currentValue = quantityInput.value;
        
        if (currentValue !== lastValue) {
            lastValue = currentValue;
            updateTotalCalculator();
        }
    }, 200);
    
    // Calcular total inicial
    updateTotalCalculator();
}

// Función para actualizar el calculador de total
function updateTotalCalculator() {
    const calculator = document.querySelector('.charrua-pb-total-calculator');
    if (!calculator) return;
    
    // Obtener precio base
    const basePriceElement = calculator.querySelector('[data-base-price]');
    const basePrice = parseFloat(basePriceElement?.dataset.basePrice || '0');
    
    // Calcular total de addons
    let addonsTotal = 0;
    const allInputs = document.querySelectorAll('input[name^="charrua_pb_group_"]:checked');
    
    allInputs.forEach(input => {
        const priceElement = input.closest('label')?.querySelector('.charrua-pb-price');
        if (priceElement && priceElement.dataset.price) {
            const price = parseFloat(priceElement.dataset.price);
            if (!isNaN(price)) {
                addonsTotal += price;
            }
        }
    });
    
    // Obtener cantidad - usar el input cacheado si está disponible
    let quantityInput = cachedQuantityInput;
    if (!quantityInput) {
        quantityInput = document.querySelector('input.qty, input[name="quantity"], .quantity input[type="number"]');
    }
    
    const quantity = parseInt(quantityInput?.value || '1') || 1;
    
    // Calcular total final
    const finalTotal = (basePrice + addonsTotal) * quantity;
    
    // Actualizar precio mostrado
    const priceElement = calculator.querySelector('.price');
    if (priceElement) {
        updatePriceDisplay(priceElement, finalTotal);
    }
}

// Función para formatear y mostrar precios
function updatePriceDisplay(element, amount) {
    if (!element) return;
    
    // Obtener el formato del precio original
    const originalHTML = element.innerHTML;
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = originalHTML;
    
    // Buscar símbolos de moneda y formato
    const currencyMatch = originalHTML.match(/([^\d.,\s]+)/);
    const currency = currencyMatch ? currencyMatch[1] : '$';
    
    // Formatear el nuevo precio manteniendo el formato original
    const formattedAmount = amount.toFixed(2);
    
    // Reemplazar manteniendo la estructura HTML si existe
    if (originalHTML.includes('<')) {
        // Mantener la estructura HTML y solo cambiar el número
        const newHTML = originalHTML.replace(/[\d.,]+/, formattedAmount);
        element.innerHTML = newHTML;
    } else {
        // Formato simple de texto
        element.textContent = currency + formattedAmount;
    }
}