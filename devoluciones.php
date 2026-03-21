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
    <title>Devoluciones y Cambios - SportZone</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
            --accent-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --dark-gradient: linear-gradient(135deg, #111827 0%, #374151 100%);
            --success-gradient: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
            --warning-gradient: linear-gradient(135deg, #92400e 0%, #d97706 50%, #f59e0b 100%);
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

        .gradient-success {
            background: var(--success-gradient);
        }

        .gradient-warning {
            background: var(--warning-gradient);
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

        .btn-success {
            background: var(--success-gradient);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-warning {
            background: var(--warning-gradient);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        .hero-returns {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.9) 0%, rgba(16, 185, 129, 0.8) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="returns-pattern" width="80" height="80" patternUnits="userSpaceOnUse"><path d="M 80 0 L 0 0 0 80" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1"/><circle cx="40" cy="40" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23returns-pattern)"/></svg>');
            background-size: cover, 80px 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-returns::before {
            content: '';
            position: absolute;
            top: 10%;
            right: 5%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 12s ease-in-out infinite;
        }

        .hero-returns::after {
            content: '';
            position: absolute;
            bottom: 15%;
            left: 8%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .return-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .return-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .process-step {
            transition: all 0.3s ease;
            position: relative;
        }

        .process-step::before {
            content: '';
            position: absolute;
            top: 50%;
            right: -50%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, transparent);
            transform: translateY(-50%);
            z-index: 0;
        }

        .process-step:last-child::before {
            display: none;
        }

        .process-step:hover {
            transform: scale(1.05);
        }

        .process-number {
            background: var(--primary-gradient);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 0 auto 1rem;
            position: relative;
            z-index: 10;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .condition-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .condition-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0) 0%, rgba(59, 130, 246, 0.03) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .condition-card:hover::before {
            opacity: 1;
        }

        .condition-accepted {
            border-color: #10b981;
        }

        .condition-partial {
            border-color: #f59e0b;
        }

        .condition-rejected {
            border-color: #ef4444;
        }

        .condition-card:hover {
            transform: translateX(8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .timeline-container {
            position: relative;
        }

        .timeline-line {
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #3b82f6, #10b981);
        }

        .timeline-item {
            position: relative;
            padding-left: 80px;
            margin-bottom: 2rem;
        }

        .timeline-dot {
            position: absolute;
            left: 20px;
            top: 10px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            border: 3px solid #3b82f6;
            z-index: 10;
        }

        .timeline-dot.completed {
            background: #10b981;
            border-color: #10b981;
        }

        .timeline-dot.current {
            background: #f59e0b;
            border-color: #f59e0b;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
            }
        }

        .form-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px dashed #cbd5e1;
            transition: all 0.3s ease;
        }

        .form-section:hover {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .upload-area.dragover {
            border-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        }

        .policy-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
        }

        .guarantee-badge {
            background: var(--success-gradient);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .hero-returns {
                background-size: cover, 60px 60px;
                min-height: 60vh;
            }
            
            .process-step::before {
                display: none;
            }
            
            .timeline-item {
                padding-left: 60px;
            }
            
            .timeline-line {
                left: 20px;
            }
            
            .timeline-dot {
                left: 10px;
            }
        }

        @media (max-width: 640px) {
            .return-card {
                padding: 16px;
            }
            
            .hero-returns {
                min-height: 50vh;
                padding: 40px 0;
            }
            
            .process-number {
                width: 50px;
                height: 50px;
                font-size: 1rem;
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
                        <a href="envios.php" class="text-gray-700 hover:text-blue-600 transition-colors font-medium block py-2 md:py-0 w-full md:w-auto">Envíos</a>
                        <a href="devoluciones.php" class="text-blue-600 hover:text-blue-800 transition-colors font-bold block py-2 md:py-0 w-full md:w-auto border-b-2 border-blue-600 md:border-0">Devoluciones</a>
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
    <section class="hero-returns min-h-screen flex items-center text-white relative">
        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <i class="fas fa-undo-alt text-6xl sm:text-8xl mb-6 animate-pulse"></i>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Devoluciones <span class="text-green-400">Sin Complicaciones</span>
                </h1>
                <p class="text-xl sm:text-2xl mb-8 leading-relaxed opacity-90">
                    Tu satisfacción es nuestra prioridad. Proceso de devolución fácil, rápido y completamente gratuito
                </p>
                
                <!-- Guarantee Badge -->
                <div class="flex justify-center mb-8">
                    <div class="guarantee-badge text-lg">
                        <i class="fas fa-shield-check mr-2"></i>
                        Garantía 30 Días - 100% Satisfacción
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="backdrop-filter backdrop-blur-lg bg-white bg-opacity-10 rounded-2xl p-6 border border-white border-opacity-20">
                        <div class="text-3xl font-bold text-green-400 mb-2">30</div>
                        <div class="text-sm opacity-90">Días para Devolver</div>
                    </div>
                    <div class="backdrop-filter backdrop-blur-lg bg-white bg-opacity-10 rounded-2xl p-6 border border-white border-opacity-20">
                        <div class="text-3xl font-bold text-blue-400 mb-2">24h</div>
                        <div class="text-sm opacity-90">Procesamiento</div>
                    </div>
                    <div class="backdrop-filter backdrop-blur-lg bg-white bg-opacity-10 rounded-2xl p-6 border border-white border-opacity-20">
                        <div class="text-3xl font-bold text-yellow-400 mb-2">$0</div>
                        <div class="text-sm opacity-90">Costo de Devolución</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#return-process" class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 inline-flex items-center justify-center transform hover:scale-105 shadow-lg">
                        <i class="fas fa-play mr-2"></i>Iniciar Devolución
                    </a>
                    <a href="#return-form" class="border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white hover:text-blue-600 transition-all duration-300 inline-flex items-center justify-center">
                        <i class="fas fa-file-alt mr-2"></i>Formulario Online
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Proceso de Devolución -->
    <section id="return-process" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Proceso Simple en 4 Pasos</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Devolver tus productos nunca fue tan fácil</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Paso 1 -->
                <div class="process-step text-center">
                    <div class="process-number">1</div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg h-full">
                        <div class="w-16 h-16 gradient-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Solicita tu Devolución</h3>
                        <p class="text-gray-600">Completa nuestro formulario online con los detalles de tu pedido y el motivo de la devolución.</p>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="process-step text-center">
                    <div class="process-number">2</div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg h-full">
                        <div class="w-16 h-16 gradient-accent rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Empaca el Producto</h3>
                        <p class="text-gray-600">Coloca el producto en su empaque original con todos los accesorios y etiquetas incluidas.</p>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div class="process-step text-center">
                    <div class="process-number">3</div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg h-full">
                        <div class="w-16 h-16 gradient-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-truck text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Recolección Gratuita</h3>
                        <p class="text-gray-600">Nuestro courier pasa a recoger el producto en la dirección que nos indiques, sin costo alguno.</p>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div class="process-step text-center">
                    <div class="process-number">4</div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg h-full">
                        <div class="w-16 h-16 gradient-success rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Reembolso Procesado</h3>
                        <p class="text-gray-600">Una vez recibido y revisado, procesamos tu reembolso en 24-48 horas hábiles.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Condiciones de Devolución -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Condiciones de Devolución</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Conoce qué productos pueden ser devueltos y en qué condiciones</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Productos Aceptados -->
                <div class="condition-card condition-accepted bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Devolución Completa</h3>
                    </div>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm"></i>
                            <span>Productos sin usar</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm"></i>
                            <span>Empaque original intacto</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm"></i>
                            <span>Etiquetas sin remover</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm"></i>
                            <span>Accesorios incluidos</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3 text-sm"></i>
                            <span>Dentro de 30 días</span>
                        </div>
                    </div>
                    <div class="mt-6 p-3 bg-green-50 rounded-lg">
                        <p class="text-green-700 font-semibold text-center">100% Reembolso</p>
                    </div>
                </div>

                <!-- Productos con Condiciones -->
                <div class="condition-card condition-partial bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Devolución Parcial</h3>
                    </div>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-minus text-yellow-500 mr-3 text-sm"></i>
                            <span>Productos probados</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-minus text-yellow-500 mr-3 text-sm"></i>
                            <span>Empaque dañado levemente</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-minus text-yellow-500 mr-3 text-sm"></i>
                            <span>Etiquetas cortadas</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-minus text-yellow-500 mr-3 text-sm"></i>
                            <span>Uso mínimo evidente</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-minus text-yellow-500 mr-3 text-sm"></i>
                            <span>Evaluación caso a caso</span>
                        </div>
                    </div>
                    <div class="mt-6 p-3 bg-yellow-50 rounded-lg">
                        <p class="text-yellow-700 font-semibold text-center">50-80% Reembolso</p>
                    </div>
                </div>

                <!-- Productos No Aceptados -->
                <div class="condition-card condition-rejected bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No Aceptamos</h3>
                    </div>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-times text-red-500 mr-3 text-sm"></i>
                            <span>Productos personalizados</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-times text-red-500 mr-3 text-sm"></i>
                            <span>Ropa interior deportiva</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-times text-red-500 mr-3 text-sm"></i>
                            <span>Suplementos abiertos</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-times text-red-500 mr-3 text-sm"></i>
                            <span>Productos dañados por uso</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-times text-red-500 mr-3 text-sm"></i>
                            <span>Más de 30 días</span>
                        </div>
                    </div>
                    <div class="mt-6 p-3 bg-red-50 rounded-lg">
                        <p class="text-red-700 font-semibold text-center">Sin Reembolso</p>
                    </div>
                </div>
            </div>

            <!-- Política Destacada -->
            <div class="policy-highlight rounded-2xl p-6 mt-<!-- Política Destacada -->
            <div class="policy-highlight rounded-2xl p-6 mt-8">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-info-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Política Especial COVID-19</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Durante estos tiempos especiales, hemos extendido nuestro período de devolución a <strong>45 días</strong> 
                            para darte mayor tranquilidad. Además, ofrecemos desinfección gratuita de todos los productos devueltos 
                            antes de reintegrarlos a nuestro inventario.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline de Seguimiento -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Seguimiento de tu Devolución</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Mantente informado en cada paso del proceso</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="timeline-container">
                    <div class="timeline-line"></div>
                    
                    <!-- Timeline Item 1 -->
                    <div class="timeline-item">
                        <div class="timeline-dot completed"></div>
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-800">Solicitud Recibida</h3>
                                <span class="text-sm text-green-600 font-semibold bg-green-100 px-3 py-1 rounded-full">Completado</span>
                            </div>
                            <p class="text-gray-600 mb-2">Tu solicitud de devolución ha sido registrada exitosamente.</p>
                            <p class="text-xs text-gray-500">Tiempo estimado: Inmediato</p>
                        </div>
                    </div>

                    <!-- Timeline Item 2 -->
                    <div class="timeline-item">
                        <div class="timeline-dot current"></div>
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-800">Recolección Programada</h3>
                                <span class="text-sm text-yellow-600 font-semibold bg-yellow-100 px-3 py-1 rounded-full">En Proceso</span>
                            </div>
                            <p class="text-gray-600 mb-2">Nuestro courier se pondrá en contacto contigo para coordinar la recolección.</p>
                            <p class="text-xs text-gray-500">Tiempo estimado: 24-48 horas</p>
                        </div>
                    </div>

                    <!-- Timeline Item 3 -->
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="bg-white rounded-xl shadow-lg p-6 opacity-60">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-800">Producto Recibido</h3>
                                <span class="text-sm text-gray-500 font-semibold bg-gray-100 px-3 py-1 rounded-full">Pendiente</span>
                            </div>
                            <p class="text-gray-600 mb-2">Recibimos y revisamos el estado del producto devuelto.</p>
                            <p class="text-xs text-gray-500">Tiempo estimado: 1-2 días hábiles</p>
                        </div>
                    </div>

                    <!-- Timeline Item 4 -->
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="bg-white rounded-xl shadow-lg p-6 opacity-60">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-800">Reembolso Procesado</h3>
                                <span class="text-sm text-gray-500 font-semibold bg-gray-100 px-3 py-1 rounded-full">Pendiente</span>
                            </div>
                            <p class="text-gray-600 mb-2">El reembolso ha sido aprobado y procesado a tu método de pago original.</p>
                            <p class="text-xs text-gray-500">Tiempo estimado: 3-5 días hábiles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulario de Devolución -->
    <section id="return-form" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Formulario de Devolución</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Completa los siguientes datos para procesar tu devolución</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <form class="return-card rounded-3xl p-8 shadow-xl" id="returnForm">
                    <!-- Información del Pedido -->
                    <div class="form-section rounded-2xl p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-receipt text-blue-600 mr-3"></i>
                            Información del Pedido
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Número de Pedido *</label>
                                <input type="text" name="order_number" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Ej: SPZ-2024-001234">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Fecha de Compra *</label>
                                <input type="date" name="purchase_date" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Email de Compra *</label>
                                <input type="email" name="email" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="tu-email@ejemplo.com">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Teléfono de Contacto *</label>
                                <input type="tel" name="phone" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="+56 9 1234 5678">
                            </div>
                        </div>
                    </div>

                    <!-- Información del Producto -->
                    <div class="form-section rounded-2xl p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-box text-green-600 mr-3"></i>
                            Producto a Devolver
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Nombre del Producto *</label>
                                <input type="text" name="product_name" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Nombre exacto del producto">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Talla/Modelo</label>
                                <input type="text" name="size_model" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Ej: M, L, XL o modelo específico">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Color</label>
                                <input type="text" name="color" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Color del producto">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Cantidad a Devolver *</label>
                                <select name="quantity" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Selecciona cantidad</option>
                                    <option value="1">1 unidad</option>
                                    <option value="2">2 unidades</option>
                                    <option value="3">3 unidades</option>
                                    <option value="4">4 unidades</option>
                                    <option value="5+">5 o más unidades</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Motivo de Devolución -->
                    <div class="form-section rounded-2xl p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-question-circle text-red-600 mr-3"></i>
                            Motivo de la Devolución
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="return_reason" value="size_issue" class="mr-3 text-blue-600">
                                <span class="text-gray-700">Problema con la talla</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="return_reason" value="quality_issue" class="mr-3 text-blue-600">
                                <span class="text-gray-700">Problema de calidad</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="return_reason" value="not_as_described" class="mr-3 text-blue-600">
                                <span class="text-gray-700">No como se describe</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="return_reason" value="wrong_item" class="mr-3 text-blue-600">
                                <span class="text-gray-700">Producto incorrecto</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="return_reason" value="damaged" class="mr-3 text-blue-600">
                                <span class="text-gray-700">Llegó dañado</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                <input type="radio" name="return_reason" value="other" class="mr-3 text-blue-600">
                                <span class="text-gray-700">Otro motivo</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Descripción Detallada *</label>
                            <textarea name="description" required rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Describe detalladamente el motivo de tu devolución..."></textarea>
                        </div>
                    </div>

                    <!-- Dirección de Recolección -->
                    <div class="form-section rounded-2xl p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-3"></i>
                            Dirección de Recolección
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-semibold mb-2">Dirección Completa *</label>
                                <input type="text" name="address" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Calle, número, depto/casa">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Comuna *</label>
                                <input type="text" name="commune" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Comuna">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Ciudad *</label>
                                <input type="text" name="city" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Ciudad">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Código Postal</label>
                                <input type="text" name="postal_code" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Código postal">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Región *</label>
                                <select name="region" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Selecciona región</option>
                                    <option value="metropolitana">Región Metropolitana</option>
                                    <option value="valparaiso">Región de Valparaíso</option>
                                    <option value="ohiggins">Región de O'Higgins</option>
                                    <option value="maule">Región del Maule</option>
                                    <option value="biobio">Región del Biobío</option>
                                    <option value="araucania">Región de La Araucanía</option>
                                    <option value="los-rios">Región de Los Ríos</option>
                                    <option value="los-lagos">Región de Los Lagos</option>
                                    <option value="aysen">Región de Aysén</option>
                                    <option value="magallanes">Región de Magallanes</option>
                                    <option value="antofagasta">Región de Antofagasta</option>
                                    <option value="atacama">Región de Atacama</option>
                                    <option value="coquimbo">Región de Coquimbo</option>
                                    <option value="tarapaca">Región de Tarapacá</option>
                                    <option value="arica">Región de Arica y Parinacota</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-gray-700 font-semibold mb-2">Instrucciones Especiales</label>
                            <textarea name="special_instructions" rows="3" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Instrucciones adicionales para la recolección (horarios preferidos, ubicación específica, etc.)"></textarea>
                        </div>
                    </div>

                    <!-- Subir Fotos -->
                    <div class="form-section rounded-2xl p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-camera text-indigo-600 mr-3"></i>
                            Fotos del Producto (Opcional)
                        </h3>
                        
                        <div class="upload-area rounded-xl p-8 text-center cursor-pointer" id="uploadArea">
                            <div class="mb-4">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-2">Arrastra las fotos aquí o haz clic para seleccionar</p>
                                <p class="text-sm text-gray-500">Máximo 5 fotos, 5MB cada una</p>
                            </div>
                            <input type="file" id="photoUpload" name="photos[]" multiple accept="image/*" class="hidden">
                            <div id="photoPreview" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4 hidden"></div>
                        </div>
                    </div>

                    <!-- Términos y Condiciones -->
                    <div class="mb-8">
                        <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                            <input type="checkbox" name="terms" required class="mr-3 mt-1 text-blue-600">
                            <span class="text-gray-700">
                                Acepto los <a href="#" class="text-blue-600 hover:underline">términos y condiciones</a> 
                                de devolución y confirmo que la información proporcionada es veraz y completa.
                            </span>
                        </label>
                    </div>

                    <!-- Botones de Envío -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button type="submit" class="btn-sport text-white px-8 py-4 rounded-full font-bold hover:shadow-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Solicitud de Devolución
                        </button>
                        <button type="button" class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Borrador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- FAQ Devoluciones -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Preguntas Frecuentes</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Resolvemos las dudas más comunes sobre devoluciones</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 gap-6">
                    <!-- FAQ Item 1 -->
                    <div class="return-card rounded-2xl p-6 shadow-lg">
                        <button class="faq-button w-full text-left flex items-center justify-between" onclick="toggleFAQ(1)">
                            <h3 class="text-lg font-bold text-gray-800">¿Cuánto tiempo tengo para devolver un producto?</h3>
                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-200" id="faq-icon-1"></i>
                        </button>
                        <div class="faq-content hidden mt-4 text-gray-600" id="faq-content-1">
                            <p>Tienes 30 días calendario desde la fecha de recepción del producto para solicitar una devolución. 
                            Durante situaciones especiales (como COVID-19), este período puede extenderse a 45 días. 
                            El producto debe estar en condiciones originales para ser elegible para devolución completa.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="return-card rounded-2xl p-6 shadow-lg">
                        <button class="faq-button w-full text-left flex items-center justify-between" onclick="toggleFAQ(2)">
                            <h3 class="text-lg font-bold text-gray-800">¿La recolección del producto es gratuita?</h3>
                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-200" id="faq-icon-2"></i>
                        </button>
                        <div class="faq-content hidden mt-4 text-gray-600" id="faq-content-2">
                            <p>Sí, la recolección es completamente gratuita en toda la Región Metropolitana y principales ciudades de Chile. 
                            Nuestro courier pasará a recoger el producto en la dirección que nos indiques sin costo adicional. 
                            Para zonas más alejadas, podrías tener un costo mínimo que te comunicaremos previamente.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="return-card rounded-2xl p-6 shadow-lg">
                        <button class="faq-button w-full text-left flex items-center justify-between" onclick="toggleFAQ(3)">
                            <h3 class="text-lg font-bold text-gray-800">¿Cuándo recibiré mi reembolso?</h3>
                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-200" id="faq-icon-3"></i>
                        </button>
                        <div class="faq-content hidden mt-4 text-gray-600" id="faq-content-3">
                            <p>Una vez que recibamos y revisemos tu producto devuelto, procesaremos el reembolso en un plazo de 24-48 horas hábiles. 
                            El tiempo para que aparezca en tu cuenta depende de tu banco o método de pago: 
                            tarjetas de crédito (3-5 días hábiles), débito (1-3 días hábiles), transferencia bancaria (1-2 días hábiles).</p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="return-card rounded-2xl p-6 shadow-lg">
                        <button class="faq-button w-full text-left flex items-center justify-between" onclick="toggleFAQ(4)">
                            <h3 class="text-lg font-bold text-gray-800">¿Puedo cambiar un producto por otro en lugar de devolverlo?</h3>
                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-200" id="faq-icon-4"></i>
                        </button>
                        <div class="faq-content hidden mt-4 text-gray-600" id="faq-content-4">
                            <p>¡Por supuesto! Ofrecemos cambios directos por otro producto de igual o mayor valor. 
                            Si el nuevo producto tiene un valor menor, te reembolsamos la diferencia. 
                            Si tiene un valor mayor, puedes pagar la diferencia. Los cambios siguen el mismo proceso de recolección gratuita.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="return-card rounded-2xl p-6 shadow-lg">
                        <button class="faq-button w-full text-left flex items-center justify-between" onclick="toggleFAQ(5)">
                            <h3 class="text-lg font-bold text-gray-800">¿Qué pasa si mi producto llegó defectuoso?</h3>
                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-200" id="faq-icon-5"></i>
                        </button>
                        <div class="faq-content hidden mt-4 text-gray-600" id="faq-content-5">
                            <p>Si tu producto llegó defectuoso o dañado, procesamos la devolución de manera prioritaria. 
                            Además del reembolso completo, te ofrecemos una compensación adicional por las molestias. 
                            También puedes optar por un cambio inmediato con envío express gratuito del nuevo producto.</p>
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="return-card rounded-2xl p-6 shadow-lg">
                        <button class="faq-button w-full text-left flex items-center justify-between" onclick="toggleFAQ(6)">
                            <h3 class="text-lg font-bold text-gray-800">¿Puedo hacer seguimiento de mi devolución?</h3>
                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-200" id="faq-icon-6"></i>
                        </button>
                        <div class="faq-content hidden mt-4 text-gray-600" id="faq-content-6">
                            <p>Sí, una vez que envíes tu solicitud de devolución, recibirás un número de seguimiento por email y SMS. 
                            Podrás ver el estado en tiempo real: solicitud recibida, recolección programada, producto recibido, 
                            revisión en proceso y reembolso procesado. También te notificaremos en cada etapa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto de Soporte -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">¿Necesitas Ayuda?</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Nuestro equipo de soporte está aquí para ayudarte</p>
            </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Soporte por Chat -->
                <div class="return-card rounded-2xl p-6 shadow-lg text-center">
                    <div class="w-16 h-16 gradient-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comment-dots text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Chat en Vivo</h3>
                    <p class="text-gray-600 mb-4">Conéctate instantáneamente con nuestro equipo de soporte</p>
                    <button class="btn-sport text-white px-6 py-3 rounded-full font-medium w-full">
                        <i class="fas fa-comment-alt mr-2"></i>Iniciar Chat
                    </button>
                </div>

                <!-- Soporte por Email -->
                <div class="return-card rounded-2xl p-6 shadow-lg text-center">
                    <div class="w-16 h-16 gradient-accent rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Correo Electrónico</h3>
                    <p class="text-gray-600 mb-4">Responde en menos de 4 horas durante días hábiles</p>
                    <a href="mailto:soporte@Requejo Fashion Lab.cl" class="btn-success text-white px-6 py-3 rounded-full font-medium w-full inline-block">
                        <i class="fas fa-paper-plane mr-2"></i>Enviar Email
                    </a>
                </div>

                <!-- Soporte Telefónico -->
                <div class="return-card rounded-2xl p-6 shadow-lg text-center">
                    <div class="w-16 h-16 gradient-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Llámanos</h3>
                    <p class="text-gray-600 mb-4">Disponible de lunes a viernes de 9:00 a 18:00 hrs</p>
                    <a href="tel:+56223456789" class="btn-warning text-white px-6 py-3 rounded-full font-medium w-full inline-block">
                        <i class="fas fa-phone mr-2"></i>+56 2 2345 6789
                    </a>
                </div>
            </div>

            <!-- Horario de Atención -->
            <div class="max-w-2xl mx-auto mt-12 bg-white rounded-2xl p-6 shadow-lg text-center">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 mr-3"></i>
                    Horario de Atención
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-3">
                        <div class="font-semibold text-gray-700">Lunes-Viernes</div>
                        <div class="text-gray-600">9:00 - 18:00</div>
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-gray-700">Sábados</div>
                        <div class="text-gray-600">10:00 - 14:00</div>
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-gray-700">Domingos</div>
                        <div class="text-gray-600">Cerrado</div>
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-gray-700">Feriados</div>
                        <div class="text-gray-600">Cerrado</div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">* El horario puede variar en fechas especiales</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="gradient-dark text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo y Redes -->
                <div class="md:col-span-1">
                    <h2 class="text-2xl font-bold mb-4">
                        <i class="fas fa-dumbbell mr-2"></i>Requejo Fashion Lab
                    </h2>
                    <p class="text-gray-300 mb-4">Tu tienda de confianza para equipamiento deportivo de alta calidad.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white hover:text-blue-300 transition-colors">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-white hover:text-blue-400 transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-white hover:text-pink-500 transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-white hover:text-red-500 transition-colors">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Enlaces Rápidos -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="index.php#products" class="text-gray-300 hover:text-white transition-colors">Productos</a></li>
                        <li><a href="ofertas.php" class="text-gray-300 hover:text-white transition-colors">Ofertas</a></li>
                        <li><a href="contacto.php" class="text-gray-300 hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="envios.php" class="text-gray-300 hover:text-white transition-colors">Envíos</a></li>
                        <li><a href="devoluciones.php" class="text-gray-300 hover:text-white transition-colors">Devoluciones</a></li>
                    </ul>
                </div>

                <!-- Información de Contacto -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Contacto</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-blue-300"></i>
                            <span class="text-gray-300">Av. Deportiva 1234, Santiago, Chile</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-3 text-blue-300"></i>
                            <span class="text-gray-300">+56 2 2345 6789</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-blue-300"></i>
                            <span class="text-gray-300">contacto@Requejo Fashion Lab.cl</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock mt-1 mr-3 text-blue-300"></i>
                            <span class="text-gray-300">Lun-Vie: 9:00 - 18:00</span>
                        </li>
                    </ul>
                </div>

                <!-- Boletín Informativo -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Boletín</h3>
                    <p class="text-gray-300 mb-4">Suscríbete para recibir ofertas exclusivas y novedades.</p>
                    <form class="flex flex-col space-y-3">
                        <input type="email" placeholder="Tu correo electrónico" class="px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="btn-sport text-white px-4 py-2 rounded-lg font-medium">
                            Suscribirse
                        </button>
                    </form>
                </div>
            </div>

            <!-- Derechos de Autor -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Requejo Fashion Lab. Todos los derechos reservados.</p>
                <div class="mt-2 text-sm">
                    <a href="#" class="hover:text-white transition-colors">Términos y Condiciones</a> | 
                    <a href="#" class="hover:text-white transition-colors">Política de Privacidad</a> | 
                    <a href="#" class="hover:text-white transition-colors">Política de Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle Mobile Menu
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Toggle FAQ Items
        function toggleFAQ(id) {
            const content = document.getElementById(`faq-content-${id}`);
            const icon = document.getElementById(`faq-icon-${id}`);
            
            content.classList.toggle('hidden');
            icon.classList.toggle('transform');
            icon.classList.toggle('rotate-180');
        }

        // Upload Area Functionality
        const uploadArea = document.getElementById('uploadArea');
        const photoUpload = document.getElementById('photoUpload');
        const photoPreview = document.getElementById('photoPreview');

        uploadArea.addEventListener('click', () => {
            photoUpload.click();
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        photoUpload.addEventListener('change', () => {
            handleFiles(photoUpload.files);
        });

        function handleFiles(files) {
            if (files.length > 5) {
                Swal.fire({
                    icon: 'error',
                    title: 'Demasiadas imágenes',
                    text: 'Solo puedes subir un máximo de 5 fotos',
                });
                return;
            }

            photoPreview.innerHTML = '';
            photoPreview.classList.remove('hidden');

            Array.from(files).slice(0, 5).forEach(file => {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'relative';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-32 object-cover rounded-lg';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center';
                    removeBtn.innerHTML = '<i class="fas fa-times text-xs"></i>';
                    removeBtn.onclick = function() {
                        imgContainer.remove();
                        if (photoPreview.children.length === 0) {
                            photoPreview.classList.add('hidden');
                        }
                    };
                    
                    imgContainer.appendChild(img);
                    imgContainer.appendChild(removeBtn);
                    photoPreview.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            });
        }

        // Form Submission
        document.getElementById('returnForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Solicitud Enviada',
                text: 'Tu solicitud de devolución ha sido recibida. Te enviaremos un correo con los detalles en breve.',
                icon: 'success',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3b82f6',
            }).then(() => {
                this.reset();
                photoPreview.innerHTML = '';
                photoPreview.classList.add('hidden');
            });
        });

        // Cart Functionality
        function toggleCart() {
            // Implement cart toggle functionality here
            console.log('Cart toggled');
        }
    </script>
</body>
</html>