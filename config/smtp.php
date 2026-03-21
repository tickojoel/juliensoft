<?php
// SMTP configuration
// IMPORTANT: For security, prefer to set these values via environment variables and
// don't commit real passwords to the repo. This file provides sane defaults.

$SMTP = [
    'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'port' => getenv('SMTP_PORT') ?: 587,
    'user' => getenv('SMTP_USER') ?: 'joeljulien576@gmail.com',
    // If you paste a Google App Password, it may be displayed with spaces in the UI.
    // We'll strip spaces before using it in PHPMailer.
    'pass' => getenv('SMTP_PASS') ?: 'opmc wtjf vhqg utzi',
    // accepted values: 'tls' or 'ssl'
    'secure' => getenv('SMTP_SECURE') ?: 'tls',
    'from_email' => getenv('SMTP_FROM') ?: 'joeljulien576@gmail.com',
    'from_name' => getenv('SMTP_FROM_NAME') ?: 'Requejo Fashion Lab',
];
