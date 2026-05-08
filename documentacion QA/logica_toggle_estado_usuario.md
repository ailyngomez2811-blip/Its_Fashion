**Vista:** `views/admin/usuarios.php`  
**Controlador:** `controllers/auth/UsuarioController.php` → acción `toggleEstado`  
**Modelo:** No usa modelo, query directo

Toggle renderizado con clase 'on' o 'off' según estado. Click ejecuta `toggleEstado(element, id)` que determina nuevo estado y ejecuta fetch GET a action=toggleEstado. Controlador hace UPDATE directo sin modelo. JavaScript cambia clases del toggle y actualiza label sin recargar página.
