**Vista:** `public/index.php`  
**Descripción:** Página de inicio pública del sistema

Landing page para usuarios no autenticados. Muestra información de Its Fashion. Secciones: Hero con logo y eslogan, botones "Iniciar sesión" y "Registrarse" que redirigen a views/auth/login.php y views/auth/registro_cliente.php. Sección características/beneficios. Galería productos destacados (opcional). Footer con contacto. Si usuario tiene sesión activa (verifica $_SESSION['user_id']): redirige automáticamente a dashboard según rol con `header('Location: ...')`. Diseño responsive con Tailwind CSS, animaciones CSS transitions.
