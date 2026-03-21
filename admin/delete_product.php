<?php
session_start();

// Verificación CSRF
if (!isset($_GET['csrf_token']) || !isset($_SESSION['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    die('Token CSRF inválido. Por favor, recarga la página e intenta nuevamente.');
}

require_once '../config/database.php';
require_once '../classes/Product.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Validar ID del producto
if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    die('ID de producto no proporcionado');
}

$product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($product_id === false || $product_id <= 0) {
    header('HTTP/1.1 400 Bad Request');
    die('ID de producto no válido');
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    $product = new Product($conn);
    
    // Verificar si el producto existe
    $existingProduct = $product->getById($product_id);
    if (!$existingProduct) {
        header('HTTP/1.1 404 Not Found');
        die('Producto no encontrado');
    }
    
    // Eliminar imagen asociada si existe
    if (!empty($existingProduct['image'])) {
        $imagePath = '../' . $existingProduct['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Eliminar el producto
    if ($product->delete($product_id)) {
        // Registrar la acción
        $logMessage = sprintf(
            "Usuario eliminó el producto ID %d (%s) el %s",
            $product_id,
            $existingProduct['name'],
            date('Y-m-d H:i:s')
        );
        file_put_contents('../logs/admin_actions.log', $logMessage . PHP_EOL, FILE_APPEND);
        
        $_SESSION['success_message'] = 'Producto eliminado correctamente';
        header('Location: index.php');
        exit();
    }
    
    throw new Exception('Error al eliminar el producto');
    
} catch(PDOException $e) {
    error_log('Error de BD al eliminar producto: ' . $e->getMessage());
    $_SESSION['error_message'] = 'Error al eliminar el producto';
    header('Location: index.php');
    exit();
} catch(Exception $e) {
    error_log('Error al eliminar producto: ' . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: index.php');
    exit();
}