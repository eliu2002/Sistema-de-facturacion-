<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    // Si no es admin, redirigir a la página de inicio con un mensaje.
    echo "<script>alert('Debes iniciar sesión como administrador'); window.location='index.php';</script>";
    exit();
}
require 'conexion.php';

// Consulta para obtener el número total de usuarios
$result_users = $conexion->query("SELECT COUNT(id) AS total FROM usuarios");
$total_usuarios = $result_users->fetch_assoc()['total'];

// Consulta para obtener el número total de productos
$result_products = $conexion->query("SELECT COUNT(id) AS total FROM productos");
$total_productos = $result_products->fetch_assoc()['total'];

// Consulta para obtener el número total de facturas (ventas)
$result_invoices = $conexion->query("SELECT COUNT(id) AS total FROM facturas");
$total_facturas = $result_invoices->fetch_assoc()['total'];

// Consulta para obtener el monto total facturado hoy
$result_sales_today = $conexion->query("SELECT SUM(total) AS monto FROM facturas WHERE DATE(fecha_creacion) = CURDATE()");
$monto_total_hoy = $result_sales_today->fetch_assoc()['monto'] ?? 0;

// --- DATOS ADICIONALES PARA VISTA DE CONSULTOR ---

// Ganancia total (ejemplo: 30% del total vendido)
$result_total_revenue = $conexion->query("SELECT SUM(total) AS monto FROM facturas");
$ganancia_total = ($result_total_revenue->fetch_assoc()['monto'] ?? 0) * 0.30;

// Productos con bajo stock (ej: menos de 10 unidades)
$result_low_stock = $conexion->query("SELECT COUNT(id) AS total FROM productos WHERE cantidad < 10 AND cantidad > 0");
$productos_bajo_stock = $result_low_stock->fetch_assoc()['total'];

// Últimas 5 ventas para la tabla
$query_ultimas_ventas = $conexion->query("SELECT id, cliente_nombre, total, fecha_creacion FROM facturas ORDER BY fecha_creacion DESC LIMIT 5");

// --- DATOS PARA EL GRÁFICO ---
$labels_semana = [];

// Inicializar arrays con los últimos 7 días con 0 valores
$dias_ingresos = [];
$dias_productos = [];
$dias_ventas = [];
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $dias_ingresos[$fecha] = 0;
    $dias_productos[$fecha] = 0;
    $dias_ventas[$fecha] = 0;
    $labels_semana[] = date('d M', strtotime($fecha)); // Formato "24 May"
}

