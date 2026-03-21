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
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Verificar si el producto con las mismas opciones ya está en el carrito
    $found = false;
    
    // Asegurarse de que $options sea un array
    if (!is_array($options)) {
        $options = [];
    }
    
    // Ordenar las opciones para comparación consistente
    ksort($options);
    
    foreach ($_SESSION['cart'] as &$item) {
        $itemOptions = isset($item['options']) && is_array($item['options']) ? $item['options'] : [];
        ksort($itemOptions);
        
        // Verificar si es el mismo producto con las mismas opciones
        if ($item['product_id'] == $productId && $itemOptions === $options) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    // Si el producto no está en el carrito o tiene opciones diferentes, agregarlo como nuevo ítem
    if (!$found) {
        // Asegurarse de que solo guardamos las opciones válidas (talla, color, shoeSize)
        $validOptions = [];
        $validKeys = ['size', 'color', 'shoeSize'];
        
        foreach ($validKeys as $key) {
            if (isset($options[$key]) && !empty($options[$key])) {
                $validOptions[$key] = $options[$key];
            }
        }
        
        // Agregar el ítem al carrito
        $newItem = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'added_at' => time()
        ];
        
        // Solo agregar opciones si hay alguna
        if (!empty($validOptions)) {
            $newItem['options'] = $validOptions;
        }
        
        $_SESSION['cart'][] = $newItem;
        $found = true; // Para el mensaje de éxito
    }

    // Verificar stock disponible
    $totalQuantity = $quantity;
    
    // Sumar la cantidad de productos idénticos ya en el carrito
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if ($item['product_id'] == $productId) {
                $itemOptions = isset($item['options']) && is_array($item['options']) ? $item['options'] : [];
                
                // Si las opciones coinciden, sumar la cantidad
                if ($itemOptions === $options) {
                    $totalQuantity += $item['quantity'];
                }
            }
        }
    }
    
    if ($totalQuantity > $productData['stock']) {
        throw new Exception('Stock insuficiente. Solo quedan ' . $productData['stock'] . ' unidades disponibles.');
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