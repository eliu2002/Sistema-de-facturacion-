<?php
session_start();
require 'conexion.php';

// 1. VERIFICACIÓN DE PRIVILEGIOS Y DATOS
// Solo los administradores pueden acceder y debe existir un ID para eliminar.
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin' || !isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id_a_eliminar = $_GET['id'];

// 2. OBTENER ID DEL ADMIN LOGUEADO PARA EVITAR AUTO-ELIMINACIÓN
// Es una buena práctica guardar el ID del usuario en la sesión al iniciarla.
// Como solo tenemos el email, lo usamos para buscar el ID.
$email_admin_logueado = $_SESSION['usuario'];
$stmt_admin = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt_admin->bind_param("s", $email_admin_logueado);
$stmt_admin->execute();
$resultado_admin = $stmt_admin->get_result();
$admin_logueado = $resultado_admin->fetch_assoc();
$id_admin_logueado = $admin_logueado['id'];

// 3. PREVENIR AUTO-ELIMINACIÓN
if ($id_a_eliminar == $id_admin_logueado) {
    echo "<script>alert('No puedes eliminar tu propia cuenta de administrador.'); window.location='admin.php';</script>";
    exit();
}

// 4. ELIMINACIÓN SEGURA CON CONSULTA PREPARADA
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_a_eliminar); // "i" para integer
$stmt->execute();

header("Location: admin.php");
exit();
