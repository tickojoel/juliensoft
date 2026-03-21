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
    <title>Centro de Ayuda - Requejo Fashion Lab</title>
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

        .help-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .help-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
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

        .hero-help {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.95) 0%, rgba(6, 182, 212, 0.9) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="dots" width="40" height="40" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23dots)"/></svg>');
        }

        .faq-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .faq-item.active {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(6, 182, 212, 0.05));
        }

        .search-highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .contact-method {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .contact-method:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-4px);
        }

        .floating-help {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            background: var(--primary-gradient);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .floating-help:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .floating-help {
                bottom: 1rem;
                right: 1rem;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .stagger-animation {
            animation-delay: calc(var(--index) * 0.1s);
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-xl sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between py-4">
                <div class="flex items-center justify-between w-full md:w-auto mb-4 md:mb-0">
                    <h1
                        class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                        <i class="fas fa-dumbbell mr-3 text-blue-600"></i>Requejo Fashion Lab
                    </h1>
                    <button id="mobile-menu-button" class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>

                <nav id="mobile-menu"
                    class="hidden md:flex flex-col md:flex-row w-full md:w-auto items-center justify-center py-4 md:py-0">
                    <div
                        class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-8 text-center w-full">
                        <a href="index.php"
                            class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Inicio</a>
                        <a href="index.php#products"
                            class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Productos</a>
                        <a href="ofertas.php"
                            class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Ofertas</a>
                        <a href="contacto.php"
                            class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Contacto</a>
                        <a href="centro-ayuda.php" class="text-blue-600 font-bold">Centro de Ayuda</a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-help py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="fade-in-up">
                <i class="fas fa-life-ring text-6xl text-white mb-6 opacity-80"></i>
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Centro de <span class="search-highlight">Ayuda</span>
                </h1>
                <p class="text-xl text-white opacity-90 mb-8 max-w-2xl mx-auto">
                    Estamos aquí para ayudarte. Encuentra respuestas rápidas a tus preguntas o contacta con nuestro
                    equipo de soporte.
                </p>

                <!-- Buscador de ayuda -->
                <div class="max-w-xl mx-auto">
                    <div class="relative">
                        <input type="text" id="help-search" placeholder="¿En qué podemos ayudarte?"
                            class="w-full px-6 py-4 pl-14 rounded-2xl border-0 shadow-xl text-lg focus:ring-4 focus:ring-blue-300 focus:outline-none">
                        <i
                            class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        <button
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 btn-sport text-white px-6 py-2 rounded-xl font-semibold">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categorías de Ayuda -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">¿Cómo podemos ayudarte?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Selecciona una categoría para encontrar respuestas rápidas
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Pedidos y Envíos -->
                <div class="help-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 fade-in-up"
                    style="--index: 1">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shipping-fast text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Pedidos y Envíos</h3>
                        <p class="text-gray-600 mb-6">Información sobre tus pedidos, tiempos de entrega y seguimiento
                        </p>
                        <ul class="text-left space-y-2 text-sm text-gray-600 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Estado de tu pedido</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Tiempos de entrega</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Seguimiento en línea</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Problemas de envío</li>
                        </ul>
                        <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                            onclick="showCategory('pedidos')">
                            Ver más información
                        </button>
                    </div>
                </div>

                <!-- Devoluciones -->
                <div class="help-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 fade-in-up"
                    style="--index: 2">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-undo-alt text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Devoluciones</h3>
                        <p class="text-gray-600 mb-6">Política de devoluciones, cambios y reembolsos</p>
                        <ul class="text-left space-y-2 text-sm text-gray-600 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>30 días para devolver</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Proceso de devolución</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Estado de reembolsos</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Cambios de talla</li>
                        </ul>
                        <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                            onclick="showCategory('devoluciones')">
                            Ver política completa
                        </button>
                    </div>
                </div>

                <!-- Productos -->
                <div class="help-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 fade-in-up"
                    style="--index: 3">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-box text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Productos</h3>
                        <p class="text-gray-600 mb-6">Información sobre nuestros productos deportivos</p>
                        <ul class="text-left space-y-2 text-sm text-gray-600 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Guías de tallas</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Cuidado de productos</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Garantías</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Especificaciones técnicas</li>
                        </ul>
                        <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                            onclick="showCategory('productos')">
                            Explorar información
                        </button>
                    </div>
                </div>

                <!-- Cuenta y Pagos -->
                <div class="help-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 fade-in-up"
                    style="--index: 4">
                    <div class="text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-credit-card text-2xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Cuenta y Pagos</h3>
                        <p class="text-gray-600 mb-6">Gestiona tu cuenta y métodos de pago</p>
                        <ul class="text-left space-y-2 text-sm text-gray-600 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Crear cuenta</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Métodos de pago</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Seguridad</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Facturas</li>
                        </ul>
                        <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                            onclick="showCategory('cuenta')">
                            Gestionar cuenta
                        </button>
                    </div>
                </div>

                <!-- Programas de Fidelidad -->
                <div class="help-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 fade-in-up"
                    style="--index: 5">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-star text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Programas VIP</h3>
                        <p class="text-gray-600 mb-6">Descuentos exclusivos y beneficios especiales</p>
                        <ul class="text-left space-y-2 text-sm text-gray-600 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Membresía SportZone+</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Puntos de recompensa</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Ofertas exclusivas</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Envío gratuito</li>
                        </ul>
                        <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                            onclick="showCategory('vip')">
                            Conocer beneficios
                        </button>
                    </div>
                </div>

                <!-- Soporte Técnico -->
                <div class="help-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 fade-in-up"
                    style="--index: 6">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-tools text-2xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Soporte Técnico</h3>
                        <p class="text-gray-600 mb-6">Ayuda con problemas técnicos y de navegación</p>
                        <ul class="text-left space-y-2 text-sm text-gray-600 mb-6">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Problemas de navegación</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>App móvil</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Errores de compra</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Compatibilidad</li>
                        </ul>
                        <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                            onclick="showCategory('soporte')">
                            Obtener ayuda técnica
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Preguntas Frecuentes -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Preguntas Frecuentes</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Las respuestas que más buscan nuestros clientes</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="faq-item bg-white rounded-xl p-6 shadow-md">
                        <button
                            class="faq-question w-full text-left flex items-center justify-between font-semibold text-lg text-gray-800 hover:text-blue-600 transition-colors"
                            onclick="toggleFAQ(1)">
                            <span>¿Cuáles son los tiempos de entrega?</span>
                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform"
                                id="faq-icon-1"></i>
                        </button>
                        <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed" id="faq-answer-1">
                            <p class="mb-3">Nuestros tiempos de entrega varían según tu ubicación:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li><strong>Santiago:</strong> 1-2 días hábiles</li>
                                <li><strong>Regiones:</strong> 2-4 días hábiles</li>
                                <li><strong>Zonas extremas:</strong> 5-7 días hábiles</li>
                            </ul>
                            <p class="mt-3">Para miembros SportZone+, ofrecemos envío express gratuito en Santiago.</p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq-item bg-white rounded-xl p-6 shadow-md">
                        <button
                            class="faq-question w-full text-left flex items-center justify-between font-semibold text-lg text-gray-800 hover:text-blue-600 transition-colors"
                            onclick="toggleFAQ(2)">
                            <span>¿Puedo devolver un producto si no me queda bien?</span>
                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform"
                                id="faq-icon-2"></i>
                        </button>
                        <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed" id="faq-answer-2">
                            <p class="mb-3">¡Por supuesto! Tenemos una política de devolución flexible:</p>
                            <ul class="list-disc ml-6 space-y-1">
                                <li>30 días para devolver desde la fecha de compra</li>
                                <li>Productos en estado original con etiquetas</li>
                                <li>Proceso de devolución gratuito</li>
                                <li>Reembolso completo o cambio por otra talla/producto</li>
                            </ul>
                            <p class="mt-3">Solo inicia el proceso desde tu cuenta o contactándonos directamente.</p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq-item bg-white rounded-xl p-6 shadow-md">
                        <button
                            class="faq-question w-full text-left flex items-center justify-between font-semibold text-lg text-gray-800 hover:text-blue-600 transition-colors"
                            onclick="toggleFAQ(3)">
                            <span>¿Qué métodos de pago aceptan?</span>
                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform"
                                id="faq-icon-3"></i>
                        </button>
                        <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed" id="faq-answer-3">
                            <p class="mb-3">Aceptamos múltiples formas de pago para tu comodidad:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold mb-2">Tarjetas:</h4>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li>Visa, MasterCard, American Express</li>
                                        <li>Tarjetas de débito</li>
                                        <li>Tarjetas de casa comercial</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-2">Otros métodos:</h4>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li>Transferencia bancaria</li>
                                        <li>PayPal</li>
                                        <li>Pago contra entrega</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="faq-item bg-white rounded-xl p-6 shadow-md">
                        <button
                            class="faq-question w-full text-left flex items-center justify-between font-semibold text-lg text-gray-800 hover:text-blue-600 transition-colors"
                            onclick="toggleFAQ(4)">
                            <span>¿Cómo puedo seguir mi pedido?</span>
                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform"
                                id="faq-icon-4"></i>
                        </button>
                        <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed" id="faq-answer-4">
                            <p class="mb-3">Seguir tu pedido es muy fácil:</p>
                            <ol class="list-decimal ml-6 space-y-2">
                                <li>Revisa tu email de confirmación con el número de seguimiento</li>
                                <li>Visita nuestra página de "Seguir Pedido"</li>
                                <li>Ingresa tu número de pedido y email</li>
                                <li>Ve el estado en tiempo real de tu envío</li>
                            </ol>
                            <p class="mt-3">También puedes crear una cuenta para ver todos tus pedidos en un solo lugar.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq-item bg-white rounded-xl p-6 shadow-md">
                        <button
                            class="faq-question w-full text-left flex items-center justify-between font-semibold text-lg text-gray-800 hover:text-blue-600 transition-colors"
                            onclick="toggleFAQ(5)">
                            <span>¿Ofrecen garantía en sus productos?</span>
                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform"
                                id="faq-icon-5"></i>
                        </button>
                        <div class="faq-answer hidden mt-4 text-gray-600 leading-relaxed" id="faq-answer-5">
                            <p class="mb-3">Sí, todos nuestros productos incluyen garantía:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold mb-2">Calzado deportivo:</h4>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li>6 meses contra defectos de fabricación</li>
                                        <li>Incluye suela y estructura</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-2">Ropa deportiva:</h4>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li>3 meses contra defectos</li>
                                        <li>Costuras y materiales</li>
                                    </ul>
                                </div>
                            </div>
                            <p class="mt-3">Equipamiento especializado incluye garantía extendida según fabricante.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Métodos de Contacto -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">¿Necesitas más ayuda?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Nuestro equipo de soporte está disponible para ayudarte</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Chat en vivo -->
                <div class="contact-method rounded-2xl p-8 text-center shadow-lg transition-all duration-300">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-comments text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Chat en Vivo</h3>
                    <p class="text-gray-600 mb-4">Respuesta inmediata de nuestros expertos</p>
                    <p class="text-sm text-gray-500 mb-6">Lun-Dom: 9:00-22:00</p>
                    <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                        onclick="openLiveChat()">
                        Iniciar Chat
                    </button>
                </div>

                <!-- Email -->
                <div class="contact-method rounded-2xl p-8 text-center shadow-lg transition-all duration-300">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Correo Electrónico</h3>
                    <p class="text-gray-600 mb-4">Respuesta en menos de 24 horas</p>
                    <p class="text-sm text-gray-500 mb-6">soporte@Requejo Fashion Lab.cl</p>
                    <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                        onclick="location.href='mailto:soporte@Requejo Fashion Lab.cl'">
                        Enviar Email
                    </button>
                </div>

                <!-- Teléfono -->
                <div class="contact-method rounded-2xl p-8 text-center shadow-lg transition-all duration-300">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone-alt text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Llámanos</h3>
                    <p class="text-gray-600 mb-4">Atención personalizada</p>
                    <p class="text-sm text-gray-500 mb-6">+56 2 2345 6789</p>
                    <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                        onclick="location.href='tel:+56223456789'">
                        Llamar Ahora
                    </button>
                </div>

                <!-- Redes Sociales -->
                <div class="contact-method rounded-2xl p-8 text-center shadow-lg transition-all duration-300">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-hashtag text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Redes Sociales</h3>
                    <p class="text-gray-600 mb-4">Conéctate con nosotros</p>
                    <div class="flex justify-center space-x-4 mb-6">
                        <a href="#" class="text-blue-500 hover:text-blue-700"><i
                                class="fab fa-facebook-f text-xl"></i></a>
                        <a href="#" class="text-pink-500 hover:text-pink-700"><i
                                class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="text-blue-400 hover:text-blue-600"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-red-500 hover:text-red-700"><i class="fab fa-youtube text-xl"></i></a>
                    </div>
                    <button class="btn-sport text-white px-6 py-3 rounded-xl font-semibold w-full"
                        onclick="location.href='contacto.php'">
                        Ver Perfiles
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Botón flotante de ayuda -->
    <button class="floating-help w-16 h-16 rounded-full text-white flex items-center justify-center text-2xl"
        onclick="openLiveChat()">
        <i class="fas fa-question"></i>
    </button>

    <!-- Footer -->
    <footer class="gradient-dark text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-xl font-bold mb-6">Requejo Fashion Lab</h3>
                    <p class="text-gray-300 mb-4">Tu tienda de confianza para equipamiento deportivo de alta calidad.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6">Enlaces Rápidos</h4>
                    <ul class="space-y-3">
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="index.php#products"
                                class="text-gray-300 hover:text-white transition-colors">Productos</a></li>
                        <li><a href="ofertas.php" class="text-gray-300 hover:text-white transition-colors">Ofertas</a>
                        </li>
                        <li><a href="contacto.php" class="text-gray-300 hover:text-white transition-colors">Contacto</a>
                        </li>
                        <li><a href="centro-ayuda.php" class="text-gray-300 hover:text-white transition-colors">Centro
                                de Ayuda</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6">Información</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Términos y
                                condiciones</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Política de
                                privacidad</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Política de envíos</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Devoluciones</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Preguntas
                                frecuentes</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-6">Contacto</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-gray-300"></i>
                            <span class="text-gray-300">Av. Deportiva 1234, Santiago, Chile</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-gray-300"></i>
                            <span class="text-gray-300">+56 2 2345 6789</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-gray-300"></i>
                            <span class="text-gray-300">contacto@Requejo Fashion Lab.cl</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-gray-300"></i>
                            <span class="text-gray-300">Lun-Vie: 9:00 - 19:00</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Requejo Fashion Lab. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // FAQ toggle function
        function toggleFAQ(num) {
            const answer = document.getElementById(`faq-answer-${num}`);
            const icon = document.getElementById(`faq-icon-${num}`);
            const item = answer.parentElement;

            answer.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
            item.classList.toggle('active');
        }

        // Show category function
        function showCategory(category) {
            // Aquí podrías implementar la lógica para mostrar contenido específico de cada categoría
            Swal.fire({
                title: `Información sobre ${category}`,
                text: `Estás viendo información sobre ${category}. Pronto implementaremos esta sección completa.`,
                icon: 'info',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3b82f6'
            });
        }

        // Live chat function
        function openLiveChat() {
            Swal.fire({
                title: 'Chat de Soporte',
                html: `
                    <div class="text-left">
                        <p class="mb-4">Nuestro equipo de soporte está disponible para ayudarte.</p>
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="font-semibold">Horario de atención:</p>
                            <p>Lunes a Domingo: 9:00 - 22:00 hrs</p>
                        </div>
                        <button class="btn-sport text-white px-4 py-2 rounded-lg font-semibold w-full mb-2">
                            <i class="fas fa-comment-dots mr-2"></i> Iniciar Chat
                        </button>
                        <p class="text-sm text-gray-500">O contáctanos por otros medios si prefieres.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Iniciar Chat',
                cancelButtonText: 'Cerrar',
                confirmButtonColor: '#3b82f6'
            });
        }

        // Search functionality
        document.getElementById('help-search').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    Swal.fire({
                        title: `Resultados para: "${query}"`,
                        text: 'Estamos trabajando en el buscador de ayuda. Pronto podrás encontrar respuestas rápidas aquí.',
                        icon: 'info',
                        confirmButtonText: 'Entendido'
                    });
                }
            }
        });

        // Animate elements on scroll
        document.addEventListener('DOMContentLoaded', function () {
            const animatedElements = document.querySelectorAll('.fade-in-up');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            animatedElements.forEach(el => {
                el.style.animationPlayState = 'paused';
                observer.observe(el);
            });
        });
    </script>
</body>

</html>