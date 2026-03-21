<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Función para enviar respuesta JSON
function sendResponse($success, $message = '', $data = []) {
    $response = ['success' => $success];
    if ($message) $response['message'] = $message;
    if (!empty($data)) $response = array_merge($response, $data);
    echo json_encode($response);
    exit;
}

try {
    // Verificar que el usuario ha iniciado sesión
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Debes iniciar sesión para modificar el carrito');
    }

    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos JSON del cuerpo de la petición
    $json = file_get_contents('php://input');
    if ($json === false) {
        throw new Exception('Error al leer los datos de la petición');
    }

    $input = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar los datos JSON: ' . json_last_error_msg());
    }

    // Validar datos de entrada
    if (!isset($input['index']) || !isset($input['quantity'])) {
        throw new Exception('Datos incompletos. Se requieren índice y cantidad.');
    }

    $index = (int)$input['index'];
    $quantity = (int)$input['quantity'];

    // Validar valores
    if ($index < 0) {
        throw new Exception('Índice de producto no válido');
    }
    
    if ($quantity < 0) {
        throw new Exception('La cantidad no puede ser negativa');
    }

    // Inicializar carrito si no existe
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Verificar si el carrito está vacío
    if (empty($_SESSION['cart'])) {
        throw new Exception('El carrito está vacío');
    }

    // Verificar que el índice existe en el carrito
    if (!array_key_exists($index, $_SESSION['cart'])) {
        throw new Exception('El producto seleccionado no se encuentra en el carrito');
    }

    // Si la cantidad es 0, eliminar el ítem del carrito
    if ($quantity === 0) {
        array_splice($_SESSION['cart'], $index, 1);
        $message = 'Producto eliminado del carrito correctamente';
    } else {
        // Actualizar la cantidad del ítem
        $_SESSION['cart'][$index]['quantity'] = $quantity;
        $message = 'Cantidad actualizada correctamente';
    }

    // Devolver respuesta exitosa con los datos actualizados
    sendResponse(true, $message, [
        'cart' => $_SESSION['cart'],
        'cart_count' => count($_SESSION['cart'])
    ]);

} catch (Exception $e) {
    // Registrar el error en el log del servidor
    error_log('Error en update_cart_item.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    sendResponse(false, 'Error al actualizar el carrito: ' . $e->getMessage());
}
?>
