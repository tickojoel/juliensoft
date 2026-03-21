<?php
session_start();

// Redirigir si el carrito está vacío
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'classes/Product.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    $product = new Product($conn);
    
    // Obtener productos del carrito con información completa
    $cart_items = [];
    $total = 0;
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product_data = $product->getById($product_id);
        if ($product_data) {
            $product_data['quantity'] = $quantity;
            $product_data['subtotal'] = $product_data['price'] * $quantity;
            $total += $product_data['subtotal'];
            $cart_items[] = $product_data;
        }
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Tienda Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-accent {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        
        .price-highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }
        
        .form-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2);
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-gray-800">Tienda Requejo Fashion Lab</a>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-home"></i>
                    </a>
                    <a href="#" class="relative text-gray-600 hover:text-gray-800" onclick="history.back(); return false;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8">Finalizar Compra</h1>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Formulario de pago -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-6">Información de Pago</h2>
                        
                        <form id="payment-form" action="procesar_pago.php" method="POST">
                            <!-- Datos de contacto -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-700 mb-4">Datos de Contacto</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                        <input type="text" id="nombre" name="nombre" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                        <input type="email" id="email" name="email" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                        <input type="tel" id="telefono" name="telefono" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dirección de envío -->
                            <div class="mb-8">
                                <h3 class="text-lg font-medium text-gray-700 mb-4">Dirección de Envío</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                        <input type="text" id="direccion" name="direccion" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                                            <input type="text" id="ciudad" name="ciudad" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Región</label>
                                            <input type="text" id="region" name="region" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                                            <input type="text" id="codigo_postal" name="codigo_postal" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Método de pago -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-700 mb-4">Método de Pago</h3>
                                <div class="space-y-4">
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center">
                                            <input id="credit-card" name="payment_method" type="radio" value="credit_card" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300" checked>
                                            <label for="credit-card" class="ml-3 block text-sm font-medium text-gray-700">
                                                Tarjeta de Crédito/Débito
                                            </label>
                                        </div>
                                        <div id="credit-card-fields" class="mt-4 pl-7">
                                            <div class="space-y-4">
                                                <div>
                                                    <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1">Número de tarjeta</label>
                                                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-1">Vencimiento (MM/AA)</label>
                                                        <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/AA"
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                    </div>
                                                    <div>
                                                        <label for="card_cvc" class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                                        <input type="text" id="card_cvc" name="card_cvc" placeholder="123"
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="card_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre en la tarjeta</label>
                                                    <input type="text" id="card_name" name="card_name" placeholder="Como aparece en la tarjeta"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center">
                                            <input id="paypal" name="payment_method" type="radio" value="paypal" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                            <label for="paypal" class="ml-3 block text-sm font-medium text-gray-700">
                                                PayPal
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="terms" name="terms" type="checkbox" required
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="terms" class="ml-2 block text-sm text-gray-700">
                                    Acepto los <a href="#" class="text-green-600 hover:text-green-500">Términos y Condiciones</a> y la <a href="#" class="text-green-600 hover:text-green-500">Política de Privacidad</a>
                                </label>
                            </div>
                            
                            <div class="mt-8">
                                <button type="submit"
                                    class="w-full gradient-accent text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all duration-200 text-lg">
                                    <i class="fas fa-credit-card mr-2"></i>Pagar $<?php echo number_format($total, 2); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Resumen del pedido -->
                <div>
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                        <h2 class="text-xl font-semibold mb-6">Resumen del Pedido</h2>
                        
                        <div class="space-y-4 mb-6">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="flex justify-between items-start">
                                    <div class="flex">
                                        <div class="h-16 w-16 rounded-lg bg-gray-100 overflow-hidden mr-3">
                                           <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full bg-white object-contain sm:object-cover">
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <p class="text-sm text-gray-500">Cantidad: <?php echo $item['quantity']; ?></p>
                                        </div>
                                    </div>
                                    <span class="font-medium">$<?php echo number_format($item['subtotal'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Envío</span>
                                <span class="text-green-600">Gratis</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg pt-2">
                                <span>Total</span>
                                <span class="price-highlight">$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Tienda Requejo Fashion Lab</h3>
                    <p class="text-gray-400">Los mejores productos al mejor precio.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white">Inicio</a></li>
                        <li><a href="ofertas.php" class="text-gray-400 hover:text-white">Ofertas</a></li>
                        <li><a href="categorias.php" class="text-gray-400 hover:text-white">Categorías</a></li>
                        <li><a href="contacto.php" class="text-gray-400 hover:text-white">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Dirección: Calle 123, Ciudad</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>Teléfono: +123 456 789</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>Email: info@tiendaRequejo Fashion Lab.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Tienda Requejo Fashion Lab. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Validación del formulario
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            const cardNumber = document.getElementById('card_number').value.replace(/\s+/g, '');
            const cardExpiry = document.getElementById('card_expiry').value;
            const cardCvc = document.getElementById('card_cvc').value;
            const cardName = document.getElementById('card_name').value;
            
            // Validar número de tarjeta (solo si se seleccionó tarjeta)
            if (document.getElementById('credit-card').checked) {
                if (!/^\d{13,19}$/.test(cardNumber)) {
                    e.preventDefault();
                    alert('Por favor ingrese un número de tarjeta válido (13-19 dígitos)');
                    return false;
                }
                
                if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
                    e.preventDefault();
                    alert('Por favor ingrese una fecha de vencimiento válida (MM/AA)');
                    return false;
                }
                
                if (!/^\d{3,4}$/.test(cardCvc)) {
                    e.preventDefault();
                    alert('Por favor ingrese un código CVC válido (3-4 dígitos)');
                    return false;
                }
                
                if (cardName.trim() === '') {
                    e.preventDefault();
                    alert('Por favor ingrese el nombre que aparece en la tarjeta');
                    return false;
                }
            }
            
            // Si todo está bien, el formulario se envía
            return true;
        });
        
        // Formatear número de tarjeta
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            let formatted = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            
            e.target.value = formatted.trim();
        });
        
        // Formatear fecha de vencimiento
        document.getElementById('card_expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            e.target.value = value;
        });
        
        // Solo números en CVC
        document.getElementById('card_cvc').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>
