<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if (!filter_var($product_id, FILTER_VALIDATE_INT) || $product_id <= 0) {
    if ($action !== 'clear' && $action !== 'get') {
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido.']);
        exit();
    }
}

try {
    switch ($action) {
        case 'add':
            $stmt = $conexion->prepare("SELECT cantidad FROM productos WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                throw new Exception("Producto no encontrado.");
            }

            $current_stock = $result['cantidad'];
            $quantity_in_cart = isset($_SESSION['cart'][$product_id]['quantity']) ? $_SESSION['cart'][$product_id]['quantity'] : 0;

            if (($quantity_in_cart + 1) > $current_stock) {
                throw new Exception("No hay suficiente stock para agregar más unidades de este producto.");
            }

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                $_SESSION['cart'][$product_id] = ['quantity' => 1];
            }
            break;

        case 'update':
            if (isset($_SESSION['cart'][$product_id])) {
                $stmt = $conexion->prepare("SELECT cantidad FROM productos WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                if ($quantity > $result['cantidad']) {
                    throw new Exception("Stock insuficiente. Disponible: " . $result['cantidad']);
                }
                $_SESSION['cart'][$product_id]['quantity'] = max(1, (int)$quantity);
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            break;

        default:
            throw new Exception("Acción no válida.");
    }

    // Devolver siempre el estado del carrito para actualizar el contador de tipos de producto
    echo json_encode(['success' => true, 'message' => 'Carrito actualizado.', 'cart' => $_SESSION['cart']]);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>