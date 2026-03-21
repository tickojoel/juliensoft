<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Product.php';
require_once 'functions/helpers.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inicializar conexión a la base de datos
$database = new Database();
$conn = $database->getConnection();
$product = new Product($conn);

// Obtener productos del carrito (en un caso real, esto vendría de la base de datos o sesión)
$cart_items = [
    [
        'id' => 1,
        'name' => 'Zapatillas Running Pro',
        'price' => 129.99,
        'original_price' => 149.99,
        'image' => 'images/products/zapatilla-running.jpg',
        'quantity' => 1,
        'in_stock' => true,
        'max_quantity' => 5
    ],
    [
        'id' => 2,
        'name' => 'Camiseta Deportiva',
        'price' => 39.99,
        'original_price' => 49.99,
        'image' => 'images/products/camiseta.jpg',
        'quantity' => 2,
        'in_stock' => true,
        'max_quantity' => 10
    ]
];

// Calcular totales
$subtotal = array_reduce($cart_items, function($carry, $item) {
    return $carry + ($item['price'] * $item['quantity']);
}, 0);

$shipping = $subtotal > 0 ? 15.00 : 0; // Costo de envío fijo
$total = $subtotal + $shipping;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
        }
        .discount-badge {
            background: var(--secondary-gradient);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .quantity-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            cursor: pointer;
            user-select: none;
        }
        .quantity-btn:hover {
            background-color: #f3f4f6;
        }
        .quantity-input {
            width: 40px;
            text-align: center;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            border-left: none;
            border-right: none;
            padding: 0;
            -moz-appearance: textfield;
        }
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Carrito de Compras</h1>
            
            <?php if (empty($cart_items)): ?>
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-shopping-cart text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Tu carrito está vacío</h3>
                    <p class="text-gray-500 mb-6">Añade algunos productos a tu carrito antes de continuar.</p>
                    <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Continuar comprando
                    </a>
                </div>
            <?php else: ?>
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Lista de productos -->
                    <div class="lg:w-2/3">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <!-- Encabezado de la tabla -->
                            <div class="hidden md:grid grid-cols-12 bg-gray-50 border-b border-gray-200 px-6 py-3 text-sm font-medium text-gray-500 uppercase tracking-wider">
                                <div class="col-span-6">Producto</div>
                                <div class="col-span-2 text-center">Precio</div>
                                <div class="col-span-2 text-center">Cantidad</div>
                                <div class="col-span-2 text-right">Total</div>
                            </div>
                            
                            <!-- Lista de productos -->
                            <div class="divide-y divide-gray-200">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="p-4 md:p-6">
                                        <div class="flex flex-col md:flex-row md:items-center">
                                            <!-- Imagen del producto -->
                                            <div class="w-full md:w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden mb-4 md:mb-0 md:mr-6">
                                                <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full bg-white object-contain sm:object-cover">
                                            </div>
                                            
                                            <!-- Detalles del producto -->
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h3 class="text-md font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                                                        <?php if ($item['original_price'] > $item['price']): ?>
                                                            <span class="inline-block mt-1">
                                                                <span class="text-sm text-gray-500 line-through mr-2">S/<?php echo number_format($item['original_price'], 2); ?></span>
                                                                <span class="discount-badge text-xs">
                                                                    <?php echo round((($item['original_price'] - $item['price']) / $item['original_price']) * 100); ?>% OFF
                                                                </span>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <button class="text-gray-400 hover:text-red-500 transition-colors">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Precio móvil -->
                                                <div class="md:hidden mt-2">
                                                    <div class="text-lg font-bold text-gray-900">S/<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                                    <div class="text-sm text-gray-500">S/<?php echo number_format($item['price'], 2); ?> c/u</div>
                                                </div>
                                                
                                                <div class="mt-4 flex items-center justify-between md:justify-start">
                                                    <!-- Selector de cantidad -->
                                                    <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                                                        <button class="quantity-btn decrease" data-id="<?php echo $item['id']; ?>">-</button>
                                                        <input type="number" 
                                                               value="<?php echo $item['quantity']; ?>" 
                                                               min="1" 
                                                               max="<?php echo $item['max_quantity']; ?>" 
                                                               class="quantity-input"
                                                               data-id="<?php echo $item['id']; ?>"
                                                               data-price="<?php echo $item['price']; ?>">
                                                        <button class="quantity-btn increase" data-id="<?php echo $item['id']; ?>">+</button>
                                                    </div>
                                                    
                                                    <!-- Precio escritorio -->
                                                    <div class="hidden md:block text-right md:ml-12">
                                                        <div class="text-lg font-bold text-gray-900">S/<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <?php if (!$item['in_stock']): ?>
                                                    <div class="mt-2 text-sm text-red-600">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> Producto agotado
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Acciones del carrito -->
                            <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-between items-center border-t border-gray-200">
                                <a href="index.php" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-3 sm:mb-0">
                                    <i class="fas fa-arrow-left mr-2"></i> Seguir comprando
                                </a>
                                <div class="space-x-3">
                                    <button class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                        <i class="fas fa-trash-alt mr-1"></i> Vaciar carrito
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="fas fa-sync-alt mr-1"></i> Actualizar carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Código de descuento -->
                        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Código de descuento</h3>
                            <div class="flex">
                                <input type="text" placeholder="Ingresa el código" class="flex-1 border border-gray-300 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-r-md font-medium transition-colors">
                                    Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen del pedido -->
                    <div class="lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Resumen del pedido</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">S/<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Envío</span>
                                    <span class="font-medium"><?php echo $shipping > 0 ? 'S/' . number_format($shipping, 2) : 'Gratis'; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Descuento</span>
                                    <span class="font-medium text-green-600">-S/0.00</span>
                                </div>
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span>S/<?php echo number_format($total, 2); ?></span>
                                    </div>
                                </div>
                                <div class="pt-2">
                                    <p class="text-sm text-gray-500 mb-4">Los impuestos se calculan al finalizar la compra.</p>
                                    <a href="checkout.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-md font-medium transition-colors">
                                        Proceder al pago
                                    </a>
                                    <p class="text-xs text-gray-500 mt-2 text-center">
                                        <i class="fas fa-lock mr-1"></i> Pago seguro con encriptación SSL
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Métodos de pago aceptados</h4>
                                <div class="flex space-x-2">
                                    <div class="w-12 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                                        <i class="fab fa-cc-visa text-blue-800 text-xl"></i>
                                    </div>
                                    <div class="w-12 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                                        <i class="fab fa-cc-mastercard text-red-600 text-xl"></i>
                                    </div>
                                    <div class="w-12 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                                        <i class="fab fa-cc-amex text-blue-500 text-xl"></i>
                                    </div>
                                    <div class="w-12 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                                        <i class="fab fa-cc-paypal text-blue-700 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seguridad y garantías -->
                        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 text-green-500">
                                    <i class="fas fa-shield-alt text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Compra segura</h4>
                                    <p class="text-sm text-gray-500 mt-1">Tus datos están protegidos con encriptación de 256-bit.</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-start space-x-4">
                                <div class="flex-shrink-0 text-blue-500">
                                    <i class="fas fa-undo-alt text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Devolución fácil</h4>
                                    <p class="text-sm text-gray-500 mt-1">30 días para devoluciones o cambios.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funcionalidad para aumentar/disminuir cantidad
            document.querySelectorAll('.quantity-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.quantity-input');
                    const currentValue = parseInt(input.value);
                    const max = parseInt(input.getAttribute('max'));
                    const min = parseInt(input.getAttribute('min'));
                    
                    if (this.classList.contains('increase') && currentValue < max) {
                        input.value = currentValue + 1;
                        updateCartItem(input);
                    } else if (this.classList.contains('decrease') && currentValue > min) {
                        input.value = currentValue - 1;
                        updateCartItem(input);
                    }
                });
            });
            
            // Actualizar cuando se cambia manualmente el valor
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const max = parseInt(this.getAttribute('max'));
                    const min = parseInt(this.getAttribute('min'));
                    let value = parseInt(this.value);
                    
                    if (isNaN(value) || value < min) value = min;
                    if (value > max) value = max;
                    
                    this.value = value;
                    updateCartItem(this);
                });
            });
            
            function updateCartItem(input) {
                const id = input.getAttribute('data-id');
                const quantity = parseInt(input.value);
                const price = parseFloat(input.getAttribute('data-price'));
                const total = (quantity * price).toFixed(2);
                
                // Aquí iría la llamada AJAX para actualizar el carrito en el servidor
                console.log(`Actualizando producto ${id} a cantidad ${quantity}`);
                
                // Actualizar el total en la interfaz
                const totalElement = input.closest('.flex').querySelector('.text-lg');
                if (totalElement) {
                    totalElement.textContent = `S/${total}`;
                }
                
                // Recalcular totales (en un caso real, esto se haría con los datos actualizados del servidor)
                updateOrderSummary();
            }
            
            function updateOrderSummary() {
                // En un caso real, esto se haría con los datos actualizados del servidor
                console.log('Actualizando resumen del pedido...');
            }
            
            // Vaciar carrito
            const emptyCartBtn = document.querySelector('button:contains("Vaciar carrito")');
            if (emptyCartBtn) {
                emptyCartBtn.addEventListener('click', function() {
                    if (confirm('¿Estás seguro de que quieres vaciar tu carrito?')) {
                        // Aquí iría la llamada AJAX para vaciar el carrito
                        console.log('Carrito vaciado');
                        // Recargar la página o actualizar la interfaz
                        window.location.reload();
                    }
                });
            }
        });
    </script>
</body>
</html>
