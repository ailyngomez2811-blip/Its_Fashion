**Vista:** `views/clientes/mis_devoluciones.php`
**Controlador:** `controllers/clientes/ClienteController.php` → acción `misDevoluciones`
**Modelo:** Consulta directa en controlador

1. Al cargar vista, JavaScript ejecuta fetch GET a action=misDevoluciones.
2. Controlador hace SELECT de devoluciones JOIN venta WHERE v.id_cliente = $_SESSION['user_id'] ORDER BY fecha DESC. Retorna: id_devolucion, fecha, motivo, total_devolucion, estado, id_venta.
3. JavaScript renderiza tabla con columnas: fecha solicitud, venta asociada, motivo, monto, estado (badge con colores: amarillo para Pendiente, verde para Aceptada, rojo para Rechazada), acciones (botón "Ver detalle").
4. Click en "Ver detalle" ejecuta fetch para obtener productos devueltos y muestra modal con información completa.
