**Vista:** `views/auth/recuperar_password.php`  
**Controlador:** `controllers/auth/RecuperacionController.php`  
**Modelo:** `models/Usuario.php`

Formulario con input email. JavaScript valida formato. Ejecuta fetch POST con action=solicitar. Controlador llama `guardarTokenRecuperacion()` que genera token con `bin2hex(random_bytes(32))`, calcula expiración +1 hora, hace UPDATE usuario SET reset_token, token_expiracion WHERE email AND estado='Activo'. Envía email con link conteniendo token. Usuario hace click en link que lleva a reset_password.php?token=X. Vista valida token con `validarToken()` que hace SELECT WHERE reset_token AND token_expiracion > NOW(). Si válido muestra formulario de nueva contraseña.
