**Vista:** `views/admin/categorias.php`  
**Controlador:** `controllers/admin/CategoriaController.php` → acción `editar`  
**Modelo:** `models/Categoria.php`

Click "Editar" carga datos en modal. Usuario modifica campos. JavaScript ejecuta fetch POST con action=editar, id, nombre, descripcion. Controlador valida id y nombre no vacíos. Modelo hace UPDATE categoria SET nombre, descripcion WHERE id_categoria. Retorna JSON. JavaScript actualiza tabla sin recargar.
