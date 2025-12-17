<?php
session_start();

// 1. VERIFICACIÓN DE SESIÓN Y ROL
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';

// LÓGICA PARA DESCARGAR UN RESPALDO EXISTENTE
if (isset($_GET['descargar'])) {
    $id_descargar = $_GET['descargar'];
    $stmt = $conexion->prepare("SELECT ruta_archivo FROM respaldos WHERE id = ?");
    $stmt->bind_param("i", $id_descargar);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    if ($resultado && file_exists($resultado['ruta_archivo'])) {
        $ruta_completa_archivo = $resultado['ruta_archivo'];
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($ruta_completa_archivo) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($ruta_completa_archivo));
        readfile($ruta_completa_archivo);
        exit;
    } else {
        $mensaje = "<div class='alert alert-danger'>Error: El archivo de respaldo no se encontró en el servidor.</div>";
    }
}

// 2. CONSULTA PARA OBTENER TODOS LOS RESPALDOS
$query_respaldos = $conexion->query("
    SELECT 
        r.id,
        r.nombre_archivo,
        r.fecha_creacion,
        u.email as usuario_email
    FROM respaldos r
    LEFT JOIN usuarios u ON r.usuario_id = u.id
    ORDER BY r.fecha_creacion DESC
");

include 'admin_header.php';
?>

<!-- Título de la Página -->
<h1 class="h3 mb-2 text-gray-800">Historial de Respaldos</h1>
<p class="mb-4">Registro de todas las copias de seguridad de la base de datos que se han generado.</p>

<?php echo $mensaje; ?>

<!-- Tabla de Respaldos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Historial</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Archivo</th>
                        <th>Fecha de Creación</th>
                        <th>Creado por</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($respaldo = $query_respaldos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $respaldo['id']; ?></td>
                        <td><?php echo htmlspecialchars($respaldo['nombre_archivo']); ?></td>
                        <td class="text-nowrap"><?php echo date('d/m/Y H:i', strtotime($respaldo['fecha_creacion'])); ?></td>
                        <td><?php echo htmlspecialchars($respaldo['usuario_email'] ?? 'N/A'); ?></td>
                        <td class="text-center">
                            <a href="historial_respaldos.php?descargar=<?php echo $respaldo['id']; ?>" class="btn btn-sm btn-info" title="Descargar de nuevo">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>