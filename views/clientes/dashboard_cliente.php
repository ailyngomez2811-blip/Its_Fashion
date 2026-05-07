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
require_once __DIR__ . '/../../models/Venta.php';

$db      = (new Database())->conectar();
$ventaM  = new Venta($db);

// Compras del cliente
$compras      = $ventaM->porCliente($_SESSION['user_id']);
$totalCompras = count($compras);
$totalGastado = array_sum(array_column($compras, 'total'));
$ultimaCompra = $compras[0] ?? null;

// Devoluciones del cliente
$stmt = $db->prepare(
    "SELECT d.id_devolucion, d.fecha, d.motivo, d.total_devolucion, v.id_venta
     FROM devoluciones d
     JOIN venta v ON v.id_venta = d.id_venta
     WHERE v.id_cliente = :id ORDER BY d.fecha DESC LIMIT 5"
);
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$devoluciones    = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalDevoluciones = count($devoluciones);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Its Fashion | Mi Cuenta</title>
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

        .gradient-accent {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        }

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
            max-width: 600px;
            max-height: 94vh;
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
    </style>
</head>

<body class="bg-brand-light text-brand-dark antialiased">

    <!-- SIDEBAR -->
    <aside class="w-64 h-screen fixed top-0 left-0 bg-brand-dark text-white flex flex-col z-50">
        <!-- Brand -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-800">
            <img src="../../img/logo en blanco.png" alt="Its Fashion Logo" class="w-8 h-auto object-contain" onerror="this.src='../../img/logo en nombre.png'; this.classList.add('brightness-0','invert');">
            <span class="font-serif text-xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mb-2">Mi cuenta</p>
            <a href="dashboard_cliente.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:text-white">
                <i class="fas fa-th-large w-5 text-center"></i> Inicio
            </a>
            <a href="mis_compras.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-shopping-bag w-5 text-center"></i> Mis compras
            </a>
            <a href="mis_devoluciones.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-undo-alt w-5 text-center"></i> Mis devoluciones
            </a>
            <a href="mi_perfil.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-user-circle w-5 text-center"></i> Mi perfil
            </a>
        </div>

        <!-- User info -->
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
                    <i class="fas fa-th-large text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Mi cuenta</h1>
            </div>
            <span class="text-xs text-slate-500"><?= date('d/m/Y') ?></span>
        </header>

        <main class="flex-1 p-6 fade-in">

            <!-- BANNER BIENVENIDA -->
            <div class="gradient-bg rounded-2xl p-6 mb-6 text-white flex items-center justify-between"
                style="box-shadow:0 8px 24px rgba(37,99,235,.3);">
                <div>
                    <p class="text-blue-100 text-sm">Bienvenida de vuelta</p>
                    <h2 class="text-2xl font-bold mt-0.5"><?= $nombre ?></h2>
                    <p class="text-blue-100 text-sm mt-1"><?= $email ?></p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center
                        border border-white/20 text-2xl font-bold text-white flex-shrink-0">
                    <?= $iniciales ?>
                </div>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-slate-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-brand-accent rounded-xl flex items-center justify-center"
                            style="box-shadow:0 4px 12px rgba(37,99,235,.3);">
                            <i class="fas fa-shopping-bag text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $totalCompras ?></p>
                            <p class="text-xs text-slate-500">Total de compras</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800">
                                $<?= number_format($totalGastado, 0, ',', '.') ?>
                            </p>
                            <p class="text-xs text-slate-500">Total gastado</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-red-400 rounded-xl flex items-center justify-center">
                            <i class="fas fa-undo-alt text-white"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $totalDevoluciones ?></p>
                            <p class="text-xs text-slate-500">Devoluciones</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCESOS RÁPIDOS -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <a href="mis_compras.php"
                    class="bg-white rounded-2xl p-6 border border-slate-100 stat-card
                      flex items-center gap-4 hover:no-underline">
                    <div class="w-12 h-12 gradient-accent rounded-xl flex items-center justify-center flex-shrink-0"
                        style="box-shadow:0 4px 12px rgba(37,99,235,.3);">
                        <i class="fas fa-shopping-bag text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">Mis compras</p>
                        <p class="text-xs text-slate-500 mt-0.5">Ver historial completo</p>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
                </a>
                <a href="mis_devoluciones.php"
                    class="bg-white rounded-2xl p-6 border border-slate-100 stat-card
                      flex items-center gap-4 hover:no-underline">
                    <div class="w-12 h-12 bg-red-400 rounded-xl flex items-center justify-center flex-shrink-0"
                        style="box-shadow:0 4px 12px rgba(239,68,68,.3);">
                        <i class="fas fa-undo-alt text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">Mis devoluciones</p>
                        <p class="text-xs text-slate-500 mt-0.5">Ver solicitudes registradas</p>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
                </a>
            </div>

            <!-- TABLAS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Últimas compras -->
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden"
                    style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div>
                            <h3 class="font-bold text-slate-800">Últimas compras</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tu historial reciente</p>
                        </div>
                        <a href="mis_compras.php" class="text-xs text-blue-500 hover:text-blue-700 font-semibold">
                            Ver todas
                        </a>
                    </div>
                    <?php if (empty($compras)): ?>
                        <div class="py-14 text-center text-slate-400">
                            <i class="fas fa-shopping-bag text-4xl mb-3 block opacity-20"></i>
                            <p class="text-sm">Aún no tienes compras registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-50">
                            <?php foreach (array_slice($compras, 0, 5) as $c): ?>
                                <div class="flex items-center justify-between px-6 py-3 trow">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">#<?= $c['id_venta'] ?></p>
                                        <p class="text-xs text-slate-400">
                                            <?= date('d/m/Y', strtotime($c['fecha'])) ?>
                                            · <?= $c['metodo_pago'] ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-slate-800">
                                            $<?= number_format($c['total'], 2) ?>
                                        </p>
                                        <span class="badge <?= $c['estado'] === 'Completada'
                                                                ? 'bg-emerald-100 text-emerald-700'
                                                                : 'bg-red-100 text-red-700' ?>">
                                            <?= $c['estado'] ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Devoluciones recientes -->
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden"
                    style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div>
                            <h3 class="font-bold text-slate-800">Mis devoluciones</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Solicitudes registradas</p>
                        </div>
                        <a href="mis_devoluciones.php" class="text-xs text-blue-500 hover:text-blue-700 font-semibold">
                            Ver todas
                        </a>
                    </div>
                    <?php if (empty($devoluciones)): ?>
                        <div class="py-14 text-center text-slate-400">
                            <i class="fas fa-undo-alt text-4xl mb-3 block opacity-20"></i>
                            <p class="text-sm">Sin devoluciones registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-50">
                            <?php foreach ($devoluciones as $d): ?>
                                <div class="flex items-center justify-between px-6 py-3 trow">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            Venta #<?= $d['id_venta'] ?>
                                        </p>
                                        <p class="text-xs text-slate-400 truncate max-w-xs">
                                            <?= htmlspecialchars($d['motivo']) ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-red-600">
                                            -$<?= number_format($d['total_devolucion'], 2) ?>
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            <?= date('d/m/Y', strtotime($d['fecha'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

</body>

</html>