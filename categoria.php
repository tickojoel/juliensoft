<?php
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'config/database.php';

// Validar ID de categoría
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: categorias.php');
    exit;
}

$category_id = (int)$_GET['id'];

try {
    $db = new PDO("mysql:host=localhost;dbname=beautystore;charset=utf8", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $product = new Product($db);
    $category = new Category($db);
    
    // Obtener información de la categoría
    $currentCategory = $category->getById($category_id);
    if (!$currentCategory) {
        header('Location: categorias.php');
        exit;
    }
    
    // Obtener productos de esta categoría
    $products = $product->getAll($category_id, '');
    
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($currentCategory['name']) ?> - Tienda Requejo Fashion Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header/Navbar (igual que en categorias.php) -->
    
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($currentCategory['name']) ?></h1>
        <?php if (!empty($currentCategory['description'])): ?>
            <p class="text-gray-600 mb-8"><?= htmlspecialchars($currentCategory['description']) ?></p>
        <?php endif; ?>
        
        <?php if (empty($products)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">No hay productos en esta categoría.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Mostrar productos igual que en ofertas.php -->
            </div>
        <?php endif; ?>
    </main>
    
    <!-- Footer (igual que en categorias.php) -->
</body>
</html>