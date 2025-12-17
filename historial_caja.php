<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado. Debes ser administrador.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

$query_sesiones = $conexion->query("
    SELECT 
        cs.*,
        u.email as usuario_email
    FROM caja_sesiones cs
    JOIN usuarios u ON cs.usuario_id = u.id
    ORDER BY cs.fecha_apertura DESC
");

include 'admin_header.php';
?>

<h1 class="h3 mb-2 text-gray-800">Historial de Cajas</h1>
<p class="mb-4">Aquí se muestra un registro de todas las aperturas y cierres de caja realizados.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Registros de Sesiones de Caja</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Monto Inicial</th>
                        <th>Ventas</th>
                        <th>Monto Esperado</th>
                        <th>Monto Real</th>
                        <th>Diferencia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sesion = $query_sesiones->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sesion['usuario_email']); ?></td>
                        <td><?php echo date('d/m/y H:i', strtotime($sesion['fecha_apertura'])); ?></td>
                        <td><?php echo $sesion['fecha_cierre'] ? date('d/m/y H:i', strtotime($sesion['fecha_cierre'])) : 'N/A'; ?></td>
                        <td>C$ <?php echo number_format($sesion['monto_inicial'], 2); ?></td>
                        <td>C$ <?php echo number_format($sesion['total_ventas'], 2); ?></td>
                        <td>C$ <?php echo number_format($sesion['monto_final_esperado'], 2); ?></td>
                        <td>C$ <?php echo number_format($sesion['monto_final_real'], 2); ?></td>
                        <td>
                            <?php 
                            $diferencia = $sesion['diferencia'];
                            $clase = '';
                            if ($diferencia < 0) $clase = 'text-danger';
                            if ($diferencia > 0) $clase = 'text-success';
                            echo "<strong class='{$clase}'>" . number_format($diferencia, 2) . "</strong>";
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $sesion['estado'] == 'abierta' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($sesion['estado']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>