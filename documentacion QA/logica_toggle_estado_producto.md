**Vista:** `views/admin/productos.php`  
**Controlador:** `controllers/admin/ProductoController.php` → acción `toggleEstado`  
**Modelo:** `models/Producto.php`

Toggle con clase 'on' o 'off'. Click ejecuta `toggleEstado(element, id)` que determina nuevo estado y ejecuta fetch a action=toggleEstado. Controlador valida parámetros y llama `cambiarEstado()` que hace UPDATE productos SET estado. Retorna JSON. JavaScript cambia clases del toggle y actualiza label sin recargar página.
