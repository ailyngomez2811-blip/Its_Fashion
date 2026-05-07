<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 3) {
    header('Location: ../auth/login.php');
    exit();
}
$nombre    = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));
$email     = htmlspecialchars($_SESSION['user_email'] ?? '');

require_once __DIR__ . '/../../config/database.php';
$db = (new Database())->conectar();

$stmt = $db->prepare("SELECT nombre, apellido, username, telefono, email, fecha_registro FROM usuario WHERE id_usuario = :id");
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Its Fashion | Mi Perfil</title>
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
                            muted: '#64748b'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-item {
            transition: all 0.3s ease;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .sidebar-item.active {
            background-color: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border-right: 3px solid #2563eb;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        }

        .gradient-accent {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        input:focus {
            border-color: #2563eb;
            outline: none;
        }
    </style>
</head>

<body class="bg-brand-light text-brand-dark antialiased">

    <!-- SIDEBAR -->
    <aside class="w-64 h-screen fixed top-0 left-0 bg-brand-dark text-white flex flex-col z-50">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-800">
            <img src="../../img/logo en blanco.png" alt="Its Fashion Logo" class="w-8 h-auto object-contain" onerror="this.src='../../img/logo en nombre.png'; this.classList.add('brightness-0','invert');">
            <span class="font-serif text-xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mb-2">Mi cuenta</p>
            <a href="dashboard_cliente.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-th-large w-5 text-center"></i> Inicio
            </a>
            <a href="mis_compras.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-shopping-bag w-5 text-center"></i> Mis compras
            </a>
            <a href="mis_devoluciones.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-undo-alt w-5 text-center"></i> Mis devoluciones
            </a>
            <a href="mi_perfil.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:text-white">
                <i class="fas fa-user-circle w-5 text-center"></i> Mi perfil
            </a>
        </div>

        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                <div class="w-10 h-10 rounded-full bg-brand-accent flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-blue-500/30 flex-shrink-0">
                    <?= $iniciales ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate"><?= $nombre ?></p>
                    <p class="text-xs text-gray-400">Cliente</p>
                </div>
                <a href="../../controllers/auth/UsuarioController.php?action=logout" title="Cerrar sesión" class="text-gray-400 hover:text-red-400 transition-colors p-2">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="ml-64 flex flex-col min-h-screen">
        <header class="glass-header sticky top-0 z-40 flex items-center justify-between px-8 h-20">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                    <i class="fas fa-user-circle text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Mi perfil</h1>
            </div>
        </header>

        <main class="flex-1 p-6 fade-in">

            <!-- Toast -->
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #2563eb;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <!-- Banner avatar -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 flex items-center gap-6 stat-card" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="w-24 h-24 gradient-accent rounded-2xl flex items-center justify-center text-white text-4xl font-bold flex-shrink-0" style="box-shadow:0 4px 20px rgba(37,99,235,.3);">
                    <?= $iniciales ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-serif font-bold text-slate-800"><?= $nombre ?></h2>
                    <p class="text-sm text-slate-500 mt-0.5"><?= $email ?></p>
                    <p class="text-xs text-slate-400 mt-1">Cliente desde <?= $user ? date('d/m/Y', strtotime($user['fecha_registro'])) : '—' ?></p>
                </div>
                <div class="hidden lg:flex items-center gap-4">
                    <div class="px-5 py-3 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <p class="text-xs text-slate-500 mb-0.5">Usuario</p>
                        <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($user['username'] ?? '—') ?></p>
                    </div>
                    <div class="px-5 py-3 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                        <p class="text-xs text-slate-500 mb-0.5">Teléfono</p>
                        <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($user['telefono'] ?? '—') ?></p>
                    </div>
                </div>
            </div>

            <!-- Formularios en 2 columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Datos personales -->
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Datos personales</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Actualiza tu información de contacto</p>
                    </div>
                    <form id="form-perfil" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre *</label>
                                <input type="text" name="nombre" id="inp-nombre" required
                                    value="<?= htmlspecialchars($user['nombre'] ?? '') ?>"
                                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Apellido *</label>
                                <input type="text" name="apellido" id="inp-apellido" required
                                    value="<?= htmlspecialchars($user['apellido'] ?? '') ?>"
                                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Teléfono *</label>
                            <input type="text" name="telefono" id="inp-telefono" required
                                value="<?= htmlspecialchars($user['telefono'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Correo electrónico *</label>
                            <input type="email" name="email" id="inp-email" required
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre de usuario</label>
                            <input type="text" value="<?= htmlspecialchars($user['username'] ?? '') ?>" readonly
                                class="w-full px-4 py-2.5 bg-slate-100 border-2 border-slate-200 rounded-xl text-sm text-slate-400 cursor-not-allowed">
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-2.5 gradient-accent text-white font-semibold rounded-xl text-sm hover:shadow-lg transition" style="box-shadow:0 4px 12px rgba(37,99,235,.25);">
                                <i class="fas fa-save mr-2 text-xs"></i>Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Cambiar contraseña -->
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Cambiar contraseña</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Deja los campos vacíos si no deseas cambiarla</p>
                    </div>
                    <form id="form-password" class="p-6 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Contraseña actual</label>
                            <input type="password" name="password_actual" id="inp-pw-actual" placeholder="••••••••"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nueva contraseña</label>
                            <input type="password" name="password_nueva" id="inp-pw-nueva" placeholder="Mínimo 6 caracteres"
                                oninput="checkStrength(this.value)"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        </div>

                        <!-- Indicador de fortaleza -->
                        <div id="pw-strength" class="hidden space-y-1.5">
                            <div class="flex gap-1">
                                <div id="bar1" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                <div id="bar2" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                <div id="bar3" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                <div id="bar4" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                            </div>
                            <p id="strength-label" class="text-xs text-slate-400"></p>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Confirmar nueva contraseña</label>
                            <input type="password" id="inp-pw-confirm" placeholder="Repite la nueva contraseña"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        </div>

                        <!-- Consejo seguridad -->
                        <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <i class="fas fa-shield-alt text-blue-400 mt-0.5 flex-shrink-0 text-sm"></i>
                            <p class="text-xs text-blue-700">Usa mínimo 6 caracteres combinando letras, números y símbolos para una contraseña más segura.</p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition">
                                <i class="fas fa-lock mr-2 text-xs"></i>Actualizar contraseña
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script>
        const CTRL = '../../controllers/clientes/ClienteController.php';

        // Guardar datos personales
        document.getElementById('form-perfil').addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData();
            fd.append('action', 'actualizarPerfil');
            fd.append('nombre', document.getElementById('inp-nombre').value.trim());
            fd.append('apellido', document.getElementById('inp-apellido').value.trim());
            fd.append('telefono', document.getElementById('inp-telefono').value.trim());
            fd.append('email', document.getElementById('inp-email').value.trim());
            fetch(CTRL, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(d => {
                showToast(d.msg, d.ok ? 'success' : 'error');
            });
        });

        // Cambiar contraseña
        document.getElementById('form-password').addEventListener('submit', function(e) {
            e.preventDefault();
            const actual = document.getElementById('inp-pw-actual').value;
            const nueva = document.getElementById('inp-pw-nueva').value;
            const confirm = document.getElementById('inp-pw-confirm').value;
            if (!actual || !nueva) {
                showToast('Completa los campos de contraseña', 'error');
                return;
            }
            if (nueva !== confirm) {
                showToast('Las contraseñas no coinciden', 'error');
                return;
            }
            if (nueva.length < 6) {
                showToast('La contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }

            const fd = new FormData();
            fd.append('action', 'actualizarPerfil');
            fd.append('nombre', document.getElementById('inp-nombre').value.trim());
            fd.append('apellido', document.getElementById('inp-apellido').value.trim());
            fd.append('telefono', document.getElementById('inp-telefono').value.trim());
            fd.append('email', document.getElementById('inp-email').value.trim());
            fd.append('password_actual', actual);
            fd.append('password_nueva', nueva);
            fetch(CTRL, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(d => {
                if (d.ok) {
                    document.getElementById('inp-pw-actual').value = '';
                    document.getElementById('inp-pw-nueva').value = '';
                    document.getElementById('inp-pw-confirm').value = '';
                }
                showToast(d.msg, d.ok ? 'success' : 'error');
            });
        });

        // Indicador de fortaleza de contraseña
        function checkStrength(pw) {
            const el = document.getElementById('pw-strength');
            const label = document.getElementById('strength-label');
            const bars = ['bar1', 'bar2', 'bar3', 'bar4'];
            if (!pw) {
                el.classList.add('hidden');
                return;
            }
            el.classList.remove('hidden');

            let score = 0;
            if (pw.length >= 6) score++;
            if (pw.length >= 10) score++;
            if (/[A-Z]/.test(pw) && /[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;

            const colors = ['bg-red-400', 'bg-orange-400', 'bg-amber-400', 'bg-emerald-400'];
            const labels = ['Muy débil', 'Débil', 'Buena', 'Fuerte'];
            const labelColors = ['text-red-500', 'text-orange-500', 'text-amber-500', 'text-emerald-600'];

            bars.forEach((id, i) => {
                const bar = document.getElementById(id);
                bar.className = 'h-1.5 flex-1 rounded-full transition-all duration-300 ' + (i < score ? colors[score - 1] : 'bg-slate-200');
            });
            label.textContent = labels[score - 1] || '';
            label.className = 'text-xs transition-all ' + (labelColors[score - 1] || 'text-slate-400');
        }

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast'),
                i = document.getElementById('toast-icon'),
                s = document.getElementById('toast-text');
            const colors = {
                    success: '#10b981',
                    error: '#ef4444'
                },
                icons = {
                    success: 'fa-check-circle',
                    error: 'fa-times-circle'
                };
            t.style.borderLeftColor = colors[type];
            i.className = `fas ${icons[type]} mt-0.5 flex-shrink-0`;
            i.style.color = colors[type];
            s.textContent = msg;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 3500);
        }
    </script>
</body>

</html>