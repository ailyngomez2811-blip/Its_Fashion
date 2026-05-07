<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}
$usuario  = htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']);
$rol      = (int)$_SESSION['user_rol'] === 1 ? 'Administrador' : 'Empleado';
$iniciales = strtoupper(substr($_SESSION['user_nombre'], 0, 1) . substr($_SESSION['user_apellido'], 0, 1));

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Producto.php';
require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/Caja.php';
require_once __DIR__ . '/../../models/Devolucion.php';

$db      = (new Database())->conectar();
$ventaM  = new Venta($db);
$prodM   = new Producto($db);
$clienteM = new Cliente($db);
$cajaM   = new Caja($db);
$devM    = new Devolucion($db);

$kpiVentas   = $ventaM->totales();
$kpiProductos = $prodM->totales();
$kpiClientes = $clienteM->totales();
$cajaActiva  = $cajaM->cajaActiva();
$devPendientes = $devM->pendientes();
?>
<<!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Its Fashion | Dashboard</title>
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

            .stat-card {
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
                <a href="dashboard.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
                    <i class="fas fa-th-large w-5 text-center"></i> Dashboard
                </a>

                <?php if ($rol === 'Administrador'): ?>
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
                <a href="proveedores.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
                    <i class="fas fa-truck-loading w-5 text-center"></i> Proveedores
                </a>

                <?php if ($rol === 'Administrador'): ?>
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
                        <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($usuario) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($rol) ?></p>
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

            <!-- Topbar -->
            <header class="glass-header sticky top-0 z-40 flex items-center justify-between px-8 h-20">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-serif font-bold text-brand-dark">Dashboard</h1>
                </div>
                <div class="flex items-center gap-5">
                    <span class="text-sm text-brand-muted hidden md:block font-light">
                        <i class="far fa-calendar-alt mr-2 text-brand-accent"></i><?= date('l, d \d\e F \d\e Y') ?>
                    </span>

                    <button onclick="window.location='devoluciones.php'" title="Solicitudes de devolución pendientes"
                        class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-muted hover:text-brand-accent hover:bg-blue-50 transition-colors shadow-sm border border-gray-100 relative group">
                        <i class="fas fa-bell text-lg group-hover:scale-110 transition-transform"></i>
                        <?php if ($devPendientes > 0): ?>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center shadow-sm border-2 border-white"><?= $devPendientes ?></span>
                        <?php endif; ?>
                    </button>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-8 fade-in">

                <div class="mb-8">
                    <h2 class="text-3xl font-serif font-bold text-brand-dark mb-2">Bienvenido, <?= explode(' ', htmlspecialchars($usuario))[0] ?></h2>
                    <p class="text-brand-muted font-light">Aquí tienes un resumen de la actividad de tu boutique hoy.</p>
                </div>

                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <!-- Card 1: Ventas Hoy -->
                    <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 stat-card">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-brand-accent text-xl"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-brand-accent">
                                Hoy
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-brand-muted mb-1">Ventas de Hoy</p>
                            <h3 class="text-3xl font-serif font-bold text-brand-dark">$0</h3>
                            <p class="text-xs mt-2 text-gray-400 font-light"><i class="fas fa-info-circle mr-1"></i>Sin registros aún</p>
                        </div>
                    </div>

                    <!-- Card 2: Productos -->
                    <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 stat-card">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
                                <i class="fas fa-box-open text-indigo-500 text-xl"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-indigo-50 text-indigo-600">
                                Inventario
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-brand-muted mb-1">Productos Activos</p>
                            <h3 class="text-3xl font-serif font-bold text-brand-dark"><?= $kpiProductos['activos'] ?? 0 ?></h3>
                            <p class="text-xs mt-2 <?= ($kpiProductos['criticos'] ?? 0) > 0 ? 'text-red-500' : 'text-gray-400' ?> font-light">
                                <i class="fas fa-exclamation-triangle mr-1"></i><?= $kpiProductos['criticos'] ?? 0 ?> en stock crítico
                            </p>
                        </div>
                    </div>

                    <!-- Card 3: Ingresos -->
                    <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 stat-card relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 z-0"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                                    <i class="fas fa-chart-line text-emerald-500 text-xl"></i>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-600">
                                    Este Mes
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-brand-muted mb-1">Ingresos del Mes</p>
                                <h3 class="text-3xl font-serif font-bold text-brand-dark">$<?= number_format($kpiVentas['monto'] ?? 0, 0, ',', '.') ?></h3>
                                <p class="text-xs mt-2 text-emerald-600 font-light">
                                    <i class="fas fa-check-circle mr-1"></i><?= $kpiVentas['completadas'] ?? 0 ?> ventas completadas
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Clientes -->
                    <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 stat-card">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center">
                                <i class="fas fa-user-friends text-purple-500 text-xl"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-purple-50 text-purple-600">
                                Comunidad
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-brand-muted mb-1">Clientes Registrados</p>
                            <h3 class="text-3xl font-serif font-bold text-brand-dark"><?= $kpiClientes['total'] ?? 0 ?></h3>
                            <p class="text-xs mt-2 text-gray-400 font-light">
                                <i class="fas fa-users mr-1"></i><?= $kpiClientes['activos'] ?? 0 ?> activos
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Alertas (Devoluciones) -->
                <?php if ($devPendientes > 0): ?>
                    <a href="devoluciones.php" class="flex items-center gap-5 mb-8 p-5 bg-amber-50 border border-amber-200 rounded-[1.5rem] hover:bg-amber-100 transition-colors shadow-sm group">
                        <div class="w-12 h-12 bg-amber-400 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fas fa-undo-alt text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-base font-semibold text-amber-800">
                                Atención requerida: <?= $devPendientes ?> solicitud<?= $devPendientes > 1 ? 'es' : '' ?> de devolución pendiente<?= $devPendientes > 1 ? 's' : '' ?>
                            </p>
                            <p class="text-sm text-amber-700 font-light">Haz clic aquí para revisar y gestionar las solicitudes de tus clientes.</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-amber-200/50 flex items-center justify-center text-amber-600 group-hover:bg-amber-300 transition-colors">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                <?php endif; ?>

                <!-- Empty state (Opcional, si no hay datos en absoluto) -->
                <?php if (($kpiVentas['completadas'] ?? 0) === 0 && ($kpiProductos['activos'] ?? 0) === 0 && ($kpiClientes['total'] ?? 0) === 0): ?>
                    <div class="bg-white rounded-[2rem] border border-gray-100 p-16 text-center shadow-sm">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-gray-100">
                            <i class="fas fa-store-slash text-gray-300 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-brand-dark mb-2">Tu boutique está lista</h3>
                        <p class="text-brand-muted font-light max-w-md mx-auto">
                            Aún no hay datos registrados. Comienza agregando productos a tu inventario y registrando tus primeras ventas para ver las estadísticas aquí.
                        </p>
                        <div class="mt-8 flex justify-center gap-4">
                            <a href="productos.php" class="px-6 py-2.5 bg-brand-accent text-white font-medium rounded-full hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/20">
                                <i class="fas fa-plus mr-2"></i>Añadir Producto
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </main>
        </div>

    </body>

    </html>