<?php
session_start();
require_once 'classes/Product.php';
require_once 'config/database.php';

// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'beautystore';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $product = new Product($db);

    // Obtener solo productos con descuento
    $ofertas = $product->getAll('all', '');
    $ofertas = array_filter($ofertas, function ($producto) {
        return $producto['discount'] > 0;
    });

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos en Oferta - Tienda Requejo Fashion Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .cart-animation {
            animation: cartPulse 0.6s ease-out;
        }

        @keyframes cartPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .cart-bounce {
            animation: cartBounce 0.5s ease-in-out;
        }

        @keyframes cartBounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .notification-enter {
            animation: slideIn 0.3s ease-out;
        }

        .notification-exit {
            animation: slideOut 0.3s ease-in;
        }

        /* Animación de los botones flotantes */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        .floating-btn {
            animation: pulse 2s infinite;
        }

        .floating-btn:hover {
            animation: none;
        }

        .cart-float {
            animation: pulse 2s infinite;
            animation-delay: 0.5s;
            /* Desfase para que no estén sincronizados */
        }

        .cart-float:hover {
            animation: none;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(100%);
            }
        }

        .price-highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        /* Estilos para móviles */
        @media (max-width: 640px) {
            .product-card {
                padding: 0.75rem;
            }

            .product-image {
                height: 150px;
            }

            .product-title {
                font-size: 1rem;
            }

            .cart-modal {
                width: 95%;
                max-height: 80vh;
            }
        }

        /* Estilos para tablets */
        @media (min-width: 641px) and (max-width: 1024px) {
            .product-image {
                height: 200px;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Header/Navbar Responsivo -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <!-- Logo y botón móvil -->
                <div class="flex justify-between items-center w-full md:w-auto">
                    <h1 class="text-xl md:text-2xl font-bold text-purple-600">Requejo Fashion Lab</h1>
                    <button id="mobile-menu-button" class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

                <!-- Menú de navegación -->
                <nav id="mobile-menu" class="hidden md:flex w-full md:w-auto mt-4 md:mt-0">
                    <ul
                        class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6 w-full md:w-auto items-center justify-center text-center">
                        <li><a href="index.php"
                                class="text-gray-600 hover:text-purple-600 transition-colors block py-2 px-4">Inicio</a>
                        </li>
                        <li><a href="ofertas.php" class="text-purple-600 font-medium block py-2 px-4">Ofertas</a></li>
                        <li><a href="categorias.php"
                                class="text-gray-600 hover:text-purple-600 transition-colors block py-2 px-4">Categorías</a>
                        </li>
                        <li><a href="contacto.php"
                                class="text-gray-600 hover:text-purple-600 transition-colors block py-2 px-4">Contactarnos</a>
                        </li>
                    </ul>
                </nav>

                <!-- Iconos de usuario y carrito -->
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <button onclick="toggleCart()"
                        class="fixed bottom-24 right-6 z-40 bg-blue-500 hover:bg-blue-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 cart-float"
                        style="box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count"
                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    </button>
                    <a href="admin/" class="text-gray-600 hover:text-purple-600"><i class="fas fa-user"></i></a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Productos en Oferta</h1>

        <?php if (empty($ofertas)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">Actualmente no hay productos en oferta.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                <?php foreach ($ofertas as $producto): ?>
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 relative product-card">
                        <!-- Badge de descuento -->
                        <?php if ($producto['discount'] > 0): ?>
                            <div
                                class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg rounded-tr-lg z-10">
                                -<?= $producto['discount'] ?>%
                            </div>
                        <?php endif; ?>

                        <!-- Imagen del producto -->
                        <div class="w-full bg-gray-100 flex items-center justify-center overflow-hidden"
                            style="aspect-ratio: 1;">
                            <img src="<?= htmlspecialchars($producto['image']) ?>"
                                alt="<?= htmlspecialchars($producto['name']) ?>"
                                class="w-full h-full object-contain transition-transform duration-500 hover:scale-105">
                        </div>

                        <!-- Detalles del producto -->
                        <div class="p-3 md:p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-base md:text-lg product-title">
                                    <?= htmlspecialchars($producto['name']) ?>
                                </h3>
                                <?php if ($producto['is_new']): ?>
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Nuevo</span>
                                <?php endif; ?>
                            </div>

                            <p class="text-gray-600 text-xs md:text-sm mb-2"><?= htmlspecialchars($producto['brand']) ?></p>

                            <div class="flex items-center mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $producto['rating']): ?>
                                        <i class="fas fa-star text-yellow-400 text-xs md:text-sm"></i>
                                    <?php else: ?>
                                        <i class="far fa-star text-yellow-400 text-xs md:text-sm"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span class="text-gray-500 text-xs md:text-sm ml-1">(<?= $producto['reviews_count'] ?>)</span>
                            </div>

                            <div class="flex items-center justify-between mt-3">
                                <?php if ($producto['discount'] > 0): ?>
                                    <div>
                                        <span
                                            class="text-gray-400 line-through text-xs md:text-sm mr-1">$<?= number_format($producto['price'], 2) ?></span>
                                        <span
                                            class="text-red-500 font-bold text-sm md:text-base">$<?= number_format($producto['price'] * (1 - $producto['discount'] / 100), 2) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span
                                        class="text-gray-800 font-bold text-sm md:text-base">$<?= number_format($producto['price'], 2) ?></span>
                                <?php endif; ?>

                                <button onclick="addToCart(<?php echo $producto['id']; ?>)"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition-colors">
                                    <i class="fas fa-cart-plus"></i> <span class="hidden sm:inline">Añadir</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/+56977106814?text=Hola,%20estoy%20interesado%20en%20realizar%20una%20compra" target="_blank"
        class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 floating-btn"
        style="box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);">
        <i class="fab fa-whatsapp text-2xl"></i>
        <span class="sr-only">Chat de WhatsApp</span>
    </a>

    <!-- Modal del Carrito Responsivo -->
    <div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
            <div
                class="bg-white rounded-xl md:rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col cart-modal">
                <div
                    class="flex items-center justify-between p-4 md:p-6 border-b bg-gradient-to-r from-purple-600 to-purple-500 text-white">
                    <h3 class="text-lg md:text-xl font-bold">Carrito</h3>
                    <button onclick="toggleCart()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                </div>
                <div id="cart-items" class="p-4 md:p-6 overflow-y-auto flex-1">
                    <!-- Los items del carrito se cargarán aquí dinámicamente -->
                </div>
                <div class="border-t p-4 md:p-6 bg-gray-50">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <span class="text-base md:text-lg font-semibold">Total:</span>
                        <span id="cart-total" class="text-xl md:text-2xl font-bold price-highlight">$0</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="toggleCart()"
                            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 md:py-4 rounded-xl font-bold hover:shadow-lg transition-all duration-200 text-sm md:text-base flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </button>
                        <button id="checkout-button" onclick="proceedToCheckout()"
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-500 text-white py-3 md:py-4 rounded-xl font-bold hover:shadow-lg transition-all duration-200 text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                            <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>>
                            <i class="fas fa-credit-card mr-2"></i>Pagar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Compacto -->
    <footer class="bg-gray-800 text-white pt-8 pb-4">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                <!-- Columna 1: Logo y redes -->
                <div class="col-span-2 md:col-span-1">
                    <h3 class="text-lg font-bold mb-2">Requejo Fashion Lab</h3>
                    <p class="text-gray-300 text-xs mb-3">Tendencias en moda y accesorios con estilo único.</p>
                    <div class="flex space-x-3">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-pink-500 transition-colors" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Columna 2: Enlaces rápidos -->
                <div>
                    <h4 class="font-bold text-sm mb-3 pb-1 border-b border-gray-700">Explorar</h4>
                    <ul class="space-y-1 text-sm">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="ofertas.php" class="text-gray-400 hover:text-white transition-colors">Ofertas</a>
                        </li>
                        <li><a href="categorias.php"
                                class="text-gray-400 hover:text-white transition-colors">Categorías</a></li>
                        <li><a href="contacto.php" class="text-gray-400 hover:text-white transition-colors">Contacto</a>
                        </li>
                    </ul>
                </div>

                <!-- Columna 3: Mi Cuenta -->
                <div>
                    <h4 class="font-bold text-sm mb-3 pb-1 border-b border-gray-700">Mi Cuenta</h4>
                    <ul class="space-y-1 text-sm">
                        <li><a href="perfil.php" class="text-gray-400 hover:text-white transition-colors">Mi Perfil</a>
                        </li>
                        <li><a href="pedidos.php" class="text-gray-400 hover:text-white transition-colors">Mis
                                Pedidos</a></li>
                        <li><a href="lista-deseos.php" class="text-gray-400 hover:text-white transition-colors">Lista de
                                Deseos</a></li>
                        <li><a href="carrito.php" class="text-gray-400 hover:text-white transition-colors">Carrito</a>
                        </li>
                    </ul>
                </div>

                <!-- Columna 4: Contacto -->
                <div class="col-span-2 md:col-span-1">
                    <h4 class="font-bold text-sm mb-3 pb-1 border-b border-gray-700">Contacto</h4>
                    <ul class="space-y-1 text-xs">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400"></i>
                            <span class="text-gray-300">Av. Principal 1234, Santiago</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2 text-gray-400"></i>
                            <a href="tel:+56912345678" class="text-gray-300 hover:text-white">+56 9 1234 5678</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>
                            <a href="mailto:contacto@requejofashionlab.cl"
                                class="text-gray-300 hover:text-white">contacto@requejofashionlab.cl</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter Compacto -->
            <div class="bg-gray-700 rounded-lg p-4 mb-4">
                <div class="text-center">
                    <h4 class="text-sm font-bold mb-2">¡Suscríbete a nuestro boletín!</h4>
                    <form class="flex gap-2 max-w-md mx-auto">
                        <input type="email" placeholder="Tu email"
                            class="flex-grow px-3 py-2 rounded text-sm focus:outline-none focus:ring-1 focus:ring-pink-500 text-gray-800"
                            required>
                        <button type="submit"
                            class="bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                            OK
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-700 pt-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-xs mb-2 md:mb-0">&copy; <?= date('Y') ?> Requejo Fashion Lab</p>
                    <div class="flex flex-wrap justify-center gap-2 text-xs">
                        <a href="#" class="text-gray-400 hover:text-white">Términos</a>
                        <span class="text-gray-600">•</span>
                        <a href="#" class="text-gray-400 hover:text-white">Privacidad</a>
                        <span class="text-gray-600">•</span>
                        <a href="#" class="text-gray-400 hover:text-white">Envíos</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Variables globales
        let cart = <?php echo json_encode($_SESSION['cart']); ?>;

        // Función para alternar el modal del carrito
        function toggleCart() {
            const modal = document.getElementById('cart-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                updateCartDisplay();
            }
        }

        // Función para agregar productos al carrito
        function addToCart(productId) {
            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cart = data.cart;
                        updateCartCount();
                        showCartAnimation();
                        showNotification('Producto agregado al carrito', 'success');
                    } else {
                        showNotification(data.message || 'Error al agregar producto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error de conexión', 'error');
                });
        }

        // Función para actualizar cantidad de un producto
        function updateQuantity(productId, change) {
            fetch('ajax/update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    change: change
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cart = data.cart;
                        updateCartCount();
                        updateCartDisplay();
                        showNotification('Cantidad actualizada', 'success');
                    } else {
                        showNotification(data.message || 'Error al actualizar cantidad', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error de conexión', 'error');
                });
        }

        // Función para eliminar producto del carrito
        function removeFromCart(productId) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
                fetch('ajax/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cart = data.cart;
                            updateCartCount();
                            updateCartDisplay();
                            showNotification('Producto eliminado del carrito', 'success');
                        } else {
                            showNotification(data.message || 'Error al eliminar producto', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error de conexión', 'error');
                    });
            }
        }

        // Función para vaciar el carrito completo
        function clearCart() {
            if (confirm('¿Estás seguro de que quieres vaciar todo el carrito?')) {
                fetch('ajax/clear_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cart = {};
                            updateCartCount();
                            updateCartDisplay();
                            showNotification('Carrito vaciado', 'success');
                        } else {
                            showNotification('Error al vaciar carrito', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error de conexión', 'error');
                    });
            }
        }

        // Función para actualizar el contador del carrito
        function updateCartCount() {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = Object.keys(cart).length;
                // Agregar clase de animación si hay items
                if (Object.keys(cart).length > 0) {
                    cartCount.classList.add('bg-red-500', 'text-white');
                    cartCount.classList.remove('bg-gray-300');
                } else {
                    cartCount.classList.remove('bg-red-500', 'text-white');
                    cartCount.classList.add('bg-gray-300');
                }
            }
        }

        // Función para mostrar animación del carrito
        function showCartAnimation() {
            const cartButton = document.querySelector('[onclick="toggleCart()"]');
            if (cartButton) {
                cartButton.classList.add('cart-bounce');
                setTimeout(() => {
                    cartButton.classList.remove('cart-bounce');
                }, 500);
            }
        }

        // Función para actualizar la visualización del carrito
        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');

            if (!cartItems || !cartTotal) return;

            if (Object.keys(cart).length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-3xl md:text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Tu carrito está vacío</p>
                    </div>
                `;
                cartTotal.textContent = '$0';
                return;
            }

            fetch('ajax/get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cartItems.innerHTML = data.html;
                        cartTotal.textContent = '$' + data.total.toLocaleString();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al cargar items del carrito', 'error');
                });
        }

        // Función para mostrar notificaciones
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-2 md:px-6 md:py-3 rounded-lg text-white font-semibold transition-all transform translate-x-full max-w-xs md:max-w-sm text-sm md:text-base`;
            notification.className += type === 'success' ? ' bg-green-500' : ' bg-red-500';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Función para proceder al checkout
        function proceedToCheckout() {
            if (Object.keys(cart).length === 0) {
                showNotification('El carrito está vacío', 'error');
                return;
            }

            // Redirigir a la página de checkout
            window.location.href = 'checkout.php';
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function () {
            // Actualizar contador inicial
            updateCartCount();

            // Cerrar modal al hacer clic fuera
            const cartModal = document.getElementById('cart-modal');
            if (cartModal) {
                cartModal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        toggleCart();
                    }
                });
            }

            // Cerrar modal con tecla Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('cart-modal');
                    if (modal && !modal.classList.contains('hidden')) {
                        toggleCart();
                    }
                }
            });

            // Toggle mobile menu
            document.getElementById('mobile-menu-button').addEventListener('click', function () {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
            });
        });
    </script>
</body>

</html>