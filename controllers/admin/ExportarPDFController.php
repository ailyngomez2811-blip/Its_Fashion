<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../dompdf/autoload.inc.php';

// Establecer la zona horaria correcta (GMT-5)
date_default_timezone_set('America/Bogota');

use Dompdf\Dompdf;
use Dompdf\Options;

$db = (new Database())->conectar();

// Obtener parámetros
$incluirVentas = isset($_POST['Ventas']) || isset($_GET['Ventas']);
$incluirInventario = isset($_POST['Inventario']) || isset($_GET['Inventario']);
$incluirProductos = isset($_POST['Productos_más_vendidos']) || isset($_GET['Productos_más_vendidos']);
$incluirDevoluciones = isset($_POST['Devoluciones']) || isset($_GET['Devoluciones']);

// Si no hay nada seleccionado, por defecto mostramos todo
if (!$incluirVentas && !$incluirInventario && !$incluirProductos && !$incluirDevoluciones) {
    $incluirVentas = true;
    $incluirInventario = true;
    $incluirProductos = true;
    $incluirDevoluciones = true;
}

$logoPath = __DIR__ . '/../../img/logo en nombre.png';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,' . $logoData;
} else {
    $logoSrc = '';
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Its Fashion</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,600;0,700&display=swap');
        
        body {
            font-family: 'Outfit', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
            border: none;
            margin: 0;
        }
        .header td {
            border: none;
            padding: 0;
        }
        .logo {
            max-width: 90px;
            height: auto;
        }
        .report-info {
            text-align: right;
        }
        .report-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 5px 0;
        }
        .report-date {
            font-size: 13px;
            color: #64748b;
            margin: 0;
            font-weight: 400;
        }
        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: #2563eb;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            font-weight: 600;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 13px;
        }
        .data-table th, .data-table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .data-table td {
            color: #334155;
            font-weight: 400;
        }
        .badge {
            font-weight: 600;
            color: #dc2626;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table style="border: none; margin: 0; width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <?php if($logoSrc): ?>
                            <img src="<?= $logoSrc ?>" class="logo" alt="Its Fashion Logo">
                        <?php else: ?>
                            <h1 style="color: #2563eb; margin: 0;">Its Fashion</h1>
                        <?php endif; ?>
                    </td>
                    <td style="width: 50%;" class="report-info">
                        <h1 class="report-title">Reporte Administrativo</h1>
                        <p class="report-date">Generado el <?= date('d/m/Y H:i') ?></p>
                        <p class="report-date">Por: <?= htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']) ?></p>
                    </td>
                </tr>
            </table>
        </div>

    <?php if ($incluirVentas): ?>
        <h2>Ventas Últimos 7 Días</h2>
        <?php
        $stmt = $db->prepare("SELECT DATE(fecha) AS dia, COALESCE(SUM(total),0) AS total FROM venta WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND estado='Completada' GROUP BY DATE(fecha) ORDER BY dia ASC");
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td><?= $v['dia'] ?></td>
                        <td>$<?= number_format($v['total'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($incluirInventario): ?>
        <h2>Inventario Crítico</h2>
        <?php
        $stmt7 = $db->prepare("SELECT p.nombre, p.talla, p.color, p.stock, p.stock_minimo, c.nombre AS categoria FROM productos p LEFT JOIN categoria c ON c.id_categoria = p.id_categoria WHERE p.stock <= p.stock_minimo AND p.estado='Activo' ORDER BY p.stock ASC LIMIT 10");
        $stmt7->execute();
        $inv = $stmt7->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Stock</th>
                    <th>Minimo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inv as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nombre']) ?></td>
                        <td><?= htmlspecialchars($r['categoria'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['talla']) ?></td>
                        <td><?= htmlspecialchars($r['color']) ?></td>
                        <td><strong><?= $r['stock'] ?></strong></td>
                        <td><?= $r['stock_minimo'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($incluirProductos): ?>
        <h2>Top 5 Productos Más Vendidos</h2>
        <?php
        $stmt5 = $db->prepare("SELECT p.nombre, p.talla, p.color, SUM(dv.cantidad) AS vendidos, SUM(dv.cantidad * dv.precio_unitario) AS ingresos FROM detalle_venta dv JOIN productos p ON p.id_producto = dv.id_producto JOIN venta v ON v.id_venta = dv.id_venta WHERE v.estado='Completada' GROUP BY dv.id_producto ORDER BY vendidos DESC LIMIT 5");
        $stmt5->execute();
        $top_productos = $stmt5->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla/Color</th>
                    <th>Vendidos</th>
                    <th>Ingresos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($top_productos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['talla']) ?> / <?= htmlspecialchars($p['color']) ?></td>
                        <td><?= $p['vendidos'] ?> uds</td>
                        <td>$<?= number_format($p['ingresos'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($incluirDevoluciones): ?>
        <h2>Últimas Devoluciones</h2>
        <?php
        $stmt6 = $db->prepare("SELECT d.id_devolucion, d.id_venta, d.fecha, d.motivo, d.total_devolucion, CONCAT(u.nombre,' ',u.apellido) AS cliente FROM devoluciones d JOIN venta v ON v.id_venta = d.id_venta LEFT JOIN usuario u ON u.id_usuario = v.id_cliente ORDER BY d.fecha DESC LIMIT 8");
        $stmt6->execute();
        $devols = $stmt6->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Motivo</th>
                    <th>Total Devuelto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devols as $d): ?>
                    <tr>
                        <td>#<?= $d['id_devolucion'] ?></td>
                        <td><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                        <td><?= htmlspecialchars($d['cliente'] ?? 'Mostrador') ?></td>
                        <td><?= htmlspecialchars($d['motivo']) ?></td>
                        <td style="color:red;">-$<?= number_format($d['total_devolucion'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

        <div class="footer">
            <p>Its Fashion &copy; <?= date('Y') ?> - Todos los derechos reservados</p>
        </div>
    </div> <!-- end container -->
</body>

</html>
<?php
$html = ob_get_clean();

// Configurar dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

// Renderizar PDF
$dompdf->render();

// Enviar al navegador
$dompdf->stream("Reporte_Its_Fashion_" . date('Ymd_Hi') . ".pdf", array("Attachment" => true));
exit();
