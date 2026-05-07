**Vista:** `views/clientes/mis_compras.php`
**Controlador:** `controllers/clientes/ClienteController.php` → acción `misCompras`
**Modelo:** `models/Venta.php`

1. Al cargar vista, JavaScript ejecuta fetch GET a action=misCompras.
2. Controlador verifica sesión y rol=3. Llama `porCliente($_SESSION['user_id'])` del modelo Venta.
3. Modelo hace SELECT de venta JOIN detalle_venta JOIN productos WHERE id_cliente. Usa GROUP_CONCAT para concatenar productos con formato "nombre × cantidad" separados por coma. GROUP BY id_venta ORDER BY fecha DESC.
4. Retorna array con: id_venta, fecha, total, metodo_pago, estado, productos (string concatenado).
5. JavaScript renderiza tabla con columnas: fecha, productos, total, método de pago, estado (badge), acciones (botones "Ver detalle" y "Solicitar devolución" si aplica).
6. Botón "Solicitar devolución" solo visible si: fecha de compra < 30 días (validado con JavaScript comparando fechas) y no tiene devolución pendiente/aceptada.
