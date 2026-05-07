**Vista:** `views/auth/registro_cliente.php`
**Controlador:** `controllers/auth/RegistroController.php` → acción `registrar`
**Modelo:** `models/Cliente.php`

1. Formulario envía POST a action=registrar. JavaScript verifica que password y confirmar_password coincidan antes de enviar. If no coinciden, muestra alert y no envía.
2. Controlador hace session_start(). Obtiene datos con trim() y ?? '': nombre, apellido, documento, telefono, email, password, confirmar.
3. Valida if (empty()) de cualquier campo, guarda alert en sesión con icon='warning', redirige a registro_cliente.php con header() y exit.
4. Valida if ($password !== $confirmar) en servidor, guarda alert con icon='error', redirige y exit.
5. Llama `existeEmail($email)` del modelo Cliente. Este hace SELECT WHERE email LIMIT 1. If ($stmt->rowCount() > 0) retorna true. If existe, guarda alert 'Correo ya registrado', redirige y exit.
6. Crea array $datos con compact('nombre', 'apellido', 'documento', 'telefono', 'email', 'password'). Llama `registrar($datos)`.
7. Modelo `registrar()` hace prepare() de INSERT en tabla usuario con id_rol=3 (cliente), estado='Activo', username generado desde documento. Usa password_hash($datos['password'], PASSWORD_BCRYPT) para cifrar. Ejecuta con execute() y retorna boolean.
8. If registro exitoso: guarda alert con icon='success', title='¡Registro exitoso!', redirect='login.php'. Else: guarda alert con icon='error'.
9. Redirige a registro_cliente.php. Vista muestra alert con SweetAlert, if tiene redirect hace window.location después de cerrar.
