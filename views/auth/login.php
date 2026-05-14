<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acceso al sistema de gestión de inventario y ventas Its Fashion">
    <title>Its Fashion | Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="icon" href="../../img/icono head .png" type="image/png">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0f172a',
                            light: '#f8fafc',
                            accent: '#2563eb',
                            /* blue-600 */
                            muted: '#64748b'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .error-shake {
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .hero-overlay {
            background: linear-gradient(to right, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.6) 100%);
        }
    </style>
</head>

<body class="bg-brand-light text-brand-dark min-h-screen flex items-center justify-center p-4 sm:p-8 antialiased">

    <div class="w-11/12 max-w-[90vw] bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">

        <!-- Left Panel - Branding (Image + Overlay) -->
        <div class="md:w-5/12 relative hidden md:flex flex-col justify-between overflow-hidden">
            <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Boutique">
            <div class="absolute inset-0 hero-overlay"></div>

            <div class="relative z-10 p-12">
                <a href="../../../public/index.php" class="inline-flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img src="../../img/logo en blanco.png" alt="Its Fashion Logo" class="w-12 h-auto object-contain">
                    <span class="font-serif text-2xl font-bold text-white tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
                </a>
            </div>

            <div class="relative z-10 p-12 mt-auto">
                <h2 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">El arte de<br>gestionar tu moda</h2>
                <p class="text-gray-300 font-light text-lg leading-relaxed mb-8">
                    Accede a tu panel para gestionar inventario, ventas, caja y mucho más. Todo el control de tu boutique en un solo lugar.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                            <i class="fas fa-lock text-brand-accent"></i>
                        </div>
                        <span class="font-light">Protección de datos con encriptación</span>
                    </div>
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                            <i class="fas fa-clock text-brand-accent"></i>
                        </div>
                        <span class="font-light">Acceso seguro disponible 24/7</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="w-full md:w-7/12 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-white relative">
            <a href="../../../public/index.php" class="md:hidden flex items-center gap-2 mb-8 text-brand-dark">
                <img src="../../img/logo en nombre.png" alt="Its Fashion Logo" class="w-8 h-auto object-contain">
                <span class="font-serif text-xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
            </a>

            <div class="max-w-md w-full mx-auto">
                <div class="mb-6">
                    <h1 class="text-3xl font-serif font-bold text-brand-dark tracking-tight mb-2">Iniciar Sesión</h1>
                    <p class="text-brand-muted text-base font-light">Ingresa tus credenciales para continuar</p>
                </div>

                <!-- Mensaje de error -->
                <div id="error-message" class="hidden mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-sm flex items-start gap-3 fade-in" role="alert">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span id="error-text" class="flex-1"></span>
                </div>

                <!-- Mensaje de éxito -->
                <div id="success-message" class="hidden mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg text-green-700 text-sm flex items-start gap-3 fade-in" role="alert">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span id="success-text" class="flex-1"></span>
                </div>

                <form id="login-form" method="POST" action="../../controllers/auth/UsuarioController.php?action=login" class="space-y-6" novalidate>
                    <div>
                        <label for="username" class="block text-sm font-medium text-brand-dark mb-2">
                            Usuario o Correo Electrónico
                        </label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                <i class="fas fa-user text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                placeholder="usuario o correo@ejemplo.com"
                                required
                                autocomplete="username"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light">
                        </div>
                        <span id="username-error" class="hidden text-xs text-red-500 mt-1.5 ml-1 block"></span>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-brand-dark mb-2">
                            Contraseña
                        </label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                <i class="fas fa-lock text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                                class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light">
                            <button
                                type="button"
                                id="toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-dark transition-colors focus:outline-none"
                                aria-label="Mostrar contraseña">
                                <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        <span id="password-error" class="hidden text-xs text-red-500 mt-1.5 ml-1 block"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                                class="h-4 w-4 text-brand-accent focus:ring-brand-accent border-gray-300 rounded cursor-pointer transition">
                            <label for="remember" class="ml-2 block text-sm text-brand-muted cursor-pointer hover:text-brand-dark transition">
                                Recordar sesión
                            </label>
                        </div>
                        <a href="recuperar_password.php" class="text-sm text-brand-accent font-medium hover:text-blue-700 transition">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full bg-brand-dark text-white font-medium py-3 rounded-xl hover:bg-brand-accent transform hover:-translate-y-0.5 transition-all duration-300 shadow-lg hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-brand-accent focus:ring-offset-2 flex justify-center items-center gap-2">
                        <span id="btn-text">Entrar al Sistema</span>
                        <i id="btn-spinner" class="fas fa-spinner fa-spin hidden" aria-hidden="true"></i>
                    </button>
                </form>

                <div class="mt-10 text-center">
                    <p class="text-sm text-brand-muted">
                        ¿No tienes una cuenta? <a href="registro_cliente.php" class="text-brand-accent font-medium hover:underline">Regístrate</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const toggleIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'text') {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                togglePassword.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                togglePassword.setAttribute('aria-label', 'Mostrar contraseña');
            }
        });

        // Form validation and submission
        const loginForm = document.getElementById('login-form');
        const usernameInput = document.getElementById('username');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');

        // Show error message
        function showError(message) {
            errorText.textContent = message;
            errorMessage.classList.remove('hidden');
            errorMessage.classList.add('error-shake');
            successMessage.classList.add('hidden');

            setTimeout(() => {
                errorMessage.classList.remove('error-shake');
            }, 500);
        }

        // Show success message
        function showSuccess(message) {
            successText.textContent = message;
            successMessage.classList.remove('hidden');
            errorMessage.classList.add('hidden');
        }

        // Hide messages
        function hideMessages() {
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');
        }

        // Validate field
        function validateField(input, errorSpan, message) {
            if (!input.value.trim()) {
                input.classList.add('border-red-500', 'focus:ring-red-400', 'focus:border-red-400');
                input.classList.remove('border-gray-200', 'focus:ring-brand-accent', 'focus:border-brand-accent');
                errorSpan.textContent = message;
                errorSpan.classList.remove('hidden');
                return false;
            } else {
                input.classList.remove('border-red-500', 'focus:ring-red-400', 'focus:border-red-400');
                input.classList.add('border-gray-200', 'focus:ring-brand-accent', 'focus:border-brand-accent');
                errorSpan.classList.add('hidden');
                return true;
            }
        }

        // Real-time validation
        usernameInput.addEventListener('input', function() {
            validateField(this, document.getElementById('username-error'), 'Este campo es requerido');
            hideMessages();
        });

        passwordInput.addEventListener('input', function() {
            validateField(this, document.getElementById('password-error'), 'Este campo es requerido');
            hideMessages();
        });

        // Form submission
        loginForm.addEventListener('submit', function(e) {
            hideMessages();

            const usernameValid = validateField(
                usernameInput,
                document.getElementById('username-error'),
                'Por favor ingresa tu usuario o correo electrónico'
            );

            const passwordValid = validateField(
                passwordInput,
                document.getElementById('password-error'),
                'Por favor ingresa tu contraseña'
            );

            if (!usernameValid || !passwordValid) {
                e.preventDefault();
                showError('Por favor completa todos los campos requeridos');
                return;
            }

            if (passwordInput.value.length < 6) {
                e.preventDefault();
                showError('La contraseña debe tener al menos 6 caracteres');
                passwordInput.classList.add('border-red-500', 'focus:ring-red-400', 'focus:border-red-400');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Iniciando sesión...';
            btnSpinner.classList.remove('hidden');
        });

        // Check for session alerts
        <?php
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            echo "showError('" . addslashes($alert['text']) . "');";
            unset($_SESSION['alert']);
        }
        ?>
    </script>
</body>

</html>