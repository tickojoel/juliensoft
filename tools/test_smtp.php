<?php
// Script de prueba SMTP para diagnosticar autenticación con PHPMailer
// Ejecutar desde navegador o consola: php tools/test_smtp.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/smtp.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Preparar archivo de log
$logDir = __DIR__ . '/../storage';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/smtp_debug.log';

$debugBuffer = '';
$logger = function($str, $level) use (&$debugBuffer, $logFile) {
    $line = date('[Y-m-d H:i:s]') . " DEBUG[$level]: $str\n";
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    $debugBuffer .= $line;
};

$mail = new PHPMailer(true);
try {
    // Configuración desde config/smtp.php
    $mail->isSMTP();
    $mail->Host = $SMTP['host'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $SMTP['user'] ?? '';
    $mail->Password = str_replace(' ', '', $SMTP['pass'] ?? '');
    $secure = strtolower($SMTP['secure'] ?? 'tls');
    if ($secure === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }
    $mail->Port = (int)($SMTP['port'] ?? 587);

    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // nivel detallado
    $mail->Debugoutput = $logger;
    $mail->Timeout = 30;
    $mail->do_verp = false;

    // Envío a la misma cuenta para probar autenticación
    $mail->setFrom($SMTP['from_email'] ?? $mail->Username, $SMTP['from_name'] ?? 'SMTP Test');
    $mail->addAddress($SMTP['user']);
    $mail->Subject = 'Prueba SMTP desde Tienda_kimberly';
    $mail->Body = 'Mensaje de prueba para verificar conexión SMTP.';

    $sent = $mail->send();
    $status = $sent ? 'ENVIADO' : 'NO ENVIADO';
    echo "Resultado: $status\n";
} catch (Exception $e) {
    echo "Error PHPMailer: " . $e->getMessage() . "\n";
}

echo "Log escrito en: $logFile\n";
// Mostrar los últimos 2000 caracteres del buffer de depuración para ayudar al diagnóstico
$tail = strlen($debugBuffer) > 2000 ? substr($debugBuffer, -2000) : $debugBuffer;
echo "\n--- Última depuración ---\n";
echo $tail;
