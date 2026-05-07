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
require_once __DIR__ . '/../../models/Compra.php';
require_once __DIR__ . '/../../models/Proveedor.php';
require_once __DIR__ . '/../../models/Producto.php';

$db      = (new Database())->conectar();
$compraM = new Compra($db);
$provM   = new Proveedor($db);
$prodM   = new Producto($db);
$compras    = $compraM->listar();
$kpi        = $compraM->totales();
$proveedores = $provM->listar();
$productos  = $prodM->listarActivos();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Abastecimiento</title>
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

        .modal-det {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
            width: 100%;
            max-width: 560px;
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
            <a href="compras.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
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
                    <i class="fas fa-truck text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Abastecimiento</h1>
            </div>
            <button onclick="openModal()" class="flex items-center gap-2 px-4 py-2 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                <i class="fas fa-plus text-xs"></i> Nueva compra
            </button>
        </header>

        <main class="flex-1 p-6 fade-in">
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-brand-accent rounded-xl flex items-center justify-center"><i class="fas fa-truck text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['total'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Total compras</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center"><i class="fas fa-dollar-sign text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800">$<?= number_format($kpi['monto'] ?? 0, 0, ',', '.') ?></p>
                            <p class="text-xs text-slate-500">Inversión total</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-purple-100 stat-card">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-purple-500 rounded-xl flex items-center justify-center"><i class="fas fa-calendar-day text-white"></i></div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800"><?= $kpi['hoy'] ?? 0 ?></p>
                            <p class="text-xs text-slate-500">Compras hoy</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Historial de compras</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Entradas de mercancía registradas</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['#', 'Fecha', 'Proveedor', 'Registrado por', 'Total', ''] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($compras)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-truck text-4xl mb-3 block opacity-20"></i>
                                        <p class="text-sm">No hay compras registradas aún</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($compras as $c): ?>
                                    <tr class="trow cursor-pointer" onclick="verDetalle(<?= $c['id_compra'] ?>)">
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#<?= $c['id_compra'] ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-semibold text-slate-800"><?= htmlspecialchars($c['proveedor_nombre'] ?? '—') ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600"><?= htmlspecialchars($c['empleado'] ?? '—') ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">$<?= number_format($c['total'], 2) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-blue-500"><i class="fas fa-eye"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL NUEVA COMPRA -->
    <div id="modal-compra" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center"><i class="fas fa-truck text-white text-sm"></i></div>
                    <h3 class="text-lg font-bold text-slate-800">Nueva compra</h3>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition"><i class="fas fa-times text-sm"></i></button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Proveedor *</label>
                    <select id="sel-proveedor" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                        <option value="">Seleccionar proveedor...</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?= $p['id_proveedor'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Agregar producto</label>
                    <div class="flex gap-2">
                        <select id="sel-producto" class="flex-1 px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                            <option value="">Seleccionar producto...</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?= $p['id_producto'] ?>" data-precio="<?= $p['precio_compra'] ?>"><?= htmlspecialchars("{$p['nombre']} ({$p['talla']}/{$p['color']})") ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" id="inp-qty" placeholder="Cant." min="1" class="w-20 px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                        <input type="number" id="inp-precio" placeholder="Precio" min="0" step="0.01" class="w-28 px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                        <button onclick="agregarItem()" class="px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-md transition">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
                <div id="items-list" class="space-y-2"></div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                    <span class="font-semibold text-slate-700">Total compra</span>
                    <span id="total-display" class="text-xl font-bold text-blue-600">$0.00</span>
                </div>
                <div class="flex gap-3 pt-2">
                    <button onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                    <button onclick="guardarCompra()" class="flex-1 py-3 bg-brand-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg">
                        <i class="fas fa-save mr-2 text-xs"></i>Registrar compra
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE -->
    <div id="modal-det" class="modal-overlay hidden">
        <div class="modal-det scale-up">
            <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Detalle de compra</h3>
                <button onclick="closeDet()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition"><i class="fas fa-times text-sm"></i></button>
            </div>
            <div id="det-body" class="p-6 overflow-y-auto flex-1"></div>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/admin/CompraController.php';
        let items = [];

        function openModal() {
            items = [];
            renderItems();
            document.getElementById('modal-compra').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-compra').classList.add('hidden');
        }

        function closeDet() {
            document.getElementById('modal-det').classList.add('hidden');
        }

        function agregarItem() {
            const sel = document.getElementById('sel-producto');
            const id = parseInt(sel.value);
            const qty = parseInt(document.getElementById('inp-qty').value);
            const precio = parseFloat(document.getElementById('inp-precio').value);
            const nombre = sel.options[sel.selectedIndex]?.text;
            if (!id || !qty || qty < 1 || !precio || precio <= 0) {
                showToast('Completa producto, cantidad y precio', 'error');
                return;
            }
            const idx = items.findIndex(i => i.id_producto === id);
            if (idx >= 0) items[idx].cantidad += qty;
            else items.push({
                id_producto: id,
                nombre,
                cantidad: qty,
                precio_unitario: precio
            });
            renderItems();
            document.getElementById('inp-qty').value = '';
            document.getElementById('inp-precio').value = '';
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

        function guardarCompra() {
            const id_proveedor = document.getElementById('sel-proveedor').value;
            if (!id_proveedor) {
                showToast('Selecciona un proveedor', 'error');
                return;
            }
            if (!items.length) {
                showToast('Agrega al menos un producto', 'error');
                return;
            }
            const fd = new FormData();
            fd.append('action', 'crear');
            fd.append('id_proveedor', id_proveedor);
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

        function verDetalle(id) {
            fetch(`${CTRL}?action=detalle&id=${id}`).then(r => r.json()).then(items => {
                const body = document.getElementById('det-body');
                if (!items.length) {
                    body.innerHTML = '<p class="text-slate-400 text-sm text-center py-8">Sin detalle disponible</p>';
                } else {
                    body.innerHTML = `<table class="w-full text-sm"><thead><tr>
                <th class="text-left py-2 text-xs text-slate-500 uppercase">Producto</th>
                <th class="text-left py-2 text-xs text-slate-500 uppercase">Talla/Color</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase">Cant.</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase">P.Unit</th>
                <th class="text-right py-2 text-xs text-slate-500 uppercase">Subtotal</th>
            </tr></thead><tbody>` +
                        items.map(i => `<tr class="border-t border-slate-100">
                <td class="py-3 font-medium text-slate-800">${i.producto}</td>
                <td class="py-3 text-slate-500">${i.talla}/${i.color}</td>
                <td class="py-3 text-right">${i.cantidad}</td>
                <td class="py-3 text-right">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                <td class="py-3 text-right font-bold text-blue-700">$${parseFloat(i.subtotal).toFixed(2)}</td>
            </tr>`).join('') + '</tbody></table>';
                }
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

        // Auto-fill precio compra al seleccionar producto
        document.getElementById('sel-producto').addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            document.getElementById('inp-precio').value = opt.dataset.precio || '';
        });
    </script>
</body>

</html>