**Vista:** `views/admin/usuarios.php`  
**Controlador:** `controllers/auth/UsuarioController.php` → acción `update`  
**Modelo:** `models/Usuario.php`

Click en botón editar ejecuta `openModal('edit', data)` pasando objeto JSON. Modal se abre con campos pre-llenados y action='update'. Contraseña opcional (solo actualiza si se ingresa nueva). Submit envía POST. Controlador verifica sesión admin, llama `existeDuplicado()` excluyendo ID actual. Si existe: toast error y redirige. Construye query UPDATE dinámico, si password no vacío lo incluye con `password_hash()`. Ejecuta con bindParam. Toast success/error y redirige recargando página completa.
