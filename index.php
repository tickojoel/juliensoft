<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Product.php';
require_once 'functions/helpers.php';

// Inicializar conexión
$database = new Database();
$conn = $database->getConnection();
$product = new Product($conn);

// Obtener categorías de la base de datos
try {
    $query = "SELECT * FROM categories ORDER BY id ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $categories_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories_db = [];
    error_log("Error al obtener categorías: " . $e->getMessage());
}

// Mapeo de iconos y colores por categoría
$categoryStyles = [
    'Running' => ['icon' => 'fa-running', 'color' => 'text-blue-600'],
    'Fitness' => ['icon' => 'fa-dumbbell', 'color' => 'text-red-600'],
    'Natación' => ['icon' => 'fa-swimmer', 'color' => 'text-cyan-600'],
    'Fútbol' => ['icon' => 'fa-futbol', 'color' => 'text-green-600'],
];

// Obtener parámetros de filtrado
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener productos
$products = $product->getAll($category, $search);
$categories = getProductsByCategory();

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
    <title>SportZone - Equipamiento Deportivo Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
            --accent-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --dark-gradient: linear-gradient(135deg, #111827 0%, #374151 100%);
        }

        .gradient-primary {
            background: var(--primary-gradient);
        }

        .gradient-secondary {
            background: var(--secondary-gradient);
        }

        .gradient-accent {
            background: var(--accent-gradient);
        }

        .gradient-dark {
            background: var(--dark-gradient);
        }

        .sport-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .sport-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .hero-background {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.9) 0%, rgba(6, 182, 212, 0.8) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            background-size: cover, 50px 50px;
            position: relative;
            overflow: hidden;
        }

        .performance-badge {
            background: linear-gradient(135deg, #dc2626, #f59e0b);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        
        /* Estilos responsivos para móviles */
        @media (max-width: 640px) {
            .welcome-image {
                width: 100%;
                max-height: 70vh;
                position: relative;
                overflow: hidden;
            }
            .welcome-image img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                object-position: center;
            }
            .relative.h-screen {
                height: auto !important;
                min-height: 50vh;
            }
            section.relative {
                height: auto !important;
            }
        }

        .new-arrival-badge {
            background: linear-gradient(135deg, #059669, #10b981);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }

        .btn-sport {
            background: var(--primary-gradient);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-sport:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .floating-elements::before {
            content: '';
            position: absolute;
            top: 20%;
            right: 10%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements::after {
            content: '';
            position: absolute;
            bottom: 20%;
            left: 10%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .performance-stats {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .category-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .category-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 1);
        }

        .price-highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

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

        .sport-divider {
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            height: 1px;
        }

        /* Reset para Main Content */
        .main-content {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Welcome Section - Completamente responsiva */
        .welcome-section {
            position: relative;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 0;
            overflow: hidden;
            margin-top: 0;
        }

        .welcome-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            max-height: 100vh;
            overflow: hidden;
            height: 100%;
            z-index: 1;
        }

        .welcome-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            filter: brightness(0.6);
        }

        .welcome-content {
            position: relative;
            z-index: 2;
            color: white;
            width: 90%;
            max-width: 800px;
            padding: 30px;
            margin: 0 auto;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            backdrop-filter: blur(3px);
        }

        .welcome-content h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            margin-bottom: 25px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            position: relative;
            padding-bottom: 15px;
        }

        .welcome-content h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--gold, #d4af37);
        }

        .welcome-content p {
            font-size: clamp(1rem, 2vw, 1.4rem);
            margin-bottom: 20px;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .signature {
            margin-top: 40px;
            font-style: italic;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .hero-background {
                background-size: cover, 30px 30px;
            }
        }

        /* Performance optimizations */
        .lazy-load {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .lazy-load.loaded {
            opacity: 1;
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
            animation-delay: 0.5s; /* Desfase para que no estén sincronizados */
        }
        
        .cart-float:hover {
            animation: none;
        }

        /* Mejoras de responsividad */
        @media (max-width: 640px) {
            .hero-background {
                min-height: 70vh;
                padding: 60px 0 40px;
            }
            
            .welcome-content {
                padding: 20px;
                width: 95%;
            }
            
            .welcome-content h1 {
                font-size: 2rem;
                margin-bottom: 15px;
            }
            
            .welcome-content p {
                font-size: 1rem;
            }
            
            .performance-stats {
                padding: 12px;
            }
            
            .performance-stats .text-3xl {
                font-size: 1.5rem;
            }
            
            .btn-sport, .border-white {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .hero-background {
                min-height: 80vh;
            }
            
            .welcome-content {
                padding: 15px;
            }
            
            .welcome-content h1 {
                font-size: 1.8rem;
            }
            
            .category-card {
                padding: 12px;
            }
            
            .category-card i {
                font-size: 2rem;
            }
            
            .category-card h4 {
                font-size: 1rem;
            }
            
            .category-card p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Header Responsivo -->
    <header class="bg-white shadow-xl sticky top-0 z-50 border-b border-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between py-4">
            <div class="flex items-center justify-between w-full md:w-auto mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                    <i class="fas fa-dumbbell mr-3 text-blue-600"></i>Requejo Fashion Lab
                </h1>
                <button id="mobile-menu-button" class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Menú de navegación modificado -->
            <nav id="mobile-menu" class="hidden md:flex flex-col md:flex-row w-full md:w-auto items-center justify-center py-4 md:py-0">
                <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8 text-center w-full">
                    <a href="index.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Inicio</a>
                    <a href="#products" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Productos</a>
                    <a href="ofertas.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Ofertas</a>
                    <a href="contacto.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Contactarnos</a>
                </div>
            </nav>

            <div class="flex items-center space-x-4 mt-4 md:mt-0 w-full md:w-auto justify-between md:justify-start">
                <!-- Cart Button -->
                <div class="relative">
                    <button onclick="toggleCart()" class="fixed bottom-24 right-6 z-50 bg-blue-500 hover:bg-blue-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110"
                        style="box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    </button>
                </div>

                <!-- Admin Button -->
                <a href="admin/" class="text-gray-600 hover:text-blue-600 transition-colors ml-2">
                    <i class="fas fa-user-cog text-xl"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Botón flotante de WhatsApp -->
<a href="https://wa.me/+56977106814?text=Hola,%20estoy%20interesado%20en%20realizar%20una%20compra" 
   target="_blank"
       class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 floating-btn"
   style="box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);">
    <i class="fab fa-whatsapp text-2xl"></i>
    <span class="sr-only">Chat de WhatsApp</span>
</a>
            <!-- Hero Section -->
    <section class="relative h-screen flex items-center justify-center overflow-hidden" style="min-height: 500px;">
        <div class="welcome-image absolute inset-0 w-full h-full">
            <img src="images/joel.jpg" alt="Requejo Fashion Lab" class="w-full h-full object-cover object-center">
        </div>
        <div class="welcome-content relative z-10 text-center text-white px-4">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Bienvenidos a Requejo Fashion Lab</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Desde 1985, hemos sido un referente en equipamiento deportivo de alta calidad. 
                Combinamos tradición e innovación para ofrecer productos excepcionales.
            </p>                            

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#products"
                    class="bg-white text-blue-600 px-6 md:px-8 py-3 md:py-4 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 inline-flex items-center justify-center transform hover:scale-105 shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>Explorar Colección
                </a>
                <a href="ofertas.php"
                    class="border-2 border-white text-white px-6 md:px-8 py-3 md:py-4 rounded-full font-bold hover:bg-white hover:text-blue-600 transition-all duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-fire mr-2"></i>Ofertas Especiales
                </a>
            </div>
            <div class="mt-12">
                <p class="italic">Con cariño,</p>
                <p class="font-semibold">La Familia Requejo Fashion Lab</p>
            </div>
        </div>
    </section>

    <!-- Categories Preview -->
    <section class="py-16 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">Explora Nuestras Categorías</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-blue-500 to-cyan-400 mx-auto mb-4"></div>
                <p class="text-gray-600 max-w-2xl mx-auto text-base md:text-lg">Descubre equipamiento especializado para cada disciplina deportiva</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 sm:gap-6 max-w-6xl mx-auto">
                <?php foreach ($categories_db as $cat): 
                    $icon = !empty($cat['icon']) ? 
                           (strpos($cat['icon'], 'fa-') === 0 ? $cat['icon'] : 'fa-'.$cat['icon']) : 
                           'fa-question-circle';
                    
                    $color = !empty($cat['color']) ? $cat['color'] : 'text-blue-500';
                    $hoverColor = !empty($cat['color']) ? str_replace('text-', 'hover:bg-', $cat['color']) : 'hover:bg-blue-500';
                ?>
                    <!-- <a href="?category=<?php echo $cat['id']; ?>" 
                       class="group category-card bg-white rounded-xl p-5 sm:p-6 text-center shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-transparent">
                        <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gray-50 group-hover:bg-opacity-10 transition-colors duration-300 mb-4 mx-auto">
                            <i class="fas <?php echo htmlspecialchars($icon); ?> text-2xl sm:text-3xl <?php echo htmlspecialchars($color); ?> group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm sm:text-base mb-2 group-hover:text-white transition-colors duration-300">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </h4>
                        <p class="text-xs sm:text-sm text-gray-500 group-hover:text-gray-200 transition-colors duration-300">
                            <?php echo htmlspecialchars($cat['description']); ?>
                        </p>
                    </a> -->
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Filtros y Búsqueda Responsivos -->
    <section class="bg-white py-6 sm:py-8 border-b" id="products">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">
                <!-- Barra de búsqueda mejorada -->
                <div class="w-full mb-6">
                    <form method="GET" class="relative">
                        <input type="hidden" name="category" value="<?php echo $category; ?>">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Buscar productos..." 
                                value="<?php echo htmlspecialchars($search); ?>"
                                class="w-full pl-12 pr-24 py-3 sm:py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-gray-700 placeholder-gray-400 text-sm sm:text-base shadow-sm hover:shadow-md transition-shadow duration-200">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button 
                                type="submit"
                                class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-4 sm:px-6 py-2 rounded-lg font-semibold text-sm sm:text-base transition-all duration-300 shadow-md hover:shadow-lg">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Filtros de categoría mejorados -->
                <div class="w-full overflow-x-auto pb-2 sm:pb-0 custom-scrollbar">
                    <div class="flex gap-2 sm:gap-3 flex-nowrap sm:flex-wrap">
                        <a href="?category=all&search=<?php echo urlencode($search); ?>"
                            class="px-5 py-2.5 rounded-full font-semibold text-sm sm:text-base whitespace-nowrap transition-all duration-300 flex items-center
                                   <?php echo $category === 'all' ? 
                                   'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-md' : 
                                   'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:shadow-sm'; ?>">
                            <i class="fas fa-th mr-2 text-sm"></i>Todos
                        </a>
                        
                        <?php foreach ($categories_db as $cat): 
                            $icon = !empty($cat['icon']) ? 
                                   (strpos($cat['icon'], 'fa-') === 0 ? $cat['icon'] : 'fa-'.$cat['icon']) : 
                                   false;
                            $isActive = $category == $cat['id'];
                        ?>
                            <a href="?category=<?php echo $cat['id']; ?>&search=<?php echo urlencode($search); ?>"
                                class="px-4 py-2 rounded-full font-medium text-sm sm:text-base whitespace-nowrap transition-all duration-300 flex items-center
                                       <?php echo $isActive ? 
                                       'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-md' : 
                                       'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:shadow-sm'; ?>">
                                <?php if ($icon): ?>
                                    <i class="fas <?php echo htmlspecialchars($icon); ?> mr-2 text-xs"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($cat['name']); ?>
                                <?php if ($isActive): ?>
                                    <span class="ml-1.5 w-2 h-2 bg-white rounded-full"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estilos adicionales para la barra de desplazamiento personalizada -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>

    <!-- Grid de Productos Responsivo -->
    <main class="container mx-auto px-4 py-8 sm:py-12">
        <div class="mb-6 sm:mb-8">
            <h3 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-1 sm:mb-2">
                <?php if ($search): ?>
                    Resultados para "<?php echo htmlspecialchars($search); ?>"
                <?php elseif ($category !== 'all' && !empty($categories)): ?>
                    <?php
                    $categoryName = 'Equipamiento Deportivo';
                    foreach ($categories as $cat) {
                        if (isset($cat['id']) && $cat['id'] == $category && isset($cat['name'])) {
                            $categoryName = $cat['name'];
                            break;
                        }
                    }
                    echo htmlspecialchars($categoryName);
                    ?>
                <?php else: ?>
                    Equipamiento Deportivo
                <?php endif; ?>
            </h3>
            <p class="text-gray-600 text-base sm:text-lg">
                <?php echo count($products); ?> 
                <?php echo (count($products) === 1) ? 'producto' : 'productos'; ?> 
                encontrados
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($products as $prod): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 relative">
                    <!-- Badge de descuento -->
                    <?php if ($prod['discount'] > 0): ?>
                        <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-bl-lg rounded-tr-lg z-10">
                            -<?= $prod['discount'] ?>%
                        </div>
                    <?php endif; ?>

                    <!-- Imagen del producto -->
                    <div class="w-full bg-gray-100 flex items-center justify-center overflow-hidden" style="aspect-ratio: 1;">
                        <img src="<?= htmlspecialchars($prod['image']) ?>" 
                             alt="<?= htmlspecialchars($prod['name']) ?>"
                             class="w-full h-full object-contain transition-transform duration-500 hover:scale-105 lazy-load">
                    </div>

                    <!-- Detalles del producto -->
                    <div class="p-3 md:p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-base md:text-lg">
                                <?= htmlspecialchars($prod['name']) ?>
                            </h3>
                            <?php if ($prod['is_new']): ?>
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Nuevo</span>
                            <?php endif; ?>
                        </div>

                        <p class="text-gray-600 text-xs md:text-sm mb-2">
                            <?= htmlspecialchars($prod['brand']) ?>
                        </p>

                        <p class="text-gray-600 text-xs md:text-sm mb-2 line-clamp-2">
                            <?= htmlspecialchars($prod['description']) ?>
                        </p>

                        <div class="flex items-center mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $prod['rating']): ?>
                                    <i class="fas fa-star text-yellow-400 text-xs md:text-sm"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-yellow-400 text-xs md:text-sm"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="text-gray-500 text-xs md:text-sm ml-1">(<?= $prod['reviews_count'] ?>)</span>
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <div>
                                <?php if ($prod['discount'] > 0): ?>
                                    <span class="text-gray-400 line-through text-xs md:text-sm mr-1">
                                        $<?= number_format($prod['price'], 0, ',', '.') ?>
                                    </span>
                                    <span class="text-red-500 font-bold text-sm md:text-base">
                                        $<?= number_format($prod['price'] * (1 - $prod['discount'] / 100), 0, ',', '.') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-800 font-bold text-sm md:text-base">
                                        $<?= number_format($prod['price'], 0, ',', '.') ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <button 
                                onclick="showProductOptions(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['name']); ?>', <?php echo (stripos($prod['name'], 'zapatilla') !== false || stripos($prod['category_name'] ?? '', 'zapatilla') !== false) ? 'true' : 'false'; ?>)"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded text-xs md:text-sm transition-colors">
                                <i class="fas fa-cart-plus"></i> <span class="hidden sm:inline">Agregar</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-12 sm:py-20">
                <i class="fas fa-search text-6xl sm:text-8xl text-gray-300 mb-6 sm:mb-8"></i>
                <h3 class="text-2xl sm:text-3xl font-bold text-gray-600 mb-3 sm:mb-4">No se encontraron productos</h3>
                <p class="text-gray-500 mb-6 sm:mb-8 text-base sm:text-lg">Intenta con otros términos de búsqueda o categorías</p>
                <a href="?" class="btn-sport text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl inline-block font-bold text-sm sm:text-base">
                    Ver todos los productos
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Modal del Carrito -->
        <div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden backdrop-blur-sm transition-opacity duration-300">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] flex flex-col overflow-hidden">
                    <div class="flex items-center justify-between p-4 sm:p-6 border-b bg-gradient-to-r from-blue-600 to-cyan-500 text-white">
                    <h3 class="text-lg sm:text-xl font-bold">Carrito de Compras</h3>
                    <button onclick="toggleCart()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
                <div id="cart-items" class="p-4 sm:p-6 overflow-y-auto flex-1">
                    <!-- Los items del carrito se cargarán aquí dinámicamente -->
                </div>
                <div id="cart-summary" class="border-t p-4 sm:p-6 bg-gray-50">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <span class="text-base sm:text-lg font-semibold">Total:</span>
                        <span id="cart-total" class="text-2xl sm:text-3xl font-bold price-highlight">$0</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            onclick="toggleCart()"
                            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold hover:shadow-lg transition-all duration-200 text-sm sm:text-base flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </button>
                        <button
                            id="checkout-button"
                            class="w-full gradient-accent text-white py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold hover:shadow-lg transition-all duration-200 text-sm sm:text-base disabled:opacity-50 disabled:cursor-not-allowed"
                            onclick="proceedToCheckout()"
                            disabled>
                            <i class="fas fa-credit-card mr-2"></i>Pagar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </main>

    <!-- Script para el modal de opciones de producto -->
    <script src="assets/js/product-options.js"></script>
    
    <script>
        // Variables globales
        let cart = <?php echo isset($_SESSION['cart']) ? json_encode($_SESSION['cart']) : '{}'; ?>;
        if (typeof cart === 'string') {
            cart = JSON.parse(cart);
        }

        // Función para alternar el modal del carrito
        function toggleCart() {
            const modal = document.getElementById('cart-modal');
            if (modal) {
                modal.classList.toggle('hidden');
                document.body.style.overflow = modal.classList.contains('hidden') ? 'auto' : 'hidden';
                if (!modal.classList.contains('hidden')) {
                    updateCartDisplay();
                }
            }
        }

        // Función para agregar productos al carrito
        function addToCart(productId, quantity = 1, options = {}) {
            console.log('Agregando al carrito:', { productId, quantity, options });
            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    options: options
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Error en la petición');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Respuesta del servidor:', data);
                    if (data.success) {
                        cart = data.cart;
                        updateCartCount();
                        updateCartDisplay(); // Asegurarse de actualizar la visualización
                        showCartAnimation();
                        showNotification(data.message || 'Producto agregado al carrito', 'success');
                    } else {
                        showNotification(data.message || 'Error al agregar producto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error en addToCart:', error);
                    if (error.message === 'Debes iniciar sesión para agregar productos al carrito') {
                        showNotification('Debes iniciar sesión para continuar', 'error');
                        // Opcional: redirigir a la página de login
                        // window.location.href = 'login.php';
                    } else {
                        showNotification('Error al agregar al carrito: ' + error.message, 'error');
                    }
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

        // Evita que renders anteriores (peticiones aún en curso) dupliquen ítems
        let cartRenderId = 0;

        // Función para actualizar la visualización del carrito
        function updateCartDisplay() {
            const renderId = ++cartRenderId;
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const checkoutButton = document.getElementById('checkout-button');
            
            if (!cartItems) return;
            
            // Limpiar el contenedor de items
            cartItems.innerHTML = '';
            
            // Si el carrito está vacío
            if (Object.keys(cart).length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Tu carrito está vacío</p>
                    </div>
                `;
                checkoutButton.disabled = true;
                cartTotal.textContent = '$0';
                return;
            }
            
            // Calcular total
            let total = 0;
            
            // Agregar cada producto al carrito
            for (let i = 0; i < Object.keys(cart).length; i++) {
                const item = cart[Object.keys(cart)[i]];
                const productId = item.product_id;
                
                // Obtener detalles del producto
                fetch(`ajax/get_product.php?id=${productId}`)
                    .then(response => response.json())
                    .then(product => {
                        if (renderId !== cartRenderId) return;
                        const subtotal = product.price * item.quantity;
                        total += subtotal;
                        
                        const productElement = document.createElement('div');
                        productElement.className = 'py-4 border-b';
                        productElement.id = `cart-item-${i}`;
                        
                        // Construir el HTML para las opciones
                        let optionsHtml = '';
                        if (item.options && Object.keys(item.options).length > 0) {
                            optionsHtml = '<div class="mt-1 text-xs text-gray-500 space-y-1">';
                            if (item.options.size) {
                                optionsHtml += `<div>Talla: <span class="font-medium">${item.options.size}</span></div>`;
                            }
                            if (item.options.color) {
                                optionsHtml += `<div>Color: <span class="font-medium">${item.options.color}</span></div>`;
                            }
                            if (item.options.shoeSize) {
                                optionsHtml += `<div>Número: <span class="font-medium">${item.options.shoeSize}</span></div>`;
                            }
                            optionsHtml += '</div>';
                        }
                        
                        productElement.innerHTML = `
                            <div class="flex items-start">
                                <img src="${product.image || 'images/placeholder-product.jpg'}" alt="${product.name}" class="w-20 h-20 object-cover rounded-md">
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between">
                                        <h4 class="font-medium text-gray-900">${product.name}</h4>
                                        <button onclick="removeFromCart(${Object.keys(cart)[i]})" class="text-gray-400 hover:text-red-500">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-500">$${product.price.toLocaleString()}</p>
                                    ${optionsHtml}
                                    <div class="flex items-center mt-2">
                                        <button onclick="updateCartItem(${Object.keys(cart)[i]}, ${item.quantity - 1})" class="text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="mx-2">${item.quantity}</span>
                                        <button onclick="updateCartItem(${Object.keys(cart)[i]}, ${item.quantity + 1})" class="text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">$${subtotal.toLocaleString()}</p>
                                </div>
                            </div>
                        `;
                        
                        cartItems.appendChild(productElement);
                        
                        // Actualizar total
                        updateCartTotal();
                    })
                    .catch(error => {
                        console.error('Error al cargar detalles del producto:', error);
                    });
            }
            
            // Habilitar botón de pago si hay productos
            checkoutButton.disabled = Object.keys(cart).length === 0;
        }
        
        // Función para actualizar el total del carrito
        function updateCartTotal() {
            const cartTotal = document.getElementById('cart-total');
            let total = 0;
            
            // Calcular total sumando los precios de los productos visibles
            document.querySelectorAll('#cart-items > div').forEach(item => {
                const priceText = item.querySelector('.text-sm.text-gray-500').textContent.replace('$', '').replace(/\./g, '');
                const quantity = parseInt(item.querySelector('.mx-2').textContent);
                const price = parseFloat(priceText);
                total += price * quantity;
            });
            
            cartTotal.textContent = `$${total.toLocaleString()}`;
        }
        
        // Función para actualizar la cantidad de un ítem en el carrito
        function updateCartItem(index, newQuantity) {
            if (newQuantity < 1) {
                removeFromCart(index);
                return;
            }
            
            fetch('ajax/update_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    index: index,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cart = data.cart;
                    updateCartCount();
                    updateCartDisplay();
                    showNotification('Carrito actualizado', 'success');
                } else {
                    showNotification(data.message || 'Error al actualizar cantidad', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión', 'error');
            });
        }

        // Función para mostrar notificaciones
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-semibold transition-all transform translate-x-full max-w-sm`;
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

        // Menú móvil
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('block');
            });
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

            // Smooth scroll para los enlaces
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
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

        // Lazy loading implementation
        const lazyImages = document.querySelectorAll('.lazy-load');

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.remove('lazy-load');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    </script>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-8 pb-4">
        <div class="w-full px-0">
            <div class="w-full px-4 md:px-8">
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
                        <a href="https://wa.me/+56977106814?text=Hola,%20estoy%20interesado%20en%20realizar%20una%20compra" class="text-gray-400 hover:text-blue-400 transition-colors" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Columna 2: Enlaces rápidos -->
                <div>
                    <h4 class="font-bold text-sm mb-3 pb-1 border-b border-gray-700">Explorar</h4>
                    <ul class="space-y-1 text-sm">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="ofertas.php" class="text-gray-400 hover:text-white transition-colors">Ofertas</a></li>
                        <li><a href="categorias.php" class="text-gray-400 hover:text-white transition-colors">Categorías</a></li>
                        <li><a href="contacto.php" class="text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Mi Cuenta -->
                <div>
                    <h4 class="font-bold text-sm mb-3 pb-1 border-b border-gray-700">Mi Cuenta</h4>
                    <ul class="space-y-1 text-sm">
                        <li><a href="perfil.php" class="text-gray-400 hover:text-white transition-colors">Mi Perfil</a></li>
                        <li><a href="pedidos.php" class="text-gray-400 hover:text-white transition-colors">Mis Pedidos</a></li>
                        <li><a href="lista-deseos.php" class="text-gray-400 hover:text-white transition-colors">Lista de Deseos</a></li>
                        <li><a href="carrito.php" class="text-gray-400 hover:text-white transition-colors">Carrito</a></li>
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
                            <a href="mailto:contacto@requejofashionlab.cl" class="text-gray-300 hover:text-white">contacto@requejofashionlab.cl</a>
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
                               class="flex-grow px-3 py-2 rounded text-sm focus:outline-none focus:ring-1 focus:ring-pink-500 text-gray-800" required>
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
        </div>
    </footer>
    
</body>
</html>