<?php
session_start();

// 1. VERIFICACIÓN DE SESIÓN Y ROL
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

// Dependiendo del rol, incluimos el header correspondiente
if ($_SESSION['rol'] == 'admin') {
    include 'admin_header.php';
} else {
    include 'empleado_header.php';
}

// 2. CONSULTA PARA OBTENER TODOS LOS TURNOS CERRADOS
$query_turnos = $conexion->query("
    SELECT 
        t.id,
        t.fecha_apertura,
        t.fecha_cierre,
        t.capital_inicial,
        t.total_ventas_calculado,
        t.monto_final_real,
        t.diferencia,
        u.email as vendedor
    FROM turnos_caja t
    LEFT JOIN usuarios u ON t.usuario_id = u.id
    WHERE t.estado = 'cerrado'
    ORDER BY t.fecha_cierre DESC
");

?>

<!-- Título de la Página -->
<h1 class="h3 mb-2 text-gray-800">Historial de Cierres de Turno</h1>
<p class="mb-4">Aquí se muestran todos los turnos de caja que han sido cerrados.</p>

<!-- Tabla de Turnos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Historial de Turnos</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID Turno</th>
                        <th>Vendedor</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Capital Inicial</th>
                        <th>Ventas</th>
                        <th>Monto Final</th>
                        <th>Diferencia</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($turno = $query_turnos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $turno['id']; ?></td>
                        <td><?php echo htmlspecialchars($turno['vendedor'] ?? 'N/A'); ?></td>
                        <td class="text-nowrap"><?php echo date('d/m/y H:i', strtotime($turno['fecha_apertura'])); ?></td>
                        <td class="text-nowrap"><?php echo date('d/m/y H:i', strtotime($turno['fecha_cierre'])); ?></td>
                        <td class="text-nowrap">C$ <?php echo number_format($turno['capital_inicial'], 2); ?></td>
                        <td class="text-nowrap">C$ <?php echo number_format($turno['total_ventas_calculado'], 2); ?></td>
                        <td class="text-nowrap">C$ <?php echo number_format($turno['monto_final_real'], 2); ?></td>
                        <td class="text-nowrap font-weight-bold <?php echo $turno['diferencia'] == 0 ? 'text-success' : 'text-danger'; ?>">
                            C$ <?php echo number_format($turno['diferencia'], 2); ?>
                        </td>
                        <td class="text-center">
                            <a href="reporte_turno.php?id=<?php echo $turno['id']; ?>" class="btn btn-sm btn-info" title="Ver Reporte">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
if ($_SESSION['rol'] == 'admin') {
    include 'admin_footer.php';
} else {
    include 'empleado_footer.php';
} 
?>