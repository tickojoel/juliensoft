<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $token = $_POST['token'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = 'Por favor completa todos los campos';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verificar el token
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Actualizar la contraseña y limpiar el token
            $stmt = $db->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            
            if ($stmt->execute([$hashed_password, $user['id']])) {
                $success = 'Tu contraseña ha sido restablecida correctamente. Ahora puedes iniciar sesión.';
                $show_form = false;
            } else {
                $error = 'Error al actualizar la contraseña. Por favor, inténtalo de nuevo.';
            }
        } else {
            $error = 'El enlace de recuperación es inválido o ha expirado.';
            $show_form = false;
        }
    }
} else {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        header('Location: login.php');
        exit;
    }
    
    // Verificar si el token es válido
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        $error = 'El enlace de recuperación es inválido o ha expirado.';
        $show_form = false;
    } else {
        $show_form = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Tienda Kimberly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Restablecer Contraseña</h1>
            <p class="text-gray-600 mt-2">Ingresa tu nueva contraseña.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
                <div class="mt-4">
                    <a href="login.php" class="font-medium text-purple-600 hover:text-purple-800">
                        Volver al inicio de sesión
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($show_form) && $show_form): ?>
        <form action="reset_password.php" method="POST" class="space-y-6">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                <input type="password" id="password" name="password" required minlength="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Mínimo 8 caracteres">
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Vuelve a escribir tu contraseña">
            </div>

            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Restablecer contraseña
            </button>
        </form>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="login.php" class="text-sm font-medium text-purple-600 hover:text-purple-800">
                ← Volver al inicio de sesión
            </a>
        </div>
    </div>
</body>
</html>