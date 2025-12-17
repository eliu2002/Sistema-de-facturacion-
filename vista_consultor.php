<?php
session_start();

// Verificación de sesión y rol. Solo el admin puede ver esta página.
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    echo "<script>alert('Acceso denegado. Debes iniciar sesión como Administrador'); window.location='index.php';</script>";
    exit();
}
require 'conexion.php';

// --- CONSULTAS PARA OBTENER TODOS LOS DATOS ---

// 1. Métricas Clave
$result_sales_today = $conexion->query("SELECT SUM(total) AS monto FROM facturas WHERE DATE(fecha_creacion) = CURDATE()");
$monto_total_hoy = $result_sales_today->fetch_assoc()['monto'] ?? 0;

$result_total_revenue = $conexion->query("SELECT SUM(total) AS monto FROM facturas");
$ingresos_totales = $result_total_revenue->fetch_assoc()['monto'] ?? 0;
$ganancia_total_estimada = $ingresos_totales * 0.30; // Ganancia estimada del 30%

// 2. Productos con ALTA demanda (bajo stock, menos de 10 unidades)
$query_low_stock = $conexion->query("SELECT id, nombre, cantidad, precio FROM productos WHERE cantidad < 10 AND cantidad > 0 ORDER BY cantidad ASC");

// 3. Productos con BAJA demanda (los 10 con más stock)
$query_high_stock = $conexion->query("SELECT id, nombre, cantidad, precio FROM productos ORDER BY cantidad DESC LIMIT 10");

// 4. Últimas 10 ventas para la tabla
$query_ultimas_ventas = $conexion->query("
    SELECT f.id, f.cliente_nombre, f.total, f.fecha_creacion, u.email as vendedor
    FROM facturas f
    LEFT JOIN usuarios u ON f.usuario_id = u.id
    ORDER BY f.fecha_creacion DESC LIMIT 10
");

// 5. Datos para el gráfico de actividad semanal (últimos 7 días)
$labels_semana = [];
$dias_ingresos = [];
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $dias_ingresos[$fecha] = 0;
    $labels_semana[] = date('d M', strtotime($fecha));
}

