<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado. Debes ser administrador.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';
$producto = null;
$upload_dir = 'uploads/products/';

// 2. OBTENER ID DEL PRODUCTO
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header("Location: gestion_productos.php");
    exit();
}
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// 3. PROCESAR FORMULARIO DE ACTUALIZACIÓN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $categoria_id = $_POST['categoria_id'];
    $imagen_actual = $_POST['imagen_actual'];
    $imagen_url = $imagen_actual;

    // Manejo de la nueva imagen si se sube una
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        // Eliminar la imagen anterior si existe
        if (!empty($imagen_actual) && file_exists($imagen_actual)) {
            unlink($imagen_actual);
        }

        $imagen_nombre = uniqid() . '-' . basename($_FILES["imagen"]["name"]);
        $target_file = $upload_dir . $imagen_nombre;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            $imagen_url = $target_file;
        } else {
            $mensaje = "<div class='alert alert-danger'>Hubo un error al subir la nueva imagen.</div>";
        }
    }

    if (empty($mensaje)) {
        $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, cantidad = ?, imagen_url = ? WHERE id = ?");
        $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $cantidad, $imagen_url, $id);

        if ($stmt->execute()) {
            header("Location: gestion_productos.php?status=updated");
            exit();
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar el producto: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// 4. OBTENER DATOS DEL PRODUCTO PARA MOSTRAR EN EL FORMULARIO (GET)
$stmt = $conexion->prepare("SELECT id, nombre, descripcion, precio, cantidad, imagen_url FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows > 0) {
    $producto = $resultado->fetch_assoc();
} else {
    $mensaje = "<div class='alert alert-warning'>Producto no encontrado.</div>";
}
$stmt->close();

include 'admin_header.php';
?>

<h1 class="h3 mb-4 text-gray-800">Editar Producto</h1>
<?php echo $mensaje; ?>

<?php if ($producto): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Editando: <?php echo htmlspecialchars($producto['nombre']); ?></h6>
    </div>
    <div class="card-body">
        <form method="POST" action="editar_producto.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto['imagen_url']); ?>">
            
            <div class="form-group">
                <label for="nombre">Nombre del Producto</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4"><label for="precio">Precio (C$)</label><input type="number" id="precio" name="precio" class="form-control" value="<?php echo $producto['precio']; ?>" step="0.01" required></div>
                <div class="form-group col-md-4"><label for="cantidad">Cantidad</label><input type="number" id="cantidad" name="cantidad" class="form-control" value="<?php echo $producto['cantidad']; ?>" min="0" required></div>
                <div class="form-group col-md-4"><label for="imagen">Cambiar Imagen</label><div class="custom-file"><input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*"><label class="custom-file-label" for="imagen">Elegir archivo...</label></div></div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
                <a href="gestion_productos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'admin_footer.php'; ?>