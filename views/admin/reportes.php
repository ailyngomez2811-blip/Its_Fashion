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
$db = (new Database())->conectar();

// ── Ventas últimos 7 días ────────────────────────────────────────────────────
$stmt = $db->prepare(
  "SELECT DATE(fecha) AS dia, COALESCE(SUM(total),0) AS total
     FROM venta WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     AND estado='Completada' GROUP BY DATE(fecha) ORDER BY dia ASC"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$mapa_dias = [];
foreach ($rows as $r) $mapa_dias[$r['dia']] = (float)$r['total'];
$ventas_semana = [];
$dias = [];
for ($i = 6; $i >= 0; $i--) {
  $fecha = date('Y-m-d', strtotime("-$i days"));
  $ventas_semana[] = $mapa_dias[$fecha] ?? 0;
  $dias[] = date('D', strtotime($fecha));
}

// ── Ventas últimos 6 meses ───────────────────────────────────────────────────
$stmt2 = $db->prepare(
  "SELECT DATE_FORMAT(fecha,'%Y-%m') AS mes, COALESCE(SUM(total),0) AS total
     FROM venta WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
     AND estado='Completada' GROUP BY mes ORDER BY mes ASC"
);
$stmt2->execute();
$rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$mapa_meses = [];
foreach ($rows2 as $r) $mapa_meses[$r['mes']] = (float)$r['total'];
$ventas_mes = [];
$meses = [];
for ($i = 5; $i >= 0; $i--) {
  $key = date('Y-m', strtotime("-$i months"));
  $ventas_mes[] = $mapa_meses[$key] ?? 0;
  $meses[] = date('M', strtotime("-$i months"));
}

// ── KPIs generales ───────────────────────────────────────────────────────────
$stmt3 = $db->prepare(
  "SELECT COALESCE(SUM(total),0) AS total_mes,
            COUNT(*) AS transacciones,
            COALESCE(AVG(total),0) AS ticket_promedio,
            COALESCE(SUM(CASE WHEN metodo_pago='Efectivo' THEN total ELSE 0 END),0) AS efectivo
     FROM venta WHERE MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())
     AND estado='Completada'"
);
$stmt3->execute();
$kpi = $stmt3->fetch(PDO::FETCH_ASSOC);
$total_ventas_mes = $kpi['total_mes'];
$total_transacc   = $kpi['transacciones'];
$ticket_promedio  = $kpi['ticket_promedio'];
$ventas_efectivo  = $kpi['efectivo'];

// ── Clientes activos ─────────────────────────────────────────────────────────
$stmt4 = $db->prepare("SELECT COUNT(*) FROM usuario WHERE id_rol=3 AND estado='Activo'");
$stmt4->execute();
$total_clientes = $stmt4->fetchColumn();

// ── Top 5 productos más vendidos ─────────────────────────────────────────────
$stmt5 = $db->prepare(
  "SELECT p.nombre, p.talla, p.color,
            SUM(dv.cantidad) AS vendidos,
            SUM(dv.cantidad * dv.precio_unitario) AS ingresos
     FROM detalle_venta dv
     JOIN productos p ON p.id_producto = dv.id_producto
     JOIN venta v ON v.id_venta = dv.id_venta
     WHERE v.estado='Completada'
     GROUP BY dv.id_producto ORDER BY vendidos DESC LIMIT 5"
);
$stmt5->execute();
$top_productos = $stmt5->fetchAll(PDO::FETCH_ASSOC);

// ── Últimas devoluciones ─────────────────────────────────────────────────────
$stmt6 = $db->prepare(
  "SELECT d.id_devolucion, d.id_venta, d.fecha, d.motivo, d.total_devolucion,
            CONCAT(u.nombre,' ',u.apellido) AS cliente
     FROM devoluciones d
     JOIN venta v ON v.id_venta = d.id_venta
     LEFT JOIN usuario u ON u.id_usuario = v.id_cliente
     ORDER BY d.fecha DESC LIMIT 8"
);
$stmt6->execute();
$devols = $stmt6->fetchAll(PDO::FETCH_ASSOC);

// ── Inventario crítico ───────────────────────────────────────────────────────
$stmt7 = $db->prepare(
  "SELECT p.nombre, p.talla, p.color, p.stock, p.stock_minimo,
            c.nombre AS categoria
     FROM productos p
     LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
     WHERE p.stock <= p.stock_minimo AND p.estado='Activo'
     ORDER BY p.stock ASC LIMIT 10"
);
$stmt7->execute();
$inv = $stmt7->fetchAll(PDO::FETCH_ASSOC);

