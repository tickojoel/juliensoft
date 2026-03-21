<?php
session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Simular datos de pedidos (en un caso real, esto vendría de la base de datos)
$orders = [
    [
        'id' => 'ORD-1001',
        'date' => '2025-08-15',
        'status' => 'Completado',
        'total' => 129.99,
        'items' => 2,
        'tracking' => 'ENTREGADO',
        'status_class' => 'bg-green-100 text-green-800'
    ],
    [
        'id' => 'ORD-1002',
        'date' => '2025-08-20',
        'status' => 'En camino',
        'total' => 89.50,
        'items' => 1,
        'tracking' => 'EN CAMINO',
        'status_class' => 'bg-blue-100 text-blue-800'
    ],
    [
        'id' => 'ORD-1003',
        'date' => '2025-08-25',
        'status' => 'Procesando',
        'total' => 199.99,
        'items' => 3,
        'tracking' => 'EN PROCESO',
        'status_class' => 'bg-yellow-100 text-yellow-800'
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
        }
        .order-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Mis Pedidos</h1>
                    <p class="text-gray-600">Revisa el estado de tus compras recientes</p>
                </div>
                <div class="mt-4 md:mt-0
                ">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-print mr-2"></i>Imprimir lista de pedidos
                    </button>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-bag text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay pedidos recientes</h3>
                    <p class="text-gray-500 mb-6">Aún no has realizado ningún pedido en nuestra tienda.</p>
                    <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors">
                        Comprar ahora
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                    <div class="mb-2 md:mb-0">
                                        <div class="text-sm text-gray-500">Pedido #<?php echo $order['id']; ?></div>
                                        <div class="text-xs text-gray-400">Realizado el <?php echo date('d/m/Y', strtotime($order['date'])); ?></div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $order['status_class']; ?>">
                                            <?php echo $order['tracking']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-lg font-medium"><?php echo $order['items']; ?> <?php echo $order['items'] > 1 ? 'productos' : 'producto'; ?></div>
                                        <div class="text-sm text-gray-500">Total: <span class="font-medium text-gray-900">S/<?php echo number_format($order['total'], 2); ?></span></div>
                                    </div>
                                    <div class="space-x-2">
                                        <a href="detalle-pedido.php?id=<?php echo $order['id']; ?>" class="inline-block border border-gray-300 rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Ver detalles
                                        </a>
                                        <button class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                            Rastrear pedido
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <div class="mt-8 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                        <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">1</span> a <span class="font-medium">3</span> de <span class="font-medium">3</span> resultados
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <span class="sr-only">Anterior</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </a>
                                <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">1</a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">2</a>
                                <a href="#" class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex">3</a>
                                <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <span class="sr-only">Siguiente</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
