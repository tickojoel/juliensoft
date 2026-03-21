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
    $change = isset($input['change']) ? (int)$input['change'] : null;
    if ($change === null) throw new Exception('Datos incompletos');

    $cartKey = null;
    $productId = null;
    if (isset($input['cart_key'])) {
        $cartKey = $input['cart_key'];
        // extraer productId si la clave tiene formato productId::...
        if (strpos($cartKey, '::') !== false) {
            list($pid,) = explode('::', $cartKey, 2);
            $productId = (int)$pid;
        } else {
            $productId = (int)$cartKey;
            $cartKey = (string)$productId;
        }
    } elseif (isset($input['product_id'])) {
        $productId = (int)$input['product_id'];
        $cartKey = (string)$productId;
    } else {
        throw new Exception('Datos incompletos');
    }

    // Validar datos
    if ($productId <= 0) {
        throw new Exception('ID de producto inválido');
    }

    // Verificar que el carrito existe y contiene la clave
    if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$cartKey])) {
        throw new Exception('Producto no encontrado en el carrito');
    }

    // Incluir archivos necesarios
    require_once '../config/database.php';
    require_once '../classes/Product.php';

    // Verificar stock disponible si se aumenta la cantidad
    if ($change > 0) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT stock FROM products WHERE id = ? AND active = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$productId]);
        
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$productData) {
            throw new Exception('Producto no encontrado');
        }

        $newQuantity = $_SESSION['cart'][$cartKey] + $change;
        
        if ($newQuantity > $productData['stock']) {
            throw new Exception('Stock insuficiente');
        }
    }

    // Actualizar cantidad
    $_SESSION['cart'][$cartKey] += $change;

    // Si la cantidad es 0 o menor, eliminar del carrito
    if ($_SESSION['cart'][$cartKey] <= 0) {
        unset($_SESSION['cart'][$cartKey]);
    }

    // Respuesta exitosa
    echo json_encode(array(
        'success' => true,
        'cart' => $_SESSION['cart'],
        'count' => count($_SESSION['cart']),
        'message' => 'Cantidad actualizada'
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>