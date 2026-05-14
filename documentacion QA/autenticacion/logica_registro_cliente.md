**Vista:** `views/auth/registro_cliente.php`  
**Controlador:** `controllers/auth/RegistroController.php` → acción `registrar`  
**Modelo:** `models/Cliente.php`

Formulario envía POST con action=registrar. JavaScript valida que password y confirmar_password coincidan. Controlador obtiene datos con trim(), valida campos vacíos y que passwords coincidan. Llama `existeEmail()` que hace SELECT WHERE email. Si existe: alert error y redirige. Llama `registrar()` que hace INSERT con id_rol=3 (cliente), estado='Activo', username generado desde documento, password con `password_hash()`. Toast success/error y redirige. Vista muestra alert con SweetAlert.
