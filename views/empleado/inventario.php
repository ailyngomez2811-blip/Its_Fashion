<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}
$usuario   = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$rol = 'Empleado';
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/Categoria.php';
require_once __DIR__ . '/../../models/Inventario.php';

$db         = (new Database())->conectar();
$prodM      = new Producto($db);
$catM       = new Categoria($db);
$invM       = new Inventario($db);

$productos  = $prodM->listar();
$categorias = $catM->listar();
$historial  = $invM->listarHistorial();

$total    = count($productos);
$conStock = count(array_filter($productos, fn($p) => $p['stock'] > ($p['stock_minimo'] ?? 0)));
$critico  = count(array_filter($productos, fn($p) => $p['stock'] > 0 && $p['stock'] <= ($p['stock_minimo'] ?? 0)));
$sinStock = count(array_filter($productos, fn($p) => $p['stock'] == 0));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Its Fashion | Inventario</title>
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
            <a href="dashboard_empleado.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-th-large w-5 text-center"></i> Dashboard
            </a>

            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mt-6 mb-2">Operaciones</p>
            <a href="ventas.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Ventas
            </a>
            <a href="inventario.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:text-white">
                <i class="fas fa-warehouse w-5 text-center"></i> Inventario
            </a>
            <a href="caja.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-cash-register w-5 text-center"></i> Caja
            </a>
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
                    <i class="fas fa-warehouse text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Inventario</h1>
            </div>
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                <i class="fas fa-clock mr-1"></i> Actualizado: <?= date('d/m/Y H:i') ?>
            </span>
        </header>

        <main class="flex-1 p-6 fade-in">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <?php $kpis = [
                    ['Total productos', $total,    'fa-box-open',            'bg-brand-accent text-white',        'border-blue-100'],
                    ['Con stock',       $conStock, 'fa-check-circle',        'bg-emerald-100 text-emerald-600',   'border-emerald-100'],
                    ['Stock crítico',   $critico,  'fa-exclamation-triangle', 'bg-amber-100 text-amber-600',       'border-amber-100'],
                    ['Sin stock',       $sinStock, 'fa-times-circle',        'bg-red-100 text-red-600',           'border-red-100'],
                ];
                foreach ($kpis as $k): ?>
                    <div class="bg-white rounded-2xl p-5 border <?= $k[4] ?> stat-card">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 <?= $k[3] ?> rounded-xl flex items-center justify-center"><i class="fas <?= $k[2] ?>"></i></div>
                            <div>
                                <p class="text-2xl font-bold text-slate-800"><?= $k[1] ?></p>
                                <p class="text-xs text-slate-500"><?= $k[0] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php $alertas = array_filter($productos, fn($p) => $p['stock'] <= ($p['stock_minimo'] ?? 0)); ?>
            <?php if (!empty($alertas)): ?>
                <div class="mb-6 space-y-2">
                    <?php foreach ($alertas as $a): ?>
                        <?php if ($a['stock'] == 0): ?>
                            <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                                <i class="fas fa-times-circle text-red-500 flex-shrink-0"></i>
                                <span><b><?= htmlspecialchars($a['nombre']) ?></b> (<?= $a['talla'] ?>/<?= $a['color'] ?>) — Sin stock. Reponer urgente.</span>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                                <i class="fas fa-exclamation-triangle text-amber-500 flex-shrink-0"></i>
                                <span><b><?= htmlspecialchars($a['nombre']) ?></b> (<?= $a['talla'] ?>/<?= $a['color'] ?>) — Stock crítico: <?= $a['stock'] ?> uds (mínimo: <?= $a['stock_minimo'] ?>).</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Kardex de Movimientos</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Historial de Entradas, Salidas y Ajustes</p>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none"><i class="fas fa-search text-sm"></i></span>
                            <input type="text" id="search-input" placeholder="Buscar movimiento..." oninput="filterTable()"
                                class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-60">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['Fecha', 'Producto', 'Talla / Color', 'Movimiento', 'Stock Disp.'] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($historial)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-history text-4xl mb-3 block opacity-20"></i>
                                        <p class="text-sm">No hay movimientos registrados en el Kardex</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historial as $h):
                                    $tipo = $h['tipo_movimiento'];
                                    if ($tipo === 'Entrada')     $colorMov = 'emerald';
                                    elseif ($tipo === 'Salida')  $colorMov = 'red';
                                    else                         $colorMov = 'amber';
                                ?>
                                    <tr class="trow" data-search="<?= strtolower("{$h['nombre']} {$h['talla']} {$h['color']} {$h['tipo_movimiento']}") ?>">
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <p class="text-sm font-medium text-slate-800"><?= date('d/m/Y', strtotime($h['fecha_registro'])) ?></p>
                                            <p class="text-xs text-slate-400"><?= date('H:i', strtotime($h['fecha_registro'])) ?></p>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($h['nombre']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">
                                            <span class="font-medium"><?= htmlspecialchars($h['talla']) ?></span>
                                            <span class="text-slate-400 mx-1">/</span><?= htmlspecialchars($h['color']) ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-<?= $colorMov ?>-600 bg-<?= $colorMov ?>-50 px-3 py-1.5 rounded border border-<?= $colorMov ?>-200">
                                                <i class="fas fa-<?= $tipo === 'Entrada' ? 'level-down-alt' : ($tipo === 'Salida' ? 'level-up-alt' : 'exchange-alt') ?>"></i> <?= $tipo ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="text-lg font-bold text-slate-800"><?= $h['stock_disponible'] ?></span>
                                            <span class="text-xs text-slate-400">uds</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div id="empty-state" class="hidden py-16 text-center text-slate-400">
                    <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                    <p class="text-sm font-medium">No se encontraron productos</p>
                </div>
            </div>
        </main>
    </div>
    <script>
        function filterTable() {
            const q = document.getElementById('search-input').value.toLowerCase();
            let v = 0;
            document.querySelectorAll('#table-body tr.trow').forEach(r => {
                const show = (!q || r.dataset.search.includes(q));
                r.style.display = show ? '' : 'none';
                if (show) v++;
            });
            document.getElementById('empty-state').classList.toggle('hidden', v > 0);
        }
    </script>
</body>

</html>