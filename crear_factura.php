<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS (empleado o admin)
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';
$producto = null;

// 2. LÓGICA PARA PROCESAR LA VENTA (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generar_factura'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad_a_vender = (int)$_POST['cantidad_a_vender'];
    $cliente_nombre = !empty($_POST['cliente_nombre']) ? $_POST['cliente_nombre'] : 'Cliente General';
    $usuario_id = $_SESSION['usuario_id']; // Asumiendo que guardas el ID del usuario en la sesión

    // Iniciar transacción para garantizar la integridad de los datos
    $conexion->begin_transaction();

    try {
        // Bloquear la fila del producto para evitar ventas concurrentes del mismo stock
        $stmt = $conexion->prepare("SELECT nombre, precio, cantidad FROM productos WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $resultado_producto = $stmt->get_result()->fetch_assoc();

        if (!$resultado_producto) {
            throw new Exception("El producto no existe.");
        }

        if ($cantidad_a_vender <= 0) {
            throw new Exception("La cantidad debe ser mayor a cero.");
        }

        if ($resultado_producto['cantidad'] < $cantidad_a_vender) {
            throw new Exception("No hay suficiente stock. Stock disponible: " . $resultado_producto['cantidad']);
        }

        // Calcular total
        $precio_unitario = $resultado_producto['precio'];
        $total_factura = $precio_unitario * $cantidad_a_vender;

        // Insertar en la tabla `facturas`
        $stmt_factura = $conexion->prepare("INSERT INTO facturas (usuario_id, cliente_nombre, total) VALUES (?, ?, ?)");
        $stmt_factura->bind_param("isd", $usuario_id, $cliente_nombre, $total_factura);
        $stmt_factura->execute();
        $factura_id = $conexion->insert_id;

        // Insertar en la tabla `factura_detalles`
        $stmt_detalle = $conexion->prepare("INSERT INTO factura_detalles (factura_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt_detalle->bind_param("iiidd", $factura_id, $producto_id, $cantidad_a_vender, $precio_unitario, $total_factura);
        $stmt_detalle->execute();

        // Actualizar el stock del producto
        $stmt_update = $conexion->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");
        $stmt_update->bind_param("ii", $cantidad_a_vender, $producto_id);
        $stmt_update->execute();

        // Si todo fue bien, confirmar la transacción
        $conexion->commit();
        // Redirigir a la nueva página para ver/imprimir la factura
        header("Location: ver_factura.php?id=" . $factura_id);
        exit();

    } catch (Exception $e) {
        // Si algo falla, revertir la transacción
        $conexion->rollback();
        $mensaje = "<div class='alert alert-danger'>Error al generar la factura: " . $e->getMessage() . "</div>";
    }
}

// 3. OBTENER DATOS PARA MOSTRAR EL FORMULARIO (GET)
if (isset($_GET['producto_id'])) {
    $producto_id = $_GET['producto_id'];
    $stmt = $conexion->prepare("SELECT id, nombre, precio, cantidad, imagen_url FROM productos WHERE id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        if ($producto['cantidad'] <= 0) {
            $mensaje = "<div class='alert alert-warning'>Este producto no tiene stock disponible.</div>";
            $producto = null; // No mostrar el formulario si no hay stock
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Producto no encontrado.</div>";
    }
} else {
    header("Location: empleado.php");
    exit();
}

include 'empleado_header.php';
?>

<h1 class="h3 mb-4 text-gray-800">Generar Factura</h1>
<?php echo $mensaje; ?>

<?php if ($producto): ?>
<form method="POST" action="crear_factura.php">
    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalle del Producto</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="<?php echo !empty($producto['imagen_url']) ? htmlspecialchars($producto['imagen_url']) : 'img/placeholder.png'; ?>" class="img-fluid rounded" style="max-height: 150px;">
                </div>
                <div class="col-md-9">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><strong>Precio Unitario:</strong> C$ <span id="precio-unitario"><?php echo number_format($producto['precio'], 2); ?></span></p>
                    <p><strong>Stock Disponible:</strong> <?php echo $producto['cantidad']; ?></p>
                    <hr>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="cantidad_a_vender">Cantidad a Vender</label>
                            <input type="number" class="form-control" id="cantidad_a_vender" name="cantidad_a_vender" value="1" min="1" max="<?php echo $producto['cantidad']; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cliente_nombre">Nombre del Cliente (Opcional)</label>
                            <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" placeholder="Cliente General">
                        </div>
                    </div>
                    <h4 class="mt-3">Total a Pagar: C$ <span id="total-pagar" class="font-weight-bold text-success"><?php echo number_format($producto['precio'], 2); ?></span></h4>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="empleado.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" name="generar_factura" class="btn btn-success"><i class="fas fa-file-invoice-dollar"></i> Generar Factura</button>
        </div>
    </div>
</form>

<script>
document.getElementById('cantidad_a_vender').addEventListener('input', function() {
    const cantidad = this.value;
    const precio = parseFloat(document.getElementById('precio-unitario').innerText);
    const total = (cantidad * precio).toFixed(2);
    document.getElementById('total-pagar').innerText = new Intl.NumberFormat('es-NI').format(total);
});
</script>
<?php endif; ?>

<?php include 'empleado_footer.php'; ?>