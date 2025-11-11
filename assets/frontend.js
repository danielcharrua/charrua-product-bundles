/* Charrúa Product Bundles - Frontend JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar containers únicos (radio buttons)
    function handleUniqueSelection(container) {
        const labels = container.querySelectorAll('label');
        const noneOption = container.querySelector('.charrua-pb-none-option');
        
        labels.forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            
            if (radio) {
                // Manejar clics en el label para permitir deselección
                label.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir el comportamiento por defecto
                    
                    const groupName = radio.name;
                    const allRadiosInGroup = container.querySelectorAll(`input[name="${groupName}"]`);
                    
                    // Si este radio ya está seleccionado, deseleccionarlo
                    if (radio.checked) {
                        // Deseleccionar todos los radios del grupo
                        allRadiosInGroup.forEach(r => {
                            r.checked = false;
                            r.closest('label')?.classList.remove('charrua-pb-selected');
                        });
                        
                        // Seleccionar la opción "None" hidden si existe
                        if (noneOption) {
                            noneOption.checked = true;
                        }
                    } else {
                        // Deseleccionar todos los otros radios del grupo
                        allRadiosInGroup.forEach(r => {
                            r.checked = false;
                            r.closest('label')?.classList.remove('charrua-pb-selected');
                        });
                        
                        // Seleccionar este radio
                        radio.checked = true;
                        label.classList.add('charrua-pb-selected');
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
                // Manejar clics en el label para checkboxes
                label.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir el comportamiento por defecto
                    
                    // Toggle del checkbox
                    checkbox.checked = !checkbox.checked;
                    
                    // Actualizar clase visual
                    if (checkbox.checked) {
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
    
    // Para containers de lista, usar el mismo comportamiento que grid (selección/deselección)
    const listContainers = document.querySelectorAll('.charrua-pb-list-layout');
    
    listContainers.forEach(container => {
        const labels = container.querySelectorAll('label');
        const noneOption = container.querySelector('.charrua-pb-none-option');
        
        labels.forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            
            if (radio) {
                // Manejar clics en el label para permitir deselección (igual que grid)
                label.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir el comportamiento por defecto
                    
                    const groupName = radio.name;
                    const allRadiosInGroup = container.querySelectorAll(`input[name="${groupName}"]`);
                    
                    // Si este radio ya está seleccionado, deseleccionarlo
                    if (radio.checked) {
                        // Deseleccionar todos los radios del grupo
                        allRadiosInGroup.forEach(r => {
                            r.checked = false;
                            r.closest('label')?.classList.remove('charrua-pb-selected');
                        });
                        
                        // Seleccionar la opción "None" hidden si existe
                        if (noneOption) {
                            noneOption.checked = true;
                        }
                    } else {
                        // Deseleccionar todos los otros radios del grupo
                        allRadiosInGroup.forEach(r => {
                            r.checked = false;
                            r.closest('label')?.classList.remove('charrua-pb-selected');
                        });
                        
                        // Seleccionar este radio
                        radio.checked = true;
                        label.classList.add('charrua-pb-selected');
                    }
                    
                    // Actualizar calculador después del cambio
                    updateTotalCalculator();
                });
                
                // Inicializar estado
                if (radio.checked) {
                    label.classList.add('charrua-pb-selected');
                }
            }
        });
    });
    
    // Inicializar calculador de total
    initializeTotalCalculator();
});

// Función para inicializar el calculador de total
function initializeTotalCalculator() {
    const calculator = document.querySelector('.charrua-pb-total-calculator');
    if (!calculator) return;
    
    // Observar cambios en el campo de cantidad del producto
    const quantityInput = document.querySelector('input.qty, input[name="quantity"], .quantity input');
    if (quantityInput) {
        quantityInput.addEventListener('change', updateTotalCalculator);
        quantityInput.addEventListener('input', updateTotalCalculator);
    }
    
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
    
    // Obtener cantidad
    const quantityInput = document.querySelector('input.qty, input[name="quantity"], .quantity input');
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