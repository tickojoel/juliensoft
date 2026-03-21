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

// Obtener productos de la lista de deseos (en un caso real, esto vendría de la base de datos)
$wishlist_items = [
    [
        'id' => 1,
        'name' => 'Zapatillas Running Pro',
        'price' => 129.99,
        'original_price' => 149.99,
        'image' => 'images/products/zapatilla-running.jpg',
        'in_stock' => true,
        'stock_quantity' => 5
    ],
    [
        'id' => 2,
        'name' => 'Camiseta Deportiva',
        'price' => 39.99,
        'original_price' => 49.99,
        'image' => 'images/products/camiseta.jpg',
        'in_stock' => true,
        'stock_quantity' => 12
    ],
    [
        'id' => 3,
        'name' => 'Pantalón Deportivo',
        'price' => 59.99,
        'original_price' => 59.99,
        'image' => 'images/products/pantalon.jpg',
        'in_stock' => false,
        'stock_quantity' => 0
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deseos - Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
        }
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .discount-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--secondary-gradient);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Mi Lista de Deseos</h1>
                    <p class="text-gray-600">Tus productos favoritos guardados en un solo lugar</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-3">
                    <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>Compartir lista
                    </button>
                    <?php if (!empty($wishlist_items)): ?>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>Agregar todo al carrito
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($wishlist_items)): ?>
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-heart text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Tu lista de deseos está vacía</h3>
                    <p class="text-gray-500 mb-6">Guarda los artículos que te gustan para encontrarlos fácilmente más tarde.</p>
                    <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Seguir comprando
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="product-card bg-white rounded-lg shadow-md overflow-hidden relative">
                            <?php if ($item['original_price'] > $item['price']): ?>
                                <div class="discount-badge">
                                    <?php echo round((($item['original_price'] - $item['price']) / $item['original_price']) * 100) . '% OFF'; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-4">
                                <div class="h-48 bg-gray-100 rounded-lg mb-4 overflow-hidden">
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-full bg-white object-contain sm:object-cover">
                                </div>
                                <h3 class="font-medium text-gray-900 mb-1 line-clamp-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="flex items-center mb-2">
                                    <span class="text-lg font-bold text-gray-900">S/<?php echo number_format($item['price'], 2); ?></span>
                                    <?php if ($item['original_price'] > $item['price']): ?>
                                        <span class="ml-2 text-sm text-gray-500 line-through">S/<?php echo number_format($item['original_price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <?php if ($item['in_stock']): ?>
                                        <span class="h-2 w-2 rounded-full bg-green-500 mr-1"></span>
                                        <span>En stock (<?php echo $item['stock_quantity']; ?>)</span>
                                    <?php else: ?>
                                        <span class="h-2 w-2 rounded-full bg-red-500 mr-1"></span>
                                        <span>Agotado</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-md text-sm font-medium transition-colors flex items-center justify-center">
                                        <i class="fas fa-shopping-cart mr-2"></i>Comprar
                                    </button>
                                    <button class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors flex items-center justify-center">
                                        <i class="fas fa-trash-alt mr-2"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Banner de oferta -->
                <div class="mt-8 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg shadow-md p-6 text-white">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-xl font-bold mb-1">¿Necesitas ayuda para decidirte?</h3>
                            <p class="text-blue-100">¡Aprovecha nuestro envío gratuito en pedidos superiores a S/100!</p>
                        </div>
                        <a href="ofertas.php" class="bg-white text-blue-600 hover:bg-blue-50 font-medium py-2 px-6 rounded-md transition-colors whitespace-nowrap">
                            Ver ofertas especiales
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aquí puedes agregar la funcionalidad para:
            // - Agregar al carrito
            // - Eliminar de la lista de deseos
            // - Compartir la lista
            
            // Ejemplo de funcionalidad para el botón de eliminar
            document.querySelectorAll('.product-card button:last-child').forEach(button => {
                button.addEventListener('click', function() {
                    const productCard = this.closest('.product-card');
                    productCard.style.opacity = '0';
                    setTimeout(() => {
                        productCard.remove();
                        // Aquí iría la llamada AJAX para eliminar de la base de datos
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>
