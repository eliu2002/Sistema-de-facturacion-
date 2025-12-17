<?php
session_start();
session_unset();
session_destroy();  // Cierra la sesión del usuario
header("Location: index.php");  // Redirige al usuario a la página de inicio 
exit();  // Asegura que no se ejecute más código después de redirigir
?>