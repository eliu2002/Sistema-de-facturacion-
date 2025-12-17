<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS (solo admin puede gestionar clientes)
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado. Debes ser administrador.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';

// 2. LÓGICA PARA AGREGAR UN NUEVO CLIENTE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_cliente'])) {
    $nombre = $_POST['nombre'];
    $cedula_ruc = !empty($_POST['cedula_ruc']) ? $_POST['cedula_ruc'] : null;
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : null;
    $direccion = !empty($_POST['direccion']) ? $_POST['direccion'] : null;

    $stmt = $conexion->prepare("INSERT INTO clientes (nombre, cedula_ruc, telefono, direccion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $cedula_ruc, $telefono, $direccion);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Cliente agregado exitosamente.</div>";
    } else {
        if ($conexion->errno == 1062) { // Error de entrada duplicada para cedula_ruc
            $mensaje = "<div class='alert alert-danger'>Error: La cédula o RUC ya está registrada.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al agregar el cliente: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
    $stmt->close();
}

// 3. LÓGICA PARA ELIMINAR UN CLIENTE
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    // La FK en `facturas` está como ON DELETE SET NULL, así que no hay problema al eliminar.
    $stmt_delete = $conexion->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt_delete->bind_param("i", $id_eliminar);
    if ($stmt_delete->execute()) {
        header("Location: gestion_clientes.php?status=deleted");
        exit();
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al eliminar el cliente.</div>";
    }
    $stmt_delete->close();
}

// 4. MANEJO DE MENSAJES DE ESTADO
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'updated') {
        $mensaje = "<div class='alert alert-success'>Cliente actualizado exitosamente.</div>";
    } elseif ($_GET['status'] == 'deleted') {
        $mensaje = "<div class='alert alert-info'>Cliente eliminado.</div>";
    }
}

// 5. OBTENER TODOS LOS CLIENTES PARA MOSTRARLOS
$resultado = $conexion->query("SELECT id, nombre, cedula_ruc, telefono, direccion, fecha_creacion FROM clientes ORDER BY nombre ASC");

include 'admin_header.php';
?>

<!-- Título de la Página -->
<h1 class="h3 mb-2 text-gray-800">Gestión de Clientes</h1>
<p class="mb-4">Administra la información de los clientes registrados en el sistema.</p>

<?php echo $mensaje; ?>

<!-- Formulario para agregar nuevos clientes -->
<div class="card shadow mb-4">
    <div class="card-header py-3" data-toggle="collapse" href="#collapse-agregar" role="button" aria-expanded="true" aria-controls="collapse-agregar">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Nuevo Cliente</h6>
    </div>
    <div class="collapse show" id="collapse-agregar">
        <div class="card-body">
        <form method="POST" action="gestion_clientes.php">
            <div class="form-row">
                <div class="form-group col-md-6"><label for="nombre">Nombre Completo</label><input type="text" id="nombre" name="nombre" class="form-control" required></div>
                <div class="form-group col-md-6"><label for="cedula_ruc">Cédula o RUC</label><input type="text" id="cedula_ruc" name="cedula_ruc" class="form-control"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6"><label for="telefono">Teléfono</label><input type="text" id="telefono" name="telefono" class="form-control"></div>
                <div class="form-group col-md-6"><label for="direccion">Dirección</label><input type="text" id="direccion" name="direccion" class="form-control"></div>
            </div>
            <button type="submit" name="agregar_cliente" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Agregar Cliente</button>
        </form>
        </div>
    </div>
</div>

<!-- Lista de clientes existentes -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Clientes</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula/RUC</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['cedula_ruc'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($fila['telefono'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($fila['direccion'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="editar_cliente.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="gestion_clientes.php?eliminar=<?php echo $fila['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente? Se mantendrá el historial de facturas, pero se desvincularán de este cliente.');" title="Eliminar"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>