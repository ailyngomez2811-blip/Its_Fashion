**Vista:** `views/admin/reportes.php`  
**Controlador:** `controllers/admin/ExportarPDFController.php`  
**Librería:** Dompdf (ubicada en `/dompdf/`)

## Modal de exportación
Vista muestra modal con checkboxes para seleccionar tipo de reporte: Ventas, Inventario, Productos más vendidos, Devoluciones. Botones para formato: PDF o Excel. Foreach genera checkboxes: `<?php foreach (['Ventas', 'Inventario', ...] as $r): ?>` con value usando `str_replace(' ', '_', $r)` para convertir espacios en guiones bajos.

## Función exportar() - JavaScript
Click en botón PDF ejecuta `exportar('PDF')`. Función obtiene checkboxes marcados con `querySelectorAll('.report-cb:checked')`. Foreach checkboxes construye array params con `encodeURIComponent(cb.value) + '=1'`. Construye queryString con `params.join('&')`. Ejecuta `window.open('../../controllers/admin/ExportarPDFController.php' + queryString, '_blank')` abriendo controlador en nueva pestaña. Muestra toast "Generando PDF...".

## Controlador - Recepción de parámetros
Verifica sesión admin. Hace `require_once` de database.php y dompdf autoload: `require_once __DIR__ . '/../../dompdf/autoload.inc.php'`. Establece zona horaria: `date_default_timezone_set('America/Bogota')`. Usa namespace: `use Dompdf\Dompdf; use Dompdf\Options;`. Obtiene parámetros con `isset($_POST['Ventas']) || isset($_GET['Ventas'])` para cada sección. Si ninguno seleccionado: todos true por defecto.

## Logo en base64
Obtiene ruta logo: `$logoPath = __DIR__ . '/../../img/logo en nombre.png'`. Si existe: lee archivo con `file_get_contents()`, convierte a base64 con `base64_encode()`, construye data URI: `'data:image/png;base64,' . $logoData`. Sino: `$logoSrc = ''`.

## Construcción HTML con ob_start()
Ejecuta `ob_start()` para capturar output. Genera HTML completo con DOCTYPE, head con estilos CSS inline (fuentes Google Fonts Outfit y Playfair Display, estilos para tablas, header, footer). Body con div container, header con tabla mostrando logo y título "Reporte Administrativo", fecha generado con `date('d/m/Y H:i')`, usuario con `$_SESSION['user_nombre']`.

## Secciones condicionales con queries
**Si $incluirVentas:** h2 "Ventas Últimos 7 Días". Query: `SELECT DATE(fecha) AS dia, COALESCE(SUM(total),0) AS total FROM venta WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND estado='Completada' GROUP BY DATE(fecha) ORDER BY dia ASC`. Foreach resultados genera tabla con fecha y total vendido formateado con `number_format()`.

**Si $incluirInventario:** h2 "Inventario Crítico". Query: `SELECT p.nombre, p.talla, p.color, p.stock, p.stock_minimo, c.nombre AS categoria FROM productos p LEFT JOIN categoria c WHERE p.stock <= p.stock_minimo AND p.estado='Activo' ORDER BY p.stock ASC LIMIT 10`. Tabla con producto, categoría, talla, color, stock, mínimo.

**Si $incluirProductos:** h2 "Top 5 Productos Más Vendidos". Query: `SELECT p.nombre, p.talla, p.color, SUM(dv.cantidad) AS vendidos, SUM(dv.cantidad * dv.precio_unitario) AS ingresos FROM detalle_venta dv JOIN productos p JOIN venta v WHERE v.estado='Completada' GROUP BY dv.id_producto ORDER BY vendidos DESC LIMIT 5`. Tabla con producto, talla/color, vendidos, ingresos.

**Si $incluirDevoluciones:** h2 "Últimas Devoluciones". Query: `SELECT d.id_devolucion, d.fecha, d.motivo, d.total_devolucion, CONCAT(u.nombre,' ',u.apellido) AS cliente FROM devoluciones d JOIN venta v LEFT JOIN usuario u ORDER BY d.fecha DESC LIMIT 8`. Tabla con ID, fecha, cliente, motivo, total devuelto (en rojo con signo negativo).

Footer con copyright: `Its Fashion © <?= date('Y') ?>`.

## Captura HTML y configuración Dompdf
Ejecuta `$html = ob_get_clean()` capturando todo el output. Crea opciones: `$options = new Options()`. Configura: `$options->set('isHtml5ParserEnabled', true)` para HTML5, `$options->set('isRemoteEnabled', true)` para recursos externos (fuentes Google). Instancia Dompdf: `$dompdf = new Dompdf($options)`. Carga HTML: `$dompdf->loadHtml($html)`. Configura papel: `$dompdf->setPaper('A4', 'portrait')`.

## Renderizado y descarga
Ejecuta `$dompdf->render()` generando PDF en memoria. Envía al navegador: `$dompdf->stream("Reporte_Its_Fashion_" . date('Ymd_Hi') . ".pdf", array("Attachment" => true))`. Parámetro "Attachment" => true fuerza descarga. Nombre archivo incluye fecha/hora. Ejecuta `exit()` terminando script. Navegador descarga PDF automáticamente.
