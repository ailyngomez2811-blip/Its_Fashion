**Vista:** Cualquier vista con botón de cerrar sesión
**Controlador:** `controllers/auth/UsuarioController.php` → acción `logout`

1. Click en botón "Cerrar sesión" (generalmente en sidebar) ejecuta link href a UsuarioController.php?action=logout.
2. Controlador hace session_start() para acceder a la sesión actual. Switch case 'logout': ejecuta session_destroy() que elimina todas las variables de sesión y destruye la sesión.
3. Redirige con header('Location: ../../public/index.php') a la landing page o página de login. Termina con exit.
