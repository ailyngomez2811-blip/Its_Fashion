**Vista:** Cualquier vista de reportes  
**Controlador:** Endpoint específico de exportación  
**Librería:** PHPSpreadsheet para Excel o TCPDF/FPDF para PDF

Botón "Exportar" con dropdown formato: Excel o PDF. JavaScript recopila datos, ejecuta fetch POST con action=exportar, formato, datos, filtros. Excel: servidor usa PHPSpreadsheet, crea Spreadsheet, foreach datos agrega fila con setCellValue(), aplica estilos, genera archivo con Writer, configura headers Content-Type y Content-Disposition, envía con readfile() y exit. PDF: usa TCPDF, AddPage(), SetFont(), foreach datos agrega celdas con Cell(), Output con 'D' para descarga. JavaScript recibe blob, crea URL con createObjectURL(), crea elemento <a> con download, click programático, revoca URL.
