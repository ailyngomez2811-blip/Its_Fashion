**Vista:** `views/empleado/inventario.php`  
**Controlador:** Solo lectura  
**Modelo:** `models/Producto.php`

Tabla renderizada con foreach de `$productos`. Empleado tiene acceso solo lectura. Vista muestra: nombre, talla, color, stock, stock_minimo, precio_venta, estado. NO tiene botones crear, editar o eliminar. Puede usar filtros de búsqueda para encontrar productos. Útil para verificar disponibilidad antes de ventas.
