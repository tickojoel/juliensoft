<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Verificar si el usuario ha iniciado sesión (si tu flujo requiere login, mantén esto)
// Si quieres permitir agregar sin login, comenta las siguientes líneas.
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para agregar productos al carrito',
        'login_required' => true
    ]);
    exit();
}

try {
    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['product_id']) || !isset($input['quantity'])) {
        throw new Exception('Datos incompletos');
    }

    $productId = (int)$input['product_id'];
    $quantity = (int)$input['quantity'];
    $options = isset($input['options']) && is_array($input['options']) ? $input['options'] : [];

    // Validar datos
    if ($productId <= 0 || $quantity <= 0) {
        throw new Exception('Valores inválidos');
    }

    // Incluir archivos necesarios
    require_once '../classes/Product.php';

    // Verificar que el producto existe
    $database = new Database();
    $conn = $database->getConnection();
    $product = new Product($conn);
    
    $productData = $product->getById($productId);
    
    if (!$productData) {
        throw new Exception('Producto no encontrado');
    }

    // Inicializar el carrito si no existe
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Normalizar las opciones: solo claves válidas, no vacías y en orden consistente
    $validOptions = [];
    foreach (['size', 'color', 'shoeSize'] as $key) {
        if (isset($options[$key]) && $options[$key] !== '') {
            $validOptions[$key] = (string)$options[$key];
        }
    }
    ksort($validOptions);
    $options = $validOptions;

    // Buscar si el producto con las mismas opciones ya está en el carrito
    $existingIndex = null;
    foreach ($_SESSION['cart'] as $index => $item) {
        $itemOptions = isset($item['options']) && is_array($item['options']) ? $item['options'] : [];
        ksort($itemOptions);

        if ($item['product_id'] == $productId && $itemOptions === $options) {
            $existingIndex = $index;
            break;
        }
    }

    // Verificar stock disponible antes de modificar el carrito
    $currentQuantity = $existingIndex !== null ? (int)$_SESSION['cart'][$existingIndex]['quantity'] : 0;
    if ($currentQuantity + $quantity > $productData['stock']) {
        throw new Exception('Stock insuficiente. Solo quedan ' . $productData['stock'] . ' unidades disponibles.');
    }

    $found = $existingIndex !== null;
    if ($found) {
        $_SESSION['cart'][$existingIndex]['quantity'] = $currentQuantity + $quantity;
    } else {
        $newItem = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'added_at' => time()
        ];

        if (!empty($options)) {
            $newItem['options'] = $options;
        }

        $_SESSION['cart'][] = $newItem;
    }

    // Preparar datos para la respuesta
    $response = [
        'success' => true,
        'message' => $found ? 'Cantidad actualizada en el carrito' : 'Producto agregado al carrito',
        'cart' => $_SESSION['cart'],
        'cart_count' => count($_SESSION['cart']),
        'item' => [
            'product_id' => $productId,
            'quantity' => $quantity,
            'options' => $options
        ]
    ];
    
    // Si hay opciones, agregar detalles adicionales a la respuesta
    if (!empty($options)) {
        $response['item']['options_text'] = [];
        if (isset($options['size'])) {
            $response['item']['options_text'][] = 'Talla: ' . $options['size'];
        }
        if (isset($options['color'])) {
            $response['item']['options_text'][] = 'Color: ' . $options['color'];
        }
        if (isset($options['shoeSize'])) {
            $response['item']['options_text'][] = 'Número: ' . $options['shoeSize'];
        }
    }
    
    // Devolver respuesta exitosa
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>