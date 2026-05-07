**Vista:** Cualquier vista de reportes
**Controlador:** Endpoint específico de exportación
**Librería:** PHPSpreadsheet para Excel o TCPDF/FPDF para PDF

1. Botón "Exportar" con dropdown para seleccionar formato: Excel o PDF. Click ejecuta función JavaScript con formato seleccionado.
2. JavaScript recopila datos actuales de la tabla o hace fetch para obtener datos completos. Ejecuta fetch POST a endpoint de exportación con action='exportar', formato, datos, filtros aplicados.
3. Para Excel: servidor usa PHPSpreadsheet. Crea nuevo Spreadsheet, agrega hoja con título del reporte. Foreach datos: agrega fila con setCellValue(). Aplica estilos a encabezados. Genera archivo con Writer, configura headers para descarga: Content-Type: application/vnd.openxmlformats, Content-Disposition: attachment; filename="reporte.xlsx". Envía archivo con readfile() y exit.
4. Para PDF: usa TCPDF. Crea nuevo PDF, AddPage(), SetFont(). Foreach datos: agrega celdas con Cell() o MultiCell(). Output con 'D' para forzar descarga.
5. JavaScript recibe blob, crea URL temporal con URL.createObjectURL(), crea elemento <a> con download attribute, hace click programático, revoca URL.
