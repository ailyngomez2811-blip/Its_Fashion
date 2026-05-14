**Vista:** `views/clientes/mis_devoluciones.php`  
**Controlador:** `controllers/clientes/ClienteController.php` → acción `misDevoluciones`  
**Modelo:** Consulta directa en controlador

Al cargar ejecuta fetch GET a action=misDevoluciones. Controlador hace SELECT devoluciones JOIN venta WHERE v.id_cliente = $_SESSION['user_id'] ORDER BY fecha DESC. JavaScript renderiza tabla con: fecha solicitud, venta asociada, motivo, monto, estado (badge: amarillo Pendiente, verde Aceptada, rojo Rechazada), botón "Ver detalle". Click detalle ejecuta fetch para productos devueltos y muestra modal.
