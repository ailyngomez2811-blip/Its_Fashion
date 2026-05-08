**Vista:** `views/admin/productos.php`  
**Controlador:** `controllers/admin/ProductoController.php` → acción `editar`  
**Modelo:** `models/Producto.php`

Click en botón editar ejecuta `editarProducto(id)` que hace fetch GET a action=obtener. Controlador llama `obtener()` que hace SELECT WHERE id_producto. Retorna JSON. JavaScript abre modal, rellena campos y cambia action a 'editar'. Submit ejecuta fetch POST con todos los campos. Controlador verifica rol admin, valida campos y precios. Llama `actualizar()` que hace UPDATE usando foreach para bindValue. Retorna JSON. JavaScript muestra toast y ejecuta `location.reload()` recargando página completa.
