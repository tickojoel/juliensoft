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

// Verificar si se recibió un ID de producto
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = $_GET['id'];
$database = new Database();
$conn = $database->getConnection();
$product = new Product($conn);

// Obtener información del producto actual
$current_product = $product->getById($product_id);

// Procesar la subida de la imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $upload_dir = '../assets/images/products/';
    
    // Crear directorio si no existe
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['image']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['error_message'] = "Solo se permiten imágenes JPG, PNG, GIF o WEBP";
        header("Location: edit_product.php?id=$product_id");
        exit;
    }

    // Generar un nombre único para el archivo
    $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = "product_$product_id." . $file_ext;
    $file_path = $upload_dir . $file_name;

    // Mover el archivo subido
    if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
        // Actualizar la referencia en la base de datos
        $image_url = 'assets/images/products/' . $file_name;
        $product->updateImage($product_id, $image_url);
        
        $_SESSION['success_message'] = "Imagen actualizada correctamente";
        header("Location: edit_product.php?id=$product_id");
        exit;
    } else {
        $_SESSION['error_message'] = "Error al subir la imagen";
        header("Location: edit_product.php?id=$product_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen - BeautyStore Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-image mr-2"></i>Subir Imagen para <?php echo htmlspecialchars($current_product['name']); ?>
            </h1>
            <a href="edit_product.php?id=<?php echo $product_id; ?>" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden p-6">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Imagen Actual</label>
                    <?php if ($current_product['image']): ?>
                        <img src="../<?php echo htmlspecialchars($current_product['image']); ?>" 
                             alt="Imagen actual" class="h-32 object-cover rounded-lg mb-4">
                    <?php else: ?>
                        <p class="text-gray-500">No hay imagen actual</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="image" class="block text-gray-700 font-medium mb-2">Seleccionar Nueva Imagen</label>
                    <input type="file" id="image" name="image" accept="image/*" required
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-purple-50 file:text-purple-700
                                  hover:file:bg-purple-100">
                    <p class="mt-1 text-sm text-gray-500">Formatos aceptados: JPG, PNG, GIF, WEBP</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50">
                        <i class="fas fa-upload mr-2"></i>Subir Imagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>