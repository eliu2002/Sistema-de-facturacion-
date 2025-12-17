<?php
// pos_sidebar.php

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_general = 0;
?>

<!-- POS Sidebar -->
<div class="col-lg-4">
    <div class="card shadow mb-4 sticky-top" style="top: 80px;">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-cart"></i> Carrito de Venta</h6>
            <button id="clear-cart-btn" class="btn btn-danger btn-sm" title="Vaciar carrito" <?php echo empty($cart_items) ? 'disabled' : ''; ?>>
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
        <div class="card-body" style="max-height: 50vh; overflow-y: auto;" id="cart-body">
            <?php if (empty($cart_items)): ?>
                <div id="cart-empty-msg" class="text-center text-muted">
                    <p><i class="fas fa-cart-plus fa-3x mb-3"></i></p>
                    <p>El carrito está vacío.</p>
                    <p class="small">Agrega productos desde el catálogo.</p>
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush" id="cart-item-list">
                <?php
                // Necesitamos una consulta para obtener los detalles actuales de los productos en el carrito
                if (!empty($cart_items)) {
                    $product_ids = implode(',', array_keys($cart_items));
                    $cart_products_result = $conexion->query("SELECT id, nombre, precio, imagen_url FROM productos WHERE id IN ($product_ids)");
                    $cart_products = [];
                    while ($p = $cart_products_result->fetch_assoc()) {
                        $cart_products[$p['id']] = $p;
                    }

                    foreach ($cart_items as $id => $item) {
                        if (!isset($cart_products[$id])) continue; // El producto ya no existe, omitir
                        $product = $cart_products[$id];
                        $subtotal = $item['quantity'] * $product['precio'];
                        $total_general += $subtotal;
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-product-id="<?php echo $id; ?>">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo !empty($product['imagen_url']) ? htmlspecialchars($product['imagen_url']) : 'img/placeholder.png'; ?>" width="40" class="mr-3 rounded">
                            <div>
                                <h6 class="my-0"><?php echo htmlspecialchars($product['nombre']); ?></h6>
                                <small class="text-muted">C$ <?php echo number_format($product['precio'], 2); ?></small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="number" class="form-control form-control-sm update-quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px; text-align: center;">
                            <button class="btn btn-sm btn-outline-danger remove-item-btn ml-2"><i class="fas fa-times"></i></button>
                        </div>
                    </li>
                <?php
                    }
                }
                ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between font-weight-bold">
                <span>TOTAL:</span>
                <span id="cart-total">C$ <?php echo number_format($total_general, 2); ?></span>
            </div>
            <hr>
            <form action="checkout.php" method="POST" id="checkout-form">
                <div class="form-group">
                    <label for="cliente_search">Nombre del Cliente (Opcional)</label>
                    <input type="text" class="form-control" id="cliente_search" name="cliente_nombre" placeholder="Escribe nombre, cédula o deja en blanco" autocomplete="off">
                    <div id="cliente-suggestions" class="list-group" style="position: absolute; z-index: 1000; width: 95%;"></div>
                    <input type="hidden" id="cliente_id" name="cliente_id">
                </div>
                <button type="submit" id="checkout-btn" class="btn btn-success btn-block" <?php echo empty($cart_items) ? 'disabled' : ''; ?>>
                    <i class="fas fa-file-invoice-dollar"></i> Proceder a Facturar
                </button>
            </form>
        </div>
    </div>
</div>