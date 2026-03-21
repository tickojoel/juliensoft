<?php
session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Envíos - SportZone</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
            --accent-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --dark-gradient: linear-gradient(135deg, #111827 0%, #374151 100%);
        }

        .gradient-primary {
            background: var(--primary-gradient);
        }

        .gradient-secondary {
            background: var(--secondary-gradient);
        }

        .gradient-accent {
            background: var(--accent-gradient);
        }

        .gradient-dark {
            background: var(--dark-gradient);
        }

        .btn-sport {
            background: var(--primary-gradient);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-sport:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .hero-shipping {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.9) 0%, rgba(6, 182, 212, 0.8) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="shipping-grid" width="60" height="60" patternUnits="userSpaceOnUse"><path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23shipping-grid)"/></svg>');
            background-size: cover, 60px 60px;
            position: relative;
            overflow: hidden;
        }

        .shipping-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .shipping-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .price-highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        .floating-truck::before {
            content: '';
            position: absolute;
            top: 10%;
            right: 5%;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .floating-truck::after {
            content: '';
            position: absolute;
            bottom: 15%;
            left: 5%;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-15px) rotate(180deg);
            }
        }

        .timeline-item {
            transition: all 0.3s ease;
        }

        .timeline-item:hover {
            transform: translateX(10px);
        }

        .zone-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .zone-express {
            border-color: #10b981;
        }

        .zone-standard {
            border-color: #3b82f6;
        }

        .zone-remote {
            border-color: #f59e0b;
        }

        .zone-card:hover {
            border-left-width: 8px;
            transform: translateX(4px);
        }

        .tracking-demo {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px dashed #cbd5e1;
        }

        .faq-item {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .faq-item:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .accordion-content.active {
            max-height: 200px;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .hero-shipping {
                background-size: cover, 40px 40px;
                min-height: 60vh;
            }
        }

        @media (max-width: 640px) {
            .shipping-card {
                padding: 16px;
            }
            
            .hero-shipping {
                min-height: 50vh;
                padding: 40px 0;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-xl sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between py-4">
                <div class="flex items-center justify-between w-full md:w-auto mb-4 md:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                        <i class="fas fa-dumbbell mr-3 text-blue-600"></i>Requejo Fashion Lab
                    </h1>
                    <button id="mobile-menu-button" class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

                <nav id="mobile-menu" class="hidden md:flex flex-col md:flex-row w-full md:w-auto items-center justify-center py-4 md:py-0">
                    <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8 text-center w-full">
                        <a href="index.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Inicio</a>
                        <a href="index.php#products" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Productos</a>
                        <a href="ofertas.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Ofertas</a>
                        <a href="contacto.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Contactarnos</a>
                        <a href="envios.php" class="text-blue-600 hover:text-blue-800 transition-colors font-bold block py-2 md:py-0 w-full md:w-auto border-b-2 border-blue-600 md:border-0">Envíos</a>
                    </div>
                </nav>

                <div class="flex items-center space-x-4 mt-4 md:mt-0 w-full md:w-auto justify-between md:justify-start">
                    <div class="relative w-full md:w-auto">
                        <button onclick="toggleCart()"
                            class="btn-sport text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full hover:shadow-lg transition-all duration-200 font-semibold w-full md:w-auto">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            <span class="hidden sm:inline">Carrito</span>
                            <span id="cart-count"
                                class="ml-2 bg-red-500 text-white rounded-full px-2 py-1 text-xs font-bold">
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-shipping floating-truck min-h-screen flex items-center text-white relative">
        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <i class="fas fa-shipping-fast text-6xl sm:text-8xl mb-6 animate-pulse"></i>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Envíos Rápidos y <span class="text-yellow-400">Seguros</span>
                </h1>
                <p class="text-xl sm:text-2xl mb-8 leading-relaxed opacity-90">
                    Llevamos tu equipamiento deportivo donde lo necesites, con la velocidad y confianza que mereces
                </p>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="backdrop-filter backdrop-blur-lg bg-white bg-opacity-10 rounded-2xl p-6 border border-white border-opacity-20">
                        <div class="text-3xl font-bold text-yellow-400 mb-2">24h</div>
                        <div class="text-sm opacity-90">Envío Express</div>
                    </div>
                    <div class="backdrop-filter backdrop-blur-lg bg-white bg-opacity-10 rounded-2xl p-6 border border-white border-opacity-20">
                        <div class="text-3xl font-bold text-green-400 mb-2">98%</div>
                        <div class="text-sm opacity-90">Entregas a Tiempo</div>
                    </div>
                    <div class="backdrop-filter backdrop-blur-lg bg-white bg-opacity-10 rounded-2xl p-6 border border-white border-opacity-20">
                        <div class="text-3xl font-bold text-blue-400 mb-2">0%</div>
                        <div class="text-sm opacity-90">Productos Dañados</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#shipping-options" class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 inline-flex items-center justify-center transform hover:scale-105 shadow-lg">
                        <i class="fas fa-truck mr-2"></i>Ver Opciones de Envío
                    </a>
                    <a href="#tracking" class="border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white hover:text-blue-600 transition-all duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Rastrear Pedido
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Opciones de Envío -->
    <section id="shipping-options" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Opciones de Envío</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Elige la opción que mejor se adapte a tus necesidades</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Envío Express -->
                <div class="shipping-card rounded-2xl p-8 border border-gray-200">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 gradient-accent rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-rocket text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Express 24h</h3>
                        <p class="text-gray-600">Recibe tu pedido al día siguiente</p>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Entrega en 24 horas</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Seguimiento en tiempo real</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Seguro incluido</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Firma requerida</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="price-highlight text-3xl font-bold mb-2">$15.000</div>
                        <p class="text-gray-500 text-sm mb-6">Gratis en compras sobre $150.000</p>
                        <button class="btn-sport text-white px-6 py-3 rounded-full font-bold w-full">
                            Seleccionar Express
                        </button>
                    </div>
                </div>

                <!-- Envío Estándar -->
                <div class="shipping-card rounded-2xl p-8 border-2 border-blue-500 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-bold">MÁS POPULAR</span>
                    </div>
                    
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 gradient-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-truck text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Estándar</h3>
                        <p class="text-gray-600">Entrega confiable en 2-3 días</p>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Entrega en 2-3 días hábiles</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Seguimiento disponible</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Seguro básico incluido</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Horario flexible</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="price-highlight text-3xl font-bold mb-2">$8.000</div>
                        <p class="text-gray-500 text-sm mb-6">Gratis en compras sobre $80.000</p>
                        <button class="btn-sport text-white px-6 py-3 rounded-full font-bold w-full">
                            Seleccionar Estándar
                        </button>
                    </div>
                </div>

                <!-- Envío Económico -->
                <div class="shipping-card rounded-2xl p-8 border border-gray-200">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 gradient-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Económico</h3>
                        <p class="text-gray-600">La opción más económica</p>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Entrega en 5-7 días</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Seguimiento básico</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Empaque estándar</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Punto de retiro</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="price-highlight text-3xl font-bold mb-2">$5.000</div>
                        <p class="text-gray-500 text-sm mb-6">Siempre disponible</p>
                        <button class="btn-sport text-white px-6 py-3 rounded-full font-bold w-full">
                            Seleccionar Económico
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Zonas de Cobertura -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Zonas de Cobertura</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Llegamos a todo el país con diferentes tiempos de entrega</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Zona Metropolitana -->
                <div class="zone-card zone-express bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-city text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Zona Metropolitana</h3>
                            <p class="text-green-600 font-semibold">Express disponible</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-gray-600">
                        <p>• Santiago y comunas aledañas</p>
                        <p>• Valparaíso y Viña del Mar</p>
                        <p>• Concepción área metropolitana</p>
                        <p>• Entrega en 24-48 horas</p>
                    </div>
                </div>

                <!-- Ciudades Principales -->
                <div class="zone-card zone-standard bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Ciudades Principales</h3>
                            <p class="text-blue-600 font-semibold">Entrega estándar</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-gray-600">
                        <p>• La Serena, Antofagasta</p>
                        <p>• Rancagua, Talca, Chillán</p>
                        <p>• Temuco, Valdivia, Osorno</p>
                        <p>• Entrega en 2-4 días</p>
                    </div>
                </div>

                <!-- Zonas Remotas -->
                <div class="zone-card zone-remote bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-mountain text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Zonas Remotas</h3>
                            <p class="text-yellow-600 font-semibold">Entrega extendida</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-gray-600">
                        <p>• Regiones extremas</p>
                        <p>• Islas y zonas rurales</p>
                        <p>• Localidades aisladas</p>
                        <p>• Entrega en 5-10 días</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seguimiento de Pedidos -->
    <section id="tracking" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Rastrea tu Pedido</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Mantente informado sobre el estado de tu envío en tiempo real</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <!-- Formulario de Tracking -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-8 mb-12">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-gray-700 font-semibold mb-2">Número de Pedido</label>
                            <input type="text" placeholder="Ej: SPZ-2024-001234" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                        <div class="md:w-auto">
                            <label class="block text-transparent font-semibold mb-2">.</label>
                            <button class="btn-sport text-white px-8 py-3 rounded-xl font-bold w-full md:w-auto">
                                <i class="fas fa-search mr-2"></i>Rastrear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Demo de Tracking -->
                <div class="tracking-demo rounded-2xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Estado del Pedido: SPZ-2024-001234</h3>
                    
                    <div class="space-y-6">
                        <!-- Timeline -->
                        <div class="timeline-item flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-4 flex-shrink-0"></div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-bold text-gray-800">Pedido Confirmado</h4>
                                    <span class="text-sm text-gray-600">25 Jun, 10:30</span>
                                </div>
                                <p class="text-gray-600">Tu pedido ha sido recibido y confirmado</p>
                            </div>
                        </div>

                        <div class="timeline-item flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-4 flex-shrink-0"></div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-bold text-gray-800">En Preparación</h4>
                                    <span class="text-sm text-gray-600">25 Jun, 14:15</span>
                                </div>
                                <p class="text-gray-600">Estamos preparando tu pedido en nuestro almacén</p>
                            </div>
                        </div>

                        <div class="timeline-item flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-4 flex-shrink-0 animate-pulse"></div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-bold text-blue-600">En Tránsito</h4>
                                    <span class="text-sm text-gray-600">26 Jun, 08:00</span>
                                </div>
                                <p class="text-gray-600">Tu pedido está en camino a su destino</p>
                            </div>
                        </div>

                        <div class="timeline-item flex items-center opacity-50">
                            <div class="w-4 h-4 bg-gray-300 rounded-full mr-4 flex-shrink-0"></div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-bold text-gray-600">Entregado</h4>
                                    <span class="text-sm text-gray-600">Estimado: 26 Jun, 18:00</span>
                                </div>
                                <p class="text-gray-600">Tu pedido será entregado en la dirección especificada</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Adicional -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white
                        rounded-xl p-4 border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-2">Información de Entrega</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Transportista:</strong> Express Cargo</p>
                                <p><strong>Método:</strong> Envío Express 24h</p>
                                <p><strong>Dirección:</strong> Av. Principal 123, Santiago</p>
                                <p><strong>Contacto:</strong> +56 9 8765 4321</p>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                            <h4 class="font-bold text-gray-800 mb-2">Detalles del Paquete</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Peso:</strong> 2.5 kg</p>
                                <p><strong>Dimensiones:</strong> 35x25x15 cm</p>
                                <p><strong>Productos:</strong> 3 artículos</p>
                                <p><strong>Valor:</strong> $125.000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Políticas de Envío -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Políticas de Envío</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Todo lo que necesitas saber sobre nuestros envíos</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Políticas Generales -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 gradient-primary rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Garantía de Entrega</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Garantizamos la entrega de tu pedido en el tiempo estimado. Si no cumplimos con el plazo, 
                            te reembolsamos el costo del envío automáticamente.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 gradient-accent rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-box-open text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Empaque Seguro</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Todos nuestros productos se empaquetan con materiales de alta calidad para garantizar 
                            que lleguen en perfecto estado. Utilizamos burbujas, espuma y cajas resistentes.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 gradient-secondary rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-undo text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Devoluciones Fáciles</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Si no estás satisfecho con tu compra, tienes 30 días para devolverla. 
                            Nosotros nos encargamos de la recolección sin costo adicional.
                        </p>
                    </div>
                </div>

                <!-- Términos y Condiciones -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Términos Importantes</h3>
                        <div class="space-y-4 text-gray-600">
                            <div class="flex items-start">
                                <i class="fas fa-clock text-blue-500 mr-3 mt-1"></i>
                                <div>
                                    <strong>Horarios de Entrega:</strong> Lunes a Viernes de 9:00 a 18:00, 
                                    Sábados de 9:00 a 14:00
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-500 mr-3 mt-1"></i>
                                <div>
                                    <strong>Identificación:</strong> Se requiere cédula de identidad o 
                                    documento oficial para recibir el pedido
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-home text-blue-500 mr-3 mt-1"></i>
                                <div>
                                    <strong>Dirección:</strong> Debe haber alguien en el domicilio para 
                                    recibir el pedido en el horario acordado
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-phone text-blue-500 mr-3 mt-1"></i>
                                <div>
                                    <strong>Contacto:</strong> Mantenemos comunicación constante vía 
                                    SMS y correo durante todo el proceso
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Costos Especiales</h3>
                        <div class="space-y-3 text-gray-600">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span>Productos voluminosos (+50kg)</span>
                                <span class="font-semibold">+$10.000</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span>Entrega en día específico</span>
                                <span class="font-semibold">+$5.000</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span>Entrega en horario extendido</span>
                                <span class="font-semibold">+$8.000</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span>Seguro adicional</span>
                                <span class="font-semibold">2% del valor</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Preguntas Frecuentes</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Resolvemos tus dudas sobre envíos</p>
            </div>

            <div class="max-w-4xl mx-auto space-y-4">
                <!-- FAQ Item 1 -->
                <div class="faq-item bg-white p-6">
                    <button class="faq-trigger w-full text-left flex justify-between items-center" onclick="toggleFAQ(1)">
                        <h3 class="text-lg font-bold text-gray-800">¿Cuáles son los tiempos de entrega?</h3>
                        <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="faq-icon-1"></i>
                    </button>
                    <div class="accordion-content" id="faq-content-1">
                        <div class="pt-4 text-gray-600">
                            <p>Los tiempos de entrega varían según la modalidad elegida:</p>
                            <ul class="mt-2 space-y-1">
                                <li>• <strong>Express 24h:</strong> Entrega al día siguiente hábil</li>
                                <li>• <strong>Estándar:</strong> 2-3 días hábiles</li>
                                <li>• <strong>Económico:</strong> 5-7 días hábiles</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item bg-white p-6">
                    <button class="faq-trigger w-full text-left flex justify-between items-center" onclick="toggleFAQ(2)">
                        <h3 class="text-lg font-bold text-gray-800">¿Cómo puedo rastrear mi pedido?</h3>
                        <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="faq-icon-2"></i>
                    </button>
                    <div class="accordion-content" id="faq-content-2">
                        <div class="pt-4 text-gray-600">
                            <p>Una vez confirmado tu pedido, recibirás un número de seguimiento por email y SMS. 
                            Puedes usar este número en nuestra sección de tracking o en el sitio web del transportista.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item bg-white p-6">
                    <button class="faq-trigger w-full text-left flex justify-between items-center" onclick="toggleFAQ(3)">
                        <h3 class="text-lg font-bold text-gray-800">¿Qué pasa si no estoy en casa?</h3>
                        <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="faq-icon-3"></i>
                    </button>
                    <div class="accordion-content" id="faq-content-3">
                        <div class="pt-4 text-gray-600">
                            <p>Si no te encontramos en casa, el transportista dejará una notificación y reagendará 
                            la entrega. También puedes autorizar la entrega con un tercero o elegir un punto de retiro cercano.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item bg-white p-6">
                    <button class="faq-trigger w-full text-left flex justify-between items-center" onclick="toggleFAQ(4)">
                        <h3 class="text-lg font-bold text-gray-800">¿Los envíos incluyen seguro?</h3>
                        <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="faq-icon-4"></i>
                    </button>
                    <div class="accordion-content" id="faq-content-4">
                        <div class="pt-4 text-gray-600">
                            <p>Sí, todos nuestros envíos incluyen seguro básico hasta $50.000. Para productos de mayor valor, 
                            ofrecemos seguro adicional por un 2% del valor total del pedido.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="faq-item bg-white p-6">
                    <button class="faq-trigger w-full text-left flex justify-between items-center" onclick="toggleFAQ(5)">
                        <h3 class="text-lg font-bold text-gray-800">¿Puedo cambiar la dirección de entrega?</h3>
                        <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="faq-icon-5"></i>
                    </button>
                    <div class="accordion-content" id="faq-content-5">
                        <div class="pt-4 text-gray-600">
                            <p>Puedes cambiar la dirección de entrega antes de que el pedido sea despachado. 
                            Una vez en tránsito, solo es posible cambiar a una dirección en la misma zona de cobertura.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 gradient-primary">
        <div class="container mx-auto px-4 text-center text-white">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                    ¿Listo para recibir tu equipamiento deportivo?
                </h2>
                <p class="text-xl mb-8 opacity-90">
                    Aprovecha nuestros envíos rápidos y seguros. ¡Tu próximo entrenamiento te está esperando!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="index.php#products" class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-shopping-bag mr-2"></i>Explorar Productos
                    </a>
                    <a href="contacto.php" class="border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white hover:text-blue-600 transition-all duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-headset mr-2"></i>Contactar Soporte
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="gradient-dark text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo y descripción -->
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                        <i class="fas fa-dumbbell mr-2 text-blue-400"></i>Requejo Fashion Lab
                    </h3>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Tu tienda de confianza para equipamiento deportivo de alta calidad. 
                        Enviamos a todo Chile con la mejor experiencia de compra online.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

                <!-- Enlaces rápidos -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="index.php#products" class="text-gray-300 hover:text-white transition-colors">Productos</a></li>
                        <li><a href="ofertas.php" class="text-gray-300 hover:text-white transition-colors">Ofertas</a></li>
                        <li><a href="contacto.php" class="text-gray-300 hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="envios.php" class="text-blue-400 hover:text-blue-300 transition-colors">Envíos</a></li>
                    </ul>
                </div>

                <!-- Información de contacto -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-blue-400"></i>
                            +56 9 8765 4321
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-400"></i>
                            info@Requejo Fashion Lab.cl
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i>
                            Santiago, Chile
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-2 text-blue-400"></i>
                            Lun-Vie 9:00-18:00
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-600 pt-8 mt-8 text-center text-gray-400">
                <p>&copy; 2024 Requejo Fashion Lab. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Carrito Lateral -->
    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-80 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-bold text-gray-800">Mi Carrito</h3>
                <button onclick="toggleCart()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="flex-1 p-4 overflow-y-auto">
                <div id="cart-items">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-shopping-cart text-4xl mb-4 opacity-50"></i>
                        <p>Tu carrito está vacío</p>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t">
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-bold" id="cart-subtotal">$0</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Envío:</span>
                        <span class="font-bold text-green-600" id="cart-shipping">Gratis</span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span id="cart-total">$0</span>
                    </div>
                </div>
                <button class="btn-sport text-white px-6 py-3 rounded-full font-bold w-full">
                    <i class="fas fa-credit-card mr-2"></i>Proceder al Pago
                </button>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div id="cart-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleCart()"></div>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Cart functionality
        function toggleCart() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-overlay');
            
            sidebar.classList.toggle('translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // FAQ toggle functionality
        function toggleFAQ(index) {
            const content = document.getElementById(`faq-content-${index}`);
            const icon = document.getElementById(`faq-icon-${index}`);
            
            content.classList.toggle('active');
            icon.classList.toggle('rotate-180');
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Shipping option selection
        document.querySelectorAll('.shipping-card button').forEach(button => {
            button.addEventListener('click', function() {
                // Remove previous selections
                document.querySelectorAll('.shipping-card').forEach(card => {
                    card.classList.remove('border-blue-500', 'border-2');
                    card.classList.add('border-gray-200', 'border');
                });
                
                // Add selection to current card
                this.closest('.shipping-card').classList.remove('border-gray-200', 'border');
                this.closest('.shipping-card').classList.add('border-blue-500', 'border-2');
                
                // Show confirmation
                alert('Opción de envío seleccionada. Procede al checkout para confirmar tu pedido.');
            });
        });

        // Tracking form functionality
        document.querySelector('.btn-sport').addEventListener('click', function(e) {
            if (this.innerHTML.includes('Rastrear')) {
                e.preventDefault();
                const trackingInput = document.querySelector('input[placeholder*="SPZ"]');
                if (trackingInput.value.trim()) {
                    alert('Buscando información del pedido: ' + trackingInput.value);
                    // Here you would normally make an AJAX call to get tracking info
                } else {
                    alert('Por favor ingresa un número de pedido válido');
                }
            }
        });

        // Add some interactivity to zone cards
        document.querySelectorAll('.zone-card').forEach(card => {
            card.addEventListener('click', function() {
                const zoneName = this.querySelector('h3').textContent;
                alert(`Información detallada sobre ${zoneName} estará disponible próximamente.`);
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.shipping-card, .zone-card, .faq-item').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>