<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 3) {
    header('Location: ../auth/login.php');
    exit();
}
$nombre    = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));

require_once __DIR__ . '/../../config/database.php';

$db = (new Database())->conectar();

$stmt = $db->prepare(
    "SELECT d.id_devolucion, d.fecha, d.motivo, d.total_devolucion, d.estado, d.fecha_resolucion, v.id_venta
     FROM devoluciones d
     JOIN venta v ON v.id_venta = d.id_venta
     WHERE v.id_cliente = :id ORDER BY d.fecha DESC"
);
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$devoluciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalReembolsado = array_sum(array_map(
    fn($d) => $d['estado'] === 'Aceptada' ? $d['total_devolucion'] : 0,
    $devoluciones
));
$pendientesCount = count(array_filter($devoluciones, fn($d) => $d['estado'] === 'Pendiente'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Its Fashion | Mis Devoluciones</title>
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
        .sidebar-item { transition: all 0.3s ease; }
        .sidebar-item:hover { background-color: rgba(255,255,255,0.05); transform: translateX(4px); }
        .sidebar-item.active { background-color: rgba(37,99,235,0.15); color: #60a5fa; border-right: 3px solid #2563eb; }

        .glass-header { background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(226,232,240,0.8); }

        .trow:hover { background: #f8fafc; }

        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }

        .stat-card { transition: all 0.3s ease; background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(15,23,42,0.06); background: white; }

        .gradient-accent { background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%); }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
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
            <a href="dashboard_cliente.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-th-large w-5 text-center"></i> Inicio
            </a>
            <a href="mis_compras.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-shopping-bag w-5 text-center"></i> Mis compras
            </a>
            <a href="mis_devoluciones.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:text-white">
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
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-400 shadow-sm border border-red-100">
                    <i class="fas fa-undo-alt text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Mis devoluciones</h1>
            </div>
        </header>

        <main class="flex-1 p-6 fade-in">
            <!-- KPIs -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center"><i class="fas fa-clock text-sm"></i></div>
                        <div>
                            <p class="text-lg font-bold text-slate-800"><?= $pendientesCount ?></p>
                            <p class="text-xs text-slate-500">En revisión</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 text-red-500 rounded-xl flex items-center justify-center"><i class="fas fa-undo-alt text-sm"></i></div>
                        <div>
                            <p class="text-lg font-bold text-slate-800"><?= count($devoluciones) ?></p>
                            <p class="text-xs text-slate-500">Solicitudes totales</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fas fa-dollar-sign text-sm"></i></div>
                        <div>
                            <p class="text-lg font-bold text-red-600">-$<?= number_format($totalReembolsado, 0, ',', '.') ?></p>
                            <p class="text-xs text-slate-500">Monto reembolsado</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($pendientesCount > 0): ?>
            <div class="mb-4 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl text-sm text-amber-800">
                <i class="fas fa-info-circle text-amber-500 flex-shrink-0"></i>
                Tienes <?= $pendientesCount ?> solicitud<?= $pendientesCount > 1 ? 'es' : '' ?> pendiente<?= $pendientesCount > 1 ? 's' : '' ?> de revisión por el administrador.
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Historial de solicitudes</h3>
                    <p class="text-xs text-slate-500 mt-0.5">El administrador revisará y resolverá cada solicitud</p>
                </div>

                <?php if (empty($devoluciones)): ?>
                    <div class="py-20 text-center text-slate-400">
                        <i class="fas fa-undo-alt text-5xl mb-4 block opacity-20"></i>
                        <p class="text-lg font-semibold text-slate-600">Aún no tienes solicitudes</p>
                        <p class="text-sm mt-1">Puedes solicitar una devolución desde "Mis compras"</p>
                        <a href="mis_compras.php" class="inline-block mt-4 px-6 py-2 gradient-accent text-white font-semibold rounded-full hover:shadow-lg transition">Ir a Mis compras</a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <?php foreach (['ID', 'Venta', 'Fecha solicitud', 'Motivo', 'Monto', 'Estado'] as $h): ?>
                                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($devoluciones as $d):
                                    $estadoClasses = [
                                        'Pendiente' => 'bg-amber-100 text-amber-700',
                                        'Aceptada'  => 'bg-emerald-100 text-emerald-700',
                                        'Rechazada' => 'bg-red-100 text-red-600',
                                    ];
                                    $cls = $estadoClasses[$d['estado']] ?? 'bg-slate-100 text-slate-600';
                                ?>
                                    <tr class="trow">
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#<?= str_pad($d['id_devolucion'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-blue-500">#<?= str_pad($d['id_venta'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= date('d/m/Y H:i', strtotime($d['fecha'])) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600 truncate max-w-xs"><?= htmlspecialchars($d['motivo']) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-red-600">-$<?= number_format($d['total_devolucion'], 2) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge <?= $cls ?>">
                                                <?php if ($d['estado'] === 'Pendiente'): ?>
                                                    <i class="fas fa-clock mr-1 text-xs"></i>
                                                <?php elseif ($d['estado'] === 'Aceptada'): ?>
                                                    <i class="fas fa-check mr-1 text-xs"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-times mr-1 text-xs"></i>
                                                <?php endif; ?>
                                                <?= $d['estado'] ?>
                                            </span>
                                            <?php if ($d['fecha_resolucion']): ?>
                                                <p class="text-xs text-slate-400 mt-0.5"><?= date('d/m/Y', strtotime($d['fecha_resolucion'])) ?></p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
