**Vista:** `views/admin/clientes.php` modal
**Controlador:** `controllers/admin/ClienteController.php`
**Modelo:** `models/Cliente.php`

1. Click en "Ver devoluciones" ejecuta fetch GET con id_cliente.
2. Modelo `devoluciones($id_cliente)` hace SELECT de devoluciones JOIN venta WHERE id_cliente ORDER BY fecha DESC.
3. JavaScript renderiza modal con tabla: fecha solicitud, venta asociada, motivo, monto, estado (badge con colores según estado).
