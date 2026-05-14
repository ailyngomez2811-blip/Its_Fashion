<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}
$usuario   = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$rol       = (int)$_SESSION['user_rol'] === 1 ? 'Administrador' : 'Empleado';
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Cliente.php';

$database  = new Database();
$db        = $database->conectar();
$clienteM  = new Cliente($db);

$clientes = $clienteM->listar();
$kpi      = $clienteM->totales();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Gestión de Clientes</title>
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
        .gradient-accent { background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%); }

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
            max-width: 680px;
            max-height: 92vh;
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

        /* Tabs */
        .tab-btn {
            padding: 8px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            color: #64748b;
            border: none;
            background: transparent;
        }

        .tab-btn.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .3);
        }

        .tab-btn:not(.active):hover {
            background: #f1f5f9;
            color: #1e40af;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
                <a href="usuarios.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-users w-5 text-center"></i> Usuarios
                </a>
                <a href="clientes.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
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
                    <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($usuario ?? $_SESSION['user_nombre']) ?></p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($rol ?? 'Empleado') ?></p>
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
                    <i class="fas fa-user-friends text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Gestión de Clientes</h1>
            </div>
        </header>

        <main class="flex-1 p-6 fade-in">

            <!-- Toast -->
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4
                            rounded-2xl shadow-2xl bg-white max-w-xs"
                style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 gradient-accent rounded-xl flex items-center justify-center"
                            style="box-shadow:0 4px 12px rgba(59,130,246,.3);">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['total'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Total clientes</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-check text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['activos'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Clientes activos</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['compras'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Compras totales</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-purple-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-purple-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-undo-alt text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['devoluciones'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Devoluciones</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden"
                style="box-shadow:0 2px 16px rgba(0,0,0,.04);">

                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Clientes registrados</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Consulta, historial de compras y devoluciones</p>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                                <i class="fas fa-search text-sm"></i>
                            </span>
                            <input type="text" id="search-input"
                                placeholder="Nombre, teléfono o email..."
                                oninput="filterTable()"
                                class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200
                          rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-72">
                        </div>
                        <select id="filter-estado" onchange="filterTable()"
                            class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl
                         text-sm text-slate-700 cursor-pointer transition">
                            <option value="todos">Todos los estados</option>
                            <option value="Activo">Activos</option>
                            <option value="Inactivo">Inactivos</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['Cliente', 'Contacto', 'Compras', 'Devoluciones', 'Estado', 'Registro'] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500
                         uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($clientes)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-users text-4xl mb-3 block opacity-20"></i>
                                        <p class="text-sm">No hay clientes registrados aún</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clientes as $c):
                                    $ini    = strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellido'], 0, 1));
                                    $activo = $c['estado'] === 'Activo';
                                    $fecha  = date('d/m/Y', strtotime($c['fecha_registro']));
                                ?>
                                    <tr class="trow cursor-pointer"
                                        id="row-<?= $c['id_usuario'] ?>"
                                        data-search="<?= strtolower("{$c['nombre']} {$c['apellido']} {$c['telefono']} {$c['email']}") ?>"
                                        data-estado="<?= $c['estado'] ?>"
                                        onclick="openDetalle(<?= htmlspecialchars(json_encode($c)) ?>)">

                                        <!-- Cliente -->
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 gradient-accent rounded-xl flex items-center
                              justify-center text-white text-xs font-bold flex-shrink-0"><?= $ini ?></div>
                                                <div>
                                                    <p class="font-semibold text-slate-800 text-sm">
                                                        <?= htmlspecialchars("{$c['nombre']} {$c['apellido']}") ?>
                                                    </p>
                                                    <p class="text-xs text-slate-500">@<?= htmlspecialchars($c['username']) ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contacto -->
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <p class="text-xs text-slate-700"><?= htmlspecialchars($c['email']) ?></p>
                                            <p class="text-xs text-slate-500"><?= htmlspecialchars($c['telefono']) ?></p>
                                        </td>

                                        <!-- Compras -->
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge bg-blue-100 text-blue-700">
                                                <i class="fas fa-shopping-bag text-xs mr-1"></i><?= $c['compras'] ?> compras
                                            </span>
                                        </td>

                                        <!-- Devoluciones -->
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <?php if ($c['devoluciones'] > 0): ?>
                                                <span class="badge bg-amber-100 text-amber-700">
                                                    <i class="fas fa-undo-alt text-xs mr-1"></i><?= $c['devoluciones'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400">Sin devoluciones</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Estado (toggle igual que usuarios) -->
                                        <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                            <div class="flex items-center gap-2 cursor-pointer"
                                                onclick="toggleEstado(this, <?= $c['id_usuario'] ?>)">
                                                <div class="toggle <?= $activo ? 'on' : 'off' ?>"></div>
                                                <span class="text-xs text-slate-600 estado-label"><?= $c['estado'] ?></span>
                                            </div>
                                        </td>

                                        <!-- Registro -->
                                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-slate-500"><?= $fecha ?></td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div id="empty-state" class="hidden py-16 text-center text-slate-400">
                    <i class="fas fa-user-slash text-4xl mb-3 block opacity-30"></i>
                    <p class="text-sm font-medium">No se encontraron clientes con ese criterio</p>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL DETALLE -->
    <div id="modal-detalle" class="modal-overlay hidden">
        <div class="modal-box scale-up">

            <div class="flex items-start justify-between p-7 pb-0">
                <div class="flex items-center gap-4">
                    <div id="modal-avatar"
                        class="w-14 h-14 gradient-accent rounded-2xl flex items-center justify-center
                    text-white text-xl font-bold flex-shrink-0"></div>
                    <div>
                        <h3 id="modal-nombre" class="text-xl font-bold text-slate-800"></h3>
                        <p id="modal-email" class="text-sm text-slate-500 mt-0.5"></p>
                    </div>
                </div>
                <button onclick="closeModal()"
                    class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center
                     justify-center text-slate-500 transition flex-shrink-0 ml-4">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Tabs -->
            <div class="px-7 pt-5 pb-0 border-b border-slate-100">
                <div class="flex gap-2">
                    <button class="tab-btn active" onclick="switchTab('info', this)">
                        <i class="fas fa-user mr-1.5 text-xs"></i>Información
                    </button>
                    <button class="tab-btn" onclick="switchTab('compras', this)" id="tab-btn-compras">
                        <i class="fas fa-shopping-bag mr-1.5 text-xs"></i>Compras
                        <span id="badge-compras" class="ml-1.5 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">0</span>
                    </button>
                    <button class="tab-btn" onclick="switchTab('devoluciones', this)" id="tab-btn-devol">
                        <i class="fas fa-undo-alt mr-1.5 text-xs"></i>Devoluciones
                        <span id="badge-devol" class="ml-1.5 px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">0</span>
                    </button>
                </div>
            </div>

            <div class="p-7 overflow-y-auto flex-1">

                <!-- TAB: Info -->
                <div id="tab-info" class="tab-content active">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Datos personales</p>
                            <div class="space-y-2.5">
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Nombre</span><span id="info-nombre" class="font-semibold text-slate-800">—</span></div>
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Usuario</span><span id="info-username" class="font-mono font-semibold text-slate-800">—</span></div>
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Teléfono</span><span id="info-tel" class="font-semibold text-slate-800">—</span></div>
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Email</span><span id="info-email" class="font-semibold text-slate-800 truncate max-w-40">—</span></div>
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Registro</span><span id="info-registro" class="font-semibold text-slate-800">—</span></div>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Resumen comercial</p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-blue-100">
                                    <div class="flex items-center gap-2"><i class="fas fa-shopping-bag text-blue-500 text-sm"></i><span class="text-sm text-slate-700 font-medium">Total compras</span></div>
                                    <span id="info-compras" class="font-bold text-blue-600 text-lg">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-amber-100">
                                    <div class="flex items-center gap-2"><i class="fas fa-undo-alt text-amber-500 text-sm"></i><span class="text-sm text-slate-700 font-medium">Devoluciones</span></div>
                                    <span id="info-devoluciones" class="font-bold text-amber-600 text-lg">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-emerald-100">
                                    <div class="flex items-center gap-2"><i class="fas fa-circle text-emerald-500 text-sm"></i><span class="text-sm text-slate-700 font-medium">Estado</span></div>
                                    <span id="info-estado" class="badge">—</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Asociar a una venta</p>
                            <p class="text-xs text-slate-500 mt-0.5">Vincular este cliente al registrar una nueva venta</p>
                        </div>
                        <a href="ventas.php"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5"
                            style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                            <i class="fas fa-shopping-cart text-xs"></i> Ir a ventas
                        </a>
                    </div>
                </div>

                <!-- TAB: Compras -->
                <div id="tab-compras" class="tab-content">
                    <p class="text-xs text-slate-500 mb-4">Historial de compras asociadas a este cliente</p>
                    <div id="compras-loading" class="py-8 text-center text-slate-400 text-sm">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2 block text-blue-400"></i>Cargando...
                    </div>
                    <div class="space-y-3" id="lista-compras"></div>
                    <div id="no-compras" class="hidden py-10 text-center text-slate-400">
                        <i class="fas fa-shopping-bag text-3xl mb-3 block opacity-20"></i>
                        <p class="text-sm">Este cliente no tiene compras registradas</p>
                    </div>
                </div>

                <!-- TAB: Devoluciones -->
                <div id="tab-devoluciones" class="tab-content">
                    <p class="text-xs text-slate-500 mb-4">Devoluciones registradas asociadas a este cliente</p>
                    <div id="devol-loading" class="py-8 text-center text-slate-400 text-sm">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2 block text-amber-400"></i>Cargando...
                    </div>
                    <div class="space-y-3" id="lista-devoluciones"></div>
                    <div id="no-devoluciones" class="hidden py-10 text-center text-slate-400">
                        <i class="fas fa-undo-alt text-3xl mb-3 block opacity-20"></i>
                        <p class="text-sm">Este cliente no tiene devoluciones registradas</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/admin/ClienteController.php';
        let currentId = null;

        /* ── Filtro ── */
        function filterTable() {
            const q = document.getElementById('search-input').value.toLowerCase().trim();
            const estado = document.getElementById('filter-estado').value;
            const rows = document.querySelectorAll('#table-body tr[id^="row-"]');
            let visible = 0;
            rows.forEach(row => {
                const matchQ = !q || row.dataset.search.includes(q);
                const matchE = estado === 'todos' || row.dataset.estado === estado;
                const show = matchQ && matchE;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('empty-state').classList.toggle('hidden', visible > 0);
        }

        /* ── Abrir modal ── */
        function openDetalle(data) {
            currentId = data.id_usuario;
            document.getElementById('modal-detalle').classList.remove('hidden');

            const ini = (data.nombre[0] + data.apellido[0]).toUpperCase();
            document.getElementById('modal-avatar').textContent = ini;
            document.getElementById('modal-nombre').textContent = data.nombre + ' ' + data.apellido;
            document.getElementById('modal-email').textContent = data.email;

            document.getElementById('info-nombre').textContent = data.nombre + ' ' + data.apellido;
            document.getElementById('info-username').textContent = data.username;
            document.getElementById('info-tel').textContent = data.telefono;
            document.getElementById('info-email').textContent = data.email;
            document.getElementById('info-registro').textContent = data.fecha_registro ? data.fecha_registro.substring(0, 10) : '—';
            document.getElementById('info-compras').textContent = data.compras;
            document.getElementById('info-devoluciones').textContent = data.devoluciones;

            const estadoBadge = document.getElementById('info-estado');
            estadoBadge.textContent = data.estado;
            estadoBadge.className = data.estado === 'Activo' ?
                'badge bg-emerald-100 text-emerald-700' :
                'badge bg-slate-100 text-slate-500';

            document.getElementById('badge-compras').textContent = data.compras;
            document.getElementById('badge-devol').textContent = data.devoluciones;

            // Limpiar tabs de historial
            ['lista-compras', 'lista-devoluciones'].forEach(id => document.getElementById(id).innerHTML = '');
            ['no-compras', 'no-devoluciones'].forEach(id => document.getElementById(id).classList.add('hidden'));
            ['compras-loading', 'devol-loading'].forEach(id => document.getElementById(id).classList.remove('hidden'));

            switchTabTo('info');
        }

        /* ── Cargar compras vía AJAX al cambiar tab ── */
        function loadCompras() {
            if (!currentId) return;
            fetch(`${CTRL}?action=compras&id=${currentId}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('compras-loading').classList.add('hidden');
                    const lista = document.getElementById('lista-compras');
                    const noC = document.getElementById('no-compras');
                    if (!data.length) {
                        noC.classList.remove('hidden');
                        return;
                    }
                    data.forEach(c => {
                        const metodoIcon = c.metodo_pago === 'Efectivo' ? 'money-bill' : 'university';
                        lista.innerHTML += `
          <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="flex items-start justify-between mb-2">
              <div>
                <span class="font-mono text-xs text-slate-400">#${String(c.id_venta).padStart(5,'0')}</span>
                <p class="font-semibold text-slate-800 text-sm mt-0.5">${c.productos ?? 'Sin detalle'}</p>
              </div>
              <span class="badge ${c.estado === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600'} flex-shrink-0 ml-3">${c.estado}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500">
              <span><i class="fas fa-calendar-alt mr-1"></i>${c.fecha ? c.fecha.substring(0,10) : '—'}</span>
              <span><i class="fas fa-${metodoIcon} mr-1"></i>${c.metodo_pago}</span>
              <span class="font-bold text-blue-600 text-sm">$${Number(c.total).toLocaleString('es-CO')}</span>
            </div>
          </div>`;
                    });
                })
                .catch(() => document.getElementById('compras-loading').classList.add('hidden'));
        }

        /* ── Cargar devoluciones vía AJAX ── */
        function loadDevoluciones() {
            if (!currentId) return;
            fetch(`${CTRL}?action=devoluciones&id=${currentId}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('devol-loading').classList.add('hidden');
                    const lista = document.getElementById('lista-devoluciones');
                    const noD = document.getElementById('no-devoluciones');
                    if (!data.length) {
                        noD.classList.remove('hidden');
                        return;
                    }
                    data.forEach(d => {
                        lista.innerHTML += `
          <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
            <div class="flex items-start justify-between mb-2">
              <div>
                <span class="font-mono text-xs text-amber-600">DEV-${String(d.id_devolucion).padStart(3,'0')}</span>
                <p class="text-sm text-slate-700 mt-0.5">Venta asociada: <span class="font-semibold">#${String(d.id_venta).padStart(5,'0')}</span></p>
              </div>
              <span class="font-bold text-amber-600 text-sm flex-shrink-0 ml-3">$${Number(d.total_devolucion).toLocaleString('es-CO')}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-1">
              <span class="flex items-center gap-1"><i class="fas fa-comment-alt text-amber-400"></i><em>${d.motivo ?? '—'}</em></span>
              <span><i class="fas fa-calendar-alt mr-1"></i>${d.fecha ? d.fecha.substring(0,10) : '—'}</span>
            </div>
          </div>`;
                    });
                })
                .catch(() => document.getElementById('devol-loading').classList.add('hidden'));
        }

        /* ── Activar / Desactivar cliente (toggle igual que usuarios) ── */
        function toggleEstado(wrapper, id) {
            const toggle = wrapper.querySelector('.toggle');
            const label = wrapper.querySelector('.estado-label');
            const isOn = toggle.classList.contains('on');
            const nuevo = isOn ? 'Inactivo' : 'Activo';
            toggle.className = 'toggle ' + (isOn ? 'off' : 'on');
            label.textContent = nuevo;
            const row = document.getElementById(`row-${id}`);
            if (row) row.dataset.estado = nuevo;
            fetch(`${CTRL}?action=cambiarEstado&id=${id}&estado=${nuevo}`)
                .catch(() => {
                    // revertir si falla
                    toggle.className = 'toggle ' + (isOn ? 'on' : 'off');
                    label.textContent = isOn ? 'Activo' : 'Inactivo';
                    showToast('Error al actualizar estado', 'error');
                });
        }

        /* ── Tabs ── */
        function switchTab(tab, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            switchTabTo(tab);
            if (tab === 'compras' && document.getElementById('lista-compras').innerHTML === '') loadCompras();
            if (tab === 'devoluciones' && document.getElementById('lista-devoluciones').innerHTML === '') loadDevoluciones();
        }

        function switchTabTo(tab) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }

        function closeModal() {
            document.getElementById('modal-detalle').classList.add('hidden');
            currentId = null;
        }

        document.getElementById('modal-detalle').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        /* ── Toast ── */
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            document.getElementById('toast-text').textContent = msg;
            toast.style.borderLeftColor = type === 'success' ? '#3b82f6' : '#ef4444';
            document.getElementById('toast-icon').className =
                `fas ${type === 'success' ? 'fa-check-circle text-blue-500' : 'fa-exclamation-circle text-red-500'} mt-0.5 flex-shrink-0`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }
    </script>
</body>

</html>