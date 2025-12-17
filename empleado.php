<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

$mensaje = '';
if (isset($_GET['status']) && $_GET['status'] == 'factura_ok') {
    $mensaje = "<div class='alert alert-success'>Factura generada y stock actualizado correctamente.</div>";
}

// --- DATOS PARA EL DASHBOARD ---

// 1. Tarjetas de resumen (Ventas de Hoy)
$query_ventas_hoy = $conexion->query("SELECT COUNT(id) AS total_ventas FROM facturas WHERE DATE(fecha_creacion) = CURDATE()");
$datos_ventas_hoy = $query_ventas_hoy->fetch_assoc();
$total_ventas_hoy = $datos_ventas_hoy['total_ventas'] ?? 0;

// 2. Últimas facturas para mostrar en el dashboard
$query_ultimas_facturas = $conexion->query("
    SELECT 
        f.id, 
        f.cliente_nombre, 
        f.total, 
        f.fecha_creacion,
        GROUP_CONCAT(CONCAT(p.nombre, ' (x', fd.cantidad, ')') SEPARATOR ', ') AS productos_vendidos
    FROM facturas f
    JOIN factura_detalles fd ON f.id = fd.factura_id
    JOIN productos p ON fd.producto_id = p.id
    GROUP BY f.id
    ORDER BY f.fecha_creacion DESC
    LIMIT 5
");

include 'empleado_header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard de Empleado</h1>
    <a href="lista_facturas.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-list fa-sm text-white-50"></i> Ver Todas las Facturas</a>
</div>

<?php echo $mensaje; ?>

<!-- Fila de Tarjetas de Resumen -->
<div class="row">
    <!-- Ventas Realizadas Hoy -->
    <div class="col-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas Realizadas (Hoy)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_ventas_hoy; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fila de Contenido Principal -->
<div class="row">

    <!-- Últimas Ventas Realizadas -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Últimas Ventas Realizadas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th class="text-nowrap">Fecha y Hora</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($factura = $query_ultimas_facturas->fetch_assoc()): ?>
                            <tr>
                                
                                <td><?php echo htmlspecialchars($factura['cliente_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($factura['productos_vendidos']); ?></td>
                                <td class="text-nowrap">C$ <?php echo number_format($factura['total'], 2); ?></td>
                                <td class="text-nowrap"><?php echo date('d/m/Y H:i', strtotime($factura['fecha_creacion'])); ?></td>
                                <td><a href="ver_factura.php?id=<?php echo $factura['id']; ?>" class="btn btn-sm btn-info" title="Ver Factura"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'empleado_footer.php'; ?>
