**Vista:** Cualquier vista con botón de cerrar sesión  
**Controlador:** `controllers/auth/UsuarioController.php` → acción `logout`

Click en botón "Cerrar sesión" ejecuta link href a UsuarioController.php?action=logout. Controlador hace `session_start()` y ejecuta `session_destroy()` eliminando todas las variables de sesión. Redirige con `header('Location: ../../public/index.php')` a landing page y termina con exit.
