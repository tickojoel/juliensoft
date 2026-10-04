<?php
// Credenciales del panel de administración.
// Se guardan como hash (password_hash), nunca en texto plano. Defínelas con variables
// de entorno o copia config/admin.local.example.php a config/admin.local.php (ignorado por git).

$ADMIN = [
    'username' => getenv('ADMIN_USER') ?: '',
    'password_hash' => getenv('ADMIN_PASSWORD_HASH') ?: '',
];

$adminLocalFile = __DIR__ . '/admin.local.php';
if (is_file($adminLocalFile)) {
    $ADMIN = array_merge($ADMIN, require $adminLocalFile);
}
