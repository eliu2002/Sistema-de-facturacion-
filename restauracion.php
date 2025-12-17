<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS (solo admin)
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';

// 2. PROCESAR EL FORMULARIO DE RESTAURACIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['backup_file'])) {
    // Validar que el archivo se haya subido correctamente
    if ($_FILES['backup_file']['error'] == UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['backup_file']['name'];
        $ruta_temporal = $_FILES['backup_file']['tmp_name'];
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);

        // Validar que sea un archivo .sql
        if (strtolower($extension) == 'sql') {
            // Ruta a mysql.exe (ajustar si es necesario)
            $ruta_mysql = "C:\\xampp\\mysql\\bin\\mysql.exe";
            if (!file_exists($ruta_mysql)) {
                $mensaje = "<div class='alert alert-danger'>Error Crítico: No se encontró 'mysql.exe' en la ruta: {$ruta_mysql}.</div>";
            } else {
                // Comando para restaurar la base de datos
                // Es más seguro pasar la contraseña a través de una variable de entorno.
                putenv("MYSQL_PASSWORD={$clave}");
                $comando = "\"{$ruta_mysql}\" --user={$usuario} --host={$servidor} {$baseDatos} < \"{$ruta_temporal}\"";

                // Ejecutar el comando
                $salida = [];
                $codigo_retorno = -1;
                exec($comando . ' 2>&1', $salida, $codigo_retorno);
                putenv('MYSQL_PASSWORD='); // Limpiar la variable de entorno

                if ($codigo_retorno === 0) {
                    $mensaje = "<div class='alert alert-success'><strong>¡Restauración completada!</strong> La base de datos ha sido restaurada exitosamente. Se recomienda cerrar sesión y volver a ingresar.</div>";
                } else {
                    $mensaje = "<div class='alert alert-danger'><strong>Error en la restauración.</strong> Código de retorno: {$codigo_retorno}. Salida: <pre>" . htmlspecialchars(implode("\n", $salida)) . "</pre></div>";
                }
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error: El archivo debe ser de tipo .sql</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al subir el archivo. Código: " . $_FILES['backup_file']['error'] . "</div>";
    }
}

include 'admin_header.php';
?>

<!-- Título de la Página -->
<h1 class="h3 mb-2 text-gray-800">Respaldo y Restauración</h1>
<p class="mb-4">Realiza copias de seguridad de la base de datos o restaura el sistema a un punto anterior.</p>

<?php echo $mensaje; ?>

<!-- Sección de Respaldo -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">1. Crear un Respaldo (Backup)</h6>
    </div>
    <div class="card-body">
        <p>Haz clic en el botón para generar una copia de seguridad completa de la base de datos. Se descargará un archivo <code>.sql</code> que contiene toda la información del sistema.</p>
        <a href="backup.php" class="btn btn-primary">
            <i class="fas fa-download fa-sm text-white-50"></i> Generar y Descargar Respaldo
        </a>
    </div>
</div>

<!-- Sección de Restauración -->
<div class="card shadow mb-4 border-left-danger">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger">2. Restaurar desde un Respaldo</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <strong>¡ADVERTENCIA!</strong> Esta acción es destructiva y no se puede deshacer. Restaurar desde un archivo <strong>borrará permanentemente todos los datos actuales</strong> y los reemplazará con los datos del archivo de respaldo. Úsalo con extrema precaución.
        </div>
        <form method="POST" action="restauracion.php" enctype="multipart/form-data" onsubmit="return confirm('¿Estás absolutamente seguro de que quieres restaurar la base de datos? TODOS los datos actuales se perderán.');">
            <div class="form-group">
                <label for="backup_file">Selecciona el archivo de respaldo (<code>.sql</code>)</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="backup_file" name="backup_file" required accept=".sql">
                    <label class="custom-file-label" for="backup_file">Elegir archivo...</label>
                </div>
            </div>
            <button type="submit" class="btn btn-danger"><i class="fas fa-upload"></i> Restaurar Base de Datos</button>
        </form>
    </div>
</div>

<?php include 'admin_footer.php'; ?>