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
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Producto.php';

$db     = (new Database())->conectar();
$ventaM = new Venta($db);
$prodM  = new Producto($db);
$ventas = $ventaM->listar();
$kpi    = $ventaM->totales();
$productos = $prodM->listar();
$cajaAbierta = $ventaM->cajaAbierta();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Ventas</title>
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
            max-width: 760px;
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

        .item-row {
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 8px;
            align-items: center;
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
            <a href="ventas.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white hover:text-white">
                <i class="fas fa-shopping-cart w-5 text-center"></i> Ventas
            </a>
            <a href="inventario.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
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
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Ventas</h1>
                <?php if (!$cajaAbierta): ?>
                    <span class="ml-2 px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Sin caja abierta
                    </span>
                <?php endif; ?>
            </div>
            <button onclick="openModal()" class="flex items-center gap-2 px-4 py-2 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                <i class="fas fa-plus text-xs"></i> Nueva venta
            </button>
        </header>

        <main class="flex-1 p-6 fade-in">
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-brand-accent rounded-xl flex items-center justify-center" style="box-shadow:0 4px 12px rgba(59,130,246,.3);"><i class="fas fa-shopping-cart text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['total'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Total ventas</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center"><i class="fas fa-dollar-sign text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800">$<?= number_format($kpi['monto'] ?? 0, 0, ',', '.') ?></p>
                            <p class="text-xs text-slate-500">Ingresos totales</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center"><i class="fas fa-check-circle text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['completadas'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Completadas</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-purple-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-purple-500 rounded-xl flex items-center justify-center"><i class="fas fa-calendar-day text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['hoy'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Ventas hoy</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Historial de ventas</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Todas las transacciones registradas</p>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <input type="date" id="filter-desde" onchange="filterTable()" class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        <input type="date" id="filter-hasta" onchange="filterTable()" class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
                        <select id="filter-metodo" onchange="filterTable()" class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer transition">
                            <option value="">Todos los métodos</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia bancaria">Transferencia</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['#', 'Fecha', 'Cliente', 'Empleado', 'Método', 'Total', 'Estado', ''] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($ventas)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-shopping-cart text-4xl mb-3 block opacity-20"></i>
                                        <p class="text-sm">No hay ventas registradas aún</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ventas as $v): ?>
                                    <tr class="trow cursor-pointer" id="vrow-<?= $v['id_venta'] ?>"
                                        data-metodo="<?= $v['metodo_pago'] ?>"
                                        data-fecha="<?= date('Y-m-d', strtotime($v['fecha'])) ?>"
                                        onclick="verDetalle(<?= $v['id_venta'] ?>)">
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#<?= $v['id_venta'] ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= htmlspecialchars($v['cliente_nombre'] ?? 'Mostrador') ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= htmlspecialchars($v['empleado']) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge <?= $v['metodo_pago'] === 'Efectivo' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' ?>">
                                                <i class="fas <?= $v['metodo_pago'] === 'Efectivo' ? 'fa-money-bill' : 'fa-university' ?> mr-1 text-xs"></i>
                                                <?= $v['metodo_pago'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">$<?= number_format($v['total'], 2) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge <?= $v['estado'] === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= $v['estado'] ?></span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-blue-500"><i class="fas fa-eye"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div id="empty-state" class="hidden py-16 text-center text-slate-400">
                    <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                    <p class="text-sm font-medium">No se encontraron ventas con ese criterio</p>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL NUEVA VENTA -->
    <div id="modal-venta" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center"><i class="fas fa-shopping-cart text-white text-sm"></i></div>
                    <h3 class="text-lg font-bold text-slate-800">Nueva venta</h3>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition"><i class="fas fa-times text-sm"></i></button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 space-y-4">
                <!-- Buscar cliente -->
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Cliente (opcional)</label>
                    <div class="flex gap-2">
                        <input type="text" id="inp-cliente" placeholder="Buscar por nombre o email..." oninput="buscarCliente(this.value)"
                            class="flex-1 px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                        <button onclick="limpiarCliente()" class="px-3 py-2.5 bg-slate-100 text-slate-500 rounded-xl text-sm hover:bg-slate-200 transition">Mostrador</button>
                    </div>
                    <div id="cliente-results" class="hidden mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-10 relative"></div>
                    <div id="cliente-sel" class="hidden mt-2 p-2 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700 flex items-center justify-between">
                        <span id="cliente-sel-nombre"></span>
                        <button onclick="limpiarCliente()" class="text-blue-400 hover:text-blue-600"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <!-- Método de pago -->
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Método de pago *</label>
                    <select id="sel-metodo" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                    </select>
                </div>
                <!-- Agregar producto -->
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Agregar producto</label>
                    <div class="flex gap-2">
                        <select id="sel-producto" class="flex-1 px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                            <option value="">Seleccionar producto...</option>
                            <?php foreach ($productos as $p): if ($p['estado'] !== 'Activo' || $p['stock'] <= 0) continue; ?>
                                <option value="<?= $p['id_producto'] ?>" data-precio="<?= $p['precio_venta'] ?>" data-stock="<?= $p['stock'] ?>">
                                    <?= htmlspecialchars("{$p['nombre']} ({$p['talla']}/{$p['color']}) — Stock: {$p['stock']}") ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" id="inp-qty" placeholder="Cant." min="1" class="w-20 px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                        <button onclick="agregarItem()" class="px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-md transition">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
                <div id="items-list" class="space-y-2"></div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                    <span class="font-semibold text-slate-700">Total</span>
                    <span id="total-display" class="text-xl font-bold text-blue-600">$0.00</span>
                </div>
                <div class="flex gap-3 pt-2">
                    <button onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                    <button onclick="guardarVenta()" class="flex-1 py-3 bg-brand-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg">
                        <i class="fas fa-check mr-2 text-xs"></i>Confirmar venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE VENTA -->
    <div id="modal-det" class="modal-overlay hidden">
        <div style="background:white;border-radius:24px;box-shadow:0 25px 60px rgba(0,0,0,.15);width:100%;max-width:540px;max-height:90vh;display:flex;flex-direction:column;" class="scale-up">
            <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Detalle de venta</h3>
                <button onclick="closeDet()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition"><i class="fas fa-times text-sm"></i></button>
            </div>
            <div id="det-body" class="p-6 overflow-y-auto flex-1"></div>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/admin/VentaController.php';
        let items = [],
            clienteId = null;

        function openModal() {
            items = [];
            clienteId = null;
            renderItems();
            document.getElementById('inp-cliente').value = '';
            document.getElementById('cliente-results').classList.add('hidden');
            document.getElementById('cliente-sel').classList.add('hidden');
            document.getElementById('modal-venta').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-venta').classList.add('hidden');
        }

        function closeDet() {
            document.getElementById('modal-det').classList.add('hidden');
        }

        let clienteTimer;

        function buscarCliente(q) {
            clearTimeout(clienteTimer);
            if (q.length < 2) {
                document.getElementById('cliente-results').classList.add('hidden');
                return;
            }
            clienteTimer = setTimeout(() => {
                fetch(`${CTRL}?action=buscarCliente&q=${encodeURIComponent(q)}`).then(r => r.json()).then(data => {
                    const box = document.getElementById('cliente-results');
                    if (!data.length) {
                        box.classList.add('hidden');
                        return;
                    }
                    box.innerHTML = data.map(c => `
                <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm text-slate-700 border-b border-slate-100 last:border-0"
                    onclick="selCliente(${c.id_usuario},'${c.nombre} ${c.apellido}')">
                    ${c.nombre} ${c.apellido} <span class="text-slate-400 text-xs">${c.email}</span>
                </div>`).join('');
                    box.classList.remove('hidden');
                });
            }, 300);
        }

        function selCliente(id, nombre) {
            clienteId = id;
            document.getElementById('inp-cliente').value = '';
            document.getElementById('cliente-results').classList.add('hidden');
            document.getElementById('cliente-sel-nombre').textContent = nombre;
            document.getElementById('cliente-sel').classList.remove('hidden');
        }

        function limpiarCliente() {
            clienteId = null;
            document.getElementById('inp-cliente').value = '';
            document.getElementById('cliente-sel').classList.add('hidden');
        }

        function agregarItem() {
            const sel = document.getElementById('sel-producto');
            const id = parseInt(sel.value);
            const qty = parseInt(document.getElementById('inp-qty').value);
            const opt = sel.options[sel.selectedIndex];
            const precio = parseFloat(opt.dataset.precio || 0);
            const stock = parseInt(opt.dataset.stock || 0);
            const nombre = opt.text;
            if (!id || !qty || qty < 1) {
                showToast('Selecciona producto y cantidad', 'error');
                return;
            }
            const idx = items.findIndex(i => i.id_producto === id);
            const totalQty = (idx >= 0 ? items[idx].cantidad : 0) + qty;
            if (totalQty > stock) {
                showToast(`Stock insuficiente. Disponible: ${stock}`, 'error');
                return;
            }
            if (idx >= 0) items[idx].cantidad += qty;
            else items.push({
                id_producto: id,
                nombre,
                cantidad: qty,
                precio_unitario: precio
            });
            renderItems();
            document.getElementById('inp-qty').value = '';
        }

        function renderItems() {
            const list = document.getElementById('items-list');
            const total = items.reduce((s, i) => s + i.cantidad * i.precio_unitario, 0);
            document.getElementById('total-display').textContent = '$' + total.toFixed(2);
            if (!items.length) {
                list.innerHTML = '<p class="text-xs text-slate-400 text-center py-2">Sin productos agregados</p>';
                return;
            }
            list.innerHTML = items.map((it, idx) => `
        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl text-sm">
            <span class="font-medium text-slate-700 flex-1 truncate">${it.nombre}</span>
            <span class="text-slate-500 mx-3">${it.cantidad} × $${it.precio_unitario.toFixed(2)}</span>
            <span class="font-bold text-blue-700 mr-3">$${(it.cantidad*it.precio_unitario).toFixed(2)}</span>
            <button onclick="quitarItem(${idx})" class="w-6 h-6 bg-red-100 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-200 transition"><i class="fas fa-times text-xs"></i></button>
        </div>`).join('');
        }

        function quitarItem(idx) {
            items.splice(idx, 1);
            renderItems();
        }

        function guardarVenta() {
            if (!items.length) {
                showToast('Agrega al menos un producto', 'error');
                return;
            }
            const metodo = document.getElementById('sel-metodo').value;
            const fd = new FormData();
            fd.append('action', 'crear');
            fd.append('metodo_pago', metodo);
            fd.append('id_cliente', clienteId || 0);
            fd.append('items', JSON.stringify(items));
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

        function filterTable() {
            const desde = document.getElementById('filter-desde').value;
            const hasta = document.getElementById('filter-hasta').value;
            const metodo = document.getElementById('filter-metodo').value;
            let v = 0;
            document.querySelectorAll('#table-body tr[id^="vrow-"]').forEach(r => {
                const fecha = r.dataset.fecha;
                const show = (!metodo || r.dataset.metodo === metodo) &&
                    (!desde || fecha >= desde) &&
                    (!hasta || fecha <= hasta);
                r.style.display = show ? '' : 'none';
                if (show) v++;
            });
            document.getElementById('empty-state').classList.toggle('hidden', v > 0);
        }

        function verDetalle(id) {
            fetch(`${CTRL}?action=detalle&id=${id}`).then(r => r.json()).then(data => {
                const v = data.venta;
                const body = document.getElementById('det-body');
                body.innerHTML = `
            <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                <div><p class="text-xs text-slate-400">Fecha</p><p class="font-semibold">${new Date(v.fecha).toLocaleString('es-CO')}</p></div>
                <div><p class="text-xs text-slate-400">Cliente</p><p class="font-semibold">${v.cliente_nombre||'Mostrador'}</p></div>
                <div><p class="text-xs text-slate-400">Empleado</p><p class="font-semibold">${v.empleado}</p></div>
                <div><p class="text-xs text-slate-400">Método</p><p class="font-semibold">${v.metodo_pago}</p></div>
            </div>
            <table class="w-full text-sm mb-4"><thead><tr>
                <th class="text-left py-2 text-xs text-slate-500 uppercase">Producto</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase">Cant.</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase">P.Unit</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase">Subtotal</th>
            </tr></thead><tbody>` +
                    data.detalle.map(i => `<tr class="border-t border-slate-100">
                <td class="py-3 font-medium text-slate-800">${i.producto} <span class="text-slate-400 text-xs">${i.talla}/${i.color}</span></td>
                <td class="py-3 text-right">${i.cantidad}</td>
                <td class="py-3 text-right">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                <td class="py-3 text-right font-bold text-blue-700">$${(i.cantidad*parseFloat(i.precio_unitario)).toFixed(2)}</td>
            </tr>`).join('') +
                    `</tbody></table>
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
                <span class="font-semibold text-slate-700">Total</span>
                <span class="text-xl font-bold text-blue-600">$${parseFloat(v.total).toFixed(2)}</span>
            </div>`;
                document.getElementById('modal-det').classList.remove('hidden');
            });
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
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeModal();
                closeDet();
            }
        });
        document.addEventListener('click', e => {
            if (!e.target.closest('#inp-cliente') && !e.target.closest('#cliente-results')) document.getElementById('cliente-results').classList.add('hidden');
        });
    </script>
</body>

</html>