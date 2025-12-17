<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["rol"], ['admin', 'empleado'])) {
    echo "<script>alert('Acceso denegado.'); window.location='index.php';</script>";
    exit();
}

require 'conexion.php';

// --- VERIFICACIÓN DE TURNO ABIERTO ---
$query_turno = "SELECT id FROM turnos_caja WHERE estado = 'abierto' LIMIT 1";
$resultado_turno = $conexion->query($query_turno);
if ($resultado_turno->num_rows === 0) {
    echo "<script>alert('No hay un turno de caja abierto. Debes iniciar un turno para poder vender.'); window.location='gestion_turno.php';</script>";
    exit();
}
// --- FIN VERIFICACIÓN DE TURNO ---

include 'empleado_header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_general = 0;
?>

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Carrito de Venta</h1>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-cart"></i> Productos en el Carrito</h6>
                <?php if (!empty($cart_items)): ?>
                <button id="clear-cart-btn" class="btn btn-danger btn-sm" title="Vaciar carrito">
                    <i class="fas fa-trash-alt"></i> Vaciar Carrito
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($cart_items)): ?>
                    <div class="text-center text-muted py-5">
                        <p><i class="fas fa-cart-plus fa-4x mb-3"></i></p>
                        <h4>El carrito está vacío.</h4>
                        <p>Agrega productos desde el panel de ventas.</p>
                        <a href="vender.php" class="btn btn-primary mt-3">Ir a Vender</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th colspan="2">Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-right">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-item-list">
                            <?php
                            $product_ids = implode(',', array_keys($cart_items));
                            $cart_products_result = $conexion->query("SELECT id, nombre, precio, imagen_url, cantidad as stock FROM productos WHERE id IN ($product_ids)");
                            $cart_products = [];
                            while ($p = $cart_products_result->fetch_assoc()) {
                                $cart_products[$p['id']] = $p;
                            }

                            foreach ($cart_items as $id => $item):
                                if (!isset($cart_products[$id])) continue;
                                $product = $cart_products[$id];
                                $subtotal = $item['quantity'] * $product['precio'];
                                $total_general += $subtotal;
                            ?>
                                <tr data-product-id="<?php echo $id; ?>">
                                    <td style="width: 80px;">
                                        <img src="<?php echo !empty($product['imagen_url']) ? htmlspecialchars($product['imagen_url']) : 'img/placeholder.png'; ?>" width="60" class="rounded">
                                    </td>
                                    <td>
                                        <h6 class="my-0"><?php echo htmlspecialchars($product['nombre']); ?></h6>
                                        <small class="text-muted">Precio: C$ <?php echo number_format($product['precio'], 2); ?></small>
                                    </td>
                                    <td class="text-center" style="width: 150px;">
                                        <input type="number" class="form-control form-control-sm update-quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $product['stock']; ?>">
                                    </td>
                                    <td class="text-right font-weight-bold" style="width: 150px;">
                                        C$ <?php echo number_format($subtotal, 2); ?>
                                    </td>
                                    <td class="text-center" style="width: 50px;">
                                        <button class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($cart_items)): ?>
            <div class="card-footer">
                <form action="checkout.php" method="POST" id="checkout-form">
                    <div class="row">
                        <!-- Columna de Cliente y Monto Pagado -->
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="cliente_search">Nombre del Cliente (Opcional)</label>
                                <input type="text" class="form-control" id="cliente_search" name="cliente_nombre" placeholder="Escribe nombre, cédula o deja en blanco" autocomplete="off">
                                <div id="cliente-suggestions" class="list-group" style="position: absolute; z-index: 1000; width: 95%;"></div>
                                <input type="hidden" id="cliente_id" name="cliente_id">
                            </div>
                            <div class="form-group">
                                <label for="monto_recibido">Monto Recibido (C$)</label>
                                <input type="number" class="form-control form-control-lg" id="monto_recibido" placeholder="0.00" step="0.01">
                            </div>
                            <div class="mb-3">
                                <small>Sugerencias de pago:</small><br>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="<?php echo ceil($total_general); ?>">Exacto</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="100">C$ 100</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="200">C$ 200</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="500">C$ 500</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary quick-cash" data-amount="1000">C$ 1000</button>
                            </div>
                        </div>
                        <!-- Columna de Totales y Facturación -->
                        <div class="col-md-5 d-flex flex-column justify-content-between">
                            <div class="text-right mb-3">
                                <h4>Total a Pagar</h4>
                                <h2 class="font-weight-bold text-success">C$ <span id="total-a-pagar"><?php echo number_format($total_general, 2); ?></span></h2>
                                <hr>
                                <h4>Cambio</h4>
                                <h2 class="font-weight-bold text-info">C$ <span id="cambio-a-dar">0.00</span></h2>
                            </div>
                            <div>
                                <button type="submit" id="checkout-btn" class="btn btn-success btn-lg btn-block" disabled>
                                    <i class="fas fa-file-invoice-dollar"></i> Finalizar e Imprimir Factura
                                </button>
                                <small id="checkout-btn-msg" class="form-text text-danger text-center mt-1">Ingrese un monto mayor o igual al total para activar.</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'empleado_footer.php'; ?>

