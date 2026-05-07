**Vista:** `views/admin/clientes.php` modal
**Controlador:** `controllers/admin/ClienteController.php`
**Modelo:** `models/Cliente.php`

1. Click en "Ver compras" de un cliente ejecuta fetch GET con id_cliente.
2. Modelo `compras($id_cliente)` hace SELECT de venta JOIN detalle_venta JOIN productos WHERE id_cliente. Usa GROUP_CONCAT para listar productos. GROUP BY id_venta ORDER BY fecha DESC.
3. JavaScript renderiza modal con tabla mostrando: fecha, productos (concatenados), total, método de pago, estado. Incluye botón "Ver detalle" para cada venta.
