<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    header("Location: index.php");
    exit();
}

require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    $usuario_id = $_SESSION['usuario_id'];

    // --- ACCIÓN PARA INICIAR TURNO ---
    if ($action === 'iniciar') {
        // 1. Validar que no haya otro turno abierto
        $check_stmt = $conexion->prepare("SELECT id FROM turnos_caja WHERE estado = 'abierto'");
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
            header("Location: gestion_turno.php?error=" . urlencode("Ya existe un turno abierto. Debes cerrarlo primero."));
            exit();
        }

        // 2. Insertar el nuevo turno
        $capital_inicial = $_POST['capital_inicial'];
        if (!is_numeric($capital_inicial) || $capital_inicial < 0) {
            header("Location: gestion_turno.php?error=" . urlencode("El capital inicial debe ser un número válido."));
            exit();
        }

        $stmt = $conexion->prepare("INSERT INTO turnos_caja (usuario_id, capital_inicial, estado) VALUES (?, ?, 'abierto')");
        $stmt->bind_param("id", $usuario_id, $capital_inicial);
        
        if ($stmt->execute()) {
            header("Location: vender.php"); // Redirigir a la página de ventas
            exit();
        } else {
            header("Location: gestion_turno.php?error=" . urlencode("Error al iniciar el turno."));
            exit();
        }
    }

    // --- ACCIÓN PARA CERRAR TURNO ---
    if ($action === 'cerrar') {
        $turno_id = $_POST['turno_id'];
        $monto_final_real = $_POST['monto_final_real'];

        $conexion->begin_transaction();
        try {
            // 1. Obtener datos del turno que se va a cerrar
            $stmt_turno = $conexion->prepare("SELECT capital_inicial FROM turnos_caja WHERE id = ? AND estado = 'abierto' FOR UPDATE");
            $stmt_turno->bind_param("i", $turno_id);
            $stmt_turno->execute();
            $turno = $stmt_turno->get_result()->fetch_assoc();

            // 2. Calcular el total de ventas para ese turno
            $stmt_ventas = $conexion->prepare("SELECT SUM(total) as total_ventas FROM facturas WHERE turno_id = ?");
            $stmt_ventas->bind_param("i", $turno_id);
            $stmt_ventas->execute();
            $total_ventas = $stmt_ventas->get_result()->fetch_assoc()['total_ventas'] ?? 0;

            // 3. Calcular la diferencia
            $total_esperado = $turno['capital_inicial'] + $total_ventas;
            $diferencia = $monto_final_real - $total_esperado;

            // 4. Actualizar el turno a 'cerrado'
            $stmt_update = $conexion->prepare("UPDATE turnos_caja SET fecha_cierre = NOW(), total_ventas_calculado = ?, monto_final_real = ?, diferencia = ?, estado = 'cerrado' WHERE id = ?");
            $stmt_update->bind_param("dddi", $total_ventas, $monto_final_real, $diferencia, $turno_id);
            $stmt_update->execute();

            $conexion->commit();
            header("Location: reporte_turno.php?id=" . $turno_id); // Redirigir al reporte
            exit();
        } catch (Exception $e) {
            $conexion->rollback();
            header("Location: gestion_turno.php?error=" . urlencode("Error al cerrar el turno: " . $e->getMessage()));
            exit();
        }
    }
}

header("Location: gestion_turno.php");
exit();