$query_facturas_semana = $conexion->query("
    SELECT DATE(fecha_creacion) as dia, SUM(total) as total_ingresos
    FROM facturas
    WHERE fecha_creacion >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(fecha_creacion)
");
while ($fila = $query_facturas_semana->fetch_assoc()) {
    if (isset($dias_ingresos[$fila['dia']])) {
        $dias_ingresos[$fila['dia']] = $fila['total_ingresos'];
    }
}
$datos_ingresos_semana = array_values($dias_ingresos);

include 'admin_header.php';
?>

<!-- Título de la Página -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Panel de Consultor</h1>
    <button id="export-pdf" class="btn btn-danger shadow-sm">
        <i class="fas fa-file-pdf fa-sm text-white-50"></i> Generar Reporte PDF
    </button>
</div>

<p class="mb-4">Este panel ofrece un resumen gerencial detallado con las métricas clave del negocio.</p>

<!-- Contenedor principal para el reporte -->
<div id="report-content">

    <!-- 1. Resumen de Métricas Clave -->
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Resumen Financiero</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ingresos de Hoy</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">C$ <?php echo number_format($monto_total_hoy, 2); ?></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ingresos Totales (Histórico)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">C$ <?php echo number_format($ingresos_totales, 2); ?></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Ganancias Totales (Est. 30%)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">C$ <?php echo number_format($ganancia_total_estimada, 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Gráfico de Actividad Semanal -->
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Ingresos de los Últimos 7 Días</h6></div>
        <div class="card-body">
            <div class="chart-area"><canvas id="activityChart"></canvas></div>
        </div>
    </div>

    <!-- 3. Tabla de Productos con Alta Demanda / Inventario Bajo -->
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-info">Productos con Alta Demanda (Inventario Menor a 10)</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="lowStockTable" width="100%" cellspacing="0">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock Actual</th></tr></thead>
                    <tbody>
                        <?php if ($query_low_stock->num_rows > 0): ?>
                            <?php while ($producto = $query_low_stock->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td>C$ <?php echo number_format($producto['precio'], 2); ?></td>
                                <td><?php echo $producto['cantidad']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No hay productos con inventario bajo.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Tabla de Productos con Baja Demanda / Inventario Alto -->
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-danger">Productos con Baja Demanda (Top 10 con Más Inventario)</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="highStockTable" width="100%" cellspacing="0">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock Actual</th></tr></thead>
                    <tbody>
                        <?php if ($query_high_stock->num_rows > 0): ?>
                            <?php while ($producto = $query_high_stock->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td>C$ <?php echo number_format($producto['precio'], 2); ?></td>
                                <td><?php echo $producto['cantidad']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No hay productos en el inventario.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 5. Tabla de Últimas Ventas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Últimas 10 Ventas Registradas</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="lastSalesTable" width="100%" cellspacing="0">
                    <thead><tr><th>N° Factura</th><th>Cliente</th><th>Vendedor</th><th>Total</th><th>Fecha</th></tr></thead>
                    <tbody>
                        <?php while ($venta = $query_ultimas_ventas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $venta['id']; ?></td>
                            <td><?php echo htmlspecialchars($venta['cliente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($venta['vendedor'] ?? 'N/A'); ?></td>
                            <td>C$ <?php echo number_format($venta['total'], 2); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha_creacion'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Imagen del logo oculta para usarla en el PDF -->
<img id="logo-pdf" src="Drink, Drank y Drunk.jpg" alt="Logo" style="display: none;" />

<!-- Librerías para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- GRÁFICO DE ACTIVIDAD ---
    var ctx = document.getElementById("activityChart").getContext('2d');
    var activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels_semana); ?>,
            datasets: [{
                label: "Ingresos (C$)",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                data: <?php echo json_encode($datos_ingresos_semana); ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) { return 'C$' + new Intl.NumberFormat('es-NI').format(value); }
                    }
                }],
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return 'Ingresos: C$ ' + new Intl.NumberFormat('es-NI').format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });

    // --- LÓGICA PARA EXPORTAR A PDF ---
    document.getElementById('export-pdf').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'p',
            unit: 'mm',
            format: 'a4'
        });

        const fecha = new Date().toLocaleString('es-NI');
        let finalY = 20; // Posición vertical inicial

        // Añadir el logo en la parte superior derecha
        const logoImg = document.getElementById('logo-pdf');
        doc.addImage(logoImg, 'JPEG', 170, 15, 25, 25); // X, Y, Ancho, Alto

        // Título del reporte
        doc.setFontSize(18);
        doc.text('Reporte Gerencial - Licorería D.D.D', 14, finalY);
        finalY += 8;
        doc.setFontSize(11);
        doc.setTextColor(100);
        doc.text(`Generado el: ${fecha}`, 14, finalY);
        finalY += 15;

        // Resumen financiero
        doc.setFontSize(14);
        doc.setTextColor(0);
        doc.text('Resumen Financiero', 14, finalY);
        finalY += 8;
        doc.setFontSize(11);
        doc.text(`- Ingresos de Hoy: C$ <?php echo number_format($monto_total_hoy, 2); ?>`, 16, finalY);
        finalY += 7;
        doc.text(`- Ingresos Totales (Histórico): C$ <?php echo number_format($ingresos_totales, 2); ?>`, 16, finalY);
        finalY += 7;
        doc.text(`- Ganancias Totales (Estimación 30%): C$ <?php echo number_format($ganancia_total_estimada, 2); ?>`, 16, finalY);
        finalY += 15;

        // Gráfico como imagen
        const chartCanvas = document.getElementById('activityChart');
        const chartImage = chartCanvas.toDataURL('image/png', 1.0);
        doc.setFontSize(14);
        doc.text('Ingresos de los Últimos 7 Días', 14, finalY);
        finalY += 8;
        doc.addImage(chartImage, 'PNG', 14, finalY, 180, 80); // Ajustar tamaño y posición
        finalY += 90;

        // Tabla de productos con bajo stock
        doc.addPage(); // Nueva página para las tablas.
        finalY = 20;
        doc.setFontSize(14);
        doc.text('Productos con Alta Demanda (Inventario Bajo)', 14, finalY);
        doc.autoTable({
            html: '#lowStockTable',
            startY: finalY + 5,
            theme: 'grid',
            headStyles: { fillColor: [54, 185, 204] } // Info/Azul claro
        });
        finalY = doc.lastAutoTable.finalY + 15;

        // Tabla de productos con alta demanda
        doc.setFontSize(14);
        doc.text('Productos con Baja Demanda (Más Inventario)', 14, finalY);
        doc.autoTable({
            html: '#highStockTable',
            startY: finalY + 5,
            theme: 'grid',
            headStyles: { fillColor: [231, 76, 60] } // Rojo
        });
        finalY = doc.lastAutoTable.finalY + 15;

        // Tabla de últimas ventas
        doc.setFontSize(14);
        doc.text('Últimas Ventas Registradas', 14, finalY);
        doc.autoTable({
            html: '#lastSalesTable',
            startY: finalY + 5,
            theme: 'grid',
            headStyles: { fillColor: [78, 115, 223] } // Azul
        });

        // Guardar el PDF
        doc.save(`reporte_gerencial_${new Date().toISOString().slice(0,10)}.pdf`);
    });
});
</script>

<?php include 'admin_footer.php'; ?>