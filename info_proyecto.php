<?php
// info_proyecto.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Proyecto - D.D.D</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="Drink, Drank y Drunk.jpg" type="image/x-icon">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .container {
            max-width: 800px;
        }
        .card-header {
            background: linear-gradient(to right, #6bb5d7e2, #06098cff);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header text-center p-4">
                <h1>Información del Proyecto</h1>
            </div>
            <div class="card-body p-4">
                <h5 class="card-title">Tipo de Proyecto</h5>
                <p class="card-text">
                    Sistema de Punto de Venta (POS) y Gestión para la Licorería "D.D.D". Desarrollado con PHP, MySQL, y Bootstrap.
                </p>

                <h5 class="card-title mt-4">Fecha de Realización</h5>
                <p class="card-text">
                    El proyecto fue desarrollado y finalizado en <strong>24 septiembre hasta el 25 de octubre</strong>.
                </p>
                <h5 class="card-title mt-4">Tiempo de Desarrollo</h5>
                <p class="card-text">
                    El tiempo total invertido en el desarrollo, desde la planificación hasta la implementación, fue de <strong>2 meses y 15 días</strong>.
                </p>

                <h5 class="card-title mt-4">Desarrollado por</h5>
                <p class="card-text mb-1">
                    <strong>Nombre:</strong> Eliú Peñalba Montoya
                </p>
                <p class="card-text mb-1">
                    <strong>Carnet:</strong> 20-01817-1
                </p>
                <p class="card-text">
                    <strong>Carrera:</strong> Ingeniería en Tecnología de la Información con énfasis en Desarrollo Web y Marketing.<br>
                    <small class="text-muted">Facultad de Ciencias y Tecnología.</small>
                </p>

                <h5 class="card-title mt-4">Metodología de Desarrollo</h5>
                <p class="card-text">
                    El proyecto se desarrolló siguiendo un <strong>enfoque Ágil de Desarrollo Incremental</strong>. Las funcionalidades se construyeron y entregaron en ciclos cortos y rápidos, permitiendo una adaptación constante a nuevos requisitos y la mejora continua del software a través de la refactorización y el feedback directo.
                </p>

                <h5 class="card-title mt-4">Función del Sistema</h5>
                <p class="card-text">
                    La aplicación permite gestionar las operaciones clave de la licorería, incluyendo:
                </p>
                <ul>
                    <li>Gestión de inventario (productos, stock, precios).</li>
                    <li>Registro de ventas y generación de facturas.</li>
                    <li>Control de usuarios con roles (Administrador, Empleado).</li>
                    <li>Manejo de turnos de caja (apertura, cierre, arqueo).</li>
                    <li>Generación de reportes de ventas y respaldos de la base de datos.</li>
                </ul>
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary">Volver al Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>