// 1. Consulta para Ingresos y Cantidad de Ventas por día
$query_facturas = $conexion->query("
    SELECT DATE(fecha_creacion) as dia, SUM(total) as total_ingresos, COUNT(id) as cantidad_ventas
    FROM facturas
    WHERE fecha_creacion >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(fecha_creacion)
");
while ($fila = $query_facturas->fetch_assoc()) {
    if (isset($dias_ingresos[$fila['dia']])) {
        $dias_ingresos[$fila['dia']] = $fila['total_ingresos'];
        $dias_ventas[$fila['dia']] = $fila['cantidad_ventas'];
    }
}

// 2. Consulta para Nuevos Productos por día
$query_productos = $conexion->query("
    SELECT DATE(fecha_creacion) as dia, COUNT(id) as total_productos
    FROM productos
    WHERE fecha_creacion >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(fecha_creacion)
");
while ($fila_prod = $query_productos->fetch_assoc()) {
    if (isset($dias_productos[$fila_prod['dia']])) {
        $dias_productos[$fila_prod['dia']] = $fila_prod['total_productos'];
    }
}

// Preparar los arrays de datos para el gráfico
$datos_ingresos = array_values($dias_ingresos);
$datos_productos = array_values($dias_productos);
$datos_ventas = array_values($dias_ventas);

include 'admin_header.php';
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Panel de Administración</h1>
<p>Bienvenido. Aquí tienes un resumen rápido del estado del sistema. Usa el menú lateral para gestionar las diferentes secciones.</p>

<!-- Content Row -->
<div class="row">

    <!-- Tarjeta de Usuarios Registrados -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Usuarios Registrados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_usuarios; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Productos en Inventario -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Productos en Inventario</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_productos; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wine-bottle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Ventas Realizadas (Total Histórico) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ventas Realizadas</div>
                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $total_facturas; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Total Facturado (Hoy) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ingresos de Hoy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">C$ <?php echo number_format($monto_total_hoy, 2); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row for Charts -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Resumen de Actividad (Últimos 7 Días)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="ventasSemanalesChart"></canvas> <!-- Gráfico único -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Esperar a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    // Gráfico de Líneas - Actividad Semanal
    var ctx = document.getElementById("ventasSemanalesChart").getContext('2d'); // Gráfico único

    // Mejorar la interacción del tooltip
    Chart.defaults.global.tooltips.mode = 'index';
    Chart.defaults.global.tooltips.intersect = false;

    var ventasSemanalesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels_semana); ?>,
            datasets: [{
                    label: "Ingresos (C$)",
                    yAxisID: 'y-axis-ingresos',
                    lineTension: 0.3,
                    backgroundColor: "rgba(246, 194, 62, 0.05)",
                    borderColor: "#f6c23e", // Color Warning
                    pointRadius: 3,
                    pointBackgroundColor: "#f6c23e",
                    pointBorderColor: "#f6c23e",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "#f6c23e",
                    pointHoverBorderColor: "#f6c23e",
                    pointHitRadius: 10,
                    pointBorderWidth: 2, // Color Warning
                    data: <?php echo json_encode($datos_ingresos); ?>,
                },
                {
                    label: "Nuevos Productos",
                    yAxisID: 'y-axis-cantidad',
                    lineTension: 0.3,
                    backgroundColor: "rgba(28, 200, 138, 0.05)",
                    borderColor: "#1cc88a", // Color Success
                    pointRadius: 3,
                    pointBackgroundColor: "#1cc88a",
                    pointBorderColor: "#1cc88a",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "#1cc88a",
                    pointHoverBorderColor: "#1cc88a",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: <?php echo json_encode($datos_productos); ?>,
                },
                {
                    label: "Ventas Realizadas",
                    yAxisID: 'y-axis-cantidad',
                    lineTension: 0.3,
                    backgroundColor: "rgba(54, 185, 204, 0.05)",
                    borderColor: "#36b9cc", // Color Info
                    pointRadius: 3,
                    pointBackgroundColor: "#36b9cc",
                    pointBorderColor: "#36b9cc",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "#36b9cc",
                    pointHoverBorderColor: "#36b9cc",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: <?php echo json_encode($datos_ventas); ?>,
                }
            ],
        },
        options: {
            maintainAspectRatio: false,
            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
            scales: {
                xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
                yAxes: [
                    {
                        id: 'y-axis-ingresos',
                        type: 'linear',
                        position: 'left',
                        ticks: { maxTicksLimit: 5, padding: 10, callback: function(v) { return 'C$' + new Intl.NumberFormat('es-NI').format(v); } },
                        gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                    },
                    {
                        id: 'y-axis-cantidad',
                        type: 'linear',
                        position: 'right',
                        ticks: { maxTicksLimit: 5, padding: 10, callback: function(v) { if (v % 1 === 0) return v; } }, // Solo mostrar enteros
                        gridLines: { drawOnChartArea: false } // No mostrar rejilla para este eje
                    }
                ],
            },
            legend: { display: true, position: 'bottom' },
            tooltips: { callbacks: { label: function(item, data) {
                let label = data.datasets[item.datasetIndex].label || '';
                if (label) { label += ': '; }
                if (item.datasetIndex === 0) { // Ingresos
                    label += 'C$ ' + new Intl.NumberFormat('es-NI').format(item.yLabel);
                } else { // Cantidad
                    label += item.yLabel;
                }
                return label;
            }}}
        }
    });
});
</script>

<?php include 'admin_footer.php'; ?>
