<?php
session_start();

// Verificación de sesión y rol. Solo el admin puede ver esta página.
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    echo "<script>alert('Acceso denegado. Debes iniciar sesión como Administrador'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

$mensaje = '';

// LÓGICA PARA AGREGAR UN NUEVO USUARIO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_usuario'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt_add = $conexion->prepare("INSERT INTO usuarios (email, password, rol) VALUES (?, ?, ?)");
    $stmt_add->bind_param("sss", $email, $password_hashed, $rol);

    if ($stmt_add->execute()) {
        header("Location: gestion_usuarios.php");
        exit();
    } else {
        if ($conexion->errno == 1062) {
            $mensaje = "<div class='alert alert-danger'>Error: El email '$email' ya está registrado.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al crear el usuario: " . $stmt_add->error . "</div>";
        }
    }
}

// LÓGICA PARA ELIMINAR UN USUARIO
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    // Prevenir que un admin se elimine a sí mismo
    if ($id_eliminar == $_SESSION['usuario_id']) {
        $mensaje = "<div class='alert alert-danger'>No puedes eliminar tu propia cuenta.</div>";
    } else {
        $stmt_delete = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt_delete->bind_param("i", $id_eliminar);
        if ($stmt_delete->execute()) {
            header("Location: gestion_usuarios.php"); // Redirigir para limpiar la URL
            exit();
        }
    }
}

// Obtener todos los usuarios para mostrarlos en la tabla
$resultado = $conexion->query("SELECT id, email, rol FROM usuarios");

include 'admin_header.php';
?>

<!-- Título de la Página -->
<h1 class="h3 mb-4 text-gray-800">Gestión de Usuarios y Roles</h1>

<?php echo $mensaje; // Muestra mensajes de éxito o error ?>

<!-- Formulario para agregar nuevos usuarios -->
<div class="card shadow mb-4">
    <div class="card-header py-3" data-toggle="collapse" href="#collapse-agregar-usuario" role="button" aria-expanded="false" aria-controls="collapse-agregar-usuario">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Nuevo Usuario</h6>
    </div>
    <div class="collapse show" id="collapse-agregar-usuario">
        <div class="card-body">
            <form method="POST" action="gestion_usuarios.php">
                <div class="form-row">
                    <div class="col-md-4 mb-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                    <div class="col-md-3 mb-2"><input type="password" name="password" class="form-control" placeholder="Contraseña" required></div>
                    <div class="col-md-3 mb-2">
                        <select name="rol" class="form-control" required>
                            <option value="" disabled selected>Seleccionar rol...</option>
                            <option value="empleado">Empleado</option>
                            <option value="consulta">Consulta</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2"><button type="submit" name="agregar_usuario" class="btn btn-primary btn-block">Agregar</button></div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabla con la lista de usuarios existentes -->
<div class="card shadow mb-4">
    <div class="card-header py-3" data-toggle="collapse" href="#collapse-lista-usuarios" role="button" aria-expanded="true" aria-controls="collapse-lista-usuarios">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Usuarios</h6>
    </div>
    <div class="collapse show" id="collapse-lista-usuarios">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $fila['id']; ?></td>
                            <td><?php echo htmlspecialchars($fila['email']); ?></td>
                            <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <?php if ($fila['id'] != $_SESSION['usuario_id']): // No permitir auto-eliminación ?>
                                <a href="gestion_usuarios.php?eliminar=<?php echo $fila['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
