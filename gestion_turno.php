<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

// Buscar si ya existe un turno abierto
$turno_abierto = null;
$query_turno = "SELECT t.*, u.email as nombre_usuario FROM turnos_caja t JOIN usuarios u ON t.usuario_id = u.id WHERE t.estado = 'abierto' LIMIT 1";
$resultado_turno = $conexion->query($query_turno);
if ($resultado_turno->num_rows > 0) {
    $turno_abierto = $resultado_turno->fetch_assoc();

    // --- INICIO: CÁLCULO DE VENTAS DEL TURNO ACTUAL ---
    // Calculamos las ventas en tiempo real para mostrarlas antes de cerrar.
    $stmt_ventas = $conexion->prepare("SELECT SUM(total) as total_ventas FROM facturas WHERE turno_id = ?");
    $stmt_ventas->bind_param("i", $turno_abierto['id']);
    $stmt_ventas->execute();
    $total_ventas_actual = $stmt_ventas->get_result()->fetch_assoc()['total_ventas'] ?? 0;
    $total_esperado_actual = $turno_abierto['capital_inicial'] + $total_ventas_actual;
    $stmt_ventas->close();
    // --- FIN: CÁLCULO DE VENTAS DEL TURNO ACTUAL ---
}

include 'empleado_header.php';
?>

<!-- Título de la Página -->
<h1 class="h3 mb-4 text-gray-800">Gestión de Turno de Caja</h1>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>


<div class="row">
    <div class="col-lg-8 mx-auto">
        <?php if ($turno_abierto): ?>
            <!-- --- VISTA PARA CERRAR TURNO --- -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cerrar Turno Actual</h6>
                </div>
                <div class="card-body">
                    <p>Actualmente hay un turno abierto. Para poder iniciar uno nuevo, primero debes cerrar el actual.</p>
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><strong>Iniciado por:</strong> <?php echo htmlspecialchars($turno_abierto['nombre_usuario']); ?></li>
                        <li class="list-group-item"><strong>Fecha de Apertura:</strong> <?php echo date('d/m/Y H:i', strtotime($turno_abierto['fecha_apertura'])); ?></li>
                        <li class="list-group-item"><strong>Capital Inicial:</strong> <span class="font-weight-bold text-success">C$ <?php echo number_format($turno_abierto['capital_inicial'], 2); ?></span></li>
                        <li class="list-group-item list-group-item-info d-flex justify-content-between align-items-center">
                            <strong>(+) Ventas del Turno (hasta ahora):</strong>
                            <span class="font-weight-bold">C$ <?php echo number_format($total_ventas_actual, 2); ?></span>
                        </li>
                        <li class="list-group-item list-group-item-primary d-flex justify-content-between align-items-center">
                            <strong>(=) Total Esperado en Caja:</strong>
                            <span class="font-weight-bold" style="font-size: 1.1rem;">C$ <?php echo number_format($total_esperado_actual, 2); ?></span>
                        </li>
                    </ul>

                    <form action="turno_handler.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cerrar el turno? Esta acción no se puede deshacer.');">
                        <input type="hidden" name="action" value="cerrar">
                        <input type="hidden" name="turno_id" value="<?php echo $turno_abierto['id']; ?>">
                        
                        <div class="form-group">
                            <label for="monto_final_real" class="font-weight-bold">Monto Final Contado en Caja (C$)</label>
                            <input type="number" class="form-control form-control-lg" id="monto_final_real" name="monto_final_real" step="0.01" required placeholder="Ingresa el total de dinero físico">
                            <small class="form-text text-muted">Ingresa la cantidad total de dinero que tienes en la caja, incluyendo el capital inicial.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-door-closed"></i> Confirmar y Cerrar Turno
                        </button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- --- VISTA PARA INICIAR TURNO --- -->
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Iniciar Nuevo Turno</h6>
                </div>
                <div class="card-body">
                    <p>No hay ningún turno activo. Ingresa el capital inicial para comenzar a vender.</p>
                    <form action="turno_handler.php" method="POST">
                        <input type="hidden" name="action" value="iniciar">
                        
                        <div class="form-group">
                            <label for="capital_inicial" class="font-weight-bold">Capital Inicial en Caja (C$)</label>
                            <input type="number" class="form-control form-control-lg" id="capital_inicial" name="capital_inicial" step="0.01" required placeholder="Ej: 1500.00">
                            <small class="form-text text-muted">Es el dinero base con el que empiezas el día (sencillo, fondo de caja, etc.).</small>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-play-circle"></i> Iniciar Turno
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'empleado_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enfocar el campo principal al cargar la página
    const capitalInput = document.getElementById('capital_inicial');
    const montoFinalInput = document.getElementById('monto_final_real');
    
    if (capitalInput) {
        capitalInput.focus();
    } else if (montoFinalInput) {
        montoFinalInput.focus();
    }
});
</script>