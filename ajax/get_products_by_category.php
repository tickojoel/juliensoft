<?php
session_start();
header('Content-Type: application/json');

try {
    if (!isset($_GET['category_id'])) {
        throw new Exception('ID de categoría requerido');
    }

    $categoryId = (int)$_GET['category_id'];
    
    if ($categoryId <= 0) {
        throw new Exception('ID de categoría inválido');
    }

    // Incluir archivos necesarios
    require_once '../config/database.php';
    
    $database = new Database();
    $conn = $database->getConnection();

    // Obtener información de la categoría
    $categoryQuery = "SELECT name FROM categories WHERE id = ?";
    $categoryStmt = $conn->prepare($categoryQuery);
    $categoryStmt->execute([$categoryId]);
    $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        throw new Exception('Categoría no encontrada');
    }

    // Verificar si existe la columna category_id en products
    $checkCategoryColumn = $conn->query("SHOW COLUMNS FROM products LIKE 'category_id'");
    $hasCategoryColumn = $checkCategoryColumn->rowCount() > 0;

    if (!$hasCategoryColumn) {
        // Si no existe la columna, agregar todos los productos
        $query = "SELECT * FROM products WHERE 1=1";
        $params = [];
    } else {
        // Filtrar por categoría
        $query = "SELECT * FROM products WHERE category_id = ?";
        $params = [$categoryId];
    }

    // Verificar si existe columna active
    $checkActiveColumn = $conn->query("SHOW COLUMNS FROM products LIKE 'active'");
    $hasActiveColumn = $checkActiveColumn->rowCount() > 0;
    
    if ($hasActiveColumn) {
        $query .= $hasCategoryColumn ? " AND active = 1" : " AND active = 1";
    }

    $query .= " ORDER BY id DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generar HTML para los productos
    $html = '<div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-800">Productos de ' . htmlspecialchars($category['name']) . '</h3>
                <div class="w-16 h-1 bg-blue-500 mx-auto mt-2"></div>
             </div>';

    if (empty($products)) {
        $html .= '<div class="text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-600 mb-2">No hay productos disponibles</h4>
                    <p class="text-gray-500">En esta categoría aún no tenemos productos disponibles.</p>
                  </div>';
    } else {
        $html .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';
        
        foreach ($products as $product) {
            $price = $product['price'];
            $discount = isset($product['discount']) ? $product['discount'] : 0;
            $finalPrice = $price * (1 - $discount/100);
            
            $html .= '
            <div class="product-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative">
                    <img src="' . htmlspecialchars($product['image'] ?? 'img/default-product.jpg') . '" 
     alt="' . htmlspecialchars($product['name']) . '"
     class="w-full h-48 bg-white object-contain sm:object-cover">
                    
                    ' . ($discount > 0 ? '<span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">-' . $discount . '%</span>' : '') . '
                    
                    <button onclick="addToCart(' . $product['id'] . ')" 
                            class="absolute top-2 right-2 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-blue-50 transition-colors">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </button>
                </div>
                
                <div class="p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">' . htmlspecialchars($product['name']) . '</h4>
                    
                    ' . (isset($product['description']) ? '<p class="text-sm text-gray-600 mb-3">' . htmlspecialchars(substr($product['description'], 0, 80)) . '...</p>' : '') . '
                    
                    <div class="flex items-center justify-between">
                        <div class="price-container">';
                        
            if ($discount > 0) {
                $html .= '
                            <span class="text-lg font-bold text-green-600">$' . number_format($finalPrice, 0, ',', '.') . '</span>
                            <span class="text-sm text-gray-500 line-through ml-2">$' . number_format($price, 0, ',', '.') . '</span>';
            } else {
                $html .= '<span class="text-lg font-bold text-gray-800">$' . number_format($price, 0, ',', '.') . '</span>';
            }
            
            $html .= '
                        </div>
                        <button onclick="addToCart(' . $product['id'] . ')" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>';
        }
        
        $html .= '</div>';
    }

    echo json_encode([
        'success' => true,
        'html' => $html,
        'category' => $category['name'],
        'count' => count($products)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>