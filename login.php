<?php
session_start();
require_once 'config/database.php';

// Crear instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor ingresa tu correo y contraseña';
    } else {
        // Consulta para verificar las credenciales del usuario
        $query = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                // Guardar el carrito actual antes de autenticar
                $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                
                // Destruir la sesión actual
                session_destroy();
                
                // Iniciar una nueva sesión
                session_start();
                
                // Establecer las variables de sesión del usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_created_at'] = $user['created_at'];
                
                // Restaurar el carrito
                if (!empty($cart)) {
                    $_SESSION['cart'] = $cart;
                }
                
                // Redirigir al perfil
                header('Location: perfil.php');
                exit();
            } else {
                $error = 'Correo o contraseña incorrectos';
            }
        } else {
            $error = 'Correo o contraseña incorrectos';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-purple-600 mb-2">
                <i class="fas fa-user-circle mr-2"></i>Iniciar Sesión
            </h1>
            <p class="text-center text-sm text-gray-600 mb-6">
                O <a href="registro.php" class="text-purple-600 hover:text-purple-800 font-medium">regístrate</a> para crear una cuenta
            </p>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label for="email-address" class="block text-gray-700 text-sm font-bold mb-2">Correo electrónico</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                           placeholder="tucorreo@ejemplo.com">
                </div>
                
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Recordarme
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="recuperar.php" class="font-medium text-purple-600 hover:text-purple-800">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 
                               focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">
                    ¿No tienes una cuenta? 
                    <a href="registro.php" class="font-medium text-purple-600 hover:text-purple-800">
                        Regístrate
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
