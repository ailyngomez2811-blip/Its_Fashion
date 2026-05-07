**Vista:** `views/admin/clientes.php`
**Controlador:** `controllers/admin/ClienteController.php`
**Modelo:** `models/Cliente.php`

1. Vista carga lista de clientes con `listar()` que hace SELECT de usuario WHERE id_rol=3 LEFT JOIN con venta y devoluciones. Usa COUNT(DISTINCT) para contar compras y devoluciones por cliente. GROUP BY id_usuario.
2. Tabla muestra: nombre, apellido, email, telefono, estado (badge), fecha registro, número de compras, número de devoluciones, acciones.
3. Botón "Ver compras" ejecuta fetch a endpoint que llama `compras($id_cliente)`. Muestra modal con historial de compras del cliente.
4. Botón "Ver devoluciones" ejecuta fetch a `devoluciones($id_cliente)`. Muestra modal con historial de devoluciones.
5. Toggle de estado ejecuta `cambiarEstado($id, $estado)` que hace UPDATE usuario SET estado WHERE id_usuario AND id_rol=3.