// ── Compras del mes ──────────────────────────────────────────────────────────
$stmt8 = $db->prepare(
  "SELECT COALESCE(SUM(total),0) AS total_compras, COUNT(*) AS num_compras
     FROM compras WHERE MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())"
);
$stmt8->execute();
$kpi_compras = $stmt8->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Its Fashion | Reportes</title>
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

    .tab-btn {
      padding: 8px 20px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all .2s;
      color: #64748b;
      border: none;
      background: transparent;
    }

    .tab-btn.active {
      background: #2563eb;
      color: white;
      box-shadow: 0 4px 12px rgba(59, 130, 246, .3);
    }

    .tab-btn:not(.active):hover {
      background: #f1f5f9;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .chart-bar {
      border-radius: 6px 6px 0 0;
      transition: height .5s cubic-bezier(.4, 0, .2, 1), opacity .3s;
    }

    .chart-bar:hover {
      opacity: .8;
      cursor: pointer;
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
      max-width: 500px;
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
      width: 5px;
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
      <a href="proveedores.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white">
        <i class="fas fa-truck-loading w-5 text-center"></i> Proveedores
      </a>

      <?php if (isset($_SESSION['user_rol']) && (int)$_SESSION['user_rol'] === 1): ?>
        <a href="caja.php" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:text-white mt-1">
          <i class="fas fa-cash-register w-5 text-center"></i> Caja
        </a>
        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider px-4 mt-6 mb-2">Análisis</p>
        <a href="reportes.php" class="sidebar-item active flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white">
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

  <!-- MAIN -->
  <div class="ml-64 flex flex-col min-h-screen">
    <header class="glass-header sticky top-0 z-40 flex items-center justify-between px-8 h-20">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
          <i class="fas fa-chart-line text-lg"></i>
        </div>
        <h1 class="text-2xl font-serif font-bold text-brand-dark">Reportes</h1>
      </div>
      <div class="flex items-center gap-3">
        <button onclick="openExportar()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-200 text-brand-dark text-sm font-semibold rounded-xl hover:border-blue-300 hover:text-brand-accent transition shadow-sm">
          <i class="fas fa-download text-xs"></i>Exportar
        </button>
      </div>
    </header>

    <main class="flex-1 p-6 fade-in">
      <!-- Toast -->
      <div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl bg-white max-w-xs" style="border-left:4px solid #3b82f6;">
        <i id="toast-icon" class="fas fa-check-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
        <span id="toast-text" class="text-slate-700 text-sm font-medium flex-1"></span>
      </div>

      <!-- Filtro de período -->
      <div class="flex items-center gap-3 mb-6 flex-wrap">
        <div class="flex gap-1 p-1 bg-white rounded-2xl border border-slate-100 shadow-sm">
          <?php foreach (['Esta semana', 'Este mes', 'Últimos 3 meses', 'Este año'] as $p): ?>
            <button onclick="setPeriodo(this)" class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $p === 'Este mes' ? 'bg-brand-accent text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' ?>">
              <?= $p ?>
            </button>
          <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-2 ml-auto">
          <input type="date" class="px-3 py-2 bg-white border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
          <span class="text-slate-400 text-sm">→</span>
          <input type="date" class="px-3 py-2 bg-white border-2 border-slate-200 rounded-xl text-sm text-slate-700 transition">
          <button onclick="showToast('Reporte actualizado.')" class="px-4 py-2 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg transition">Aplicar</button>
        </div>
      </div>

      <!-- KPIs -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <?php $kpis = [
          ['Ingresos del mes',  '$' . number_format($total_ventas_mes, 0, ',', '.'), 'fa-dollar-sign', 'bg-blue-500',   'border-blue-100',   'Sin datos aún'],
          ['Transacciones',     $total_transacc,                                'fa-shopping-bag', 'bg-emerald-500', 'border-emerald-100', 'Sin datos aún'],
          ['Ticket promedio',   '$' . number_format($ticket_promedio, 0, ',', '.'),  'fa-receipt',     'bg-amber-500',  'border-amber-100',  'por venta'],
          ['Clientes activos',  $total_clientes,                                'fa-users',       'bg-purple-500', 'border-purple-100', 'con compras este mes'],
        ];
        foreach ($kpis as $k): ?>
          <div class="bg-white rounded-2xl p-5 border <?= $k[4] ?> stat-card">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 <?= $k[3] ?> rounded-xl flex items-center justify-center">
                <i class="fas <?= $k[2] ?> text-white text-sm"></i>
              </div>
              <p class="text-xs text-slate-500"><?= $k[0] ?></p>
            </div>
            <p class="text-2xl font-bold text-slate-800"><?= $k[1] ?></p>
            <p class="text-xs text-slate-400 mt-1"><?= $k[5] ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Tabs -->
      <div class="flex gap-2 mb-5 p-1 bg-slate-100 rounded-2xl w-fit">
        <?php foreach (['ventas' => 'Ventas', 'inventario' => 'Inventario', 'productos' => 'Productos', 'devoluciones' => 'Devoluciones'] as $k => $v): ?>
          <button class="tab-btn <?= $k === 'ventas' ? 'active' : '' ?>" onclick="switchTab('<?= $k ?>',this)"><?= $v ?></button>
        <?php endforeach; ?>
      </div>

      <!-- TAB: VENTAS -->
      <div id="tab-ventas" class="tab-content active">
        <?php $max_v = !empty($ventas_semana) ? max($ventas_semana) : 1;
        $max_m = !empty($ventas_mes) ? max($ventas_mes) : 1;
        $div_v = $max_v >= 1000000 ? 1000000 : ($max_v >= 1000 ? 1000 : 1);
        $suf_v = $max_v >= 1000000 ? 'M' : ($max_v >= 1000 ? 'k' : '');
        $div_m = $max_m >= 1000000 ? 1000000 : ($max_m >= 1000 ? 1000 : 1);
        $suf_m = $max_m >= 1000000 ? 'M' : ($max_m >= 1000 ? 'k' : ''); ?>
        <div class="grid lg:grid-cols-3 gap-5">
          <!-- Gráfica semanal -->
          <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h3 class="font-bold text-slate-800">Ventas de la semana</h3>
                <p class="text-xs text-slate-500 mt-0.5"><?= date('d/m/Y') ?></p>
              </div>
              <span class="font-bold text-blue-600">$<?= number_format(array_sum($ventas_semana), 0, ',', '.') ?></span>
            </div>
            <?php if (array_sum($ventas_semana) === 0): ?>
              <div class="h-44 flex items-center justify-center text-slate-400">
                <div class="text-center"><i class="fas fa-chart-bar text-3xl mb-2 block opacity-20"></i>
                  <p class="text-sm">Sin datos de ventas</p>
                </div>
              </div>
            <?php else: ?>
              <div class="flex items-end gap-3 h-44">
                <?php foreach ($ventas_semana as $i => $v): $h = round(($v / $max_v) * 100); ?>
                  <div class="flex-1 flex flex-col justify-end items-center gap-1 h-full">
                    <span class="text-xs text-slate-500 font-semibold">$<?= number_format($v / $div_v, $div_v == 1000000 ? 1 : 0) ?><?= $suf_v ?></span>
                    <div class="chart-bar w-full bg-brand-accent rounded-t-sm" style="height:<?= $h ?>%;min-height:8px;" title="$<?= number_format($v, 0, ',', '.') ?>"></div>
                    <span class="text-xs text-slate-500"><?= $dias[$i] ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Métodos de pago -->
          <div class="bg-white rounded-2xl border border-slate-100 p-6" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <h3 class="font-bold text-slate-800 mb-4">Métodos de pago</h3>
            <?php if ($total_transacc === 0): ?>
              <div class="flex items-center justify-center h-32 text-slate-400">
                <div class="text-center"><i class="fas fa-chart-pie text-3xl mb-2 block opacity-20"></i>
                  <p class="text-sm">Sin datos</p>
                </div>
              </div>
            <?php else:
              $porc_efectivo = $total_ventas_mes > 0 ? round(($ventas_efectivo / $total_ventas_mes) * 100) : 0;
              $porc_trans = 100 - $porc_efectivo;
            ?>
              <div class="flex items-center justify-center mb-5">
                <div class="relative w-32 h-32">
                  <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3.8" />
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#3b82f6" stroke-width="3.8"
                      stroke-dasharray="<?= $porc_efectivo ?> <?= $porc_trans ?>" stroke-linecap="round" />
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#0ea5e9" stroke-width="3.8"
                      stroke-dasharray="<?= $porc_trans ?> <?= $porc_efectivo ?>"
                      stroke-dashoffset="-<?= $porc_efectivo ?>" stroke-linecap="round" />
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold text-slate-800"><?= $porc_efectivo ?>%</span>
                  </div>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex items-center justify-between p-2.5 bg-blue-50 rounded-xl">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div><span class="text-sm font-medium text-slate-700">Efectivo</span>
                  </div>
                  <span class="font-bold text-blue-600"><?= $porc_efectivo ?>%</span>
                </div>
                <div class="flex items-center justify-between p-2.5 bg-sky-50 rounded-xl">
                  <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-sky-400 rounded-full"></div><span class="text-sm font-medium text-slate-700">Transferencia</span>
                  </div>
                  <span class="font-bold text-sky-500"><?= $porc_trans ?>%</span>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <!-- Tendencia mensual -->
          <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-6" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <h3 class="font-bold text-slate-800 mb-5">Tendencia de ventas — últimos 6 meses</h3>
            <?php if (array_sum($ventas_mes) === 0): ?>
              <div class="h-32 flex items-center justify-center text-slate-400">
                <div class="text-center"><i class="fas fa-chart-line text-3xl mb-2 block opacity-20"></i>
                  <p class="text-sm">Sin datos de ventas</p>
                </div>
              </div>
            <?php else: ?>
              <div class="flex items-end gap-4 h-32">
                <?php foreach ($ventas_mes as $i => $v): $h = round(($v / $max_m) * 100); ?>
                  <div class="flex-1 flex flex-col justify-end items-center gap-1 h-full">
                    <span class="text-xs text-slate-400 font-semibold">$<?= number_format($v / $div_m, $div_m == 1000000 ? 1 : 0) ?><?= $suf_m ?></span>
                    <div class="chart-bar w-full rounded-t-sm" style="height:<?= $h ?>%;min-height:6px;background:<?= $i === count($ventas_mes) - 1 ? 'linear-gradient(135deg,#3b82f6,#0ea5e9)' : '#bfdbfe' ?>"></div>
                    <span class="text-xs text-slate-500 font-medium"><?= $meses[$i] ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- TAB: INVENTARIO -->
      <div id="tab-inventario" class="tab-content">
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
          <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Reporte de inventario actual</h3>
            <p class="text-xs text-slate-500 mt-0.5">Generado el <?= date('d/m/Y H:i') ?></p>
          </div>
          <?php if (empty($inv)): ?>
            <div class="py-16 text-center text-slate-400">
              <i class="fas fa-warehouse text-3xl mb-3 block opacity-20"></i>
              <p class="text-sm">Sin datos de inventario</p>
            </div>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr>
                    <?php foreach (['Producto', 'Categoría', 'Talla', 'Color', 'Stock', 'Mínimo', 'Estado'] as $h): ?>
                      <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($inv as $r):
                    if ($r['stock'] == 0)                          $est = 'Sin stock';
                    elseif ($r['stock'] <= $r['stock_minimo'])     $est = 'Crítico';
                    else                                           $est = 'Disponible';
                    $bc = $est === 'Disponible' ? 'bg-emerald-100 text-emerald-700' : ($est === 'Crítico' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600');
                  ?>
                    <tr class="trow">
                      <td class="px-6 py-4 border-b border-slate-50 text-sm font-semibold text-slate-800"><?= htmlspecialchars($r['nombre']) ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600"><?= htmlspecialchars($r['categoria'] ?? '—') ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600"><?= htmlspecialchars($r['talla']) ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600"><?= htmlspecialchars($r['color']) ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800"><?= $r['stock'] ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500"><?= $r['stock_minimo'] ?></td>
                      <td class="px-6 py-4 border-b border-slate-50"><span class="badge <?= $bc ?>"><?= $est ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- TAB: PRODUCTOS MÁS VENDIDOS -->
      <div id="tab-productos" class="tab-content">
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
          <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Productos más vendidos</h3>
            <p class="text-xs text-slate-500 mt-0.5">Ranking del período seleccionado</p>
          </div>
          <?php if (empty($top_productos)): ?>
            <div class="py-16 text-center text-slate-400">
              <i class="fas fa-box-open text-3xl mb-3 block opacity-20"></i>
              <p class="text-sm">Sin datos de ventas</p>
            </div>
          <?php else: ?>
            <?php $max_u = max(array_column($top_productos, 'vendidos')) ?: 1; ?>
            <div class="p-6 space-y-4">
              <?php $bgs = ['bg-brand-accent', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-pink-500'];
              foreach ($top_productos as $i => $p):
                $pct_bar = round(($p['vendidos'] / $max_u) * 100); ?>
                <div class="flex items-center gap-4">
                  <span class="w-6 text-center text-sm font-bold text-slate-400"><?= $i + 1 ?></span>
                  <div class="flex-1">
                    <div class="flex items-center justify-between mb-1.5">
                      <div>
                        <span class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($p['nombre']) ?></span>
                        <span class="text-xs text-slate-400 ml-1"><?= $p['talla'] ?>/<?= $p['color'] ?></span>
                      </div>
                      <div class="text-right">
                        <span class="text-sm font-bold text-slate-800"><?= $p['vendidos'] ?> uds</span>
                        <span class="text-xs text-slate-500 block">$<?= number_format($p['ingresos'], 0, ',', '.') ?></span>
                      </div>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div class="h-full rounded-full <?= $bgs[$i] ?>" style="width:<?= $pct_bar ?>%;transition:width .6s;"></div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- TAB: DEVOLUCIONES -->
      <div id="tab-devoluciones" class="tab-content">
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
          <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Reporte de devoluciones</h3>
            <p class="text-xs text-slate-500 mt-0.5">Devoluciones registradas en el período</p>
          </div>
          <?php if (empty($devols)): ?>
            <div class="py-16 text-center text-slate-400">
              <i class="fas fa-undo text-3xl mb-3 block opacity-20"></i>
              <p class="text-sm">Sin devoluciones registradas</p>
            </div>
          <?php else: ?>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr>
                    <?php foreach (['ID', 'Fecha', 'Venta', 'Cliente', 'Motivo', 'Total devuelto'] as $h): ?>
                      <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100"><?= $h ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($devols as $d): ?>
                    <tr class="trow">
                      <td class="px-6 py-4 border-b border-slate-50 font-mono text-xs text-amber-600">#<?= $d['id_devolucion'] ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 font-mono text-xs text-slate-500">#<?= $d['id_venta'] ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700"><?= htmlspecialchars($d['cliente'] ?? 'Mostrador') ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600 italic"><?= htmlspecialchars($d['motivo']) ?></td>
                      <td class="px-6 py-4 border-b border-slate-50 font-bold text-red-600">-$<?= number_format($d['total_devolucion'], 0, ',', '.') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" class="px-6 py-3 text-right text-sm font-semibold text-slate-600 bg-slate-50">Total devuelto:</td>
                    <td class="px-6 py-3 font-bold text-red-600 bg-slate-50">-$<?= number_format(array_sum(array_column($devols, 'total_devolucion')), 0, ',', '.') ?></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </main>
  </div>

  <!-- MODAL EXPORTAR -->
  <div id="modal-exp" class="modal-overlay hidden">
    <div class="modal-box scale-up">
      <div class="flex items-start justify-between p-7 pb-0">
        <div>
          <h3 class="text-xl font-bold text-slate-800">Exportar reporte</h3>
          <p class="text-sm text-slate-500 mt-1">Selecciona el formato y el contenido</p>
        </div>
        <button onclick="closeExp()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition flex-shrink-0 ml-4">
          <i class="fas fa-times text-sm"></i>
        </button>
      </div>
      <div class="p-7 overflow-y-auto">
        <div class="mb-5">
          <p class="text-sm font-semibold text-slate-700 mb-3">Tipo de reporte</p>
          <div class="grid grid-cols-2 gap-2">
            <?php foreach (['Ventas', 'Inventario', 'Productos más vendidos', 'Devoluciones'] as $r): ?>
              <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 hover:border-blue-300 cursor-pointer transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                <input type="checkbox" class="accent-blue-600">
                <span class="text-sm font-medium text-slate-700"><?= $r ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="mb-5">
          <p class="text-sm font-semibold text-slate-700 mb-3">Formato de exportación</p>
          <div class="grid grid-cols-2 gap-2">
            <button onclick="exportar('PDF')" class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 border-red-200 bg-red-50 text-red-600 hover:bg-red-100 font-semibold text-sm transition">
              <i class="fas fa-file-pdf"></i>PDF
            </button>
            <button onclick="exportar('Excel')" class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-semibold text-sm transition">
              <i class="fas fa-file-excel"></i>Excel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function switchTab(tab, btn) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');
    }

    function setPeriodo(btn) {
      btn.closest('div').querySelectorAll('button').forEach(b => {
        b.className = b.className.replace('bg-brand-accent text-white shadow-sm', 'text-slate-500 hover:bg-slate-50');
      });
      btn.className = btn.className.replace('text-slate-500 hover:bg-slate-50', 'bg-brand-accent text-white shadow-sm');
      showToast('Período actualizado: ' + btn.textContent.trim());
    }

    function openExportar() {
      document.getElementById('modal-exp').classList.remove('hidden');
    }

    function closeExp() {
      document.getElementById('modal-exp').classList.add('hidden');
    }
    document.getElementById('modal-exp').addEventListener('click', e => {
      if (e.target === document.getElementById('modal-exp')) closeExp();
    });

    function exportar(fmt) {
      showToast(`Reporte exportado en formato ${fmt}.`);
      closeExp();
    }

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