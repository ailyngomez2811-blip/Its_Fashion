**Vista:** `views/admin/usuarios.php`  
**Controlador:** `controllers/auth/UsuarioController.php` → acción `create`  
**Modelo:** `models/Usuario.php`

Tabla renderizada con foreach de PHP iterando variable `$users` obtenida con query SQL directo. Cada fila `<tr>` tiene atributo `data-search` con texto concatenado en minúsculas. Input búsqueda ejecuta función JavaScript `filterTable()` que itera filas con `querySelectorAll('#table-body tr')` y oculta las que no coinciden con `row.dataset.search?.includes(q)`. NO usa DataTables, filtrado manual con JavaScript. NO hay paginación.

Click en "Nuevo usuario" abre modal. Submit envía POST a UsuarioController con datos: nombre, apellido, username, email, telefono, password, id_rol (default 2), estado (default 'Activo'). Controlador verifica sesión admin, llama `existeDuplicado()` que hace SELECT con username o email. Si existe: toast error y redirige. Llama `registrar()` que hace INSERT con `password_hash()`. Toast success/error y redirige recargando página completa.
