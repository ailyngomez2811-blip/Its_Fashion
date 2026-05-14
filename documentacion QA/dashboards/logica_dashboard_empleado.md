**Vista:** `views/empleado/dashboard_empleado.php`  
**Controlador:** Similar a admin con acceso limitado  
**Modelos:** `Venta.php`, `Caja.php`

Al cargar vista, JavaScript envía fetch para datos del día. Ejecuta `cajaActiva()` para verificar estado. Si existe: muestra saldo. Si no: botón "Abrir caja". Ventas del día: SELECT WHERE DATE(fecha)=CURDATE(), usa SUM(total) y COUNT(*). JavaScript renderiza bienvenida con $_SESSION['user_nombre'], cards con ventas y total del día. Menú lateral con PHP muestra solo opciones permitidas: if rol=2 muestra Ventas, Caja, Inventario. Oculta Usuarios, Clientes, Proveedores, Categorías, Reportes.
