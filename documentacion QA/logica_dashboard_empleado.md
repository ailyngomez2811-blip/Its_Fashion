**Vista:** `views/empleado/dashboard_empleado.php`
**Controlador:** Similar a admin pero con acceso limitado
**Modelos:** `Venta.php`, `Caja.php`

1. Al cargar vista, JavaScript envía peticiones AJAX para obtener datos del día actual.
2. Ejecuta `cajaActiva()` para verificar estado de caja. If existe: muestra saldo actual. If no existe: muestra botón "Abrir caja".
3. Para ventas del día: ejecuta SELECT con WHERE DATE(fecha)=CURDATE() para obtener total vendido hoy y número de transacciones. Usa SUM(total) y COUNT(*).
4. JavaScript renderiza mensaje de bienvenida personalizado con $_SESSION['user_nombre']. Muestra cards con: ventas realizadas hoy, total vendido hoy.
5. Menú lateral renderizado con PHP muestra solo opciones permitidas: if ($rol === 2) muestra Ventas, Caja, Inventario (solo lectura). Oculta Usuarios, Clientes, Proveedores, Categorías, Reportes.
6. Accesos rápidos con botones: "Nueva venta" redirige a ventas.php, "Gestionar caja" a caja.php, "Ver inventario" a inventario.php.
