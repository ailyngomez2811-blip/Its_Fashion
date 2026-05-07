**Vista:** `views/admin/usuarios.php`
**Controlador:** `controllers/auth/UsuarioController.php` → acción `update`
**Modelo:** `models/Usuario.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 1) redirige a login (solo admin).
2. Click en "Editar" carga datos del usuario en modal. Usuario modifica campos. Obtiene: id (int), nombre, apellido, username, email, telefono, estado, password (opcional).
3. Llama `existeDuplicado($user, $email, $id)` que hace SELECT excluyendo el id actual con AND id_usuario != :id. If existe: guarda toast error, redirige y exit.
4. Construye SQL dinámicamente: "UPDATE usuario SET nombre=:n, apellido=:a, username=:u, email=:e, telefono=:t, estado=:s". If (!empty($pw)) concatena ", password=:p". Concatena " WHERE id_usuario=:id".
5. Hace prepare() y bindParam() de todos los campos. If password no está vacío: hace password_hash() y bindParam(':p', $hash). Ejecuta con execute().
6. If exitoso: guarda toast success. Else: guarda toast error. Redirige a usuarios.php.
