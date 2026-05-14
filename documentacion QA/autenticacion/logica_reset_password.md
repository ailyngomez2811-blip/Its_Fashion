**Vista:** `views/auth/reset_password.php`  
**Controlador:** `controllers/auth/RecuperacionController.php` → acción `resetear`  
**Modelo:** `models/Usuario.php`

Vista recibe token por GET. Al cargar valida token con `validarToken()`. Si inválido: muestra error. Si válido: muestra formulario con password_nueva y confirmar_password. JavaScript valida que coincidan y mínimo 6 caracteres. Ejecuta fetch POST con action=resetear. Controlador valida campos y llama `actualizarPasswordYLimpiarToken()` que hace `password_hash()`, UPDATE usuario SET password, reset_token=NULL, token_expiracion=NULL WHERE reset_token. Muestra mensaje success con SweetAlert y redirige a login.
