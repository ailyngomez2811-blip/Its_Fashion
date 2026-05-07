**Vista:** `views/auth/reset_password.php`
**Controlador:** `controllers/auth/RecuperacionController.php` → acción `resetear`
**Modelo:** `models/Usuario.php`

1. Vista recibe token por GET. Al cargar, valida token con `validarToken($token)` que verifica token existe y no ha expirado.
2. If token inválido o expirado: muestra mensaje de error y link para solicitar nuevo token.
3. If token válido: muestra formulario con inputs: password_nueva, confirmar_password.
4. JavaScript valida que ambas contraseñas coincidan y tengan mínimo 6 caracteres. Ejecuta fetch POST con action='resetear', token, password.
5. Controlador obtiene token y password. Valida if (empty($token) || empty($password)) retorna error. Valida if (strlen($password) < 6) retorna error.
6. Llama `actualizarPasswordYLimpiarToken($token, $password)` que hace password_hash(), UPDATE usuario SET password, reset_token=NULL, token_expiracion=NULL WHERE reset_token. Retorna execute() && rowCount() > 0.
7. If exitoso: muestra mensaje "Contraseña actualizada" con SweetAlert y redirige a login.php después de 2 segundos.
