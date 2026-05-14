<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}
$usuario   = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$rol       = (int)$_SESSION['user_rol'] === 1 ? 'Administrador' : 'Empleado';
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));
$esAdmin   = (int)$_SESSION['user_rol'] === 1;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/Categoria.php';

$db        = (new Database())->conectar();
$prodM     = new Producto($db);
$catM      = new Categoria($db);
$productos = $prodM->listar();
$categorias = $catM->listar();
$kpi       = $prodM->totales();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Productos</title>
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
        .gradient-accent {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        }

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
            max-width: 640px;
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
                <a href="clientes.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-user-friends w-5 text-center"></i> Clientes
                </a>
                <a href="productos.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
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
                    <i class="fas fa-box-open text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Gestión de Productos</h1>
            </div>
            <?php if ($esAdmin): ?>
                <button onclick="openModal()" class="flex items-center gap-2 px-4 py-2 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-plus text-xs"></i> Nuevo producto
                </button>
            <?php endif; ?>
        </header>

        <main class="flex-1 p-6 fade-in">

            <!-- Toast -->
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 gradient-accent rounded-xl flex items-center justify-center" style="box-shadow:0 4px 12px rgba(59,130,246,.3);">
                            <i class="fas fa-box-open text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['total'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Total productos</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['activos'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Activos</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['criticos'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Stock crítico</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-red-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-red-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-times-circle text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['agotados'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Agotados</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Catálogo de productos</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Prendas registradas en el sistema</p>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none"><i class="fas fa-search text-sm"></i></span>
                            <input type="text" id="search-input" placeholder="Nombre, talla, color..." oninput="filterTable()" class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-64">
                        </div>
                        <select id="filter-cat" onchange="filterTable()" class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer transition">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filter-estado" onchange="filterTable()" class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer transition">
                            <option value="">Todos</option>
                            <option value="Activo">Activos</option>
                            <option value="Inactivo">Inactivos</option>
                            <option value="critico">Stock crítico</option>
                            <option value="agotado">Agotados</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['Producto', 'Categoría', 'Talla / Color', 'Precio venta', 'Stock', 'Estado', ''] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($productos)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-box-open text-4xl mb-3 block opacity-20"></i>
                                        <p class="text-sm">No hay productos registrados aún</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $p):
                                    $activo  = $p['estado'] === 'Activo';
                                    $critico = $p['stock'] > 0 && $p['stock'] <= $p['stock_minimo'];
                                    $agotado = $p['stock'] == 0;
                                ?>
                                    <tr class="trow"
                                        id="row-<?= $p['id_producto'] ?>"
                                        data-search="<?= strtolower("{$p['nombre']} {$p['talla']} {$p['color']} {$p['categoria_nombre']}") ?>"
                                        data-cat="<?= $p['id_categoria'] ?>"
                                        data-estado="<?= $p['estado'] ?>"
                                        data-stock="<?= $agotado ? 'agotado' : ($critico ? 'critico' : 'ok') ?>">

                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($p['nombre']) ?></p>
                                            <p class="text-xs text-slate-400 truncate max-w-48"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge bg-blue-100 text-blue-700"><?= htmlspecialchars($p['categoria_nombre'] ?? '—') ?></span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">
                                            <span class="font-medium"><?= htmlspecialchars($p['talla']) ?></span>
                                            <span class="text-slate-400 mx-1">/</span>
                                            <?= htmlspecialchars($p['color']) ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-semibold text-slate-800">
                                            $<?= number_format($p['precio_venta'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <?php if ($agotado): ?>
                                                <span class="badge bg-red-100 text-red-700"><i class="fas fa-times-circle mr-1 text-xs"></i>Agotado</span>
                                            <?php elseif ($critico): ?>
                                                <span class="badge bg-amber-100 text-amber-700"><i class="fas fa-exclamation-triangle mr-1 text-xs"></i><?= $p['stock'] ?> uds</span>
                                            <?php else: ?>
                                                <span class="badge bg-emerald-100 text-emerald-700"><i class="fas fa-check mr-1 text-xs"></i><?= $p['stock'] ?> uds</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                            <?php if ($esAdmin): ?>
                                                <div class="flex items-center gap-2 cursor-pointer" onclick="toggleEstado(this, <?= $p['id_producto'] ?>)">
                                                    <div class="toggle <?= $activo ? 'on' : 'off' ?>"></div>
                                                    <span class="text-xs text-slate-600 estado-label"><?= $p['estado'] ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge <?= $activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= $p['estado'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                            <?php if ($esAdmin): ?>
                                                <button onclick="editarProducto(<?= $p['id_producto'] ?>)"
                                                    class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div id="empty-state" class="hidden py-16 text-center text-slate-400">
                    <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                    <p class="text-sm font-medium">No se encontraron productos con ese criterio</p>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL CREAR / EDITAR -->
    <div id="modal-prod" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-center justify-between p-7 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-accent rounded-xl flex items-center justify-center">
                        <i class="fas fa-box-open text-white text-sm"></i>
                    </div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-800">Nuevo producto</h3>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form id="form-prod" class="p-7 overflow-y-auto flex-1 space-y-4">
                <input type="hidden" id="prod-id" name="id">
                <input type="hidden" name="action" id="form-action" value="crear">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre del producto *</label>
                        <input type="text" name="nombre" id="prod-nombre" required placeholder="Ej: Blusa floral manga larga"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Descripción</label>
                        <textarea name="descripcion" id="prod-desc" rows="2" placeholder="Descripción opcional..."
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Categoría *</label>
                        <select name="id_categoria" id="prod-cat" required class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Estado</label>
                        <select name="estado" id="prod-estado" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Talla *</label>
                        <select name="talla" id="prod-talla" required class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                            <option value="">Seleccionar...</option>
                            <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Única'] as $t): ?>
                                <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Color *</label>
                        <input type="text" name="color" id="prod-color" required placeholder="Ej: Azul marino"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Precio compra *</label>
                        <input type="number" name="precio_compra" id="prod-pc" required min="0" step="0.01" placeholder="0.00"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Precio venta *</label>
                        <input type="number" name="precio_venta" id="prod-pv" required min="0" step="0.01" placeholder="0.00"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Stock inicial *</label>
                        <input type="number" name="stock" id="prod-stock" required min="0" placeholder="0"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Stock mínimo</label>
                        <input type="number" name="stock_minimo" id="prod-smin" min="0" placeholder="0"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                    <button type="submit" class="flex-1 py-3 gradient-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                        <i class="fas fa-save mr-2 text-xs"></i><span id="btn-submit-text">Guardar producto</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/admin/ProductoController.php';

        // ── Filtros ──────────────────────────────────────────────────────────────────
        function filterTable() {
            const q = document.getElementById('search-input').value.toLowerCase();
            const cat = document.getElementById('filter-cat').value;
            const estado = document.getElementById('filter-estado').value;
            let visible = 0;
            document.querySelectorAll('#table-body tr[id^="row-"]').forEach(row => {
                const matchQ = !q || row.dataset.search.includes(q);
                const matchC = !cat || row.dataset.cat === cat;
                let matchE = true;
                if (estado === 'Activo' || estado === 'Inactivo') matchE = row.dataset.estado === estado;
                else if (estado === 'critico') matchE = row.dataset.stock === 'critico';
                else if (estado === 'agotado') matchE = row.dataset.stock === 'agotado';
                const show = matchQ && matchC && matchE;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('empty-state').classList.toggle('hidden', visible > 0);
        }

        // ── Toggle estado ─────────────────────────────────────────────────────────────
        function toggleEstado(el, id) {
            const toggle = el.querySelector('.toggle');
            const label = el.querySelector('.estado-label');
            const isOn = toggle.classList.contains('on');
            const nuevo = isOn ? 'Inactivo' : 'Activo';
            const fd = new FormData();
            fd.append('action', 'toggleEstado');
            fd.append('id', id);
            fd.append('estado', nuevo);
            fetch(CTRL, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(d => {
                    if (d.ok) {
                        toggle.classList.toggle('on', !isOn);
                        toggle.classList.toggle('off', isOn);
                        label.textContent = nuevo;
                        const row = document.getElementById('row-' + id);
                        if (row) row.dataset.estado = nuevo;
                        showToast('Estado actualizado', 'success');
                    } else showToast(d.msg, 'error');
                });
        }

        // ── Modal ─────────────────────────────────────────────────────────────────────
        function openModal() {
            document.getElementById('form-prod').reset();
            document.getElementById('prod-id').value = '';
            document.getElementById('form-action').value = 'crear';
            document.getElementById('modal-title').textContent = 'Nuevo producto';
            document.getElementById('btn-submit-text').textContent = 'Guardar producto';
            document.getElementById('modal-prod').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-prod').classList.add('hidden');
        }

        function editarProducto(id) {
            fetch(`${CTRL}?action=obtener&id=${id}`)
                .then(r => r.json())
                .then(p => {
                    if (!p) return;
                    document.getElementById('prod-id').value = p.id_producto;
                    document.getElementById('form-action').value = 'editar';
                    document.getElementById('prod-nombre').value = p.nombre;
                    document.getElementById('prod-desc').value = p.descripcion ?? '';
                    document.getElementById('prod-cat').value = p.id_categoria;
                    document.getElementById('prod-estado').value = p.estado;
                    document.getElementById('prod-talla').value = p.talla;
                    document.getElementById('prod-color').value = p.color;
                    document.getElementById('prod-pc').value = p.precio_compra;
                    document.getElementById('prod-pv').value = p.precio_venta;
                    document.getElementById('prod-stock').value = p.stock;
                    document.getElementById('prod-smin').value = p.stock_minimo;
                    document.getElementById('modal-title').textContent = 'Editar producto';
                    document.getElementById('btn-submit-text').textContent = 'Actualizar producto';
                    document.getElementById('modal-prod').classList.remove('hidden');
                });
        }

        // ── Submit ────────────────────────────────────────────────────────────────────
        document.getElementById('form-prod').addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch(CTRL, {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(d => {
                    if (d.ok) {
                        showToast(d.msg, 'success');
                        closeModal();
                        setTimeout(() => location.reload(), 800);
                    } else showToast(d.msg, 'error');
                });
        });

        // ── Toast ─────────────────────────────────────────────────────────────────────
        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            const i = document.getElementById('toast-icon');
            const s = document.getElementById('toast-text');
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b'
            };
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-circle'
            };
            t.style.borderLeftColor = colors[type] || colors.success;
            i.className = `fas ${icons[type] || icons.success} mt-0.5 flex-shrink-0`;
            i.style.color = colors[type] || colors.success;
            s.textContent = msg;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 3500);
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>

</html>