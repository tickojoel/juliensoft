<?php
// Iniciar sesión al principio del archivo
session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

require_once 'config/database.php';
// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'beautystore';
$username = 'root';
$password = '';

// Procesar formulario de contacto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $mensaje = htmlspecialchars(trim($_POST['mensaje'] ?? ''));
    $errors = [];

    // Validaciones
    if (empty($nombre)) $errors[] = 'El nombre es requerido';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
    if (empty($mensaje)) $errors[] = 'El mensaje es requerido';

    if (empty($errors)) {
        // Guardar en base de datos
        try {
            // Crear conexión usando tu archivo de configuración
            $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Obtener información adicional
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            // Preparar la consulta SQL
            $stmt = $db->prepare("
                INSERT INTO contactos 
                (nombre, email, telefono, mensaje, fecha, estado, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, NOW(), 'nuevo', ?, ?)
            ");
            
            // Ejecutar la consulta
            $stmt->execute([
                $nombre, 
                $email, 
                $telefono ?: null, // Si está vacío, guarda NULL
                $mensaje, 
                $ip_address, 
                $user_agent
            ]);
            
            $mensajeGuardado = true;
            
            // Limpiar variables para evitar reenvío accidental
            $nombre = $email = $telefono = $mensaje = '';
            
        } catch(PDOException $e) {
            error_log("Error al guardar contacto: " . $e->getMessage());
            $errors[] = 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Tienda Requejo Fashion Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8B5CF6',
                        secondary: '#7C3AED',
                        dark: '#1F2937',
                        light: '#F3F4F6'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
<!-- Header/Navbar Moderno y Responsivo -->
<header class="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo/Marca -->
            <div class="flex-shrink-0">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                    <i class="fas fa-spa mr-2 text-purple-600"></i>Requejo Fashion Lab                </h1>
            </div>

            <!-- Menú Desktop (hidden en mobile) -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-700 hover:text-purple-600 transition-colors font-medium">Inicio</a>
                <a href="ofertas.php" class="text-gray-700 hover:text-purple-600 transition-colors font-medium flex items-center">
                    Ofertas <span class="ml-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-xs px-2 py-0.5 rounded-full">Hot</span>
                </a>
                <a href="categorias.php" class="text-gray-700 hover:text-purple-600 transition-colors font-medium">Categorías</a>
                <a href="contacto.php" class="text-purple-600 font-medium">Contactarnos</a>
            </nav>

            <!-- Iconos de usuario y carrito -->
            <div class="flex items-center space-x-4 mt-4 md:mt-0">
                <button onclick="toggleCart()" class="fixed bottom-24 right-6 z-40 bg-blue-500 hover:bg-blue-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 cart-float"
                    style="box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold">
                        <?php echo count($_SESSION['cart']); ?>
                    </span>
                </button>
                <a href="admin/" class="text-gray-600 hover:text-purple-600"><i class="fas fa-user text-xl"></i></a>
                
                <!-- Botón Mobile -->
                <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-purple-600 ml-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Menú Mobile Centrado - Versión mejorada -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="container mx-auto px-4 py-4">
                <div class="flex flex-col items-center justify-center space-y-4 w-full text-center">
                    <a href="index.php" class="block w-full py-2 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 font-medium">Inicio</a>
                    <a href="ofertas.php" class="block w-full py-2 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 font-medium">
                        <div class="flex items-center justify-center">
                            Ofertas <span class="ml-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-xs px-2 py-0.5 rounded-full">Hot</span>
                        </div>
                    </a>
                    <a href="categorias.php" class="block w-full py-2 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 font-medium">Categorías</a>
                    <a href="contacto.php" class="block w-full py-2 px-4 text-purple-600 hover:bg-purple-50 rounded-lg font-medium">Contactarnos</a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    /* Estilos adicionales para el menú móvil */
    #mobile-menu a {
        transition: all 0.3s ease;
    }
    
    #mobile-menu a:hover {
        transform: translateX(5px);
    }
    
    @media (max-width: 767px) {
        #mobile-menu {
            position: relative;
            z-index: 40;
        }
    }
</style>

<script>
    // Toggle Mobile Menu mejorado
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        
        // Cambiar icono de hamburguesa a X con animación
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
        icon.style.transition = 'transform 0.3s ease';
        
        if (menu.classList.contains('hidden')) {
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.style.transform = 'rotate(90deg)';
        }
    });

    // Función para el carrito
    function toggleCart() {
        // Tu implementación existente del carrito
    }
