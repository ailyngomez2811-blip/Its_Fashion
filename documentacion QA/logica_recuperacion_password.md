**Vista:** `views/auth/recuperar_password.php`
**Controlador:** `controllers/auth/RecuperacionController.php`
**Modelo:** `models/Usuario.php`

1. Formulario con input de email. Al enviar, JavaScript valida formato de email. Ejecuta fetch POST con action='solicitar', email.
2. Controlador obtiene email con trim(). Valida if (empty($email)) retorna error. Llama `guardarTokenRecuperacion($email, $token)` del modelo.
3. Modelo hace SELECT para verificar que email existe y estado='Activo'. Genera token único con bin2hex(random_bytes(32)). Calcula expiracion = date('+1 hour'). UPDATE usuario SET reset_token, token_expiracion WHERE email AND estado='Activo'. Retorna rowCount() > 0.
4. If exitoso: envía email con link de recuperación conteniendo el token (simulado o real según configuración). Muestra mensaje "Revisa tu correo".
5. Usuario hace click en link que lo lleva a reset_password.php?token=X. Vista valida token llamando `validarToken($token)` que hace SELECT WHERE reset_token AND token_expiracion > NOW(). If válido muestra formulario de nueva contraseña.
6. Al enviar nueva contraseña, llama `actualizarPasswordYLimpiarToken($token, $nueva_password)` que hace password_hash(), UPDATE usuario SET password, reset_token=NULL, token_expiracion=NULL WHERE reset_token. Redirige a login.
