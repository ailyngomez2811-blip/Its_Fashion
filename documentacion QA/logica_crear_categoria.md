**Vista:** `views/admin/categorias.php`  
**Controlador:** `controllers/admin/CategoriaController.php` → acción `crear`  
**Modelo:** `models/Categoria.php`

Tabla renderizada con foreach de `$categorias`. Click "Nueva categoría" abre modal con inputs nombre y descripcion. JavaScript valida nombre no vacío. Ejecuta fetch POST con action=crear. Controlador verifica sesión admin, obtiene datos con trim(), valida nombre no vacío. Modelo hace INSERT INTO categoria. Retorna JSON. JavaScript muestra toast y recarga tabla.
