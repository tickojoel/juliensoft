<?php
session_start();
require_once 'config/database.php';
require 'vendor/autoload.php';
// Cargar configuración SMTP (usar variables de entorno si están definidas)
require_once __DIR__ . '/config/smtp.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Crear instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        $error = 'Por favor ingrese su correo electrónico';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingresa un correo electrónico válido.';
    } else {
        try {
            // Verificar si el correo existe
            $query = "SELECT id, nombre FROM usuarios WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $token = bin2hex(random_bytes(50));
                $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Guardar token en la base de datos
                $query = "UPDATE usuarios SET reset_token = :token, reset_expires = :expira WHERE email = :email";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':expira', $expira);
                $stmt->bindParam(':email', $email);
                
                if ($stmt->execute()) {
                    // Configuración de PHPMailer
                    $mail = new PHPMailer(true);
                    
                    try {
                        // Configuración SMTP (tomada desde config/smtp.php)
                        $mail->isSMTP();
                        $mail->Host = $SMTP['host'] ?? 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $SMTP['user'] ?? '';
                        $mail->Password = str_replace(' ', '', $SMTP['pass'] ?? '');
                        // Seleccionar el cifrado apropiado
                        $secure = strtolower($SMTP['secure'] ?? 'tls');
                        if ($secure === 'ssl') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        } else {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        }
                        $mail->Port = (int)($SMTP['port'] ?? 587);
                        $mail->CharSet = 'UTF-8';
                        // Registrar salida de depuración en el log de PHP (no mostrar en pantalla)
                        $mail->SMTPDebug = SMTP::DEBUG_OFF;
                        $mail->Debugoutput = function($str, $level) {
                            error_log("PHPMailer debug [{$level}]: {$str}");
                        };

                        // Configuración del correo
                        $mail->setFrom($SMTP['from_email'] ?? $mail->Username, $SMTP['from_name'] ?? 'Recuperación de Contraseña');
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperación de Contraseña';
                        
                        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/Tienda_kimberly/reset_password.php?token=" . $token;
                        
                        $mail->Body = "
                            <p>Hola,</p>
                            <p>Recibiste una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:</p>
                            <p><a href='$reset_link'>Restablecer Contraseña</a></p>
                            <p>Este enlace es válido por 1 hora.</p>
                            <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                        ";
                        
                        $mail->send();
                        $success = 'Se ha enviado un enlace de recuperación a tu correo electrónico.';
                    } catch (Exception $e) {
                        $error = "Error al enviar el correo: " . $mail->ErrorInfo;
                        error_log("Error al enviar correo: " . $e->getMessage());
                    }
                } else {
                    $error = 'Error al procesar la solicitud. Por favor, inténtalo de nuevo.';
                }
            } else {
                $error = 'No existe ninguna cuenta con ese correo electrónico.';
            }
        } catch(PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Tienda Kimberly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Recuperar Contraseña</h1>
            <p class="text-gray-600 mt-2">Ingresa tu correo electrónico para recibir un enlace de recuperación.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="recuperar.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="tu@email.com">
            </div>

            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Enviar enlace de recuperación
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="login.php" class="text-sm font-medium text-purple-600 hover:text-purple-800">
                ← Volver al inicio de sesión
            </a>
        </div>
    </div>
</body>
</html>