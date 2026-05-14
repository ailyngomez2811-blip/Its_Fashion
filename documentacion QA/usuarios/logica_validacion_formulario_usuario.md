**Vista:** `views/admin/usuarios.php` y `views/auth/registro_cliente.php`  
**Validación:** Cliente (JavaScript) y Servidor (PHP)

JavaScript: formulario con `onsubmit` que previene default. Valida campos obligatorios con trim(), formato email con regex, username alfanumérico 3-20 caracteres, contraseña mínimo 8 caracteres con mayúscula/minúscula/número, confirmar contraseña coincide. Muestra errores específicos por campo. Si pasa: ejecuta submit o fetch.

PHP: obtiene datos con trim(), valida campos vacíos con empty(), email con `filter_var()`, longitud contraseña con strlen(). Llama `existeDuplicado()` que hace SELECT. Si falla alguna validación: toast error y redirige. Usa `password_hash()` para cifrar, prepared statements con bindParam para prevenir SQL injection.
