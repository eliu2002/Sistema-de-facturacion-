<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

// 1. VERIFICACIÓN DE PRIVILEGIOS
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

$action = $_GET['action'] ?? '';

// 2. LÓGICA DE BÚSQUEDA
if ($action == 'search') {
    $term = $_GET['term'] ?? '';
    if (strlen($term) < 2) { // No buscar si el término es muy corto
        echo json_encode([]);
        exit();
    }

    $searchTerm = "%" . $term . "%";
    $stmt = $conexion->prepare("SELECT id, nombre, cedula_ruc FROM clientes WHERE nombre LIKE ? OR cedula_ruc LIKE ? LIMIT 10");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $clientes = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($clientes);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Acción no válida.']);