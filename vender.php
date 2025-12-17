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
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Catálogo de Venta</h1>
    <a href="carrito.php" class="btn btn-primary shadow-sm"><i class="fas fa-shopping-cart fa-sm text-white-50"></i> Ir al Carrito</a>
</div>

<div class="row">
    <!-- Columna de Productos -->
    <div class="col-lg-12">
        <!-- Controles para filtros, vista y búsqueda -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="btn-group" role="group" aria-label="Vistas de catálogo">
        <button type="button" class="btn btn-primary" id="card-view-btn" title="Vista de tarjetas">
            <i class="fas fa-th-large"></i>
        </button>
        <button type="button" class="btn btn-outline-primary" id="table-view-btn" title="Vista de tabla">
            <i class="fas fa-list"></i>
        </button>
    </div>
                    <div class="w-50">
                        <input type="text" id="product-search" class="form-control" placeholder="Buscar producto por nombre...">
                    </div>
                </div>
                <div id="category-filters">
                    <button class="btn btn-sm btn-secondary category-filter-btn active" data-category="all">Todos</button>
                    <?php
                    $categorias_result = $conexion->query("SELECT DISTINCT categoria FROM productos WHERE cantidad > 0 AND categoria IS NOT NULL AND categoria != '' ORDER BY categoria ASC");
                    while ($cat = $categorias_result->fetch_assoc()): ?>
                        <button class="btn btn-sm btn-outline-secondary category-filter-btn" data-category="<?php echo htmlspecialchars(strtolower($cat['categoria'])); ?>"><?php echo htmlspecialchars($cat['categoria']); ?></button>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div id="product-catalog">
<?php
// Hacemos la consulta una sola vez y guardamos los resultados en un array.
$productos = [];
$resultado = $conexion->query("SELECT id, nombre, categoria, precio, cantidad, imagen_url FROM productos WHERE cantidad > 0 ORDER BY nombre ASC");
while ($producto = $resultado->fetch_assoc()) {
    $productos[] = $producto;
}
?>

<!-- Vista de Tarjetas (Catálogo Vertical) -->
<div id="card-view">
    <div class="row">
                <?php foreach ($productos as $producto): ?>
                <div class="col-xl-4 col-md-6 mb-4 product-item" data-name="<?php echo htmlspecialchars(strtolower($producto['nombre'])); ?>" data-category="<?php echo htmlspecialchars(strtolower($producto['categoria'] ?? '')); ?>" data-img-src="<?php echo !empty($producto['imagen_url']) ? htmlspecialchars($producto['imagen_url']) : 'img/placeholder.png'; ?>">
            <div class="card h-100 shadow">
                <img src="<?php echo !empty($producto['imagen_url']) ? htmlspecialchars($producto['imagen_url']) : 'img/placeholder.png'; ?>" class="card-img-top product-image-zoom" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold text-primary"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                    <div class="mb-3">
                        <p class="card-text mb-1"><strong>Precio:</strong> <span class="text-success font-weight-bold">C$ <?php echo number_format($producto['precio'], 2); ?></span></p>
                        <p class="card-text"><strong>Stock:</strong> <span class="stock-<?php echo $producto['id']; ?>"><?php echo $producto['cantidad']; ?></span></p>
                    </div>
                    <button class="btn btn-success btn-icon-split mt-auto add-to-cart-btn" data-product-id="<?php echo $producto['id']; ?>">
                        <span class="icon text-white-50">
                            <i class="fas fa-cart-plus"></i>
                        </span>
                        <span class="text">Agregar</span>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Vista de Tabla (Catálogo Anterior) -->
<div id="table-view" style="display: none;">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Inventario de Productos para Venta</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio (C$)</th>
                            <th>Stock</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                        <tr class="product-item" data-name="<?php echo htmlspecialchars(strtolower($producto['nombre'])); ?>" data-category="<?php echo htmlspecialchars(strtolower($producto['categoria'] ?? '')); ?>" data-img-src="<?php echo !empty($producto['imagen_url']) ? htmlspecialchars($producto['imagen_url']) : 'img/placeholder.png'; ?>">
                            <td class="text-center"><img src="<?php echo !empty($producto['imagen_url']) ? htmlspecialchars($producto['imagen_url']) : 'img/placeholder.png'; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image-zoom" style="width: 50px; height: 50px; object-fit: cover;"></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>C$ <?php echo number_format($producto['precio'], 2); ?></td>
                            <td><span class="stock-<?php echo $producto['id']; ?>"><?php echo $producto['cantidad']; ?></span></td>
                            <td><button class="btn btn-success btn-sm add-to-cart-btn" data-product-id="<?php echo $producto['id']; ?>"><i class="fas fa-cart-plus"></i> Agregar</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        </div>
    </div>

</div>

<!-- Modal para ver imagen -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Vista Previa del Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="modalImage" class="img-fluid" alt="Imagen del producto">
      </div>
    </div>
  </div>
</div>

<?php include 'empleado_footer.php'; ?>

