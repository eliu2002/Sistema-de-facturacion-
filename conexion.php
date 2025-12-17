<?php
$servidor = "localhost";
$usuario = "root";     // usuario de MySQL
$clave = "";           // contraseña de MySQL
$baseDatos = "licoreria";

$conexion = new mysqli($servidor, $usuario, $clave, $baseDatos);

if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error);
}
// echo "✅ Conectado a la base de datos";
?>
