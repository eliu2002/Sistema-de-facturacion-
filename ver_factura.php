<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS (empleado o admin)
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>"; // Redirigir a la página de inicio si no tiene permiso
    exit();
}

require 'conexion.php';

// 2. VALIDAR ID DE FACTURA
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: empleado.php");
    exit();
}
$factura_id = $_GET['id'];

// 3. CONSULTA PARA OBTENER TODOS LOS DATOS DE LA FACTURA
$stmt = $conexion->prepare("
    SELECT
        f.id AS factura_id,
        f.fecha_creacion,
        f.cliente_nombre,
        f.total AS total_factura,
        u.email AS empleado_email,
        fd.cantidad,
        fd.precio_unitario,
        fd.subtotal,
        p.nombre AS producto_nombre
    FROM facturas f
    JOIN usuarios u ON f.usuario_id = u.id
    JOIN factura_detalles fd ON f.id = fd.factura_id
    JOIN productos p ON fd.producto_id = p.id
    WHERE f.id = ?
");
$stmt->bind_param("i", $factura_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<script>alert('Factura no encontrada.'); window.location='lista_facturas.php';</script>";
    exit();
}

$detalles_factura = [];
while ($fila = $resultado->fetch_assoc()) {
    if (empty($detalles_factura)) {
        // Guardar datos generales de la factura una sola vez
        $detalles_factura['general'] = [
            'id' => $fila['factura_id'],
            'fecha' => new DateTime($fila['fecha_creacion']),
            'cliente' => $fila['cliente_nombre'],
            'total' => $fila['total_factura'],
            'empleado' => $fila['empleado_email']
        ];
    }
    // Agregar cada producto al detalle
    $detalles_factura['productos'][] = [
        'nombre' => $fila['producto_nombre'],
        'cantidad' => $fila['cantidad'],
        'precio' => $fila['precio_unitario'],
        'subtotal' => $fila['subtotal']
    ];
}
$stmt->close();

// Incluimos un header limpio, sin el menú lateral para la impresión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?php echo $detalles_factura['general']['id']; ?></title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: 'Courier New', Courier, monospace;
        }
        .invoice-container {
            max-width: 80mm; /* Ancho típico de papel térmico */
            margin: 20px auto;
            padding: 15px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-size: 12px;
            color: #000;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .invoice-header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .invoice-header h4, .invoice-header p {
            margin: 0;
        }
        .invoice-details, .invoice-items, .invoice-total {
            margin-bottom: 15px;
        }
        .invoice-details p, .invoice-total p {
            margin: 2px 0;
        }
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-items th, .invoice-items td {
            padding: 4px 0;
            text-align: left;
        }
        .invoice-items .text-right { text-align: right; }
        .invoice-items thead tr {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        .invoice-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            body { background-color: #fff; }
            .invoice-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="invoice-header">
        <img src="Drink, Drank y Drunk.jpg" alt="Logo">
        <h4>Licorería D.D.D</h4>
        <p>VIVE Y DEJA VIVIR</p>
    </div>

    <div class="invoice-details">
        <p><strong>Factura #:</strong> <?php echo $detalles_factura['general']['id']; ?></p>
        <p><strong>Fecha:</strong> <?php echo $detalles_factura['general']['fecha']->format('d/m/Y'); ?></p>
        <p><strong>Hora:</strong> <?php echo $detalles_factura['general']['fecha']->format('H:i:s A'); ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($detalles_factura['general']['cliente']); ?></p>
        <p><strong>Atendido por:</strong> <?php echo htmlspecialchars($detalles_factura['general']['empleado']); ?></p>
    </div>

    <div class="invoice-items">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles_factura['productos'] as $producto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?><br><small>C$ <?php echo number_format($producto['precio'], 2); ?></small></td>
                    <td class="text-right"><?php echo $producto['cantidad']; ?></td>
                    <td class="text-right">C$ <?php echo number_format($producto['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="invoice-total">
        <p class="text-right"><strong>TOTAL: C$ <?php echo number_format($detalles_factura['general']['total'], 2); ?></strong></p>
    </div>

    <div class="invoice-footer">
        <p>¡Gracias por su compra!</p>
    </div>

    <div class="no-print">
        <a href="empleado.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</button>
    </div>
</div>

</body>
</html>