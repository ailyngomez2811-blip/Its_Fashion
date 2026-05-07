**Vista:** `views/admin/usuarios.php`
**Controlador:** `controllers/auth/UsuarioController.php` → acción `create`
**Modelo:** `models/Usuario.php`

1. Controlador verifica sesión y if ((int)$_SESSION['user_rol'] !== 1) redirige a login (solo admin).
2. Click en "Nuevo usuario" abre modal con formulario. Obtiene datos con trim() y cast: nombre, apellido, username, email, telefono, password, id_rol (int) con default 2, estado con default 'Activo', creado_por=$_SESSION['user_id'].
3. Llama `existeDuplicado($datos['username'], $datos['email'])` del modelo. Este hace SELECT WHERE (username = :u OR (email = :e AND email != '')) LIMIT 1. If ($stmt->rowCount() > 0) retorna true.
4. If existe duplicado: guarda toast en sesión con type='error', text='El username o email ya existen', redirige a usuarios.php con header() y exit.
5. Llama `registrar($datos)` del modelo. Este hace prepare() de INSERT en tabla usuario con todos los campos. Usa password_hash($datos['password'], PASSWORD_BCRYPT) para cifrar contraseña. Ejecuta y retorna boolean.
6. If registro exitoso: guarda toast con type='success', text='Empleado creado correctamente'. Else: guarda toast con type='error'. Redirige a usuarios.php.
7. Vista muestra toast con JavaScript y recarga tabla de usuarios.
