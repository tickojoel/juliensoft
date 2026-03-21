<?php
session_start();
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

// Obtener el ID del producto a editar
$product_id = isset($_GET['id']) ? $_GET['id'] : die('ID de producto no especificado');

// Obtener los datos actuales del producto
$current_product = $product->getById($product_id);

// Si no existe el producto
if (!$current_product) {
    header('Location: index.php');
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar los datos del formulario
    $data = [
        'name' => htmlspecialchars(strip_tags($_POST['name'])),
        'description' => htmlspecialchars(strip_tags($_POST['description'])),
        'price' => htmlspecialchars(strip_tags($_POST['price'])),
        'image' => !empty($_POST['image']) 
            ? htmlspecialchars(strip_tags($_POST['image'])) 
            : $current_product['image'], // conservar imagen actual si no se modifica
        'category_id' => htmlspecialchars(strip_tags($_POST['category_id'])),
        'brand' => htmlspecialchars(strip_tags($_POST['brand'])),
        'rating' => isset($_POST['rating']) ? htmlspecialchars(strip_tags($_POST['rating'])) : 0,
        'reviews_count' => isset($_POST['reviews_count']) ? htmlspecialchars(strip_tags($_POST['reviews_count'])) : 0,
        'is_new' => isset($_POST['is_new']) ? 1 : 0,
        'discount' => htmlspecialchars(strip_tags($_POST['discount'])),
        'stock' => htmlspecialchars(strip_tags($_POST['stock']))
    ];

    // Actualizar el producto
    if ($product->update($product_id, $data)) {
        $_SESSION['success_message'] = "Producto actualizado correctamente";
        header('Location: index.php');
        exit;
    } else {
        $error_message = "Error al actualizar el producto";
    }
}

// Obtener categorías para el select
$categories = getProductsByCategory();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - BeautyStore Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2"></i>Editar Producto
        </h1>
        <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna Izquierda -->
                <div>
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-medium mb-2">Nombre del Producto</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($current_product['name']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 font-medium mb-2">Descripción</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"><?php echo htmlspecialchars($current_product['description']); ?></textarea>
                    </div>

                    <!-- Precio -->
                    <div class="mb-4">
                        <label for="price" class="block text-gray-700 font-medium mb-2">Precio</label>
                        <input type="number" step="0.01" id="price" name="price" required 
                               value="<?php echo htmlspecialchars($current_product['price']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4">
                        <label for="image" class="block text-gray-700 font-medium mb-2">URL de la Imagen</label>
                        <input type="text" id="image" name="image" 
                               value="<?php echo htmlspecialchars($current_product['image']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                        <?php if ($current_product['image']): ?>
                            <div class="mt-2">
                                <img src="<?php echo htmlspecialchars($current_product['image']); ?>" 
                                     alt="Vista previa" class="h-32 object-cover rounded">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div>
                    <!-- Categoría -->
                    <div class="mb-4">
                        <label for="category_id" class="block text-gray-700 font-medium mb-2">Categoría</label>
                        <select id="category_id" name="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <?php foreach ($categories as $id => $category): ?>
                                <option value="<?php echo $id; ?>" 
                                    <?php echo $id == $current_product['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Marca -->
                    <div class="mb-4">
                        <label for="brand" class="block text-gray-700 font-medium mb-2">Marca</label>
                        <input type="text" id="brand" name="brand" 
                               value="<?php echo htmlspecialchars($current_product['brand']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Rating -->
                    <div class="mb-4">
                        <label for="rating" class="block text-gray-700 font-medium mb-2">Rating (0-5)</label>
                        <input type="number" min="0" max="5" step="0.1" id="rating" name="rating" 
                               value="<?php echo htmlspecialchars($current_product['rating']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Reviews Count -->
                    <div class="mb-4">
                        <label for="reviews_count" class="block text-gray-700 font-medium mb-2">Número de Reseñas</label>
                        <input type="number" min="0" id="reviews_count" name="reviews_count" 
                               value="<?php echo htmlspecialchars($current_product['reviews_count']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Descuento -->
                    <div class="mb-4">
                        <label for="discount" class="block text-gray-700 font-medium mb-2">Descuento (%)</label>
                        <input type="number" min="0" max="100" id="discount" name="discount" 
                               value="<?php echo htmlspecialchars($current_product['discount']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Stock -->
                    <div class="mb-4">
                        <label for="stock" class="block text-gray-700 font-medium mb-2">Stock*</label>
                        <input type="number" min="0" id="stock" name="stock" required 
                               value="<?php echo htmlspecialchars($current_product['stock']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>

                    <!-- Es nuevo -->
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_new" class="rounded text-purple-600" 
                                <?php echo $current_product['is_new'] ? 'checked' : ''; ?>>
                            <span class="ml-2 text-gray-700">Producto Nuevo</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
