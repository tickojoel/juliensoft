<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Product.php';

header('Content-Type: application/json');

try {
    // Verificar que se haya proporcionado un ID de producto
    if (!isset($_GET['id'])) {
        throw new Exception('ID de producto no proporcionado');
    }

    $productId = (int)$_GET['id'];
    
    // Validar ID
    if ($productId <= 0) {
        throw new Exception('ID de producto no válido');
    }
    
    // Obtener el producto de la base de datos
    $database = new Database();
    $conn = $database->getConnection();
    $product = new Product($conn);
    
    $productData = $product->getById($productId);
    
    if (!$productData) {
        throw new Exception('Producto no encontrado');
    }
    
    // Formatear la respuesta
    $response = [
        'success' => true,
        'id' => $productData['id'],
        'name' => $productData['name'],
        'description' => $productData['description'],
        'price' => (float)$productData['price'],
        'image' => !empty($productData['image']) ? 'images/products/' . $productData['image'] : 'images/placeholder-product.jpg',
        'stock' => (int)$productData['stock'],
        'category_id' => (int)$productData['category_id'],
        'category_name' => $productData['category_name'] ?? '',
        'discount' => (float)$productData['discount']
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
