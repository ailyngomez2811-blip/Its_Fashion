**Vista:** `views/clientes/mis_compras.php`  
**Controlador:** `controllers/clientes/ClienteController.php` → acción `misCompras`  
**Modelo:** `models/Venta.php`

Al cargar ejecuta fetch GET a action=misCompras. Controlador verifica rol=3, llama `porCliente($_SESSION['user_id'])`. Modelo hace SELECT venta JOIN detalle_venta JOIN productos WHERE id_cliente. Usa GROUP_CONCAT para concatenar productos "nombre × cantidad". GROUP BY id_venta ORDER BY fecha DESC. JavaScript renderiza tabla con: fecha, productos, total, método pago, estado (badge), acciones. Botón "Solicitar devolución" visible si fecha < 30 días y sin devolución pendiente/aceptada.
