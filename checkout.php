<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS Y ESTADO DEL CARRITO
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

if (empty($_SESSION['cart'])) {
    echo "<script>alert('El carrito está vacío.'); window.location='vender.php';</script>";
    exit();
}

require 'conexion.php';

// 1.5 OBTENER EL TURNO ABIERTO
$query_turno = "SELECT id FROM turnos_caja WHERE estado = 'abierto' LIMIT 1";
$resultado_turno = $conexion->query($query_turno);
if ($resultado_turno->num_rows === 0) {
    echo "<script>alert('Error crítico: No se encontró un turno abierto para asignar la factura.'); window.location='vender.php';</script>";
    exit();
}
$turno_id = $resultado_turno->fetch_assoc()['id'];

// 2. PROCESAR LA VENTA
$cart = $_SESSION['cart'];
$cliente_nombre = !empty($_POST['cliente_nombre']) ? trim($_POST['cliente_nombre']) : 'Cliente General';
$cliente_id = !empty($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : null;
$usuario_id = $_SESSION['usuario_id'];
$total_factura = 0;

// Iniciar transacción para garantizar la integridad de los datos
$conexion->begin_transaction();

try {
    // Obtener todos los productos del carrito en una sola consulta para validación
    $product_ids = implode(',', array_keys($cart));
    $stmt = $conexion->prepare("SELECT id, precio, cantidad FROM productos WHERE id IN ($product_ids) FOR UPDATE");
    $stmt->execute();
    $productos_db = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $productos_en_stock = [];
    foreach ($productos_db as $p) {
        $productos_en_stock[$p['id']] = $p;
    }

    // Validar stock y calcular total
    foreach ($cart as $product_id => $item) {
        if (!isset($productos_en_stock[$product_id])) {
            throw new Exception("El producto con ID $product_id ya no está disponible.");
        }
        if ($productos_en_stock[$product_id]['cantidad'] < $item['quantity']) {
            throw new Exception("No hay suficiente stock para el producto ID $product_id. Disponible: " . $productos_en_stock[$product_id]['cantidad']);
        }
        $total_factura += $item['quantity'] * $productos_en_stock[$product_id]['precio'];
    }

    // Insertar en la tabla `facturas`
    $stmt_factura = $conexion->prepare("INSERT INTO facturas (usuario_id, turno_id, cliente_id, cliente_nombre, total) VALUES (?, ?, ?, ?, ?)");
    $stmt_factura->bind_param("iiisd", $usuario_id, $turno_id, $cliente_id, $cliente_nombre, $total_factura);
    $stmt_factura->execute();
    $factura_id = $conexion->insert_id;

    // Insertar en la tabla `factura_detalles` y actualizar stock
    $stmt_detalle = $conexion->prepare("INSERT INTO factura_detalles (factura_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_update = $conexion->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");

    foreach ($cart as $product_id => $item) {
        $precio_unitario = $productos_en_stock[$product_id]['precio'];
        $cantidad_a_vender = $item['quantity'];
        $subtotal = $precio_unitario * $cantidad_a_vender;

        // Insertar detalle
        $stmt_detalle->bind_param("iiidd", $factura_id, $product_id, $cantidad_a_vender, $precio_unitario, $subtotal);
        $stmt_detalle->execute();

        // Actualizar stock
        $stmt_update->bind_param("ii", $cantidad_a_vender, $product_id);
        $stmt_update->execute();
    }

    // Si todo fue bien, confirmar la transacción y limpiar el carrito
    $conexion->commit();
    unset($_SESSION['cart']);

    // Redirigir a la nueva página para ver/imprimir la factura
    header("Location: ver_factura.php?id=" . $factura_id);
    exit();

} catch (Exception $e) {
    // Si algo falla, revertir la transacción
    $conexion->rollback();
    echo "<script>alert('Error al generar la factura: " . addslashes($e->getMessage()) . "'); window.location='vender.php';</script>";
    exit();
}
?>