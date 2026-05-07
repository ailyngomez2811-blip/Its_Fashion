**Vista:** `views/admin/categorias.php`
**Controlador:** `controllers/admin/CategoriaController.php` → acción `crear`
**Modelo:** `models/Categoria.php`

1. Controlador verifica sesión y rol admin. Click en "Nueva categoría" abre modal con input de nombre y textarea de descripción.
2. JavaScript valida campo nombre no vacío. Crea FormData con action='crear', nombre, descripcion. Ejecuta fetch POST.
3. Controlador obtiene nombre con trim(), descripcion con trim(). Valida if (empty($nombre)) retorna error.
4. Modelo hace prepare() de INSERT INTO categoria (nombre, descripcion) VALUES (:nombre, :descripcion). Ejecuta y retorna boolean.
5. Fetch recibe JSON: if (d.ok) muestra toast y recarga tabla.
