**Vista:** `views/clientes/dashboard_cliente.php`
**Controlador:** `controllers/clientes/ClienteController.php`
**Modelo:** `models/Cliente.php`, `models/Venta.php`

1. Al cargar vista, JavaScript envía peticiones AJAX para obtener datos del cliente autenticado usando $_SESSION['user_id'].
2. Para resumen de compras: ejecuta SELECT COUNT(*) AS total_compras, SUM(total) AS monto_gastado FROM venta WHERE id_cliente. Retorna totales.
3. Para última compra: ejecuta SELECT fecha, total FROM venta WHERE id_cliente ORDER BY fecha DESC LIMIT 1. Muestra fecha formateada y monto.
4. Para devoluciones pendientes: ejecuta SELECT COUNT(*) FROM devoluciones d JOIN venta v ON v.id_venta=d.id_venta WHERE v.id_cliente AND d.estado='Pendiente'.
5. JavaScript renderiza mensaje de bienvenida con $_SESSION['user_nombre']. Muestra cards con: total de compras realizadas, monto total gastado, última compra (fecha y monto), devoluciones pendientes (badge).
6. If cliente no tiene compras: muestra mensaje "Aún no has realizado compras" con ilustración y botón invitando a explorar productos.
7. Accesos rápidos con botones: "Mis compras" redirige a mis_compras.php, "Mis devoluciones" a mis_devoluciones.php, "Mi perfil" a mi_perfil.php.
