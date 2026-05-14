<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    header('Location: login.php');
    exit();
}
require_once '../../config/database.php';
require_once '../../models/Usuario.php';

$db              = (new Database())->conectar();
$usuario_session = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$iniciales       = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));

$stmt  = $db->query("SELECT u.id_usuario, u.nombre, u.apellido, u.username, u.email, u.telefono, u.estado, u.fecha_registro, r.descripcion as rol FROM usuario u LEFT JOIN rol r ON u.id_rol = r.id_rol WHERE u.id_rol IN (1,2) ORDER BY u.fecha_registro DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Gestión de Usuarios</title>
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

        /* Tables and cards */
        .trow:hover {
            background: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .stat-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
            background: white;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 16px;
        }

        .modal-box {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes scaleUp {
            from {
                opacity: 0;
                transform: scale(.97)
            }

            to {
                opacity: 1;
                transform: scale(1)
            }
        }

        .scale-up {
            animation: scaleUp .3s ease-out;
        }

        .toggle {
            width: 44px;
            height: 24px;
            border-radius: 12px;
            position: relative;
            cursor: pointer;
            transition: background .2s;
            display: inline-block;
            flex-shrink: 0;
        }

        .toggle::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: white;
            transition: transform .2s;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
        }

        .toggle.on {
            background: #2563eb;
        }

        .toggle.off {
            background: #cbd5e1;
        }

        .toggle.on::after {
            transform: translateX(20px);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
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
    </style>
</head>

<body class="bg-brand-light text-brand-dark antialiased">

    <!-- SIDEBAR -->
    <aside class="w-64 h-screen fixed top-0 left-0 bg-brand-dark text-white flex flex-col z-50">
        <!-- Brand -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-800">
            <img src="../../img/logo en blanco.png" alt="Its Fashion Logo" class="w-8 h-auto object-contain" onerror="this.src='../../img/logo en nombre.png'; this.classList.add('brightness-0', 'invert');">
            <span class="font-serif text-xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mb-2">Principal</p>
            <a href="dashboard.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-th-large w-5 text-center"></i> Dashboard
            </a>

            <?php if (isset($_SESSION['user_rol']) && (int)$_SESSION['user_rol'] === 1): ?>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mt-6 mb-2">Gestión</p>
                <a href="usuarios.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
                    <i class="fas fa-users w-5 text-center"></i> Usuarios
                </a>
                <a href="clientes.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-user-friends w-5 text-center"></i> Clientes
                </a>
                <a href="productos.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-box-open w-5 text-center"></i> Productos
                </a>
                <a href="categorias.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-tags w-5 text-center"></i> Categorías
                </a>
            <?php endif; ?>

            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mt-6 mb-2">Operaciones</p>
            <a href="ventas.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Ventas
            </a>
            <a href="compras.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-truck w-5 text-center"></i> Abastecimiento
            </a>
            <a href="inventario.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-warehouse w-5 text-center"></i> Inventario
            </a>
            <a href="devoluciones.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-undo-alt w-5 text-center"></i> Devoluciones
            </a>
            <a href="proveedores.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-truck-loading w-5 text-center"></i> Proveedores
            </a>

            <?php if (isset($_SESSION['user_rol']) && (int)$_SESSION['user_rol'] === 1): ?>
                <a href="caja.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white mt-1">
                    <i class="fas fa-cash-register w-5 text-center"></i> Caja
                </a>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mt-6 mb-2">Análisis</p>
                <a href="reportes.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-chart-line w-5 text-center"></i> Reportes
                </a>
            <?php endif; ?>
        </div>

        <!-- User info -->
        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                <div class="w-10 h-10 rounded-full bg-brand-accent flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-blue-500/30 flex-shrink-0">
                    <?= $iniciales ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($usuario_session ?? $_SESSION['user_nombre']) ?></p>
                    <p class="text-xs text-gray-400"><?= (int)$_SESSION['user_rol'] === 1 ? 'Administrador' : 'Empleado' ?></p>
                </div>
                <a href="../../controllers/auth/UsuarioController.php?action=logout" title="Cerrar sesión"
                    class="text-gray-400 hover:text-red-400 transition-colors p-2">
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
                    <i class="fas fa-users text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Gestión de Usuarios</h1>
            </div>
        </header>

        <main class="flex-1 p-6 fade-in">

            <!-- Toast -->
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <?php if ($toast): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', () => showToast('<?= addslashes($toast['text']) ?>', '<?= $toast['type'] ?>'));
                </script>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Usuarios del sistema</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Gestiona cuentas, roles y permisos</p>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none"><i class="fas fa-search text-sm"></i></span>
                            <input type="text" id="search-input" placeholder="Buscar usuario..." oninput="filterTable()"
                                class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-60" style="outline:none;">
                        </div>
                        <button onclick="openModal('create')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5"
                            style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                            <i class="fas fa-plus text-xs"></i> Nuevo usuario
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['Usuario', 'Contacto', 'Rol', 'Estado', 'Registro', ''] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">No hay empleados registrados aún.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u):
                                    $ini         = strtoupper(substr($u['nombre'], 0, 1) . substr($u['apellido'], 0, 1));
                                    $toggleClass = $u['estado'] === 'Activo' ? 'on' : 'off';
                                    $caja        = false;
                                    $dataJson    = json_encode([
                                        'id'       => $u['id_usuario'],
                                        'nombre'   => $u['nombre'],
                                        'apellido' => $u['apellido'],
                                        'username' => $u['username'],
                                        'email'    => $u['email'],
                                        'telefono' => $u['telefono'] ?? '',
                                        'rol'      => $u['rol'],
                                        'estado'   => $u['estado'],
                                        'caja'     => $caja,
                                    ]);
                                ?>
                                    <tr class="trow" data-search="<?= strtolower("{$u['nombre']} {$u['apellido']} {$u['username']} {$u['email']}") ?>">
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 bg-brand-accent rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0"><?= $ini ?></div>
                                                <div>
                                                    <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars("{$u['nombre']} {$u['apellido']}") ?></p>
                                                    <p class="text-xs text-slate-500">@<?= htmlspecialchars($u['username']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <p class="text-xs text-slate-700"><?= htmlspecialchars($u['email']) ?></p>
                                            <p class="text-xs text-slate-500"><?= htmlspecialchars($u['telefono'] ?? '') ?></p>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <?php if ($u['rol'] === 'Administrador'): ?>
                                                <span class="badge bg-blue-100 text-blue-500">Administrador</span>
                                            <?php else: ?>
                                                <span class="badge bg-amber-100 text-amber-700">Empleado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <div class="flex items-center gap-2 cursor-pointer" onclick="toggleEstado(this, <?= $u['id_usuario'] ?>)">
                                                <div class="toggle <?= $toggleClass ?>"></div>
                                                <span class="text-xs text-slate-600 estado-label"><?= $u['estado'] ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-slate-500"><?= date('Y-m-d', strtotime($u['fecha_registro'])) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <button onclick='openModal("edit", <?= $dataJson ?>)'
                                                class="w-8 h-8 bg-blue-50 hover:bg-blue-100 rounded-lg flex items-center justify-center text-blue-500 transition">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL -->
    <div id="modal" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-start justify-between p-7 pb-0">
                <div>
                    <h3 id="modal-title" class="text-xl font-bold text-slate-800">Crear nuevo usuario</h3>
                    <p class="text-sm text-slate-500 mt-1">Complete la información del usuario</p>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition flex-shrink-0 ml-4">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-7 overflow-y-auto">
                <form id="user-form" action="../../controllers/auth/UsuarioController.php" method="POST" onsubmit="submitForm(event)" novalidate>
                    <input type="hidden" id="form-action" name="action" value="create">
                    <input type="hidden" id="form-id" name="id" value="">
                    <input type="hidden" name="id_rol" value="2">

                    <div class="grid grid-cols-2 gap-x-4">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" id="f-nombre" name="nombre" placeholder="Nombre"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                            <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-nombre"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Apellido <span class="text-red-500">*</span></label>
                            <input type="text" id="f-apellido" name="apellido" placeholder="Apellido"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                            <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-apellido"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre de usuario <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">@</span>
                            <input type="text" id="f-username" name="username" placeholder="usuario_ejemplo"
                                class="w-full pl-8 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                        </div>
                        <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-username"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Correo electrónico <span class="text-red-500">*</span></label>
                            <input type="email" id="f-email" name="email" placeholder="correo@ejemplo.com"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                            <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-email"><i class="fas fa-exclamation-circle mr-1"></i>Requerido</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono</label>
                            <input type="text" id="f-telefono" name="telefono" placeholder="3001234567"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Contraseña <span class="text-red-500" id="pw-required">*</span></label>
                        <div class="relative">
                            <input type="password" id="f-password" name="password" placeholder="Mín. 8 car., mayúscula, minúscula y número" oninput="checkPwStrength()"
                                class="w-full pr-12 px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm placeholder-slate-400 transition">
                            <button type="button" id="toggle-pw" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <div class="mt-2 flex gap-1">
                            <div class="flex-1 h-1 rounded-full bg-slate-200" id="s1"></div>
                            <div class="flex-1 h-1 rounded-full bg-slate-200" id="s2"></div>
                            <div class="flex-1 h-1 rounded-full bg-slate-200" id="s3"></div>
                            <div class="flex-1 h-1 rounded-full bg-slate-200" id="s4"></div>
                        </div>
                        <p class="err hidden text-xs text-red-500 mt-1 ml-1" data-for="f-password"><i class="fas fa-exclamation-circle mr-1"></i>Mín. 8 car., mayúscula, minúscula y número</p>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estado</label>
                            <select id="f-estado" name="estado" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm cursor-pointer transition">
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                            <i class="fas fa-save text-xs"></i> Guardar usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterTable() {
            const q = document.getElementById('search-input').value.toLowerCase();
            document.querySelectorAll('#table-body tr').forEach(row => {
                row.style.display = row.dataset.search?.includes(q) ? '' : 'none';
            });
        }

        function openModal(mode, data = null) {
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal-title').textContent = mode === 'create' ? 'Crear nuevo usuario' : 'Editar usuario';
            document.getElementById('form-action').value = mode === 'create' ? 'create' : 'update';
            clearForm();
            if (data) {
                document.getElementById('form-id').value = data.id;
                document.getElementById('f-nombre').value = data.nombre;
                document.getElementById('f-apellido').value = data.apellido;
                document.getElementById('f-username').value = data.username;
                document.getElementById('f-email').value = data.email;
                document.getElementById('f-telefono').value = data.telefono || '';
                document.getElementById('f-estado').value = data.estado;
                document.getElementById('pw-required').style.display = 'none';
            }
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        function clearForm() {
            ['f-nombre', 'f-apellido', 'f-username', 'f-email', 'f-telefono', 'f-password'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('f-estado').value = 'Activo';
            document.getElementById('pw-required').style.display = '';
            document.querySelectorAll('.err').forEach(e => e.classList.add('hidden'));
            ['s1', 's2', 's3', 's4'].forEach(id => document.getElementById(id).style.background = '#e2e8f0');
        }

        document.getElementById('toggle-pw').addEventListener('click', function() {
            const pw = document.getElementById('f-password');
            const icon = this.querySelector('i');
            pw.type = pw.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        function checkPwStrength() {
            const pw = document.getElementById('f-password').value;
            const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[a-z]/.test(pw)) score++;
            if (/\d/.test(pw)) score++;
            ['s1', 's2', 's3', 's4'].forEach((id, i) => {
                document.getElementById(id).style.background = i < score ? colors[score - 1] : '#e2e8f0';
            });
        }

        function submitForm(e) {
            e.preventDefault();
            let valid = true;
            document.querySelectorAll('.err').forEach(el => el.classList.add('hidden'));
            const isCreate = document.getElementById('form-action').value === 'create';
            const required = ['f-nombre', 'f-apellido', 'f-username', 'f-email'];
            if (isCreate) required.push('f-password');
            required.forEach(id => {
                const el = document.getElementById(id);
                if (!el.value.trim()) {
                    el.style.borderColor = '#ef4444';
                    document.querySelector(`.err[data-for="${id}"]`)?.classList.remove('hidden');
                    valid = false;
                } else {
                    el.style.borderColor = '';
                }
            });
            const pw = document.getElementById('f-password').value;
            if (isCreate && pw && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}/.test(pw)) {
                document.querySelector('.err[data-for="f-password"]')?.classList.remove('hidden');
                valid = false;
            }
            if (valid) document.getElementById('user-form').submit();
        }

        function toggleEstado(wrapper, userId) {
            const toggle = wrapper.querySelector('.toggle');
            const label = wrapper.querySelector('.estado-label');
            const isOn = toggle.classList.contains('on');
            toggle.className = 'toggle ' + (isOn ? 'off' : 'on');
            label.textContent = isOn ? 'Inactivo' : 'Activo';
            fetch(`../../controllers/auth/UsuarioController.php?action=toggleEstado&id=${userId}&estado=${isOn ? 'Inactivo' : 'Activo'}`);
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            document.getElementById('toast-text').textContent = msg;
            toast.style.borderLeftColor = type === 'success' ? '#3b82f6' : '#ef4444';
            document.getElementById('toast-icon').className = `fas ${type === 'success' ? 'fa-check-circle text-blue-500' : 'fa-exclamation-circle text-red-500'} mt-0.5 flex-shrink-0`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.querySelectorAll('input,select').forEach(el => {
            el.addEventListener('focus', () => {
                el.style.borderColor = '#3b82f6';
                el.style.boxShadow = '0 0 0 3px rgba(59,130,246,.15)';
            });
            el.addEventListener('blur', () => {
                el.style.borderColor = '';
                el.style.boxShadow = '';
            });
        });
    </script>
</body>

</html>