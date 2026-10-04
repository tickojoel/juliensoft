# juliensoft

Tienda online (PHP + MySQL) pensada para correr en XAMPP.

## Instalación local

1. Copia el proyecto dentro de `htdocs` de XAMPP (por ejemplo `htdocs/Tienda_kimberly`).
2. Inicia Apache y MySQL desde el panel de XAMPP.
3. Crea la base de datos importando `database/create_tables.sql` (phpMyAdmin → Importar, o
   `mysql -u root < database/create_tables.sql`). La conexión está en `config/database.php`.
4. Configura el administrador: copia `config/admin.local.example.php` a `config/admin.local.php`,
   elige un usuario y pega el hash de tu contraseña, generado con:

   ```
   php -r "echo password_hash('tu_contraseña', PASSWORD_DEFAULT), PHP_EOL;"
   ```

5. (Opcional, para recuperar contraseñas por email) copia `config/smtp.local.example.php` a
   `config/smtp.local.php` y pon tu correo y una
   [contraseña de aplicación de Google](https://myaccount.google.com/apppasswords).
6. Abre `http://localhost/Tienda_kimberly/` (tienda) y `http://localhost/Tienda_kimberly/admin/` (panel).

Los archivos `config/*.local.php` están en `.gitignore`: **nunca subas credenciales al repositorio**.
También se pueden usar variables de entorno (`ADMIN_USER`, `ADMIN_PASSWORD_HASH`, `SMTP_USER`,
`SMTP_PASS`, ...).

## Scripts de línea de comandos

- `php tools/test_smtp.php` — prueba la configuración SMTP.
- `php tools/add_product_sm2503.php` — agrega un producto de ejemplo.
