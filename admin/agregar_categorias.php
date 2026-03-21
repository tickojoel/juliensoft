<?php
// config.php - Configuración de la base de datos
class Database {
    private $host = 'localhost';
    private $db_name = 'beautystore'; // Cambia por el nombre de tu base de datos
    private $username = 'root';           // Usuario por defecto de XAMPP
    private $password = '';               // Contraseña vacía por defecto en XAMPP
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// CategoryManager.php - Clase para manejar las categorías
class CategoryManager {
    private $conn;
    private $table_name = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, icon, color, active, image) 
                  VALUES (:name, :description, :icon, :color, :active, :image)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':icon', $data['icon']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':active', $data['active']);
        $stmt->bindParam(':image', $data['image']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, icon = :icon, 
                      color = :color, active = :active, image = :image
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':icon', $data['icon']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':active', $data['active']);
        $stmt->bindParam(':image', $data['image']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

// Función helper para formatear el icono
function formatIcon($icon) {
    if (empty($icon)) {
        return 'fas fa-tag'; // Icono por defecto
    }
    
    // Limpiar el icono de espacios en blanco
    $icon = trim($icon);
    
    // Si ya tiene el prefijo fas, fab, far, etc., lo devolvemos tal como está
    if (preg_match('/^(fas|far|fab|fal|fat|fad|fak)\s+/', $icon)) {
        return $icon;
    }
    
    // Si empieza con fa-, lo convertimos a fas
    if (strpos($icon, 'fa-') === 0) {
        return 'fas ' . $icon;
    }
    
    // Mapeo de iconos específicos que podrían tener problemas
    $iconMapping = [
        'running' => 'fas fa-running',
        'dumbbell' => 'fas fa-dumbbell', 
        'swimmer' => 'fas fa-swimmer',
        'futbol' => 'fas fa-futbol',
        'basketball' => 'fas fa-basketball-ball',
        'volleyball' => 'fas fa-volleyball-ball',
        'bicycle' => 'fas fa-bicycle',
        'golf' => 'fas fa-golf-ball',
        'tennis' => 'fas fa-table-tennis'
    ];
    
    // Buscar en el mapeo
    if (isset($iconMapping[$icon])) {
        return $iconMapping[$icon];
    }
    
    // Si no tiene ningún prefijo, agregamos fas fa-
    return 'fas fa-' . $icon;
}

// Inicializar conexión y clase
$database = new Database();
$db = $database->getConnection();
$categoryManager = new CategoryManager($db);

$message = '';
$messageType = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'icon' => $_POST['icon'],
                    'color' => $_POST['color'],
                    'active' => isset($_POST['active']) ? 1 : 0,
                    'image' => $_POST['image']
                ];
                
                if ($categoryManager->create($data)) {
                    $message = 'Categoría creada exitosamente';
                    $messageType = 'success';
                } else {
                    $message = 'Error al crear la categoría';
                    $messageType = 'error';
                }
                break;
                
            case 'update':
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'icon' => $_POST['icon'],
                    'color' => $_POST['color'],
                    'active' => isset($_POST['active']) ? 1 : 0,
                    'image' => $_POST['image']
                ];
                
                if ($categoryManager->update($_POST['id'], $data)) {
                    $message = 'Categoría actualizada exitosamente';
                    $messageType = 'success';
                } else {
                    $message = 'Error al actualizar la categoría';
                    $messageType = 'error';
                }
                break;
                
            case 'delete':
                if ($categoryManager->delete($_POST['id'])) {
                    $message = 'Categoría eliminada exitosamente';
                    $messageType = 'success';
                } else {
                    $message = 'Error al eliminar la categoría';
                    $messageType = 'error';
                }
                break;
        }
    }
}

// Obtener todas las categorías
$categories = $categoryManager->getAll();

