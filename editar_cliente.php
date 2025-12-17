<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';
$cliente = null;

// 2. OBTENER ID DEL CLIENTE
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: gestion_clientes.php");
    exit();
}
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// 3. PROCESAR FORMULARIO DE ACTUALIZACIÓN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $cedula_ruc = !empty($_POST['cedula_ruc']) ? $_POST['cedula_ruc'] : null;
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : null;
    $direccion = !empty($_POST['direccion']) ? $_POST['direccion'] : null;

    $stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, cedula_ruc = ?, telefono = ?, direccion = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nombre, $cedula_ruc, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        header("Location: gestion_clientes.php?status=updated");
        exit();
    } else {
        if ($conexion->errno == 1062) {
            $mensaje = "<div class='alert alert-danger'>Error: La cédula o RUC ya está registrada para otro cliente.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar el cliente: " . $stmt->error . "</div>";
        }
    }
    $stmt->close();
}

// 4. OBTENER DATOS DEL CLIENTE PARA MOSTRAR EN EL FORMULARIO (GET)
$stmt = $conexion->prepare("SELECT id, nombre, cedula_ruc, telefono, direccion FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows > 0) {
    $cliente = $resultado->fetch_assoc();
} else {
    $mensaje = "<div class='alert alert-warning'>Cliente no encontrado.</div>";
}
$stmt->close();

include 'admin_header.php';
?>

<h1 class="h3 mb-4 text-gray-800">Editar Cliente</h1>
<?php echo $mensaje; ?>

<?php if ($cliente): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Editando: <?php echo htmlspecialchars($cliente['nombre']); ?></h6>
    </div>
    <div class="card-body">
        <form method="POST" action="editar_cliente.php">
            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6"><label for="nombre">Nombre Completo</label><input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required></div>
                <div class="form-group col-md-6"><label for="cedula_ruc">Cédula o RUC</label><input type="text" id="cedula_ruc" name="cedula_ruc" class="form-control" value="<?php echo htmlspecialchars($cliente['cedula_ruc'] ?? ''); ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6"><label for="telefono">Teléfono</label><input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>"></div>
                <div class="form-group col-md-6"><label for="direccion">Dirección</label><input type="text" id="direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>"></div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
                <a href="gestion_clientes.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'admin_footer.php'; ?>