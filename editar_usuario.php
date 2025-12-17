<?php
session_start();
// 1. VERIFICACIÓN DE PRIVILEGIOS
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';
$mensaje = '';
$usuario_a_editar = null;

// 2. PROCESAMIENTO DEL FORMULARIO (CUANDO SE ENVÍA CON POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    // Si la contraseña no está vacía, la actualizamos. Si no, la ignoramos.
    if (!empty($password)) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET email = ?, rol = ?, password = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $email, $rol, $password_hashed, $id);
    } else {
        $sql = "UPDATE usuarios SET email = ?, rol = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssi", $email, $rol, $id);
    }

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        $mensaje = "Error al actualizar el usuario: " . $stmt->error;
    }
    $stmt->close();
}

// 3. OBTENER DATOS DEL USUARIO PARA MOSTRAR EN EL FORMULARIO (CUANDO SE CARGA LA PÁGINA CON GET)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, email, rol FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $usuario_a_editar = $resultado->fetch_assoc();
    } else {
        $mensaje = "Usuario no encontrado.";
    }
    $stmt->close();
} else {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3>Editar Usuario</h3></div>
                <div class="card-body">
                    <?php if(!empty($mensaje)): ?><div class="alert alert-danger"><?php echo $mensaje; ?></div><?php endif; ?>
                    <?php if($usuario_a_editar): ?>
                    <form action="editar_usuario.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $usuario_a_editar['id']; ?>">
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario_a_editar['email']); ?>" required></div>
                        <div class="mb-3"><label for="password" class="form-label">Nueva Contraseña</label><input type="password" class="form-control" id="password" name="password"><div class="form-text">Dejar en blanco para no cambiar la contraseña actual.</div></div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="admin" <?php echo ($usuario_a_editar['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="empleado" <?php echo ($usuario_a_editar['rol'] == 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Actualizar Usuario</button>
                        <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
