<?php
session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirigir a login si no está autenticado
    exit();
}

// Obtener datos del usuario
$user = [
    'name' => $_SESSION['user_name'] ?? 'Usuario',
    'email' => $_SESSION['user_email'] ?? '',
    'join_date' => $_SESSION['user_created_at'] ?? date('Y-m-d')
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
        }
        .profile-header {
            background: var(--primary-gradient);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Encabezado del perfil -->
            <div class="profile-header text-white rounded-lg p-6 mb-8 shadow-lg">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white bg-opacity-20 flex items-center justify-center mb-4 md:mb-0 md:mr-8">
                        <i class="fas fa-user text-4xl text-white opacity-80"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-2xl md:text-3xl font-bold"><?php echo htmlspecialchars($user['name']); ?></h1>
                        <p class="text-blue-100"><?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="text-blue-100 text-sm mt-1">Miembro desde <?php echo date('M Y', strtotime($user['join_date'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Sección de menú -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="pedidos.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Mis Pedidos</h3>
                        <p class="text-sm text-gray-500">Revisa el estado de tus compras</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                </a>

                <a href="lista-deseos.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow flex items-center">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <i class="fas fa-heart text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Lista de Deseos</h3>
                        <p class="text-sm text-gray-500">Tus productos favoritos</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                </a>

                <a href="carrito.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Carrito</h3>
                        <p class="text-sm text-gray-500">Tus productos seleccionados</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                </a>
            </div>

            <!-- Información de la cuenta -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información de la Cuenta</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-gray-600">Nombre completo</p>
                            <p class="font-medium"><?php echo htmlspecialchars($user['name']); ?></p>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</button>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-gray-600">Correo electrónico</p>
                            <p class="font-medium"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Cambiar</button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600">Contraseña</p>
                            <p class="font-medium">•••••••••</p>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Cambiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Aquí puedes agregar cualquier funcionalidad de JavaScript que necesites
        document.addEventListener('DOMContentLoaded', function() {
            // Inicialización de componentes si es necesario
        });
    </script>
</body>
</html>
