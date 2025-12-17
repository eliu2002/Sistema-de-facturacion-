<?php
session_start(); //Inicia una sesión para guardar datos del usuario (como nombre y rol).

// Si el usuario ya inició sesión, redirigirlo a su panel correspondiente.
// Esto evita que un usuario logueado vea la página de login de nuevo.
if (isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'admin':
            header('Location: admin.php');
            break;
        case 'empleado':
        default:
            header('Location: empleado.php');
            break;
    }
    exit();
}

include("conexion.php");//ncluye el archivo de conexión a la base de datos.

if ($_SERVER["REQUEST_METHOD"] == "POST") {//Verifica que el formulario se envió con método POST (cuando presionas el botón de "Ingresar").
    $email = $_POST["email"];
    $password = $_POST["password"];//Guarda el correo y contraseña que el usuario escribió en el formulario.

    // 1. Buscar al usuario solo por email.
    $stmt = $conexion->prepare("SELECT id, email, password, rol FROM usuarios WHERE email = ?");
    // Buena práctica: verificar si la preparación de la consulta falló.
    if ($stmt === false) {
        // En un entorno de producción, registrarías este error en un log.
        // Para desarrollo, podemos mostrarlo para depurar fácilmente.
        die("Error al preparar la consulta: " . htmlspecialchars($conexion->error));
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // 2. Verificar la contraseña hasheada. ¡ESTO ES CRÍTICO!
        if (password_verify($password, $usuario['password'])) {
            // Si la contraseña es correcta, creamos la sesión.
            $_SESSION["usuario"] = $usuario["email"]; // Guardamos el email como identificador del usuario.
            $_SESSION["usuario_id"] = $usuario["id"]; // Guardamos el ID del usuario.
            $_SESSION["rol"] = $usuario["rol"];

            switch ($usuario["rol"]) {
                case "admin":
                    header("Location: admin.php"); // Si es admin, va al panel de admin.
                    break;
                case "empleado":
                default:
                    header("Location: empleado.php"); // Si es empleado o cualquier otro caso, va al panel de empleado.
                    break;
            }
            exit();
        }
    }
    
    // Si el usuario no existe o la contraseña es incorrecta, muestra el mismo error.
    $error = "Usuario o contraseña incorrectos"; //Si no se encontró ningún usuario válido, muestra un mensaje de error
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D.D.D</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="Drink, Drank y Drunk.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    




</head>
<body class="bg-light">

<section class="h-100 gradient-form" style="background-color: rgba(238, 238, 238, 1);">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                    <img src="Drink, Drank y Drunk.jpg" alt="Logo" style="width: 150px; margin-bottom: 20px;">
                  <h4 class="font-effect-fire titulo-carta ">VIVE Y DEJA VIVIR</h4>
                </div>

                <?php if(isset($error)) { ?>
                  <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>

                <form method="POST">
                 

                  <div class="form-outline mb-4">
                    <input type="email" id="form2Example11" class="form-control" name="email" required />
                    <label class="form-label" for="form2Example11">Usuario</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" id="form2Example22" class="form-control" name="password" required />
                    <label class="form-label" for="form2Example22">Clave</label>
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                  
                  <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3 w-100 btn-animado" type="submit">Ingresar</button>
                    <a class="text-muted" href="info_proyecto.php">Info del Proyecto</a>
                  </div>

                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <p class="center linea-uno">"Si algo malo pasa, bebes para intentar olvidar; si algo bueno pasa, bebes para celebrar; y si nada pasa, bebes para que algo pase."</p>
                <p class="derecha">Charles Bukowski</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<style>
  .gradient-custom-2 {
    background: linear-gradient(to right, #6bb5d7e2, #06098cff);
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