<!-- Incluimos jQuery para AJAX -->
<script src="vendor/jquery/jquery.min.js"></script>
<script>
const totalAPagar = <?php echo $total_general; ?>;

document.addEventListener('DOMContentLoaded', function() {

    function handleCartAction(action, productId, quantity = 1, showSuccessAlert = false) {
        $.ajax({
            url: 'cart_handler.php',
            type: 'POST',
            data: {
                action: action,
                product_id: productId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (showSuccessAlert) {
                        alert('Carrito actualizado');
                    }
                    // Recargar la página para ver los cambios.
                    // Una mejora sería actualizar el DOM dinámicamente.
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                    // Si hay un error de stock, recargamos para que el usuario vea el stock real.
                    if (response.message.includes('stock')) {
                        window.location.reload();
                    }
                }
            },
            error: function(xhr, status, error) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Ocurrió un error inesperado.';
                alert('Error: ' + errorMsg);
            }
        });
    }

    // Evento para actualizar la cantidad
    $('.update-quantity').on('change', function() {
        const quantity = $(this).val();
        const productId = $(this).closest('tr').data('product-id');
        
        if (quantity > 0) {
            handleCartAction('update', productId, quantity);
        } else {
            // Si la cantidad es 0 o menos, lo eliminamos
            handleCartAction('remove', productId);
        }
    });

    // Evento para eliminar un item
    $('.remove-item-btn').on('click', function() {
        const productId = $(this).closest('tr').data('product-id');
        handleCartAction('remove', productId);
    });

    // Evento para vaciar el carrito
    $('#clear-cart-btn').on('click', function() {
        if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
            handleCartAction('clear', 0);
        }
    });

    // --- LÓGICA DE LA CAJA REGISTRADORA ---
    const montoRecibidoInput = $('#monto_recibido');
    const cambioDisplay = $('#cambio-a-dar');
    const checkoutBtn = $('#checkout-btn');
    const checkoutBtnMsg = $('#checkout-btn-msg');

    function calcularCambio() {
        const recibido = parseFloat(montoRecibidoInput.val()) || 0;
        let cambio = 0;

        if (recibido >= totalAPagar) {
            cambio = recibido - totalAPagar;
            checkoutBtn.prop('disabled', false);
            checkoutBtnMsg.hide();
        } else {
            checkoutBtn.prop('disabled', true);
            checkoutBtnMsg.show();
        }
        
        cambioDisplay.text(cambio.toFixed(2));
    }

    montoRecibidoInput.on('input', calcularCambio);

    $('.quick-cash').on('click', function() {
        const amount = $(this).data('amount');
        montoRecibidoInput.val(amount);
        calcularCambio();
    });

    $('#checkout-form').on('submit', function() {
        checkoutBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
    });
});
</script>