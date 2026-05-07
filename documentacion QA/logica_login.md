**Vista:** `views/auth/login.php`
**Controlador:** `controllers/auth/UsuarioController.php` → acción `login`
**Modelo:** `models/Usuario.php`

1. Formulario envía POST a action=login. JavaScript valida campos no vacíos y password mínimo 6 caracteres antes de enviar. Si falla, muestra error sin recargar.
2. Controlador hace session_start(). Obtiene identifier y password con trim() y ?? ''. Valida if (empty($identifier) || empty($password)), guarda alert en sesión, redirige a login.php con header() y exit.
3. Llama `verificarCredenciales($identifier, $password)` del modelo. Este hace SELECT con WHERE (username = :identifier OR email = :identifier) AND estado = 'Activo' LIMIT 1.
4. If ($stmt->rowCount() > 0) hace fetch() y obtiene row. Usa password_verify($password, $row['password']) para comparar contraseña cifrada. If verifica correctamente, retorna row; else retorna false.
5. If ($user) es true: guarda en sesión con $_SESSION['user_id'], $_SESSION['user_nombre'], $_SESSION['user_apellido'], $_SESSION['user_rol'], $_SESSION['user_email'].
6. Switch con cast (int)$user['id_rol']: case 1 redirige a views/admin/dashboard.php, case 2 a views/empleado/dashboard_empleado.php, case 3 a views/clientes/dashboard_cliente.php, default a login.php. Termina con exit.
7. If credenciales incorrectas: guarda alert en sesión con mensaje de error, redirige a login.php con header() y exit.
