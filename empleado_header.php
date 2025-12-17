<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Headers para prevenir que el navegador guarde la página en caché.
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Si el usuario ya no tiene una sesión activa, se le redirige a la página de inicio.
// Esto previene que pueda ver páginas protegidas usando el botón "Atrás" del navegador.
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

// Contar el total de productos en el carrito
$cart_item_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Panel de Empleado - D.D.D</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<style>
    /* Estilos para el Modo Oscuro */
    body.dark-mode {
        background-color: #2c2f33;
        color: #f8f9fc;
    }
    .dark-mode #content-wrapper,
    .dark-mode .sticky-footer,
    .dark-mode .card,
    .dark-mode .modal-content,
    .dark-mode .table {
        background-color: #36393f;
        color: #f8f9fc;
    }
    .dark-mode .navbar,
    .dark-mode .card-header,
    .dark-mode .card-footer,
    .dark-mode .modal-header,
    .dark-mode .modal-footer {
        background-color: #40444b !important;
        border-color: #2c2f33 !important;
    }
    .dark-mode .text-gray-800,
    .dark-mode .text-primary,
    .dark-mode .text-gray-900,
    .dark-mode .text-dark,
    .dark-mode .dropdown-item,
    .dark-mode .h1, .dark-mode .h2, .dark-mode .h3, .dark-mode .h4, .dark-mode .h5, .dark-mode .h6 {
        color: #f8f9fc !important;
    }
    .dark-mode .text-gray-600, .dark-mode .text-gray-500, .dark-mode .text-muted {
        color: #b7b9cc !important;
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="empleado.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-wine-glass-alt"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Licorería D.D.D</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="empleado.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Ventas</div>
            <li class="nav-item">
                <a class="nav-link" href="vender.php">
                    <i class="fas fa-fw fa-cash-register"></i>
                    <span>Vender</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="carrito.php">
                    <i class="fas fa-fw fa-shopping-cart"></i><span>Carrito</span>
                    <?php if ($cart_item_count > 0): ?>
                        <span id="cart-badge" class="badge badge-danger ml-2"><?php echo $cart_item_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lista_facturas.php">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Historial de Facturas</span></a>
            </li>
            <!-- Nav Item - Gestión de Turno -->
            <li class="nav-item">
                <a class="nav-link" href="gestion_turno.php">
                    <i class="fas fa-fw fa-business-time"></i>
                    <span>Gestión de Turno</span></a>
            </li>
            <!-- Nav Item - Historial de Turnos -->
            <li class="nav-item">
                <a class="nav-link" href="historial_turnos.php">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Historial de Turnos</span></a>
            </li>

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Reloj y Modo Oscuro -->
                        <li class="nav-item">
                            <div class="nav-link">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="dark-mode-toggle">
                                    <label class="custom-control-label" for="dark-mode-toggle"><i class="fas fa-moon"></i></label>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-gray-600" href="#" role="button">
                                <i class="fas fa-clock fa-fw"></i>
                                <span id="live-clock"></span>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['usuario']; ?></span>
                                <i class="fas fa-user-circle fa-lg"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                                <a class="dropdown-item" href="admin.php">
                                    <i class="fas fa-user-shield fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Panel de Admin
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php endif; ?>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Cerrar Sesión</a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">