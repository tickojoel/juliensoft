<?php
// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir las clases necesarias
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Product.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
        }

        .nav-link {
            position: relative;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }

        .nav-link:hover:after {
            width: 100%;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Top Bar -->
            <div class="flex justify-between items-center py-3 border-b">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-bold text-blue-600">Requejo Fashion Lab</a>
                </div>

                <!-- Search Bar -->
                <div class="hidden md:flex flex-1 max-w-xl mx-4">
                    <form action="index.php" method="GET" class="w-full flex">
                        <input type="text" name="search" placeholder="Buscar productos..."
                            class="flex-1 px-4 py-2 border border-r-0 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <a href="perfil.php" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-user text-xl"></i>
                    </a>
                    <a href="lista-deseos.php" class="text-gray-600 hover:text-red-500">
                        <i class="far fa-heart text-xl"></i>
                    </a>
                    <a href="carrito.php" class="relative group flex items-center px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-100">
                        <div class="relative">
                            <i class="fas fa-shopping-cart text-xl text-gray-600 group-hover:text-blue-600 transition-colors"></i>
                            <?php 
                            $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            $cartTotal = 0;
                            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                $database = new Database();
                                $conn = $database->getConnection();
                                $product = new Product($conn);
                                
                                foreach ($_SESSION['cart'] as $productId => $item) {
                                    $productDetails = $product->getById($productId);
                                    if ($productDetails) {
                                        $quantity = is_array($item) ? $item['quantity'] : $item;
                                        $cartTotal += $productDetails['price'] * $quantity;
                                    }
                                }
                            }
                            ?>
                            <?php if ($cartCount > 0): ?>
                                <span class="absolute -top-2 -right-2 bg-gradient-to-r from-blue-600 to-blue-400 text-white text-xs font-semibold rounded-full h-5 w-5 flex items-center justify-center transform group-hover:scale-110 transition-transform">
                                    <?php echo $cartCount; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="hidden md:block ml-2 text-left">
                            <div class="text-xs text-gray-500">Mi carrito</div>
                            <div class="text-sm font-medium text-gray-900">S/<?php echo number_format($cartTotal, 2); ?></div>
                        </div>
                    </a>
                    <button class="md:hidden text-gray-600" id="mobile-menu-button">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex justify-center space-x-6 py-3">
                <a href="index.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Inicio</a>
                <a href="ofertas.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Ofertas</a>
                <a href="categorias.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Categorías</a>
                <a href="nuevo.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Nuevo</a>
                <a href="contacto.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Contacto</a>
            </nav>
        </div>

        <!-- Mobile Menu -->
        <div class="hidden bg-white border-t" id="mobile-menu">
            <div class="px-4 py-2">
                <form action="index.php" method="GET" class="mb-3">
                    <div class="flex">
                        <input type="text" name="search" placeholder="Buscar productos..."
                            class="flex-1 px-4 py-2 border border-r-0 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <a href="index.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Inicio</a>
                <a href="ofertas.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Ofertas</a>
                <a href="categorias.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Categorías</a>
                <a href="nuevo.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Nuevo</a>
                <a href="contacto.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Contacto</a>
                <div class="border-t my-2"></div>
                <a href="perfil.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Mi Cuenta</a>
                <a href="pedidos.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Mis Pedidos</a>
                <a href="lista-deseos.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded">Lista de
                    Deseos</a>
                <a href="carrito.php"
                    class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded flex items-center justify-between">
                    <span>Carrito</span>
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <script>
        // Toggle mobile menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function (e) {
                e.preventDefault();
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('block');
            });
        }
    </script>

    <!-- Main Content -->
    <main class="min-h-screen">