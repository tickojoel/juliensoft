<?php
session_start();
header('Content-Type: application/json');

try {
    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Soportar 'cart_key' (cuando hay opciones) o el antiguo 'product_id'
    $cartKey = null;
    if (isset($input['cart_key'])) {
        $cartKey = $input['cart_key'];
        // si la clave es numérica, usarla como string
        if (!is_string($cartKey)) $cartKey = (string)$cartKey;
    } elseif (isset($input['product_id'])) {
        $cartKey = (string)(int)$input['product_id'];
    } else {
        throw new Exception('ID de producto requerido');
    }

    // Verificar que el carrito existe
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Eliminar producto del carrito por clave
    if (isset($_SESSION['cart'][$cartKey])) {
        unset($_SESSION['cart'][$cartKey]);
        $message = 'Producto eliminado del carrito';
    } else {
        $message = 'Producto no encontrado en el carrito';
    }

    // Respuesta exitosa
    echo json_encode(array(
        'success' => true,
        'cart' => $_SESSION['cart'],
        'count' => count($_SESSION['cart']),
        'message' => $message
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>