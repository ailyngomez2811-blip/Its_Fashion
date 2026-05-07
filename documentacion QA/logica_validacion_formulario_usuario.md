**Vista:** `views/admin/usuarios.php` y `views/auth/registro_cliente.php`
**Validación:** Cliente (JavaScript) y Servidor (PHP)

1. Validación cliente en JavaScript: formulario tiene onsubmit que ejecuta función de validación. Retorna false para prevenir submit si falla.
2. Valida campos obligatorios: foreach input required, verifica if (input.value.trim() === '') muestra mensaje de error específico y hace focus() en el campo. Retorna false.
3. Valida formato email: usa regex /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email). If no coincide muestra error.
4. Valida contraseña: if (password.length < 6) muestra error "Mínimo 6 caracteres". If hay confirmar_password, valida if (password !== confirmar) muestra error "Las contraseñas no coinciden".
5. Valida username: regex /^[a-zA-Z0-9_]{3,20}$/ para permitir solo alfanuméricos y guión bajo, entre 3 y 20 caracteres.
6. Validación servidor en PHP: repite todas las validaciones. Usa empty() para campos obligatorios, filter_var($email, FILTER_VALIDATE_EMAIL) para email, strlen() para longitud de contraseña.
7. Valida duplicados: llama `existeDuplicado()` que hace SELECT. If existe retorna error antes de intentar INSERT.
