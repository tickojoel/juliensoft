<?php
class Product {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

   public function getAll($category_id = null, $search = '') {
    $query = "SELECT p.*, c.name as category_name FROM " . $this->table_name . " p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    
    if ($category_id !== null && $category_id !== 'all') {
        $query .= " AND p.category_id = :category_id";
    }
    
    if (!empty($search)) {
        $query .= " AND (p.name LIKE :search OR p.description LIKE :search OR p.brand LIKE :search)";
    }
    
    $stmt = $this->conn->prepare($query);
    
    if ($category_id !== null && $category_id !== 'all') {
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    }
    
    if (!empty($search)) {
        $search_term = "%$search%";
        $stmt->bindParam(':search', $search_term);
    }
    
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Obtener un producto por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un producto
   public function update($id, $data) {
    $query = "UPDATE " . $this->table_name . " SET 
                name = :name,
                description = :description,
                price = :price,
                image = :image,
                category_id = :category_id,
                brand = :brand,
                rating = :rating,
                reviews_count = :reviews_count,
                is_new = :is_new,
                discount = :discount,
                stock = :stock
              WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    // Convertir tipos de datos adecuadamente
    $price = (float)$data['price'];
    $category_id = (int)$data['category_id'];
    $rating = (float)$data['rating'];
    $reviews_count = (int)$data['reviews_count'];
    $is_new = isset($data['is_new']) ? 1 : 0;
    $discount = (int)$data['discount'];
    $stock = (int)$data['stock'];

    // Bind parameters con tipos específicos
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':image', $data['image'], PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':brand', $data['brand'], PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':reviews_count', $reviews_count, PDO::PARAM_INT);
    $stmt->bindParam(':is_new', $is_new, PDO::PARAM_INT);
    $stmt->bindParam(':discount', $discount, PDO::PARAM_INT);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}

    // Método adicional para eliminar un producto
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateImage($id, $image_url) {
        $query = "UPDATE " . $this->table_name . " SET image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':image', $image_url, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Crear un nuevo producto
   public function create($data) {
    $query = "INSERT INTO " . $this->table_name . " 
              (name, description, price, image, category_id, brand, 
               rating, reviews_count, is_new, discount, stock, active)
              VALUES 
              (:name, :description, :price, :image, :category_id, :brand, 
               :rating, :reviews_count, :is_new, :discount, :stock, :active)";

    $stmt = $this->conn->prepare($query);

    // Establecer valores por defecto si no están presentes
    $data['rating'] = $data['rating'] ?? 0;
    $data['reviews_count'] = $data['reviews_count'] ?? 0;
    $data['is_new'] = $data['is_new'] ?? 0;
    $data['discount'] = $data['discount'] ?? 0;
    $data['stock'] = $data['stock'] ?? 0;
    $data['active'] = $data['active'] ?? 1;

    // Bind parameters
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':price', $data['price']);
    $stmt->bindParam(':image', $data['image']);
    $stmt->bindParam(':category_id', $data['category_id']);
    $stmt->bindParam(':brand', $data['brand']);
    $stmt->bindParam(':rating', $data['rating']);
    $stmt->bindParam(':reviews_count', $data['reviews_count']);
    $stmt->bindParam(':is_new', $data['is_new']);
    $stmt->bindParam(':discount', $data['discount']);
    $stmt->bindParam(':stock', $data['stock']);
    $stmt->bindParam(':active', $data['active']);

    if ($stmt->execute()) {
        return $this->conn->lastInsertId();
    }
    return false;
}
}