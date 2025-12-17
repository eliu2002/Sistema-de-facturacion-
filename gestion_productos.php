<?php
session_start();

// 1. VERIFICACIÓN DE PRIVILEGIOS (solo admin puede gestionar productos)
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'admin') {
    echo "<script>alert('Acceso denegado. Debes ser administrador.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';
$mensaje = '';
$upload_dir = 'uploads/products/';

// Asegurarse de que el directorio de subida exista
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 2. LÓGICA PARA AGREGAR UN NUEVO PRODUCTO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $categoria = $_POST['categoria']; // Capturar la nueva categoría
    $imagen_url = '';

    // Manejo de la subida de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen_nombre = uniqid() . '-' . basename($_FILES["imagen"]["name"]);
        $target_file = $upload_dir . $imagen_nombre;

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            $imagen_url = $target_file;
        } else {
            $mensaje = "<div class='alert alert-danger'>Hubo un error al subir la imagen.</div>";
        }
    }

    if (empty($mensaje)) {
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, categoria, descripcion, precio, cantidad, imagen_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdis", $nombre, $categoria, $descripcion, $precio, $cantidad, $imagen_url);

        if ($stmt->execute()) {
            header("Location: gestion_productos.php?status=success");
            exit();
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al agregar el producto: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// 3. LÓGICA PARA ELIMINAR UN PRODUCTO
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];

    // Opcional: Eliminar el archivo de imagen del servidor
    $stmt_select = $conexion->prepare("SELECT imagen_url FROM productos WHERE id = ?");
    $stmt_select->bind_param("i", $id_eliminar);
    $stmt_select->execute();
    $resultado_img = $stmt_select->get_result()->fetch_assoc();
    if ($resultado_img && !empty($resultado_img['imagen_url']) && file_exists($resultado_img['imagen_url'])) {
        unlink($resultado_img['imagen_url']);
    }
    $stmt_select->close();

    // Eliminar el registro de la base de datos
    $stmt_delete = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt_delete->bind_param("i", $id_eliminar);
    if ($stmt_delete->execute()) {
        echo "<script>window.location='gestion_productos.php';</script>"; // Redirigir para limpiar la URL
        exit();
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al eliminar el producto.</div>";
    }
    $stmt_delete->close();
}

// 5. MANEJO DE MENSAJES DE ESTADO (PARA PRG)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $mensaje = "<div class='alert alert-success'>Producto agregado exitosamente. El formulario está listo para el siguiente.</div>";
    } elseif ($_GET['status'] == 'updated') {
        $mensaje = "<div class='alert alert-success'>Producto actualizado exitosamente.</div>";
    }
}

// 4. OBTENER TODOS LOS PRODUCTOS PARA MOSTRARLOS
$resultado = $conexion->query("SELECT id, nombre, categoria, descripcion, precio, cantidad, imagen_url FROM productos ORDER BY id DESC");

// Incluimos el header de la plantilla de admin
include 'admin_header.php';
?>

<!-- Título de la Página -->
<h1 class="h3 mb-2 text-gray-800">Gestión de Inventario y Productos</h1>
<p class="mb-4">Aquí puedes agregar, editar y eliminar los productos de la licorería.</p>

<?php echo $mensaje; // Muestra mensajes de éxito o error ?>

<!-- Formulario para agregar nuevos productos -->
<div class="card shadow mb-4">
    <div class="card-header py-3" data-toggle="collapse" href="#collapse-agregar" role="button" aria-expanded="true" aria-controls="collapse-agregar">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Nuevo Producto</h6>
    </div>
    <div class="collapse show" id="collapse-agregar">
        <div class="card-body">
        <form method="POST" action="gestion_productos.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Ron Añejo" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria" class="form-control" required>
                        <option value="" disabled selected>Seleccione un tipo...</option>
                        <option value="Cerveza">Cerveza</option>
                        <option value="Tequila">Tequila</option>
                        <option value="Whisky">Whisky</option>
                        <option value="Vodka">Vodka</option>
                        <option value="Ron">Ron</option>
                        <option value="Vino">Vino</option>
                        <option value="Bebida alcohólica con sabor">Bebida alcohólica con sabor</option>
                        <option value="Bebida">Bebida (No alcohólica)</option>
                        <option value="Snack">Snack</option>
                        <option value="Otros">Otros</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Breve descripción del producto"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="precio">Precio (C$)</label>
                    <input type="number" id="precio" name="precio" class="form-control" placeholder="0.00" step="0.01" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="cantidad">Cantidad en Inventario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('cantidad').stepDown()">-</button>
                        </div>
                        <input type="number" id="cantidad" name="cantidad" class="form-control text-center" value="1" min="0" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('cantidad').stepUp()">+</button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="imagen">Imagen del Producto</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
                        <label class="custom-file-label" for="imagen">Elegir archivo...</label>
                    </div>
                </div>
            </div>
            <button type="submit" name="agregar_producto" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Agregar Producto
            </button>
        </form>
        </div>
    </div>
</div>

<!-- Catálogo de productos existentes -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Catálogo de Productos</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($resultado, 0); // Reiniciar puntero del resultado ?>
                    <?php while ($fila = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td class="text-center">
                            <img src="<?php echo !empty($fila['imagen_url']) ? htmlspecialchars($fila['imagen_url']) : 'img/placeholder.png'; ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['categoria'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                        <td>C$ <?php echo number_format($fila['precio'], 2); ?></td>
                        <td><?php echo $fila['cantidad']; ?></td>
                        <td>
                            <a href="editar_producto.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="gestion_productos.php?eliminar=<?php echo $fila['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');" title="Eliminar"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Script para mostrar el nombre del archivo en el input de subida
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'Elegir archivo...';
    e.target.nextElementSibling.innerText = fileName;
});
</script>

<?php include 'admin_footer.php'; ?>