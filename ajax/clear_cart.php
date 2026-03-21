<?php
session_start();
header('Content-Type: application/json');

try {
    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Vaciar el carrito
    $_SESSION['cart'] = array();

    // Respuesta exitosa
    echo json_encode(array(
        'success' => true,
        'cart' => $_SESSION['cart'],
        'count' => 0,
        'message' => 'Carrito vaciado exitosamente'
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>