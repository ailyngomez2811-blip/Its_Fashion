**Vista:** `public/index.php`
**Descripción:** Página de inicio pública del sistema

1. Landing page es la primera vista que ve un usuario no autenticado. Muestra información de la tienda Its Fashion.
2. Secciones típicas: Hero con logo y eslogan, botones "Iniciar sesión" y "Registrarse" que redirigen a views/auth/login.php y views/auth/registro_cliente.php.
3. Sección de características o beneficios del sistema. Galería de productos destacados (opcional). Footer con información de contacto.
4. Si usuario ya tiene sesión activa (verifica $_SESSION['user_id']), redirige automáticamente a su dashboard según rol con header('Location: ...').
5. Diseño responsive con Tailwind CSS. Animaciones suaves con CSS transitions.
