**Vista:** `views/admin/reportes.php`  
**Controlador:** `controllers/admin/ExportarExcelController.php`  
**Formato:** HTML con headers Excel (.xls)

Modal con checkboxes para seleccionar tipo reporte. Click botón Excel ejecuta `exportar('Excel')`. JavaScript obtiene checkboxes marcados, construye queryString, ejecuta `window.open('../../controllers/admin/ExportarExcelController.php' + queryString, '_blank')`. Muestra toast "Generando Excel...".

Controlador verifica sesión admin, establece zona horaria Bogotá. Obtiene parámetros con `isset()` para cada sección. Si ninguno: todos true. Configura headers HTTP: `Content-Type: application/vnd.ms-excel`, `Content-Disposition: attachment; filename=Reporte_Its_Fashion_" . date('Ymd_Hi') . ".xls"`, `Pragma: no-cache`, `Expires: 0`. Imprime BOM UTF-8 con `echo "\xEF\xBB\xBF"` para tildes y ñ.

Genera HTML con namespaces Microsoft Office: `xmlns:o`, `xmlns:x`, `xmlns`. Head con estilos CSS inline para tablas, títulos, headers, celdas. Bloque condicional `<!--[if gte mso 9]>` con XML para Excel: define `<x:ExcelWorkbook>`, nombre hoja "Reporte Its Fashion", opciones `<x:FitToPage/>`, `<x:PaperSizeIndex>9</x:PaperSizeIndex>` (A4), `<x:DoNotDisplayGridlines/>`.

Body con tablas HTML. Primera tabla con `<col>` definiendo anchos fijos. Título "Its Fashion - Reporte Administrativo", fecha generado, usuario sesión. Secciones condicionales: Si $incluirVentas SELECT ventas últimos 7 días. Si $incluirInventario SELECT productos críticos LIMIT 10. Si $incluirProductos SELECT top 5 con SUM(). Si $incluirDevoluciones SELECT últimas 8 con CONCAT(). Foreach genera filas con clases CSS: `td-data`, `td-bold`, `td-red`. Tablas espaciadoras 30px entre secciones.

NO usa ob_start(). HTML se imprime directamente. Headers configurados fuerzan descarga. Navegador recibe HTML pero lo interpreta como .xls por Content-Type. Excel abre archivo mostrando tablas con estilos aplicados.
