<?php
session_start();
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_rol'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../config/database.php';
date_default_timezone_set('America/Bogota');

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

// Configurar cabeceras para forzar la descarga como Excel (.xls)
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Its_Fashion_" . date('Ymd_Hi') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Imprimir BOM para que Excel lea correctamente los tildes y ñ (UTF-8)
echo "\xEF\xBB\xBF";
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; font-family: 'Outfit', 'Segoe UI', Arial, sans-serif; font-size: 12px; }
        .main-title { font-size: 24px; font-weight: bold; color: #0f172a; height: 60px; vertical-align: middle; border-bottom: 3px solid #2563eb; }
        .info-text { font-size: 12px; color: #64748b; height: 30px; vertical-align: top; }
        .section-title { font-size: 16px; font-weight: bold; color: #2563eb; height: 50px; vertical-align: bottom; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
        .th-header { background-color: #f8fafc; color: #64748b; font-weight: bold; border-bottom: 1px solid #cbd5e1; height: 40px; text-align: left; padding-left: 10px; text-transform: uppercase; font-size: 11px; }
        .td-data { border-bottom: 1px solid #f1f5f9; height: 35px; color: #334155; padding-left: 10px; vertical-align: middle; }
        .td-bold { font-weight: bold; color: #0f172a; }
        .td-red { color: #dc2626; font-weight: bold; }
    </style>
    <!--[if gte mso 9]>
    <xml>
     <x:ExcelWorkbook>
      <x:ExcelWorksheets>
       <x:ExcelWorksheet>
        <x:Name>Reporte Its Fashion</x:Name>
        <x:WorksheetOptions>
         <x:FitToPage/>
         <x:Print>
          <x:FitWidth>1</x:FitWidth>
          <x:FitHeight>99</x:FitHeight>
          <x:ValidPrinterInfo/>
          <x:PaperSizeIndex>9</x:PaperSizeIndex> <!-- A4 -->
          <x:HorizontalResolution>600</x:HorizontalResolution>
          <x:VerticalResolution>600</x:VerticalResolution>
         </x:Print>
         <x:Selected/>
         <x:DoNotDisplayGridlines/>
        </x:WorksheetOptions>
       </x:ExcelWorksheet>
      </x:ExcelWorksheets>
     </x:ExcelWorkbook>
    </xml>
    <![endif]-->
</head>
<body style="background-color: #ffffff;">
    <table width="800" style="table-layout: fixed;">
        <col width="150"><col width="150"><col width="150"><col width="150"><col width="100"><col width="100">
        <tr>
            <td colspan="6" class="main-title">Its Fashion - Reporte Administrativo</td>
        </tr>
        <tr>
            <td colspan="6" class="info-text">Generado el: <?= date('d/m/Y H:i') ?> | Por: <?= htmlspecialchars($_SESSION['user_nombre'] . ' ' . $_SESSION['user_apellido']) ?></td>
        </tr>
        <tr><td colspan="6" style="height: 20px;"></td></tr>
    </table>

    <?php if ($incluirVentas): ?>
        <?php
        $stmt = $db->prepare("SELECT DATE(fecha) AS dia, COALESCE(SUM(total),0) AS total FROM venta WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND estado='Completada' GROUP BY DATE(fecha) ORDER BY dia ASC");
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table width="800" style="table-layout: fixed;">
            <col width="150"><col width="650">
            <tr><td colspan="2" class="section-title">Ventas Últimos 7 Días</td></tr>
            <tr>
                <td class="th-header">Fecha</td>
                <td class="th-header">Total Vendido</td>
            </tr>
            <?php foreach ($ventas as $v): ?>
                <tr>
                    <td class="td-data"><?= $v['dia'] ?></td>
                    <td class="td-data td-bold">$<?= number_format($v['total'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <table><tr><td style="height: 30px;"></td></tr></table>
    <?php endif; ?>

    <?php if ($incluirInventario): ?>
        <?php
        $stmt7 = $db->prepare("SELECT p.nombre, p.talla, p.color, p.stock, p.stock_minimo, c.nombre AS categoria FROM productos p LEFT JOIN categoria c ON c.id_categoria = p.id_categoria WHERE p.stock <= p.stock_minimo AND p.estado='Activo' ORDER BY p.stock ASC LIMIT 10");
        $stmt7->execute();
        $inv = $stmt7->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table width="800" style="table-layout: fixed;">
            <col width="200"><col width="150"><col width="100"><col width="100"><col width="125"><col width="125">
            <tr><td colspan="6" class="section-title">Inventario Crítico</td></tr>
            <tr>
                <td class="th-header">Producto</td>
                <td class="th-header">Categoría</td>
                <td class="th-header">Talla</td>
                <td class="th-header">Color</td>
                <td class="th-header">Stock</td>
                <td class="th-header">Minimo</td>
            </tr>
            <?php foreach ($inv as $r): ?>
                <tr>
                    <td class="td-data td-bold"><?= htmlspecialchars($r['nombre']) ?></td>
                    <td class="td-data"><?= htmlspecialchars($r['categoria'] ?? '—') ?></td>
                    <td class="td-data"><?= htmlspecialchars($r['talla']) ?></td>
                    <td class="td-data"><?= htmlspecialchars($r['color']) ?></td>
                    <td class="td-data td-red"><?= $r['stock'] ?></td>
                    <td class="td-data"><?= $r['stock_minimo'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <table><tr><td style="height: 30px;"></td></tr></table>
    <?php endif; ?>

    <?php if ($incluirProductos): ?>
        <?php
        $stmt5 = $db->prepare("SELECT p.nombre, p.talla, p.color, SUM(dv.cantidad) AS vendidos, SUM(dv.cantidad * dv.precio_unitario) AS ingresos FROM detalle_venta dv JOIN productos p ON p.id_producto = dv.id_producto JOIN venta v ON v.id_venta = dv.id_venta WHERE v.estado='Completada' GROUP BY dv.id_producto ORDER BY vendidos DESC LIMIT 5");
        $stmt5->execute();
        $top_productos = $stmt5->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table width="800" style="table-layout: fixed;">
            <col width="300"><col width="200"><col width="150"><col width="150">
            <tr><td colspan="4" class="section-title">Top 5 Productos Más Vendidos</td></tr>
            <tr>
                <td class="th-header">Producto</td>
                <td class="th-header">Talla/Color</td>
                <td class="th-header">Vendidos</td>
                <td class="th-header">Ingresos</td>
            </tr>
            <?php foreach ($top_productos as $p): ?>
                <tr>
                    <td class="td-data td-bold"><?= htmlspecialchars($p['nombre']) ?></td>
                    <td class="td-data"><?= htmlspecialchars($p['talla']) ?> / <?= htmlspecialchars($p['color']) ?></td>
                    <td class="td-data"><?= $p['vendidos'] ?> uds</td>
                    <td class="td-data td-bold">$<?= number_format($p['ingresos'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <table><tr><td style="height: 30px;"></td></tr></table>
    <?php endif; ?>

    <?php if ($incluirDevoluciones): ?>
        <?php
        $stmt6 = $db->prepare("SELECT d.id_devolucion, d.id_venta, d.fecha, d.motivo, d.total_devolucion, CONCAT(u.nombre,' ',u.apellido) AS cliente FROM devoluciones d JOIN venta v ON v.id_venta = d.id_venta LEFT JOIN usuario u ON u.id_usuario = v.id_cliente ORDER BY d.fecha DESC LIMIT 8");
        $stmt6->execute();
        $devols = $stmt6->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <table width="800" style="table-layout: fixed;">
            <col width="100"><col width="150"><col width="200"><col width="200"><col width="150">
            <tr><td colspan="5" class="section-title">Últimas Devoluciones</td></tr>
            <tr>
                <td class="th-header">ID</td>
                <td class="th-header">Fecha</td>
                <td class="th-header">Cliente</td>
                <td class="th-header">Motivo</td>
                <td class="th-header">Total Devuelto</td>
            </tr>
            <?php foreach ($devols as $d): ?>
                <tr>
                    <td class="td-data">#<?= $d['id_devolucion'] ?></td>
                    <td class="td-data"><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                    <td class="td-data"><?= htmlspecialchars($d['cliente'] ?? 'Mostrador') ?></td>
                    <td class="td-data"><?= htmlspecialchars($d['motivo']) ?></td>
                    <td class="td-data td-red">-$<?= number_format($d['total_devolucion'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
