**Vista:** `views/admin/clientes.php` modal  
**Controlador:** `controllers/admin/ClienteController.php`  
**Modelo:** `models/Cliente.php`

Click "Ver devoluciones" ejecuta fetch GET con id_cliente. Modelo `devoluciones()` hace SELECT devoluciones JOIN venta WHERE id_cliente ORDER BY fecha DESC. JavaScript renderiza modal con tabla: fecha solicitud, venta asociada, motivo, monto, estado (badge con colores según estado).
