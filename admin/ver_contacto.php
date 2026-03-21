<?php
// admin/contactos.php
session_start();

// Verificar si el usuario está logueado como admin (implementa tu lógica de autenticación)
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit;
// }

require_once '../config/database.php';

try {
    $host = 'localhost';
    $dbname = 'beautystore';
    $username = 'root';
    $password = '';
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Actualizar estado si se envía
    if (isset($_POST['actualizar_estado'])) {
        $id = (int)$_POST['contacto_id'];
        $nuevo_estado = $_POST['estado'];
        
        $stmt = $db->prepare("UPDATE contactos SET estado = ? WHERE id = ?");
        $stmt->execute([$nuevo_estado, $id]);
    }
    
    // Obtener contactos con paginación
    $page = (int)($_GET['page'] ?? 1);
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    // Filtros
    $filtro_estado = $_GET['estado'] ?? '';
    $where_clause = '';
    $params = [];
    
    if ($filtro_estado) {
        $where_clause = "WHERE estado = ?";
        $params[] = $filtro_estado;
    }
    
    // Contar total de registros
    $count_query = "SELECT COUNT(*) FROM contactos $where_clause";
    $stmt = $db->prepare($count_query);
    $stmt->execute($params);
    $total_contactos = $stmt->fetchColumn();
    $total_pages = ceil($total_contactos / $per_page);
    
    // Obtener contactos
    $query = "SELECT * FROM contactos $where_clause ORDER BY fecha DESC LIMIT $per_page OFFSET $offset";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Contactos - Tienda Requejo Fashion Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-envelope mr-2 text-purple-600"></i>
                Gestión de Contactos
            </h1>
            <a href="index.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-home mr-2"></i>Volver al sitio
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'nuevo' THEN 1 ELSE 0 END) as nuevos,
                SUM(CASE WHEN estado = 'leido' THEN 1 ELSE 0 END) as leidos,
                SUM(CASE WHEN estado = 'respondido' THEN 1 ELSE 0 END) as respondidos
                FROM contactos";
            $stmt = $db->query($stats_query);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Total</p>
                        <p class="text-2xl font-bold"><?= $stats['total'] ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Nuevos</p>
                        <p class="text-2xl font-bold"><?= $stats['nuevos'] ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Leídos</p>
                        <p class="text-2xl font-bold"><?= $stats['leidos'] ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600">Respondidos</p>
                        <p class="text-2xl font-bold"><?= $stats['respondidos'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="flex items-center space-x-4">
                <label class="font-medium text-gray-700">Filtrar por estado:</label>
                <select name="estado" class="border rounded-lg px-3 py-2">
                    <option value="">Todos</option>
                    <option value="nuevo" <?= $filtro_estado === 'nuevo' ? 'selected' : '' ?>>Nuevos</option>
                    <option value="leido" <?= $filtro_estado === 'leido' ? 'selected' : '' ?>>Leídos</option>
                    <option value="respondido" <?= $filtro_estado === 'respondido' ? 'selected' : '' ?>>Respondidos</option>
                </select>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </form>
        </div>

        <!-- Tabla de contactos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($contactos as $contacto): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y H:i', strtotime($contacto['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($contacto['nombre']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="mailto:<?= $contacto['email'] ?>" class="text-purple-600 hover:text-purple-800">
                                    <?= htmlspecialchars($contacto['email']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $contacto['telefono'] ? htmlspecialchars($contacto['telefono']) : '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="contacto_id" value="<?= $contacto['id'] ?>">
                                    <select name="estado" onchange="this.form.submit()" 
                                            class="text-sm border-none rounded-full px-3 py-1 <?= 
                                                $contacto['estado'] === 'nuevo' ? 'bg-red-100 text-red-800' : 
                                                ($contacto['estado'] === 'leido' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') 
                                            ?>">
                                        <option value="nuevo" <?= $contacto['estado'] === 'nuevo' ? 'selected' : '' ?>>Nuevo</option>
                                        <option value="leido" <?= $contacto['estado'] === 'leido' ? 'selected' : '' ?>>Leído</option>
                                        <option value="respondido" <?= $contacto['estado'] === 'respondido' ? 'selected' : '' ?>>Respondido</option>
                                    </select>
                                    <input type="hidden" name="actualizar_estado" value="1">
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="verMensaje(<?= htmlspecialchars(json_encode($contacto)) ?>)" 
                                        class="text-purple-600 hover:text-purple-900 mr-3">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <a href="mailto:<?= $contacto['email'] ?>?subject=Re: Tu consulta&body=Hola <?= $contacto['nombre'] ?>,%0D%0A%0D%0AGracias por contactarnos..." 
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-reply"></i> Responder
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
            <div class="bg-gray-50 px-6 py-3 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando <?= $offset + 1 ?> a <?= min($offset + $per_page, $total_contactos) ?> de <?= $total_contactos ?> resultados
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&estado=<?= $filtro_estado ?>" 
                           class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 px-3 py-2 rounded-md text-sm">
                            Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?= $i ?>&estado=<?= $filtro_estado ?>" 
                           class="<?= $i === $page ? 'bg-purple-600 text-white' : 'bg-white border border-gray-300 text-gray-500 hover:bg-gray-50' ?> px-3 py-2 rounded-md text-sm">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&estado=<?= $filtro_estado ?>" 
                           class="bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 px-3 py-2 rounded-md text-sm">
                            Siguiente
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para ver mensaje completo -->
    <div id="mensajeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Mensaje de Contacto</h3>
                        <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="mensajeContenido" class="space-y-4">
                        <!-- Contenido del mensaje se carga aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verMensaje(contacto) {
            const contenido = document.getElementById('mensajeContenido');
            contenido.innerHTML = `
                <div><strong>Nombre:</strong> ${contacto.nombre}</div>
                <div><strong>Email:</strong> ${contacto.email}</div>
                <div><strong>Teléfono:</strong> ${contacto.telefono || 'No proporcionado'}</div>
                <div><strong>Fecha:</strong> ${new Date(contacto.fecha).toLocaleString()}</div>
                <div><strong>Mensaje:</strong></div>
                <div class="bg-gray-100 p-3 rounded-lg">${contacto.mensaje}</div>
                ${contacto.ip_address ? `<div class="text-sm text-gray-500"><strong>IP:</strong> ${contacto.ip_address}</div>` : ''}
            `;
            document.getElementById('mensajeModal').classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('mensajeModal').classList.add('hidden');
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>