<?php
session_start();
require_once 'config/database.php';

// Crear instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor ingresa un correo electrónico válido';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verificar si el correo ya está registrado
        $query = "SELECT id FROM usuarios WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = 'Este correo ya está registrado';
        } else {
            // Crear el usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            
            if ($stmt->execute()) {
                $success = '¡Registro exitoso! Ahora puedes iniciar sesión.';
                // Redirigir al login después de 2 segundos
                header('Refresh: 2; URL=login.php');
            } else {
                $error = 'Error al registrar el usuario. Por favor, inténtalo de nuevo.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Requejo Fashion Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-purple-600 mb-2">
                <i class="fas fa-user-plus mr-2"></i>Crear Cuenta
            </h1>
            <p class="text-center text-sm text-gray-600 mb-6">
                O <a href="login.php" class="text-purple-600 hover:text-purple-800 font-medium">inicia sesión</a> si ya tienes una cuenta
            </p>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre completo</label>
                    <input id="nombre" name="nombre" type="text" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                           placeholder="Tu nombre completo"
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Correo electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                           placeholder="tucorreo@ejemplo.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                    <input id="password" name="password" type="password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                           placeholder="•••••••• (mínimo 8 caracteres)">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
                    <input id="confirm_password" name="confirm_password" type="password" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
                           placeholder="Confirma tu contraseña">
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    </div>
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        Acepto los <a href="#" class="text-purple-600 hover:text-purple-800">Términos y Condiciones</a>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 
                               focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-opacity-50">
                    <i class="fas fa-user-plus mr-2"></i>Registrarse
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">
                    ¿Ya tienes una cuenta? 
                    <a href="login.php" class="font-medium text-purple-600 hover:text-purple-800">
                        Inicia sesión
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
