<?php
session_start();

// 1. VERIFICACIÓN DE SESIÓN Y ROL
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
include 'empleado_header.php';

// 2. CONSULTA PARA OBTENER TODAS LAS FACTURAS CON DETALLES
// Unimos las tablas para obtener el nombre del vendedor y la lista de productos.
$query_facturas = $conexion->query("
    SELECT 
        f.id, 
        f.cliente_nombre, 
        f.total, 
        f.fecha_creacion,
        u.email as vendedor,
        GROUP_CONCAT(CONCAT(p.nombre, ' (x', fd.cantidad, ')') SEPARATOR '<br>') AS productos_vendidos
    FROM facturas f
    LEFT JOIN usuarios u ON f.usuario_id = u.id
    LEFT JOIN factura_detalles fd ON f.id = fd.factura_id
    LEFT JOIN productos p ON fd.producto_id = p.id
    GROUP BY f.id
    ORDER BY f.fecha_creacion DESC
");

?>

<!-- Título de la Página -->
<h1 class="h3 mb-2 text-gray-800">Historial de Facturas</h1>
<p class="mb-4">Aquí se muestran todas las ventas registradas en el sistema.</p>

<!-- Tabla de Facturas -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Todas las Ventas</h6>
        <div>
            <button id="export-pdf" class="btn btn-sm btn-danger shadow-sm"><i class="fas fa-file-pdf fa-sm text-white-50"></i> Exportar a PDF</button>
            <button id="export-excel" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm text-white-50"></i> Exportar a Excel</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Fecha y Hora</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($factura = $query_facturas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $factura['id']; ?></td>
                        <td><?php echo htmlspecialchars($factura['cliente_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($factura['vendedor'] ?? 'N/A'); ?></td>
                        <td><?php echo $factura['productos_vendidos']; ?></td>
                        <td class="text-nowrap">C$ <?php echo number_format($factura['total'], 2); ?></td>
                        <td class="text-nowrap"><?php echo date('d/m/Y H:i', strtotime($factura['fecha_creacion'])); ?></td>
                        <td class="text-center">
                            <a href="ver_factura.php?id=<?php echo $factura['id']; ?>" class="btn btn-sm btn-info" title="Ver Factura">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'empleado_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const { jsPDF } = window.jspdf;

    function getTableData() {
        const table = document.getElementById('dataTable');
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim()).slice(0, -1); // Excluir 'Acción'
        const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr => {
            return Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim()).slice(0, -1); // Excluir 'Acción'
        });
        return { headers, rows };
    }

    // --- Lógica para exportar a PDF ---
    document.getElementById('export-pdf').addEventListener('click', function() {
        const doc = new jsPDF();
        const { headers, rows } = getTableData();
        const fecha = new Date().toLocaleString('es-NI');

        doc.setFontSize(18);
        doc.text('Reporte de Ventas - Licorería D.D.D', 14, 22);
        doc.setFontSize(11);
        doc.setTextColor(100);
        doc.text(`Generado el: ${fecha}`, 14, 30);

        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 35,
            theme: 'grid',
            headStyles: { fillColor: [78, 115, 223] }, // Color primario de la plantilla
            styles: { fontSize: 8 },
        });

        doc.save(`reporte_ventas_${new Date().toISOString().slice(0,10)}.pdf`);
    });

    // --- Lógica para exportar a Excel ---
    document.getElementById('export-excel').addEventListener('click', function() {
        const { headers, rows } = getTableData();
        const data = [headers, ...rows];

        // Crear una hoja de cálculo a partir de los datos
        const worksheet = XLSX.utils.aoa_to_sheet(data);

        // Ajustar el ancho de las columnas (opcional pero recomendado)
        const colWidths = headers.map((_, i) => {
            const maxLength = Math.max(...rows.map(row => (row[i] || '').length), headers[i].length);
            return { wch: maxLength + 2 }; // +2 para un poco de padding
        });
        worksheet['!cols'] = colWidths;

        // Crear un nuevo libro de trabajo y añadir la hoja
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Ventas');

        // Generar y descargar el archivo Excel
        XLSX.writeFile(workbook, `reporte_ventas_${new Date().toISOString().slice(0,10)}.xlsx`);
    });

});
</script>