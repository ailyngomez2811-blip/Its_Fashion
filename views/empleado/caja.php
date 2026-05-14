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
require_once __DIR__ . '/../../models/Caja.php';

$db    = (new Database())->conectar();
$cajaM = new Caja($db);
$caja  = $cajaM->cajaActiva();
$movimientos = $caja ? $cajaM->movimientos($caja['id_caja']) : [];
$saldo_teorico = $caja ? $cajaM->saldoTeorico($caja['id_caja']) : 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Its Fashion | Caja</title>
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
            max-width: 480px;
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
            <a href="dashboard_empleado.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-th-large w-5 text-center"></i> Dashboard
            </a>

            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mt-6 mb-2">Operaciones</p>
            <a href="ventas.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Ventas
            </a>
            <a href="inventario.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-warehouse w-5 text-center"></i> Inventario
            </a>
            <a href="caja.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:text-white">
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
                    <i class="fas fa-cash-register text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Caja</h1>
            </div>
        </header>
        <main class="flex-1 p-6 fade-in">
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <!-- Banner estado -->
            <div class="flex items-center justify-between p-6 rounded-2xl mb-6 <?= $caja ? 'bg-brand-accent' : 'bg-slate-800' ?> text-white"
                style="box-shadow:<?= $caja ? '0 8px 24px rgba(59,130,246,.35)' : '0 8px 24px rgba(0,0,0,.2)' ?>;">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center border-2 <?= $caja ? 'bg-white/20 border-white/20' : 'bg-white/10 border-white/10' ?>">
                        <i class="fas fa-cash-register text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full <?= $caja ? 'bg-green-400' : 'bg-red-400' ?>"
                                style="<?= $caja ? 'box-shadow:0 0 8px #4ade80' : '' ?>"></div>
                            <span class="font-bold text-lg">Caja <?= $caja ? 'ABIERTA' : 'CERRADA' ?></span>
                        </div>
                        <p class="text-blue-100 text-sm">
                            <?= $caja ? "Apertura: " . date('d/m/Y H:i', strtotime($caja['fecha_apertura'])) . " · " . $caja['responsable'] : "Sin sesión de caja activa" ?>
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <?php if (!$caja): ?>
                        <button onclick="openModal('apertura')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-600 font-bold text-sm rounded-xl hover:bg-blue-50 transition">
                            <i class="fas fa-play text-xs"></i>Abrir caja
                        </button>
                    <?php else: ?>
                        <button onclick="openModal('movimiento')" class="px-4 py-2.5 rounded-xl bg-white/20 border border-white/20 text-white text-sm font-semibold hover:bg-white/30 transition flex items-center gap-2">
                            <i class="fas fa-exchange-alt text-xs"></i>Movimiento manual
                        </button>
                        <button onclick="openModal('cierre')" class="px-4 py-2.5 rounded-xl bg-red-500/80 border border-red-400/50 text-white text-sm font-semibold hover:bg-red-500 transition flex items-center gap-2">
                            <i class="fas fa-stop-circle text-xs"></i>Cerrar caja
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($caja): ?>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <?php $kpis = [
                        ['Saldo inicial',    '$' . number_format($caja['saldo_inicial'], 0, ',', '.'),          'fa-play',         'bg-brand-accent text-white'],
                        ['Ventas efectivo',  '$' . number_format($caja['total_ingresos'] ?? 0, 0, ',', '.'),      'fa-shopping-cart', 'bg-emerald-100 text-emerald-600'],
                        ['Saldo teórico',    '$' . number_format($saldo_teorico, 0, ',', '.'),                  'fa-calculator',   'bg-amber-100 text-amber-600'],
                        ['Movimientos',      count($movimientos),                                           'fa-list',         'bg-purple-100 text-purple-600'],
                    ];
                    foreach ($kpis as $k): ?>
                        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 <?= $k[3] ?> rounded-xl flex items-center justify-center"><i class="fas <?= $k[2] ?> text-sm"></i></div>
                                <div>
                                    <p class="text-lg font-bold text-slate-800"><?= $k[1] ?></p>
                                    <p class="text-xs text-slate-500"><?= $k[0] ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800">Movimientos del turno</h3>
                        <p class="text-xs text-slate-500 mt-0.5"><?= date('d/m/Y') ?></p>
                    </div>
                    <?php if (empty($movimientos)): ?>
                        <div class="py-16 text-center text-slate-400">
                            <i class="fas fa-exchange-alt text-3xl mb-3 block opacity-20"></i>
                            <p class="text-sm">No hay movimientos en este turno</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <?php foreach (['Hora', 'Tipo', 'Concepto', 'Monto'] as $h): ?>
                                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movimientos as $m): ?>
                                        <tr class="trow">
                                            <td class="px-6 py-4 border-b border-slate-50"><span class="font-mono text-xs text-slate-500"><?= date('H:i', strtotime($m['fecha'])) ?></span></td>
                                            <td class="px-6 py-4 border-b border-slate-50">
                                                <span class="badge <?= $m['tipo'] === 'Ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' ?>"><?= $m['tipo'] ?></span>
                                            </td>
                                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= htmlspecialchars($m['concepto']) ?></td>
                                            <td class="px-6 py-4 border-b border-slate-50">
                                                <span class="font-bold <?= $m['tipo'] === 'Ingreso' ? 'text-emerald-600' : 'text-red-600' ?>">
                                                    <?= $m['tipo'] === 'Ingreso' ? '+' : '-' ?>$<?= number_format($m['monto'], 0, ',', '.') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="py-20 text-center text-slate-400">
                    <i class="fas fa-cash-register text-5xl mb-4 block opacity-20"></i>
                    <p class="text-lg font-semibold text-slate-600">Caja sin turno activo</p>
                    <p class="text-sm mt-1">Abre la caja para comenzar a registrar movimientos</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- MODAL -->
    <div id="modal" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-start justify-between p-7 pb-0">
                <div>
                    <h3 id="modal-title" class="text-xl font-bold text-slate-800"></h3>
                    <p id="modal-sub" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition flex-shrink-0 ml-4">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-7 overflow-y-auto" id="modal-body"></div>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/admin/CajaController.php';
        const SALDO_TEORICO = <?= $saldo_teorico ?>;
        const ID_CAJA = <?= $caja ? $caja['id_caja'] : 0 ?>;
        const USUARIO = '<?= addslashes($usuario) ?>';

        function openModal(type) {
            document.getElementById('modal').classList.remove('hidden');
            const body = document.getElementById('modal-body');

            if (type === 'apertura') {
                document.getElementById('modal-title').textContent = 'Apertura de caja';
                document.getElementById('modal-sub').textContent = 'Registra el efectivo disponible al iniciar el turno';
                body.innerHTML = `
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Monto inicial en efectivo <span class="text-red-500">*</span></label>
                <div class="relative"><span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">$</span>
                <input type="number" id="f-monto" placeholder="0" min="0" class="w-full pl-7 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm transition"></div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Responsable</label>
                <input type="text" value="${USUARIO}" readonly class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm opacity-60">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                <button onclick="submitModal('apertura')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-play text-xs"></i>Abrir caja
                </button>
            </div>`;
            }

            if (type === 'movimiento') {
                document.getElementById('modal-title').textContent = 'Movimiento manual de caja';
                document.getElementById('modal-sub').textContent = 'Registra un ingreso o egreso de efectivo';
                body.innerHTML = `
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipo</label>
                <select id="f-tipo" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm cursor-pointer transition">
                    <option value="Ingreso">Ingreso</option><option value="Egreso">Egreso</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Concepto <span class="text-red-500">*</span></label>
                <input type="text" id="f-concepto" placeholder="Describe el movimiento..." class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm transition">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Monto <span class="text-red-500">*</span></label>
                <div class="relative"><span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">$</span>
                <input type="number" id="f-monto" placeholder="0" min="0" class="w-full pl-7 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm transition"></div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                <button onclick="submitModal('movimiento')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-save text-xs"></i>Registrar
                </button>
            </div>`;
            }

            if (type === 'cierre') {
                document.getElementById('modal-title').textContent = 'Cierre de caja';
                document.getElementById('modal-sub').textContent = 'Realiza el arqueo antes de cerrar';
                body.innerHTML = `
            <div class="p-4 bg-slate-50 rounded-xl flex justify-between items-center mb-4">
                <span class="font-semibold text-slate-700 text-sm">Saldo teórico del sistema</span>
                <span class="font-bold text-blue-600 text-xl">$${SALDO_TEORICO.toLocaleString('es-CO')}</span>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Conteo físico de efectivo <span class="text-red-500">*</span></label>
                <div class="relative"><span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">$</span>
                <input type="number" id="f-conteo" placeholder="0" min="0" oninput="calcDif()" class="w-full pl-7 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm transition"></div>
            </div>
            <div id="dif-preview" class="hidden p-3 rounded-xl text-sm mb-3"></div>
            <div id="panel-justif" class="hidden mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Justificación <span class="text-red-500">*</span></label>
                <textarea id="f-justif" rows="3" placeholder="Explica el motivo de la diferencia..." class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm resize-none transition"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                <button onclick="submitModal('cierre')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition">
                    <i class="fas fa-stop-circle text-xs"></i>Cerrar caja
                </button>
            </div>`;
            }
        }

        function calcDif() {
            const conteo = +document.getElementById('f-conteo')?.value || 0;
            const dif = conteo - SALDO_TEORICO;
            const prev = document.getElementById('dif-preview');
            const justif = document.getElementById('panel-justif');
            if (conteo > 0) {
                prev.classList.remove('hidden');
                if (dif === 0) {
                    prev.className = 'p-3 rounded-xl text-sm mb-3 bg-emerald-50 border border-emerald-200 text-emerald-700';
                    prev.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Sin diferencia. El efectivo cuadra perfectamente.';
                    justif.classList.add('hidden');
                } else {
                    prev.className = `p-3 rounded-xl text-sm mb-3 ${dif>0?'bg-blue-50 border border-blue-200 text-blue-700':'bg-red-50 border border-red-200 text-red-700'}`;
                    prev.innerHTML = `<i class="fas fa-${dif>0?'info-circle':'exclamation-triangle'} mr-1"></i>${dif>0?'Sobrante':'Faltante'}: <b>$${Math.abs(dif).toLocaleString('es-CO')}</b>`;
                    justif.classList.remove('hidden');
                }
            } else {
                prev.classList.add('hidden');
                justif.classList.add('hidden');
            }
        }

        function submitModal(type) {
            const fd = new FormData();
            if (type === 'apertura') {
                const monto = document.getElementById('f-monto').value;
                if (!monto) {
                    showToast('Ingresa el monto inicial', 'error');
                    return;
                }
                fd.append('action', 'abrir');
                fd.append('saldo_inicial', monto);
                fetch(CTRL, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json()).then(d => {
                    if (d.ok) {
                        showToast(d.msg, 'success');
                        closeModal();
                        setTimeout(() => location.reload(), 800);
                    } else showToast(d.msg, 'error');
                });
            }
            if (type === 'movimiento') {
                const tipo = document.getElementById('f-tipo').value;
                const concepto = document.getElementById('f-concepto').value;
                const monto = document.getElementById('f-monto').value;
                if (!concepto || !monto) {
                    showToast('Completa todos los campos', 'error');
                    return;
                }
                fd.append('action', 'movimiento');
                fd.append('id_caja', ID_CAJA);
                fd.append('tipo', tipo);
                fd.append('concepto', concepto);
                fd.append('monto', monto);
                fetch(CTRL, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json()).then(d => {
                    if (d.ok) {
                        showToast(d.msg, 'success');
                        closeModal();
                        setTimeout(() => location.reload(), 800);
                    } else showToast(d.msg, 'error');
                });
            }
            if (type === 'cierre') {
                const conteo = document.getElementById('f-conteo').value;
                const justif = document.getElementById('f-justif')?.value || '';
                if (!conteo) {
                    showToast('Ingresa el monto contado', 'error');
                    return;
                }
                const dif = parseFloat(conteo) - SALDO_TEORICO;
                if (dif !== 0 && !justif) {
                    showToast('La justificación es obligatoria cuando hay diferencia', 'error');
                    return;
                }
                fd.append('action', 'cerrar');
                fd.append('id_caja', ID_CAJA);
                fd.append('saldo_final', conteo);
                fd.append('justificacion', justif);
                fetch(CTRL, {
                    method: 'POST',
                    body: fd
                }).then(r => r.json()).then(d => {
                    if (d.ok) {
                        showToast(d.msg, 'success');
                        closeModal();
                        setTimeout(() => location.reload(), 800);
                    } else showToast(d.msg, 'error');
                });
            }
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
        document.getElementById('modal').addEventListener('click', e => {
            if (e.target === document.getElementById('modal')) closeModal();
        });

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast'),
                i = document.getElementById('toast-icon'),
                x = document.getElementById('toast-text');
            x.textContent = msg;
            t.style.borderLeftColor = type === 'success' ? '#3b82f6' : '#ef4444';
            i.className = `fas ${type==='success'?'fa-check-circle text-blue-500':'fa-exclamation-circle text-red-500'} mt-0.5 flex-shrink-0`;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 3500);
        }
    </script>
</body>

</html>