<!-- Incluimos jQuery para AJAX -->
<script src="vendor/jquery/jquery.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- INICIO: Lógica de notificación y actualización del carrito ---
    function showToast(message, isSuccess = true) {
        const toastContainer = document.createElement('div');
        toastContainer.className = `toast-notification ${isSuccess ? 'toast-success' : 'toast-error'}`;
        toastContainer.innerText = message;
        document.body.appendChild(toastContainer);

        setTimeout(() => {
            toastContainer.classList.add('show');
        }, 100);

        setTimeout(() => {
            toastContainer.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toastContainer);
            }, 500);
        }, 3000);
    }

    // --- FIN: Lógica de notificación ---

    const cardView = document.getElementById('card-view');
    const tableView = document.getElementById('table-view');
    const cardBtn = document.getElementById('card-view-btn');
    const tableBtn = document.getElementById('table-view-btn');

    // --- LÓGICA PARA CAMBIAR VISTAS ---
    function switchView(view) {
        if (view === 'table') {
            cardView.style.display = 'none';
            tableView.style.display = 'block';
            tableBtn.classList.remove('btn-outline-primary');
            tableBtn.classList.add('btn-primary');
            cardBtn.classList.remove('btn-primary');
            cardBtn.classList.add('btn-outline-primary');
            localStorage.setItem('catalogView', 'table');
        } else { // Por defecto, la vista de tarjetas
            cardView.style.display = 'block';
            tableView.style.display = 'none';
            cardBtn.classList.remove('btn-outline-primary');
            cardBtn.classList.add('btn-primary');
            tableBtn.classList.remove('btn-primary');
            tableBtn.classList.add('btn-outline-primary');
            localStorage.setItem('catalogView', 'card');
        }
    }

    cardBtn.addEventListener('click', () => switchView('card'));
    tableBtn.addEventListener('click', () => switchView('table'));

    // Cargar la vista guardada en localStorage
    const savedView = localStorage.getItem('catalogView');
    if (savedView) {
        switchView(savedView);
    } else {
        switchView('card'); // Vista por defecto
    }

    // --- LÓGICA DE BÚSQUEDA DE PRODUCTOS ---
    const searchInput = document.getElementById('product-search');
    const categoryButtons = document.querySelectorAll('.category-filter-btn');
    let activeCategory = 'all';

    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        document.querySelectorAll('.product-item').forEach(item => {
            const productName = item.dataset.name;
            const productCategory = item.dataset.category;

            const nameMatch = productName.includes(searchTerm);
            const categoryMatch = (activeCategory === 'all' || productCategory === activeCategory);

            if (nameMatch && categoryMatch) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('keyup', filterProducts);

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Actualizar el estado visual de los botones
            categoryButtons.forEach(btn => btn.classList.remove('active', 'btn-secondary'));
            categoryButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
            this.classList.add('active', 'btn-secondary');
            this.classList.remove('btn-outline-secondary');

            activeCategory = this.dataset.category;
            filterProducts();
        });
    });

    // --- LÓGICA PARA EXPANDIR IMAGEN ---
    document.querySelectorAll('.product-image-zoom').forEach(image => {
        image.addEventListener('click', function() {
            const imgSrc = this.closest('.product-item').dataset.imgSrc;
            document.getElementById('modalImage').src = imgSrc;
            $('#imageModal').modal('show');
        });
    });

    // --- LÓGICA DEL CARRITO (AJAX con jQuery) ---
    function updateCartBadge() {
        let badge = $('#cart-badge');
        if (!badge.length) {
            // Si el badge no existe, lo creamos
            const cartLink = $('a[href="carrito.php"]');
            cartLink.append(' <span id="cart-badge" class="badge badge-danger ml-2"></span>');
            badge = $('#cart-badge');
        }
        const currentCount = parseInt(badge.text() || '0');
        badge.text(currentCount + 1);
        badge.show(); // Asegurarse de que sea visible
    }

    function handleCartAction(action, productId, quantity = 1) {
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
                    if (action === 'add') {
                        showToast('Producto agregado al carrito');
                        updateCartBadge();
                    }
                } else {
                    showToast('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Ocurrió un error inesperado.';
                showToast('Error: ' + errorMsg, false);
            }
        });
    }

    function reattachEventListeners() {
        // Botón Agregar al Carrito
        $('.add-to-cart-btn').off('click').on('click', function() {
            const productId = $(this).data('product-id');
            handleCartAction('add', productId);
        });
    }

    // Llamada inicial para atar los eventos
    reattachEventListeners();
});
</script>

<!-- Estilos para la notificación (Toast) -->
<style>
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 5px;
    color: #fff;
    font-size: 1rem;
    z-index: 1060;
    opacity: 0;
    transform: translateY(100px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.toast-notification.show {
    opacity: 1;
    transform: translateY(0);
}
.toast-success { background-color: #1cc88a; }
.toast-error { background-color: #e74a3b; }

.product-image-zoom {
    cursor: zoom-in;
}

/* Ajustes para los botones de categoría */
#category-filters .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}
</style>