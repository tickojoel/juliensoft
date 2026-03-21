// Función para mostrar el modal de opciones del producto
function showProductOptions(productId, productName, isShoe = false) {
    // Crear el modal si no existe
    let modal = document.getElementById('product-options-modal');

    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'product-options-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold">Opciones de ${productName}</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div id="size-options">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Talla</label>
                        <div class="grid grid-cols-4 gap-2">
                            ${['S', 'M', 'L', 'XL', 'XXL'].map(size => `
                                <button type="button" 
                                    onclick="selectOption('size', '${size}')"
                                    class="option-btn size-option border rounded-md py-2 px-3 text-sm font-medium hover:bg-gray-50"
                                    data-value="${size}">
                                    ${size}
                                </button>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div id="color-options">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <div class="flex flex-wrap gap-2">
                            ${['Rojo', 'Blanco', 'Negro', 'Azul', 'Amarillo'].map(color => `
                                <button type="button"
                                    onclick="selectOption('color', '${color}')"
                                    class="option-btn color-option w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400"
                                    style="background-color: ${getColorCode(color)}"
                                    data-value="${color}"
                                    title="${color}">
                                </button>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div id="shoe-size-container" class="${isShoe ? '' : 'hidden'}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                        <div class="grid grid-cols-6 gap-2">
                            ${Array.from({ length: 10 }, (_, i) => 35 + i).map(size => `
                                <button type="button"
                                    onclick="selectOption('shoeSize', '${size}')"
                                    class="option-btn shoe-size-option border rounded-md py-2 px-1 text-sm font-medium hover:bg-gray-50"
                                    data-value="${size}">
                                    ${size}
                                </button>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                        <div class="flex items-center">
                            <button type="button" 
                                onclick="updateQuantity(-1)" 
                                class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-l">
                                -
                            </button>
                            <input type="number" 
                                id="quantity" 
                                value="1" 
                                min="1" 
                                class="w-16 text-center border-t border-b border-gray-300 py-1">
                            <button type="button" 
                                onclick="updateQuantity(1)" 
                                class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-r">
                                +
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t flex justify-end space-x-2">
                    <button type="button" 
                        onclick="hideProductOptions()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="button" 
                        id="add-to-cart-btn" 
                        onclick="addToCartWithOptions(${productId})" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50"
                        disabled>
                        Agregar al carrito
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    // Mostrar el modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    // Actualizar el botón de agregar al carrito
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.setAttribute('data-product-id', productId);
    }

    // Resetear selecciones
    resetSelections();
}

// Función para ocultar el modal
function hideProductOptions() {
    const modal = document.getElementById('product-options-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}

// Función para seleccionar una opción (talla, color, número)
function selectOption(type, value) {
    // Remover la clase de selección de todos los botones del mismo tipo
    document.querySelectorAll(`.${type}-option`).forEach(btn => {
        btn.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
    });

    // Agregar la clase de selección al botón clickeado
    const clickedBtn = document.querySelector(`.${type}-option[data-value="${value}"]`);
    if (clickedBtn) {
        clickedBtn.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
    }

    // Actualizar el estado del botón de agregar al carrito
    updateAddToCartButton();
}

// Función para actualizar la cantidad
function updateQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        let newValue = parseInt(quantityInput.value) + change;
        if (newValue < 1) newValue = 1;
        quantityInput.value = newValue;
    }
}

// Función para actualizar el estado del botón de agregar al carrito
function updateAddToCartButton() {
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    if (!addToCartBtn) return;

    const sizeSelected = document.querySelector('.size-option.ring-blue-500');
    const colorSelected = document.querySelector('.color-option.ring-blue-500');
    const shoeSizeContainer = document.getElementById('shoe-size-container');

    // Verificar si se han seleccionado las opciones requeridas
    const sizeOk = sizeSelected !== null;
    const colorOk = colorSelected !== null;
    const shoeSizeOk = !shoeSizeContainer || !shoeSizeContainer.classList.contains('hidden') ?
        document.querySelector('.shoe-size-option.ring-blue-500') !== null : true;

    // Habilitar/deshabilitar el botón según las selecciones
    addToCartBtn.disabled = !(sizeOk && colorOk && shoeSizeOk);
}

// Función para resetear las selecciones
function resetSelections() {
    // Remover selecciones
    document.querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
    });

    // Resetear cantidad
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.value = '1';
    }

    // Deshabilitar botón de agregar al carrito
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.disabled = true;
    }
}

// Función para obtener el código de color
function getColorCode(colorName) {
    const colors = {
        'Rojo': '#EF4444',
        'Blanco': '#FFFFFF',
        'Negro': '#000000',
        'Azul': '#3B82F6',
        'Amarillo': '#F59E0B'
    };
    return colors[colorName] || '#CCCCCC';
}

// Función para agregar al carrito con las opciones seleccionadas
function addToCartWithOptions(productId) {
    const sizeBtn = document.querySelector('.size-option.ring-blue-500');
    const colorBtn = document.querySelector('.color-option.ring-blue-500');
    const shoeSizeBtn = document.querySelector('.shoe-size-option.ring-blue-500');
    const quantity = document.getElementById('quantity')?.value || 1;

    if (!sizeBtn || !colorBtn) {
        showNotification('Por favor selecciona todas las opciones requeridas', 'error');
        return;
    }

    const options = {
        size: sizeBtn.getAttribute('data-value'),
        color: colorBtn.getAttribute('data-value')
    };

    if (shoeSizeBtn) {
        options.shoeSize = shoeSizeBtn.getAttribute('data-value');
    }

    // Cerrar el modal
    hideProductOptions();

    // Llamar a la función original de agregar al carrito
    addToCart(productId, parseInt(quantity), options);
}

// Función para manejar clics fuera del modal
document.addEventListener('click', function (event) {
    const modal = document.getElementById('product-options-modal');
    if (modal && !modal.contains(event.target) && !event.target.closest('[onclick*="showProductOptions"]')) {
        hideProductOptions();
    }
});

// Función para manejar la tecla Escape
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        hideProductOptions();
    }
});
