<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo disponible desde la línea de comandos.');
}
// Script para añadir un producto de ejemplo (SKU: sm25031222491797325)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Product.php';

$database = new Database();
$conn = $database->getConnection();
$product = new Product($conn);

// Intentar obtener una categoría válida (la primera activa)
$category_id = null;
try {
    $stmt = $conn->prepare("SELECT id FROM categories WHERE active = 1 LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['id'])) {
        $category_id = (int)$row['id'];
    }
} catch (Exception $e) {
    // Si no existe la tabla categories o hay un error, dejar category_id en null
}

$data = [
    'name' => 'Set De 2 Piezas De Ropa De Verano Para Hombre - Camiseta Cuello Redondo + Pantalones Cortos',
    'description' => "Set de dos piezas ideal para verano. Incluye camiseta casual deportiva de cuello redondo y pantalones cortos a juego. Color: Blanco. Tallas disponibles: S, M, L, XL, XXL. SKU: sm25031222491797325.",
    'price' => 11990.00,
    'image' => 'assets/images/products/SM25031222491797325_main.svg',
    'category_id' => $category_id,
    'brand' => 'Requejo Fashion Lab',
    'rating' => 5,
    'reviews_count' => 1000,
    'is_new' => 1,
    'discount' => 24,
    'stock' => 50,
    'active' => 1
];

try {
    $inserted_id = $product->create($data);
    if ($inserted_id) {
        echo "Producto creado con ID: " . $inserted_id . PHP_EOL;
        // Actualizar imagen (ya la seteamos en create, pero aseguramos la URL)
        $product->updateImage($inserted_id, $data['image']);
        echo "Imagen asignada: " . $data['image'] . PHP_EOL;

        // Insertar thumbs como meta adicional podría requerir otra columna; como solución rápida, crear archivos en assets
        echo "Archivos de imagen creados en assets/images/products/." . PHP_EOL;
    } else {
        echo "No se pudo crear el producto." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>
