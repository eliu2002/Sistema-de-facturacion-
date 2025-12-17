<?php
session_start();
// 1. VERIFICACIÓN DE PRIVILEGIOS
// Solo los administradores pueden acceder a esta página.
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php"); // Redirige a la página de login si no es admin
    exit();
}

require 'conexion.php';
$mensaje = '';

// 2. PROCESAMIENTO DEL FORMULARIO
// Se ejecuta solo cuando el formulario se envía (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // 3. SEGURIDAD: HASHEAR LA CONTRASEÑA
    // NUNCA guardes contraseñas en texto plano.
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // 4. PREVENCIÓN DE INYECCIÓN SQL: CONSULTA PREPARADA
    $sql = "INSERT INTO usuarios (email, password, rol) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    // "sss" significa que los 3 parámetros son de tipo string (cadena)
    $stmt->bind_param("sss", $email, $password_hashed, $rol);

    if ($stmt->execute()) {
        // Si la creación es exitosa, redirige de vuelta al panel de admin
        header("Location: admin.php");
        exit();
    } else {
        // Manejo de errores, por ejemplo, si el usuario ya existe
        if ($conexion->errno == 1062) { // Código de error para "entrada duplicada"
            $mensaje = "Error: El email '$email' ya está registrado.";
        } else {
            $mensaje = "Error al crear el usuario: " . $stmt->error;
        }
    }
    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3>Crear Nuevo Usuario</h3></div>
                <div class="card-body">
                    <?php if(!empty($mensaje)): ?>
                        <div class="alert alert-danger"><?php echo $mensaje; ?></div>
                    <?php endif; ?>
                    <!-- 5. FORMULARIO HTML -->
                    <form action="crear_usuario.php" method="post">
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" required></div>
                        <div class="mb-3"><label for="password" class="form-label">Contraseña</label><input type="password" class="form-control" id="password" name="password" required></div>
                        <div class="mb-3"><label for="rol" class="form-label">Rol</label><select class="form-select" id="rol" name="rol" required><option value="">Seleccione un rol...</option><option value="admin">Administrador</option><option value="empleado">Empleado</option></select></div>
                        <button type="submit" class="btn btn-success">Crear Usuario</button>
                        <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
