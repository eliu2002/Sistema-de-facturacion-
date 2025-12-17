<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS (solo admin)
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    die("Acceso denegado. Esta función es solo para administradores.");
}

// 2. INCLUIR DATOS DE CONEXIÓN
require 'conexion.php'; // Aquí tenemos $servidor, $usuario, $clave, $baseDatos

// 3. DEFINIR RUTA Y NOMBRE DEL ARCHIVO DE RESPALDO
$backup_dir = 'backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// Formato: backup_nombre-de-la-bd_YYYY-MM-DD_HH-MM-SS.sql
$fecha = date("Y-m-d_H-i-s");
$nombre_archivo = "backup_{$baseDatos}_{$fecha}.sql";
$ruta_completa_archivo = $backup_dir . $nombre_archivo;

// 4. CONSTRUIR EL COMANDO `mysqldump`
// Este comando es una utilidad de línea de comandos de MySQL para crear respaldos.
// Es importante que la ruta a mysqldump.exe sea correcta para tu instalación de XAMPP.
// La ruta puede variar, ajústala si es necesario.
$ruta_mysqldump = "C:\\xampp\\mysql\\bin\\mysqldump.exe"; // Ruta típica en Windows

// VERIFICAR SI EL EJECUTABLE EXISTE ANTES DE INTENTAR USARLO
if (!file_exists($ruta_mysqldump)) {
    die("Error Crítico: No se encontró el ejecutable 'mysqldump.exe' en la ruta especificada: {$ruta_mysqldump}.<br>Por favor, verifica la ruta en el archivo backup.php.");
}

// Construcción del comando completo para guardar el archivo en el servidor
// Usar exec() en lugar de shell_exec para obtener el código de retorno y la salida.
// Es más seguro pasar la contraseña a través de una variable de entorno.
putenv("MYSQL_PASSWORD={$clave}");
$comando = "\"{$ruta_mysqldump}\" --user={$usuario} --host={$servidor} {$baseDatos} > \"{$ruta_completa_archivo}\"";

// 5. EJECUTAR EL COMANDO Y CAPTURAR LA SALIDA
$output = [];
$return_code = -1;
exec($comando . ' 2>&1', $output, $return_code);
putenv('MYSQL_PASSWORD='); // Limpiar la variable de entorno

// 6. VERIFICAR SI EL RESPALDO SE CREÓ Y REGISTRARLO EN LA BD
if ($return_code === 0 && file_exists($ruta_completa_archivo)) {
    // El respaldo se creó, ahora lo registramos en la tabla `respaldos`
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conexion->prepare("INSERT INTO respaldos (usuario_id, nombre_archivo, ruta_archivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $nombre_archivo, $ruta_completa_archivo);
    $stmt->execute();
    $stmt->close();

    // 7. FORZAR LA DESCARGA DEL ARCHIVO
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
    // Si hubo un error, lo mostramos
    die("Error al crear el respaldo. Código: {$return_code}. Salida: <pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>");
}
?>
