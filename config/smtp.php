<?php
// Configuración SMTP.
// Las credenciales NO se guardan en el repositorio: defínelas con variables de entorno
// o copia config/smtp.local.example.php a config/smtp.local.php (ignorado por git).

$SMTP = [
    'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'port' => getenv('SMTP_PORT') ?: 587,
    'user' => getenv('SMTP_USER') ?: '',
    // Las contraseñas de aplicación de Google se muestran con espacios; se eliminan al usarlas.
    'pass' => getenv('SMTP_PASS') ?: '',
    // valores aceptados: 'tls' o 'ssl'
    'secure' => getenv('SMTP_SECURE') ?: 'tls',
    'from_email' => getenv('SMTP_FROM') ?: '',
    'from_name' => getenv('SMTP_FROM_NAME') ?: 'Requejo Fashion Lab',
];

$smtpLocalFile = __DIR__ . '/smtp.local.php';
if (is_file($smtpLocalFile)) {
    $SMTP = array_merge($SMTP, require $smtpLocalFile);
}

if (empty($SMTP['from_email'])) {
    $SMTP['from_email'] = $SMTP['user'];
}
