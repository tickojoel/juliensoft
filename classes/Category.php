<?php
class Category {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Método existente para obtener todas las categorías
    public function getAll() {
        $query = "SELECT * FROM categories WHERE active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Método existente para contar productos
    public function getProductCount($categoryId) {
        $query = "SELECT COUNT(*) FROM products WHERE category_id = :category_id AND active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    // Nuevo método para obtener categorías con sus productos
    public function getAllWithProducts() {
        // Primero obtenemos todas las categorías
        $categories = $this->getAll();
        
        // Luego obtenemos los productos para cada categoría
        $productQuery = "SELECT * FROM products WHERE category_id = :category_id AND active = 1";
        $productStmt = $this->db->prepare($productQuery);
        
        foreach ($categories as &$category) {
            $productStmt->bindParam(':category_id', $category['id']);
            $productStmt->execute();
            $category['products'] = $productStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($category); // Romper la referencia
        
        return $categories;
    }
}
?>