</script>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-dark mb-4">Contáctanos</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Estamos aquí para ayudarte. Completa el formulario y nos pondremos en contacto contigo lo antes posible.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Formulario de Contacto -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <?php if (isset($mensajeGuardado) && $mensajeGuardado): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>¡Gracias por contactarnos! Tu mensaje ha sido enviado correctamente. Te responderemos pronto.</span>
                            </div>
                        </div>
                    <?php elseif (!empty($errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                                <div>
                                    <ul class="list-disc list-inside">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="contacto.php" method="POST">
                        <div class="mb-6">
                            <label for="nombre" class="block text-gray-700 font-medium mb-2">Nombre Completo</label>
                            <input type="text" id="nombre" name="nombre" required
                                value="<?= htmlspecialchars($nombre ?? '') ?>"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/50 transition"
                                placeholder="Tu nombre">
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required
                                value="<?= htmlspecialchars($email ?? '') ?>"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/50 transition"
                                placeholder="tu@email.com">
                        </div>

                        <div class="mb-6">
                            <label for="telefono" class="block text-gray-700 font-medium mb-2">Teléfono (Opcional)</label>
                            <input type="tel" id="telefono" name="telefono"
                                value="<?= htmlspecialchars($telefono ?? '') ?>"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/50 transition"
                                placeholder="+56 9 1234 5678">
                        </div>

                        <div class="mb-6">
                            <label for="mensaje" class="block text-gray-700 font-medium mb-2">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="5" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/50 transition"
                                placeholder="¿Cómo podemos ayudarte?"><?= htmlspecialchars($mensaje ?? '') ?></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-primary hover:bg-secondary text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Mensaje
                        </button>
                    </form>
                </div>

                <!-- Información de Contacto -->
                <div class="space-y-8">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h3 class="text-xl font-bold text-dark mb-6">Información de Contacto</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-primary/10 p-3 rounded-full mr-4">
                                    <i class="fas fa-map-marker-alt text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-dark">Dirección</h4>
                                    <p class="text-gray-600">Av. Principal 1234, Santiago, Chile</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-primary/10 p-3 rounded-full mr-4">
                                    <i class="fas fa-phone-alt text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-dark">Teléfono</h4>
                                    <p class="text-gray-600">+56 2 2345 6789</p>
                                    <p class="text-gray-600">+56 9 8765 4321 (WhatsApp)</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-primary/10 p-3 rounded-full mr-4">
                                    <i class="fas fa-envelope text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-dark">Email</h4>
                                    <p class="text-gray-600">contacto@tiendaRequejo Fashion Lab.cl</p>
                                    <p class="text-gray-600">ventas@tiendaRequejo Fashion Lab.cl</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-primary/10 p-3 rounded-full mr-4">
                                    <i class="fas fa-clock text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-dark">Horario de Atención</h4>
                                    <p class="text-gray-600">Lunes a Viernes: 9:00 - 19:00 hrs</p>
                                    <p class="text-gray-600">Sábado: 10:00 - 14:00 hrs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mapa -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3329.511212262529!2d-70.6433779241647!3d-33.433901703137336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662c5a3e0c4f39b%3A0xa2d5a7f3e0e2b5a5!2sPlaza%20de%20Armas%20de%20Santiago!5e0!3m2!1ses-419!2scl!4v1710000000000!5m2!1ses-419!2scl" 
                                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/+56977106814?text=Hola,%20estoy%20interesado%20en%20realizar%20una%20compra" 
       target="_blank"
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
                <!-- Los items del carrito se cargarán aquí dinámicamente -->
                <div
                    class="flex items-center justify-between p-4 md:p-6 border-b bg-gradient-to-r from-purple-600 to-purple-500 text-white">
                    <h3 class="text-lg md:text-xl font-bold">Carrito</h3>
                    <button onclick="toggleCart()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="cart-items" class="p-4 md:p-6 overflow-y-auto flex-1">
                    <!-- Los items del carrito se cargarán aquí dinámicamente -->
                </div>
                <div class="border-t p-4 md:p-6 bg-gray-50">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <span class="text-base md:text-lg font-semibold">Total:</span>
                        <span id="cart-total" class="text-xl md:text-2xl font-bold price-highlight">$0</span>
                    </div>
                    <button onclick="proceedToCheckout()"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-500 text-white py-3 md:py-4 rounded-xl font-bold hover:shadow-lg transition-all duration-200 text-sm md:text-base">
                        <i class="fas fa-credit-card mr-2"></i>Pagar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Requejo Fashion Lab</h3>
                    <p class="text-gray-400">Los mejores productos de belleza y cuidado personal.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="ofertas.php" class="text-gray-400 hover:text-white transition-colors">Ofertas</a></li>
                        <li><a href="categorias.php" class="text-gray-400 hover:text-white transition-colors">Categorías</a></li>
                        <li><a href="contacto.php" class="text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Mi Cuenta</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Mi Perfil</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Mis Pedidos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Lista de Deseos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Carrito</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Suscríbete para recibir ofertas exclusivas.</p>
                    <form class="flex">
                        <input type="email" placeholder="Tu email" 
                               class="px-4 py-2 rounded-l-lg focus:outline-none text-dark w-full">
                        <button type="submit" 
                                class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-r-lg transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> Requejo Fashion Lab. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Script para menú móvil -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.md\\:hidden');
            const nav = document.querySelector('nav');
            
            mobileMenuButton.addEventListener('click', function() {
                nav.classList.toggle('hidden');
                nav.classList.toggle('block');
                nav.classList.toggle('absolute');
                nav.classList.toggle('top-16');
                nav.classList.toggle('left-0');
                nav.classList.toggle('right-0');
                nav.classList.toggle('bg-white');
                nav.classList.toggle('p-4');
                nav.classList.toggle('shadow-md');
            });
        });
    </script>
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

            // Cerrar modal con tecla Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('cart-modal');
                    if (modal && !modal.classList.contains('hidden')) {
                        toggleCart();
                    }
                }
            });
        });
    </script>
</body>
</html>