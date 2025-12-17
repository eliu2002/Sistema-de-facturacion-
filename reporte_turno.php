<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

if (!isset($_GET['id'])) {
    header("Location: gestion_turno.php");
    exit();
}

$turno_id = $_GET['id'];

// Obtener datos del turno cerrado
$stmt = $conexion->prepare("
    SELECT t.*, u.email as nombre_usuario 
    FROM turnos_caja t 
    JOIN usuarios u ON t.usuario_id = u.id 
    WHERE t.id = ? AND t.estado = 'cerrado'
");
$stmt->bind_param("i", $turno_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<script>alert('Reporte de turno no encontrado o el turno no está cerrado.'); window.location='gestion_turno.php';</script>";
    exit();
}

$turno = $resultado->fetch_assoc();
$total_esperado = $turno['capital_inicial'] + $turno['total_ventas_calculado'];

include 'empleado_header.php';
?>

<!-- Título de la Página -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Reporte de Cierre de Turno #<?php echo $turno_id; ?></h1>
    <a href="gestion_turno.php" class="btn btn-primary shadow-sm"><i class="fas fa-cash-register fa-sm text-white-50"></i> Ir a Gestión de Turnos</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Resumen del Turno</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Usuario:
                        <strong><?php echo htmlspecialchars($turno['nombre_usuario']); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Fecha de Apertura:
                        <strong><?php echo date('d/m/Y H:i', strtotime($turno['fecha_apertura'])); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Fecha de Cierre:
                        <strong><?php echo date('d/m/Y H:i', strtotime($turno['fecha_cierre'])); ?></strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Capital Inicial:
                        <span class="text-info font-weight-bold">C$ <?php echo number_format($turno['capital_inicial'], 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        (+) Total Ventas:
                        <span class="text-success font-weight-bold">C$ <?php echo number_format($turno['total_ventas_calculado'], 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        (=) Total Esperado en Caja:
                        <strong class="text-primary">C$ <?php echo number_format($total_esperado, 2); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Monto Final Contado:
                        <strong>C$ <?php echo number_format($turno['monto_final_real'], 2); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center 
                        <?php echo $turno['diferencia'] == 0 ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                        Diferencia (Descuadre):
                        <strong style="font-size: 1.2rem;">C$ <?php echo number_format($turno['diferencia'], 2); ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Imprimir Reporte</button>
    </div>
</div>

<?php include 'empleado_footer.php'; ?>