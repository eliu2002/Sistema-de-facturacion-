<?php
// --- crear_hash.php ---
// Guarda este código en un nuevo archivo llamado crear_hash.php en la misma carpeta.
// Luego, ábrelo en tu navegador (ej. http://localhost/D.D.D/licoreria/crear_hash.php)
 
$password_plano = 'empleado456'; // <-- CAMBIA ESTO por la contraseña que quieres usar para tu empleado

// Generamos el hash
$hash = password_hash($password_plano, PASSWORD_DEFAULT);

echo "<h1>Generador de Hash de Contraseña</h1>";
echo "<p><strong>Contraseña en texto plano:</strong> " . htmlspecialchars($password_plano) . "</p>";
echo "<p><strong>Hash generado (cópialo y pégalo en tu base de datos):</strong></p>";
echo "<textarea rows='4' cols='80' readonly>" . htmlspecialchars($hash) . "</textarea>";

?>