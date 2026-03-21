<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Product.php';
header('Content-Type: application/json');

try {
    // Incluir archivos necesarios
    require_once '../config/database.php';
    require_once '../classes/Product.php';

    $database = new Database();
    $conn = $database->getConnection();
    $product = new Product($conn);

    $cartItems = array();
    $total = 0;

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Las claves del carrito pueden ser "productId" o "productId::base64(options)"
        $cartKeys = array_keys($_SESSION['cart']);
        $productIdMap = []; // productId => array of cartKeys
        foreach ($cartKeys as $key) {
            if (strpos($key, '::') !== false) {
                list($pid, $opt) = explode('::', $key, 2);
            } else {
                $pid = $key;
            }
            $pid = (int)$pid;
            if ($pid > 0) {
                if (!isset($productIdMap[$pid])) $productIdMap[$pid] = [];
                $productIdMap[$pid][] = $key;
            }
        }

        if (!empty($productIdMap)) {
            $productIds = array_keys($productIdMap);
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            $query = "SELECT id, name, price, discount, image FROM products WHERE id IN ($placeholders) AND active = 1";
            $stmt = $conn->prepare($query);
            $stmt->execute($productIds);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pid = $row['id'];
                foreach ($productIdMap[$pid] as $cartKey) {
                    $quantity = isset($_SESSION['cart'][$cartKey]) ? intval($_SESSION['cart'][$cartKey]) : 0;
                    if ($quantity <= 0) continue;
                    $price = $row['price'] * (1 - $row['discount']/100);
                    $subtotal = $price * $quantity;
                    $total += $subtotal;

                    // Parse options if existe
                    $options = [];
                    if (strpos($cartKey, '::') !== false) {
                        $parts = explode('::', $cartKey, 2);
                        $encoded = $parts[1];
                        $decoded = base64_decode($encoded);
                        $opts = json_decode($decoded, true);
                        if (is_array($opts)) $options = $opts;
                    }

                    $cartItems[] = array(
                        'cart_key' => $cartKey,
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'price' => $price,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'image' => $row['image'],
                        'options' => $options
                    );
                }
            }
        }
    }

    // Generar HTML para los items del carrito
    $html = '';
    foreach ($cartItems as $item) {
        $cartKeyAttr = htmlspecialchars($item['cart_key']);
        $html .= '
        <div class="flex items-center justify-between p-4 border-b border-gray-200" data-cart-key="' . $cartKeyAttr . '">
            <div class="flex items-center space-x-4">
                <img src="' . htmlspecialchars($item['image']) . '" 
                     alt="' . htmlspecialchars($item['name']) . '" 
                     class="w-16 h-16 object-cover rounded-lg">
                <div>
                    <h4 class="font-semibold text-gray-800">' . htmlspecialchars($item['name']) . '</h4>
                    <p class="text-sm text-gray-600">$' . number_format($item['price'], 0, ',', '.') . ' x ' . $item['quantity'] . '</p>
                    ' . (!empty($item['options']) ? '<p class="text-xs text-gray-500">' . htmlspecialchars(json_encode($item['options'])) . '</p>' : '') . '
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <button onclick="updateQuantityJS(\'' . $cartKeyAttr . '\', -1)" 
                            class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-full transition-colors">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-8 text-center font-semibold">' . $item['quantity'] . '</span>
                    <button onclick="updateQuantityJS(\'' . $cartKeyAttr . '\', 1)" 
                            class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-full transition-colors">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <span class="font-bold text-lg text-green-600">$' . number_format($item['subtotal'], 0, ',', '.') . '</span>
                <button onclick="removeFromCartJS(\'' . $cartKeyAttr . '\')" 
                        class="text-red-500 hover:text-red-700 transition-colors ml-2" 
                        title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>';
    }

    echo json_encode(array(
        'success' => true,
        'html' => $html,
        'total' => $total,
        'count' => count($cartItems)
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>