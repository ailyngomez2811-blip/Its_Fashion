**Vista:** `views/auth/login.php`  
**Controlador:** `controllers/auth/UsuarioController.php` → acción `login`  
**Modelo:** `models/Usuario.php`

Formulario envía POST con action=login. JavaScript valida campos no vacíos antes de enviar. Controlador obtiene identifier y password con trim(), valida vacíos. Llama `verificarCredenciales()` que hace SELECT WHERE (username OR email) AND estado='Activo'. Usa `password_verify()` para comparar contraseña cifrada. Si válido: guarda datos en sesión ($_SESSION['user_id'], user_nombre, user_rol, etc). Switch por rol: case 1 redirige a admin/dashboard, case 2 a empleado/dashboard, case 3 a clientes/dashboard. Si falla: alert error y redirige a login.
