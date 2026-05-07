**Vista:** `views/admin/categorias.php`
**Controlador:** `controllers/admin/CategoriaController.php` → acción `editar`
**Modelo:** `models/Categoria.php`

1. Click en "Editar" carga datos en modal. Usuario modifica nombre y/o descripción.
2. JavaScript crea FormData con action='editar', id, nombre, descripcion. Ejecuta fetch POST.
3. Controlador obtiene id (int), nombre y descripcion con trim(). Valida if (!$id || empty($nombre)) retorna error.
4. Modelo hace UPDATE categoria SET nombre=:nombre, descripcion=:descripcion WHERE id_categoria=:id. Retorna boolean.
5. Fetch actualiza tabla sin recargar.
