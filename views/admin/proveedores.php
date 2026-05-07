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
require_once __DIR__ . '/../../models/Proveedor.php';
$db   = (new Database())->conectar();
$provM = new Proveedor($db);
$provs = $provM->listar();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Proveedores</title>
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
            max-width: 560px;
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
            <a href="compras.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-truck w-5 text-center"></i> Abastecimiento
            </a>
            <a href="inventario.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-warehouse w-5 text-center"></i> Inventario
            </a>
            <a href="devoluciones.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                <i class="fas fa-undo-alt w-5 text-center"></i> Devoluciones
            </a>
            <a href="proveedores.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
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
                    <i class="fas fa-truck-loading text-lg"></i>
                </div>
                <h1 class="text-2xl font-serif font-bold text-brand-dark">Proveedores</h1>
            </div>
            <?php if ($esAdmin): ?>
                <button onclick="openModal()" class="flex items-center gap-2 px-4 py-2 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-plus text-xs"></i> Nuevo proveedor
                </button>
            <?php endif; ?>
        </header>

        <main class="flex-1 p-6 fade-in">
            <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
                <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
                <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">Directorio de proveedores</h3>
                        <p class="text-xs text-slate-500 mt-0.5"><?= count($provs) ?> proveedor(es) registrado(s)</p>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none"><i class="fas fa-search text-sm"></i></span>
                        <input type="text" id="search-input" placeholder="Nombre o documento..." oninput="filterTable()"
                            class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 transition w-64">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <?php foreach (['Proveedor', 'Contacto', 'Teléfono', 'Email', 'Documento', ''] as $h): ?>
                                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php if (empty($provs)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                        <i class="fas fa-truck text-4xl mb-3 block opacity-20"></i>
                                        <p class="text-sm">No hay proveedores registrados aún</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($provs as $p): ?>
                                    <tr class="trow" id="prov-<?= $p['id_proveedor'] ?>"
                                        data-search="<?= strtolower("{$p['nombre']} {$p['documento']}") ?>">
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($p['nombre']) ?></p>
                                            <p class="text-xs text-slate-400"><?= htmlspecialchars($p['direccion'] ?? '') ?></p>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= htmlspecialchars($p['contacto']) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= htmlspecialchars($p['telefono']) ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500"><?= htmlspecialchars($p['email'] ?? '—') ?></td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono"><?= htmlspecialchars($p['documento']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-slate-50">
                                            <?php if ($esAdmin): ?>
                                                <button onclick="editarProv(<?= htmlspecialchars(json_encode($p)) ?>)"
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
                    <p class="text-sm font-medium">No se encontraron proveedores</p>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL -->
    <div id="modal-prov" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-center justify-between p-7 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center"><i class="fas fa-truck-loading text-white text-sm"></i></div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-800">Nuevo proveedor</h3>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition"><i class="fas fa-times text-sm"></i></button>
            </div>
            <form id="form-prov" class="p-7 overflow-y-auto space-y-4">
                <input type="hidden" id="prov-id" name="id">
                <input type="hidden" id="form-action" name="action" value="crear">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre *</label>
                        <input type="text" name="nombre" id="prov-nombre" required placeholder="Nombre del proveedor"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Contacto</label>
                        <input type="text" name="contacto" id="prov-contacto" placeholder="Nombre del contacto"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Documento *</label>
                        <input type="text" name="documento" id="prov-doc" required placeholder="NIT o cédula"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Teléfono</label>
                        <input type="text" name="telefono" id="prov-tel" placeholder="Teléfono"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Email</label>
                        <input type="email" name="email" id="prov-email" placeholder="correo@proveedor.com"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Dirección</label>
                        <input type="text" name="direccion" id="prov-dir" placeholder="Dirección"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                    <button type="submit" class="flex-1 py-3 bg-brand-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                        <i class="fas fa-save mr-2 text-xs"></i><span id="btn-text">Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const CTRL = '../../controllers/admin/ProveedorController.php';

        function filterTable() {
            const q = document.getElementById('search-input').value.toLowerCase();
            let v = 0;
            document.querySelectorAll('#table-body tr[id^="prov-"]').forEach(r => {
                const show = !q || r.dataset.search.includes(q);
                r.style.display = show ? '' : 'none';
                if (show) v++;
            });
            document.getElementById('empty-state').classList.toggle('hidden', v > 0);
        }

        function openModal() {
            document.getElementById('form-prov').reset();
            document.getElementById('prov-id').value = '';
            document.getElementById('form-action').value = 'crear';
            document.getElementById('modal-title').textContent = 'Nuevo proveedor';
            document.getElementById('btn-text').textContent = 'Guardar';
            document.getElementById('modal-prov').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-prov').classList.add('hidden');
        }

        function editarProv(p) {
            document.getElementById('prov-id').value = p.id_proveedor;
            document.getElementById('form-action').value = 'editar';
            document.getElementById('prov-nombre').value = p.nombre;
            document.getElementById('prov-contacto').value = p.contacto ?? '';
            document.getElementById('prov-doc').value = p.documento;
            document.getElementById('prov-tel').value = p.telefono ?? '';
            document.getElementById('prov-email').value = p.email ?? '';
            document.getElementById('prov-dir').value = p.direccion ?? '';
            document.getElementById('modal-title').textContent = 'Editar proveedor';
            document.getElementById('btn-text').textContent = 'Actualizar';
            document.getElementById('modal-prov').classList.remove('hidden');
        }

        document.getElementById('form-prov').addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
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
        });

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
            if (e.key === 'Escape') closeModal();
        });
    </script>
</body>

</html>