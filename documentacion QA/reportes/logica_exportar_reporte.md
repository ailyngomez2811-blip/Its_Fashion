**Vista:** `views/admin/reportes.php`  
**Controlador:** `controllers/admin/ExportarPDFController.php`  
**Librería:** Dompdf (ubicada en `/dompdf/`)

Modal con checkboxes para seleccionar tipo reporte: Ventas, Inventario, Productos más vendidos, Devoluciones. Foreach genera checkboxes con value usando `str_replace(' ', '_', $r)`. Click botón PDF ejecuta `exportar('PDF')`. JavaScript obtiene checkboxes marcados, construye queryString con `params.join('&')`, ejecuta `window.open('../../controllers/admin/ExportarPDFController.php' + queryString, '_blank')`.

Controlador verifica sesión admin, hace `require_once` de dompdf autoload, establece zona horaria Bogotá. Obtiene parámetros con `isset($_POST['Ventas']) || isset($_GET['Ventas'])`. Si ninguno: todos true. Lee logo con `file_get_contents()`, convierte a base64, construye data URI. Ejecuta `ob_start()` para capturar output. Genera HTML con estilos CSS inline, fuentes Google Fonts (Outfit y Playfair Display). Header con logo y título, fecha con `date('d/m/Y H:i')`, usuario sesión.

Secciones condicionales: Si $incluirVentas hace SELECT ventas últimos 7 días con DATE_SUB() y GROUP BY. Si $incluirInventario SELECT productos WHERE stock <= stock_minimo LIMIT 10. Si $incluirProductos SELECT con SUM() y JOINs ORDER BY vendidos DESC LIMIT 5. Si $incluirDevoluciones SELECT con CONCAT() para cliente ORDER BY fecha DESC LIMIT 8. Foreach genera tablas HTML con `number_format()` para montos. Footer con copyright.

Ejecuta `$html = ob_get_clean()`. Crea `$options = new Options()`, configura `isHtml5ParserEnabled` y `isRemoteEnabled` true. Instancia `$dompdf = new Dompdf($options)`. Ejecuta `loadHtml($html)`, `setPaper('A4', 'portrait')`, `render()`. Envía con `stream("Reporte_Its_Fashion_" . date('Ymd_Hi') . ".pdf", array("Attachment" => true))`. Navegador descarga PDF automáticamente.
