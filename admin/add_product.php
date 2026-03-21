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

// Función para obtener categorías activas
function getActiveCategories($conn) {
    try {
        $query = "SELECT id, name, description, icon, color FROM categories WHERE active = 1 ORDER BY name ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error al obtener categorías: " . $e->getMessage());
        return [];
    }
}

// Obtener categorías para el select
$categories = getActiveCategories($conn);

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar los datos del formulario
    $data = [
    'name' => htmlspecialchars(strip_tags($_POST['name'])),
    'description' => htmlspecialchars(strip_tags($_POST['description'])),
    'price' => htmlspecialchars(strip_tags($_POST['price'])),
    'image' => 'assets/images/products/default.jpg', // Imagen por defecto
    'category_id' => htmlspecialchars(strip_tags($_POST['category_id'])),
    'brand' => htmlspecialchars(strip_tags($_POST['brand'])),
    'rating' => 0, // Valor inicial
    'reviews_count' => 0, // Valor inicial
    'is_new' => isset($_POST['is_new']) ? 1 : 0,
    'discount' => htmlspecialchars(strip_tags($_POST['discount'])),
    'stock' => htmlspecialchars(strip_tags($_POST['stock'])) // Nuevo campo
];

    // Insertar el nuevo producto
    $product_id = $product->create($data);
    
    if ($product_id) {
        // Manejar la subida de imagen si se proporcionó
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/products/';
            
            // Crear directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['image']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                // Generar un nombre único para el archivo
                $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = "product_$product_id." . $file_ext;
                $file_path = $upload_dir . $file_name;

                // Mover el archivo subido
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    // Actualizar la referencia en la base de datos
                    $image_url = 'assets/images/products/' . $file_name;
                    $product->updateImage($product_id, $image_url);
                }
            }
        }

        $_SESSION['success_message'] = "Producto creado correctamente";
        header('Location: index.php');
        exit;
    } else {
        $error_message = "Error al crear el producto";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - BeautyStore Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle mr-2"></i>Agregar Nuevo Producto
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

        <!-- Mostrar información de debug si no hay categorías -->
        <?php if (empty($categories)): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <div>
                        <strong>No se encontraron categorías activas.</strong>
                        <p>Verifica que:</p>
                        <ul class="list-disc list-inside mt-2">
                            <li>La tabla 'categories' existe en tu base de datos</li>
                            <li>Hay categorías con active = 1</li>
                            <li>La conexión a la base de datos funciona correctamente</li>
                        </ul>
                        <p class="mt-2">
                            <a href="agregar_categorias.php" class="text-blue-600 hover:underline">
                                <i class="fas fa-plus mr-1"></i>Agregar categorías aquí
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form method="POST" enctype="multipart/form-data" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna Izquierda -->
                    <div>
                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Nombre del Producto*</label>
                            <input type="text" id="name" name="name" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 font-medium mb-2">Descripción*</label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"></textarea>
                        </div>

                        <!-- Precio -->
                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 font-medium mb-2">Precio*</label>
                            <input type="number" step="0.01" id="price" name="price" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>

                        <!-- Imagen -->
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Imagen del Producto</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-purple-50 file:text-purple-700
                                          hover:file:bg-purple-100">
                            <p class="mt-1 text-sm text-gray-500">Formatos aceptados: JPG, PNG, GIF, WEBP</p>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div>
                        <!-- Categoría -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-gray-700 font-medium mb-2">
                                Categoría*
                                <?php if (empty($categories)): ?>
                                    <span class="text-red-500 text-sm">(No hay categorías disponibles)</span>
                                <?php endif; ?>
                            </label>
                            <select id="category_id" name="category_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                                    <?php echo empty($categories) ? 'disabled' : ''; ?>>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                        <?php if (!empty($category['icon'])): ?>
                                            <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i> 
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($categories)): ?>
                                <p class="mt-1 text-sm text-red-500">
                                    Necesitas crear categorías antes de agregar productos.
                                    <a href="agregar_categorias.php" class="text-blue-600 hover:underline">Crear categorías</a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Marca -->
                        <div class="mb-4">
                            <label for="brand" class="block text-gray-700 font-medium mb-2">Marca*</label>
                            <input type="text" id="brand" name="brand" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>

                        <!-- Descuento -->
                        <div class="mb-4">
                            <label for="discount" class="block text-gray-700 font-medium mb-2">Descuento (%)</label>
                            <input type="number" min="0" max="100" id="discount" name="discount" value="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>

                        <!-- Stock -->
<div class="mb-4">
    <label for="stock" class="block text-gray-700 font-medium mb-2">Stock*</label>
    <input type="number" min="0" id="stock" name="stock" required value="0"
           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600">
</div>

                        <!-- Es nuevo -->
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_new" class="rounded text-purple-600" checked>
                                <span class="ml-2 text-gray-700">Marcar como producto nuevo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" 
                            class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50 <?php echo empty($categories) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                            <?php echo empty($categories) ? 'disabled title="Necesitas crear categorías primero"' : ''; ?>>
                        <i class="fas fa-save mr-2"></i>Guardar Producto
                    </button>
                    
                    <?php if (empty($categories)): ?>
                        <a href="agregar_categorias.php" class="ml-4 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 inline-block">
                            <i class="fas fa-tags mr-2"></i>Gestionar Categorías
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Debug: Mostrar información de categorías en la consola
        console.log('Categorías cargadas:', <?php echo json_encode($categories); ?>);
        
        <?php if (empty($categories)): ?>
        // Mostrar alerta si no hay categorías
        document.addEventListener('DOMContentLoaded', function() {
            // Opcional: mostrar alerta
            // alert('No hay categorías disponibles. Necesitas crear categorías antes de agregar productos.');
        });
        <?php endif; ?>
    </script>
</body>
</html>