// Si se está editando, obtener la categoría específica
$editingCategory = null;
if (isset($_GET['edit'])) {
    $editingCategory = $categoryManager->getById($_GET['edit']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal {
            display: none;
            backdrop-filter: blur(5px);
        }
        
        .modal.show {
            display: flex;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }

        /* Estilos para mostrar iconos en el selector */
        .icon-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-tags text-2xl"></i>
                    <h1 class="text-2xl font-bold">Gestión de Categorías</h1>
                </div>
                <button onclick="openModal()" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Categoría
                </button>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Mensajes -->
        <?php if ($message): ?>
        <div class="mb-6 fade-in">
            <div class="<?php echo $messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Grid de Categorías -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($categories as $category): ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover fade-in">
                <!-- Imagen de la categoría -->
                <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-400 relative overflow-hidden">
                    <?php if ($category['image']): ?>
                        <img src="<?php echo htmlspecialchars($category['image']); ?>" 
                             alt="<?php echo htmlspecialchars($category['name']); ?>"
                             class="w-full h-full object-cover">
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                        <i class="<?php echo formatIcon($category['icon']); ?> text-4xl text-white"></i>
                    </div>
                    <!-- Estado activo/inactivo -->
                    <div class="absolute top-3 right-3">
                        <span class="<?php echo $category['active'] ? 'bg-green-500' : 'bg-red-500'; ?> text-white px-2 py-1 rounded-full text-xs font-semibold">
                            <?php echo $category['active'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </div>
                </div>

                <!-- Contenido de la tarjeta -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-bold <?php echo htmlspecialchars($category['color']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </h3>
                        <div class="flex items-center space-x-2">
                            <i class="<?php echo formatIcon($category['icon']); ?> text-lg <?php echo htmlspecialchars($category['color']); ?>" 
                               style="min-width: 20px; text-align: center;"></i>
                            <span class="text-xs text-gray-500">
                                <?php echo htmlspecialchars($category['icon']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        <?php echo htmlspecialchars($category['description'] ?? 'Sin descripción'); ?>
                    </p>
                    
                    <div class="text-xs text-gray-500 mb-4">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Creado: <?php echo date('d/m/Y', strtotime($category['created_at'])); ?>
                        <br>
                        <span class="text-xs">Icono: <?php echo formatIcon($category['icon']); ?></span>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex space-x-2">
                        <button onclick="editCategory(<?php echo $category['id']; ?>)" 
                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </button>
                        <button onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')" 
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($categories)): ?>
        <div class="text-center py-12">
            <i class="fas fa-tags text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay categorías</h3>
            <p class="text-gray-500 mb-6">Comienza creando tu primera categoría</p>
            <button onclick="openModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                <i class="fas fa-plus mr-2"></i>Crear Primera Categoría
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal para crear/editar categoría -->
    <div id="categoryModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="gradient-bg text-white p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h2 id="modalTitle" class="text-xl font-bold">
                        <i class="fas fa-plus mr-2"></i>Nueva Categoría
                    </h2>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="categoryForm" method="POST" class="p-6">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="categoryId">

                <div class="space-y-4">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1"></i>Nombre *
                        </label>
                        <input type="text" name="name" id="categoryName" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-1"></i>Descripción
                        </label>
                        <textarea name="description" id="categoryDescription" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <!-- Icono -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-icons mr-1"></i>Icono
                            <span id="iconPreview" class="ml-2"></span>
                        </label>
                        <select name="icon" id="categoryIcon"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                onchange="updateIconPreview()">
                            <option value="">Seleccionar icono</option>
                            <option value="fa-running">🏃 Correr (fa-running)</option>
                            <option value="fa-dumbbell">🏋️ Pesas (fa-dumbbell)</option>
                            <option value="fa-swimmer">🏊 Natación (fa-swimmer)</option>
                            <option value="fa-futbol">⚽ Fútbol (fa-futbol)</option>
                            <option value="fa-basketball-ball">🏀 Baloncesto (fa-basketball-ball)</option>
                            <option value="fa-volleyball-ball">🏐 Voleibol (fa-volleyball-ball)</option>
                            <option value="fa-bicycle">🚴 Ciclismo (fa-bicycle)</option>
                            <option value="fa-skating">⛸️ Patinaje (fa-skating)</option>
                            <option value="fa-skiing">🎿 Esquí (fa-skiing)</option>
                            <option value="fa-baseball-ball">⚾ Baseball (fa-baseball-ball)</option>
                            <option value="fa-golf-ball">🏌️ Golf (fa-golf-ball)</option>
                            <option value="fa-table-tennis">🏓 Ping Pong (fa-table-tennis)</option>
                            <option value="fa-heart">❤️ Cardio (fa-heart)</option>
                            <option value="fa-fire">🔥 Fitness (fa-fire)</option>
                            <option value="fa-trophy">🏆 Deportes (fa-trophy)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">O ingresa manualmente un icono de FontAwesome</p>
                        <input type="text" id="customIcon" placeholder="Ej: fa-custom-icon"
                               class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-sm"
                               onchange="updateCustomIcon()">
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-palette mr-1"></i>Color (Clase Tailwind)
                        </label>
                        <select name="color" id="categoryColor"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            <option value="text-blue-600">🔵 Azul</option>
                            <option value="text-red-600">🔴 Rojo</option>
                            <option value="text-green-600">🟢 Verde</option>
                            <option value="text-purple-600">🟣 Púrpura</option>
                            <option value="text-yellow-600">🟡 Amarillo</option>
                            <option value="text-pink-600">🩷 Rosa</option>
                            <option value="text-indigo-600">🟣 Índigo</option>
                            <option value="text-cyan-600">🔵 Cian</option>
                        </select>
                    </div>

                    <!-- Imagen -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image mr-1"></i>URL de Imagen
                        </label>
                        <input type="url" name="image" id="categoryImage" placeholder="https://ejemplo.com/imagen.jpg"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                    </div>

                    <!-- Estado activo -->
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="categoryActive" checked
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="categoryActive" class="ml-2 block text-sm font-semibold text-gray-700">
                            <i class="fas fa-toggle-on mr-1"></i>Categoría activa
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-3 mt-8">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-save mr-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div id="deleteModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full mx-4">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-trash text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Eliminar Categoría</h3>
                <p class="text-gray-600 mb-6">
                    ¿Estás seguro de que deseas eliminar la categoría "<span id="deleteCategoryName" class="font-semibold"></span>"?
                    Esta acción no se puede deshacer.
                </p>
                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition-colors">
                        Cancelar
                    </button>
                    <form method="POST" class="flex-1">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para depurar iconos
        function debugIcons() {
            console.log('=== DEBUG DE ICONOS ===');
            const icons = document.querySelectorAll('i[class*="fa-"]');
            icons.forEach((icon, index) => {
                console.log(`Icono ${index + 1}:`, icon.className);
                console.log('Elemento:', icon);
            });
        }

        // Ejecutar debug al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            debugIcons();
        });

        function updateIconPreview() {
            const iconSelect = document.getElementById('categoryIcon');
            const iconPreview = document.getElementById('iconPreview');
            const selectedIcon = iconSelect.value;
            
            if (selectedIcon) {
                const formattedIcon = selectedIcon.startsWith('fas ') ? selectedIcon : `fas ${selectedIcon}`;
                iconPreview.innerHTML = `<i class="${formattedIcon}"></i>`;
                console.log('Preview icon:', formattedIcon);
            } else {
                iconPreview.innerHTML = '';
            }
        }

        function updateCustomIcon() {
            const customIcon = document.getElementById('customIcon');
            const iconSelect = document.getElementById('categoryIcon');
            const iconPreview = document.getElementById('iconPreview');
            
            if (customIcon.value) {
                iconSelect.value = customIcon.value;
                const formattedIcon = customIcon.value.startsWith('fas ') ? customIcon.value : `fas ${customIcon.value}`;
                iconPreview.innerHTML = `<i class="${formattedIcon}"></i>`;
                console.log('Custom icon:', formattedIcon);
            }
        }

        function openModal(category = null) {
            const modal = document.getElementById('categoryModal');
            const form = document.getElementById('categoryForm');
            const title = document.getElementById('modalTitle');
            
            if (category) {
                // Modo edición
                title.innerHTML = '<i class="fas fa-edit mr-2"></i>Editar Categoría';
                document.getElementById('formAction').value = 'update';
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryName').value = category.name;
                document.getElementById('categoryDescription').value = category.description || '';
                document.getElementById('categoryIcon').value = category.icon || '';
                document.getElementById('categoryColor').value = category.color || '';
                document.getElementById('categoryImage').value = category.image || '';
                document.getElementById('categoryActive').checked = category.active == 1;
                updateIconPreview();
            } else {
                // Modo creación
                title.innerHTML = '<i class="fas fa-plus mr-2"></i>Nueva Categoría';
                form.reset();
                document.getElementById('formAction').value = 'create';
                document.getElementById('categoryActive').checked = true;
                document.getElementById('iconPreview').innerHTML = '';
            }
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('categoryModal');
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function editCategory(id) {
            // En una implementación real, harías una petición AJAX para obtener los datos
            // Por simplicidad, redirigimos con el parámetro edit
            window.location.href = '?edit=' + id;
        }

        function deleteCategory(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteCategoryName').textContent = name;
            document.getElementById('deleteModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Cerrar modales al hacer clic fuera
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        // Si hay una categoría para editar, abrir el modal automáticamente
        <?php if ($editingCategory): ?>
        openModal(<?php echo json_encode($editingCategory); ?>);
        <?php endif; ?>
    </script>
</body>
</html>