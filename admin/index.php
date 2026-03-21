<?php

session_start();
// Generar token CSRF (solo si no existe)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once '../config/database.php';
require_once '../classes/Product.php';
require_once '../functions/helpers.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$conn = $database->getConnection();
$product = new Product($conn);

// Obtener todos los productos para el dashboard
$products = $product->getAll();

// Obtener todas las categorías de la base de datos
$categoriesQuery = "SELECT id, name FROM categories WHERE active = 1";
$categoriesStmt = $conn->prepare($categoriesQuery);
$categoriesStmt->execute();
$categoriesResult = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Crear un array asociativo para acceso rápido por ID
$categories = [];
foreach ($categoriesResult as $category) {
    $categories[$category['id']] = $category['name'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - BeautyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <!-- Header del Admin -->
    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-4">
            <!-- Mobile: Stack vertically, Desktop: Horizontal -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <h1 class="text-xl sm:text-2xl font-bold text-purple-600">
                    <i class="fas fa-cog mr-2"></i>Panel de Administración
                </h1>
                <!-- Navigation buttons - wrap on mobile -->
                <div class="flex flex-wrap gap-2 sm:gap-4">
                    <a href="ver_contacto.php" class="text-gray-600 hover:text-purple-600 text-sm sm:text-base">
                        <i class="fas fa-envelope"></i> <span class="hidden sm:inline">Ver</span> Mensajería
                    </a>
                    <a href="../" class="text-gray-600 hover:text-purple-600 text-sm sm:text-base">
                        <i class="fas fa-store"></i> <span class="hidden sm:inline">Ver</span>                     </a>
                    <a href="logout.php" class="text-red-500 hover:text-red-700 text-sm sm:text-base">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="container mx-auto px-4 py-4 sm:py-8">
        <!-- Header section - responsive flex -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 space-y-4 sm:space-y-0">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Gestión de Productos</h2>
            
            <!-- Action buttons - stack on mobile -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">
                <a href="agregar_categorias.php" class="bg-purple-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-purple-700 text-center text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i> Categorías
                </a>
                <a href="add_product.php" class="bg-purple-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-purple-700 text-center text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i> Nuevo Producto
                </a>
            </div>
        </div>

        <!-- Tabla de productos - responsive container -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Desktop table - hidden on mobile -->
            <div class="hidden lg:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $product['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="../<?php echo $product['image']; ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-10 w-10 rounded-full">
                                    <a href="upload_image.php?id=<?php echo $product['id']; ?>"
                                        class="mt-1 block text-xs text-purple-600 hover:text-purple-800">
                                        <i class="fas fa-camera mr-1"></i>Cambiar
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php
                                    // Verificar si existe la categoría antes de mostrarla
                                    if (isset($categories[$product['category_id']])) {
                                        echo htmlspecialchars($categories[$product['category_id']]);
                                    } else {
                                        echo '<span class="text-red-500">Categoría no encontrada</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile cards - visible only on mobile/tablet -->
            <div class="lg:hidden">
                <?php foreach ($products as $product): ?>
                    <div class="border-b border-gray-200 p-4">
                        <!-- Product header with image and basic info -->
                        <div class="flex items-start space-x-4 mb-3">
                            <div class="flex-shrink-0">
                                <img src="../<?php echo $product['image']; ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                    class="h-16 w-16 sm:h-20 sm:w-20 rounded-lg object-cover">
                                <a href="upload_image.php?id=<?php echo $product['id']; ?>"
                                    class="mt-1 block text-xs text-purple-600 hover:text-purple-800 text-center">
                                    <i class="fas fa-camera mr-1"></i>Cambiar
                                </a>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate pr-2">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h3>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        ID: <?php echo $product['id']; ?>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-lg sm:text-xl font-bold text-purple-600">
                                        $<?php echo number_format($product['price'], 0, ',', '.'); ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <i class="fas fa-tag mr-1"></i>
                                        <?php
                                        if (isset($categories[$product['category_id']])) {
                                            echo htmlspecialchars($categories[$product['category_id']]);
                                        } else {
                                            echo '<span class="text-red-500">Categoría no encontrada</span>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action buttons -->
                        <div class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-gray-100">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>"
                                class="flex-1 bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-lg text-center text-sm font-medium transition-colors">
                                <i class="fas fa-edit mr-2"></i>Editar Producto
                            </a>
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>"
                                class="flex-1 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-center text-sm font-medium transition-colors"
                                onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                <i class="fas fa-trash mr-2"></i>Eliminar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-8 px-4">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-box-open text-4xl sm:text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-base sm:text-lg mb-6">No hay productos registrados.</p>
                    <a href="add_product.php"
                        class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Agregar primer producto
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>