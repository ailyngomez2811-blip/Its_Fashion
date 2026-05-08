**Vista:** `views/clientes/dashboard_cliente.php`  
**Controlador:** `controllers/clientes/ClienteController.php`  
**Modelo:** `models/Cliente.php`, `models/Venta.php`

Al cargar vista, JavaScript envía fetch usando $_SESSION['user_id']. Resumen compras: SELECT COUNT(*), SUM(total) FROM venta WHERE id_cliente. Última compra: SELECT fecha, total ORDER BY fecha DESC LIMIT 1. Devoluciones pendientes: SELECT COUNT con JOIN WHERE id_cliente AND estado='Pendiente'. JavaScript renderiza bienvenida, cards con: total compras, monto gastado, última compra, devoluciones pendientes. Si sin compras: mensaje invitando a explorar productos. Accesos rápidos: Mis compras, Mis devoluciones, Mi perfil.
