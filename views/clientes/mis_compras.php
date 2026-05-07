<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 3) {
    header('Location: ../auth/login.php');
    exit();
}
$nombre    = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Venta.php';

$db      = (new Database())->conectar();
$ventaM  = new Venta($db);
$compras = $ventaM->porCliente($_SESSION['user_id']);
$totalGastado = array_sum(array_column($compras, 'total'));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Its Fashion | Mis Compras</title>
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

        .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.5); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999; padding: 16px; }
        .modal-box { background: white; border-radius: 1.5rem; box-shadow: 0 25px 60px rgba(0,0,0,.15); width: 100%; max-width: 600px; max-height: 94vh; display: flex; flex-direction: column; }

        @keyframes scaleUp { from { opacity: 0; transform: scale(.97) } to { opacity: 1; transform: scale(1) } }
        .scale-up { animation: scaleUp .3s ease-out; }

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
            <a href="mis_compras.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium hover:text-white">
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
                    <i class="fas fa-shopping-bag text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Mis compras</h1>
            </div>
        </header>

        <main class="flex-1 p-6 fade-in">
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #2563eb;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center" style="box-shadow:0 4px 12px rgba(37,99,235,.3);"><i class="fas fa-shopping-bag text-white text-sm"></i></div>
                        <div>
                            <p class="text-lg font-bold text-slate-800"><?= count($compras) ?></p>
                            <p class="text-xs text-slate-500">Total compras</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fas fa-dollar-sign text-sm"></i></div>
                        <div>
                            <p class="text-lg font-bold text-slate-800">$<?= number_format($totalGastado, 0, ',', '.') ?></p>
                            <p class="text-xs text-slate-500">Total gastado</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de compras -->
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Historial de compras</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Haz clic en una compra para ver el detalle o solicitar devolución</p>
                </div>

                <?php if (empty($compras)): ?>
                    <div class="py-20 text-center text-slate-400">
                        <i class="fas fa-shopping-bag text-5xl mb-4 block opacity-20"></i>
                        <p class="text-lg font-semibold text-slate-600">Aún no tienes compras</p>
                        <p class="text-sm mt-1">Visítanos en tienda para hacer tu primera compra</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <?php foreach (['#', 'Fecha', 'Productos', 'Método', 'Total', 'Estado', ''] as $h): ?>
                                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($compras as $c): ?>
                                    <tr class="trow cursor-pointer" onclick="verDetalle(<?= $c['id_venta'] ?>)">
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#<?= $c['id_venta'] ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600 max-w-xs truncate"><?= htmlspecialchars($c['productos']) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge <?= $c['metodo_pago'] === 'Efectivo' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' ?>">
                                                <?= $c['metodo_pago'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">$<?= number_format($c['total'], 2) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="badge <?= $c['estado'] === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= $c['estado'] ?></span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-blue-500"><i class="fas fa-eye"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- MODAL DETALLE / DEVOLUCIÓN -->
    <div id="modal-det" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-accent rounded-xl flex items-center justify-center"><i class="fas fa-shopping-bag text-white text-sm"></i></div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-800">Detalle de compra</h3>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition"><i class="fas fa-times text-sm"></i></button>
            </div>
            <div id="modal-body" class="p-6 overflow-y-auto flex-1"></div>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/clientes/ClienteController.php';
        let ventaActual = null,
            itemsDev = [];

        function verDetalle(id_venta) {
            fetch(`${CTRL}?action=detalleCompra&id=${id_venta}`).then(r => r.json()).then(data => {
                if (!data.venta) {
                    showToast('No se pudo cargar la compra', 'error');
                    return;
                }
                ventaActual = data;
                itemsDev = data.detalle.map(d => ({
                    ...d,
                    dev_qty: 0
                }));
                renderDetalle();
                document.getElementById('modal-det').classList.remove('hidden');
            });
        }

        function renderDetalle() {
            const v = ventaActual.venta;
            const esCompletada = v.estado === 'Completada';
            document.getElementById('modal-title').textContent = `Compra #${v.id_venta}`;
            document.getElementById('modal-body').innerHTML = `
        <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
            <div><p class="text-xs text-slate-400">Fecha</p><p class="font-semibold">${new Date(v.fecha).toLocaleDateString('es-CO')}</p></div>
            <div><p class="text-xs text-slate-400">Método de pago</p><p class="font-semibold">${v.metodo_pago}</p></div>
            <div><p class="text-xs text-slate-400">Estado</p>
                <span class="badge ${v.estado==='Completada'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700'}">${v.estado}</span>
            </div>
            <div><p class="text-xs text-slate-400">Total</p><p class="font-bold text-blue-600 text-lg">$${parseFloat(v.total).toFixed(2)}</p></div>
        </div>
        <table class="w-full text-sm mb-4">
            <thead><tr>
                <th class="text-left py-2 text-xs text-slate-500 uppercase border-b border-slate-100">Producto</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase border-b border-slate-100">Cant.</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase border-b border-slate-100">Precio</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase border-b border-slate-100">Subtotal</th>
            </tr></thead>
            <tbody>
                ${ventaActual.detalle.map(i=>`
                <tr class="border-b border-slate-50">
                    <td class="py-3 font-medium text-slate-800">${i.producto} <span class="text-slate-400 text-xs">${i.talla}/${i.color}</span></td>
                    <td class="py-3 text-right text-slate-600">${i.cantidad}</td>
                    <td class="py-3 text-right text-slate-600">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                    <td class="py-3 text-right font-bold text-slate-800">$${(i.cantidad*parseFloat(i.precio_unitario)).toFixed(2)}</td>
                </tr>`).join('')}
            </tbody>
        </table>
        ${esCompletada ? `
        <div class="border-t border-slate-100 pt-4">
            <p class="text-sm font-semibold text-slate-700 mb-3">¿Deseas solicitar una devolución?</p>
            <div id="dev-items" class="space-y-2 mb-3"></div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Motivo *</label>
                <textarea id="inp-motivo" rows="2" placeholder="Describe el motivo de la devolución..."
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 resize-none"></textarea>
            </div>
            <div class="flex items-center justify-between mt-3 p-3 bg-slate-50 rounded-xl">
                <span class="text-sm font-semibold text-slate-700">Total a devolver</span>
                <span id="total-dev" class="font-bold text-red-600">$0.00</span>
            </div>
            <button onclick="enviarDevolucion()" class="w-full mt-3 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition text-sm">
                <i class="fas fa-undo-alt mr-2 text-xs"></i>Solicitar devolución
            </button>
        </div>` : ''}
    `;
            if (esCompletada) renderDevItems();
        }

        function renderDevItems() {
            document.getElementById('dev-items').innerHTML = itemsDev.map((it, idx) => `
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl text-sm">
            <div class="flex-1">
                <p class="font-medium text-slate-800">${it.producto} <span class="text-slate-400 text-xs">${it.talla}/${it.color}</span></p>
                <p class="text-xs text-slate-500">Comprado: ${it.cantidad} uds</p>
            </div>
            <div class="flex items-center gap-2 ml-3">
                <label class="text-xs text-slate-500">Devolver:</label>
                <input type="number" min="0" max="${it.cantidad}" value="${it.dev_qty}"
                    onchange="updateQty(${idx}, this.value)"
                    class="w-16 px-2 py-1 bg-white border-2 border-slate-200 rounded-lg text-sm text-center">
            </div>
        </div>`).join('');
            calcTotal();
        }

        function updateQty(idx, val) {
            itemsDev[idx].dev_qty = Math.min(parseInt(val) || 0, itemsDev[idx].cantidad);
            calcTotal();
        }

        function calcTotal() {
            const total = itemsDev.reduce((s, i) => s + i.dev_qty * parseFloat(i.precio_unitario), 0);
            const el = document.getElementById('total-dev');
            if (el) el.textContent = '$' + total.toFixed(2);
        }

        function enviarDevolucion() {
            const motivo = document.getElementById('inp-motivo')?.value.trim();
            if (!motivo) {
                showToast('El motivo es obligatorio', 'error');
                return;
            }
            const items = itemsDev.filter(i => i.dev_qty > 0).map(i => ({
                id_producto: i.id_producto,
                cantidad: i.dev_qty,
                precio_unitario: i.precio_unitario
            }));
            if (!items.length) {
                showToast('Selecciona al menos un producto', 'error');
                return;
            }
            const fd = new FormData();
            fd.append('action', 'solicitarDevolucion');
            fd.append('id_venta', ventaActual.venta.id_venta);
            fd.append('motivo', motivo);
            fd.append('items', JSON.stringify(items));
            fetch(CTRL, {
                method: 'POST',
                body: fd
            }).then(r => r.json()).then(d => {
                if (d.ok) {
                    showToast('Devolución registrada correctamente', 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else showToast(d.msg, 'error');
            });
        }

        function closeModal() {
            document.getElementById('modal-det').classList.add('hidden');
        }

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast'),
                i = document.getElementById('toast-icon'),
                s = document.getElementById('toast-text');
            const colors = {success: '#10b981', error: '#ef4444'},
                icons = {success: 'fa-check-circle', error: 'fa-times-circle'};
            t.style.borderLeftColor = colors[type];
            i.className = `fas ${icons[type]} mt-0.5 flex-shrink-0`;
            i.style.color = colors[type];
            s.textContent = msg;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 3500);
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>

</html>