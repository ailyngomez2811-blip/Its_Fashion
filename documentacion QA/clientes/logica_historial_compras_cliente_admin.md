**Vista:** `views/admin/clientes.php` modal  
**Controlador:** `controllers/admin/ClienteController.php`  
**Modelo:** `models/Cliente.php`

Click "Ver compras" ejecuta fetch GET con id_cliente. Modelo `compras()` hace SELECT venta JOIN detalle_venta JOIN productos WHERE id_cliente. Usa GROUP_CONCAT para listar productos. GROUP BY id_venta ORDER BY fecha DESC. JavaScript renderiza modal con tabla: fecha, productos (concatenados), total, método pago, estado, botón "Ver detalle".
