<?php include '../includes/header.php'; ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="/vamates3/assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<style>
/* ===================== VARIABLES ===================== */
:root {
    --primary-color: #8e44ad;
    --secondary-color: #9b59b6;
    --danger-color: #e74c3c;
    --warning-color: #f39c12;
    --success-color: #2ecc71;
    --light-color: #f5f5f5;
    --dark-color: #333;
    --border-radius: 8px;
    --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* ===================== LAYOUT ===================== */
.container {
    display: flex;
    gap: 30px;
    padding: 20px;
    font-family: 'Roboto', sans-serif;
}
.hidden { display: none; }
.column.left, .column.right {
    flex: 1;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: var(--border-radius);
    padding: 25px;
    box-shadow: var(--box-shadow);
    max-height: 90vh;
    overflow-y: auto;
}

/* ===================== TITULOS ===================== */
h2 {
    color: var(--dark-color);
    margin-bottom: 20px;
    font-weight: 500;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
}

/* ===================== FORMULARIOS ===================== */
.control {
    width: 100%;
    padding: 10px 15px;
    margin: 8px 0 15px;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    box-sizing: border-box;
    font-size: 14px;
    transition: border-color 0.3s;
}
.control:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
}
.form-container {
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: var(--border-radius);
    padding: 20px;
    margin-bottom: 25px;
}
.btn-submit {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: background-color 0.3s;
    width: 100%;
}
.btn-submit:hover { background-color: var(--secondary-color); }
.btn-submit:disabled { background-color: #ccc; cursor: not-allowed; }
.btn-agregar {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
}
.btn-agregar:hover { background-color: var(--secondary-color); }

/* ===================== TABLA ===================== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px;
}
th, td {
    border: 1px solid #e0e0e0;
    padding: 12px;
    text-align: left;
}
th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 500;
    position: sticky;
    top: 0;
}
tr:nth-child(even) { background-color: #fafafa; }
tr:hover { background-color: #f0e6f6; }

/* ===================== BUSQUEDA ===================== */
.containerBusq { margin-bottom: 15px; position: relative; }
.search-input {
    width: 100%;
    padding: 10px 15px 10px 40px;
    font-size: 14px;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
    box-sizing: border-box;
    transition: all 0.3s;
}
.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
}
.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #777;
}

/* ===================== TOTALES ===================== */
.total-display {
    font-size: 1.1em;
    font-weight: 500;
    margin: 15px 0;
    padding: 15px;
    background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
    border-radius: var(--border-radius);
    border-left: 4px solid var(--success-color);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.total-display .total-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
}
.total-display .total-item:last-child {
    margin-bottom: 0;
    padding-top: 10px;
    border-top: 1px solid rgba(0,0,0,0.1);
    font-weight: 600;
    color: var(--success-color);
}

/* ===================== ACCIONES ===================== */
.btn-action {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    margin: 0 3px;
    font-size: 16px;
    transition: transform 0.2s;
}
.btn-action:hover { transform: scale(1.1); }
.btn-edit { color: var(--primary-color); }
.btn-delete { color: var(--danger-color); }

/* ===================== BADGES ===================== */
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}
.badge-stock-critico {
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}
.badge-stock-bajo {
    background-color: #fff3e0;
    color: #e65100;
    border: 1px solid #ffcc02;
}
.badge-stock-normal {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

/* ===================== VALIDACION ===================== */
.control.error {
    border-color: var(--danger-color);
    background-color: #fff5f5;
}
.error-message {
    color: var(--danger-color);
    font-size: 12px;
    margin-top: -10px;
    margin-bottom: 10px;
    display: none;
}
.error-message.show { display: block; }

/* ===================== FORMULARIOS DE PRODUCTOS ===================== */
.producto-form {
    margin-bottom: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: var(--border-radius);
    position: relative;
}
.btn-eliminar-form {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--danger-color);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===================== STOCK BAJO ===================== */
.low-stock-section {
    margin-top: 30px;
    padding: 20px;
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}
.stock-cards-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}
.stock-card {
    flex: 1;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.stock-card-header {
    padding: 15px 20px;
    color: white;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.stock-card-header.danger {
    background: linear-gradient(135deg, var(--danger-color), #c0392b);
}
.stock-card-header.warning {
    background: linear-gradient(135deg, var(--warning-color), #e67e22);
}
.stock-card-body {
    background: #fafafa;
    padding: 20px;
    max-height: 300px;
    overflow-y: auto;
}
.stock-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    margin-bottom: 10px;
    background: white;
    border-radius: 6px;
    border-left: 4px solid;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.stock-item.danger { border-left-color: var(--danger-color); }
.stock-item.warning { border-left-color: var(--warning-color); }
.stock-item-name { font-weight: 500; color: var(--dark-color); }
.stock-item-stock {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
}
.stock-item-stock.danger {
    background: #ffebee;
    color: var(--danger-color);
}
.stock-item-stock.warning {
    background: #fff3e0;
    color: var(--warning-color);
}
.no-items {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}
.producto-inactivo {
    opacity: 0.7;
    background-color: #f9f9f9;
}
.producto-inactivo td { color: #999; }
</style>

<div class="container">
  <!-- ===================== COLUMNA IZQUIERDA ===================== -->
  <div class="column left">
    <h2>Agregar Productos</h2>
    <form id="formProductos" action="add_productos.php" method="post">
      <div id="productosContainer"></div>
      <button type="button" class="btn-agregar" onclick="agregarFormularioProducto()">
        <i class="fas fa-plus"></i> Agregar otro producto
      </button>
      <input class="btn-submit" type="submit" value="Guardar Productos">
    </form>

    <!-- Plantilla oculta para nuevos formularios de productos -->
    <div class="producto-form hidden" id="plantillaProducto">
      <button type="button" class="btn-eliminar-form" onclick="eliminarFormularioProducto(this)">×</button>
      <h3>Producto #<span class="producto-numero">1</span></h3>
      <input class="control" type="text" name="nombre[]" placeholder="Nombre del producto" required>
      <div class="error-message" id="nombreError">El nombre es requerido</div>
      <input class="control precio-compra-input" type="number" step="0.01" name="precio_compra[]" placeholder="Precio de compra" min="0.01" required>
      <div class="error-message" id="precioCompraError">El precio debe ser mayor a 0</div>
      <input class="control precio-venta-input" type="number" step="0.01" name="precio[]" placeholder="Precio de venta" min="0.01" required>
      <div class="error-message" id="precioVentaError">El precio debe ser mayor a 0</div>
      <input class="control stock-input" type="number" name="stock[]" placeholder="Stock inicial" min="0" required>
      <div class="error-message" id="stockError">El stock no puede ser negativo</div>
    </div>

    <!-- ===================== SECCION STOCK BAJO ===================== -->
    <?php
      include '../config/config.php';
      $sql = "SELECT * FROM productos ORDER BY nombre ASC";
      $result = $conn->query($sql);
      $productosSinStock = [];
      $productosBajoStock = [];
      while($producto = $result->fetch_assoc()):
        if ($producto['stock'] == 0) $productosSinStock[] = $producto;
        elseif ($producto['stock'] == 1) $productosBajoStock[] = $producto;
      endwhile;
      $result->data_seek(0);
    ?>
    <div class="low-stock-section">
      <h2><i class="fas fa-exclamation-triangle"></i> Control de Stock</h2>
      <div class="stock-cards-container">
        <!-- Sin stock -->
        <div class="stock-card">
          <div class="stock-card-header danger">
            <i class="fas fa-times-circle"></i>
            <span>Productos SIN stock (<?= count($productosSinStock) ?>)</span>
          </div>
          <div class="stock-card-body">
            <?php if (empty($productosSinStock)): ?>
              <div class="no-items">
                <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 24px; margin-bottom: 10px;"></i>
                <p>¡Excelente! No hay productos sin stock</p>
              </div>
            <?php else: ?>
              <?php foreach ($productosSinStock as $producto): ?>
                <div class="stock-item danger">
                  <div class="stock-item-name"><?= htmlspecialchars($producto['nombre']) ?></div>
                  <div class="stock-item-stock danger">Stock: <?= $producto['stock'] ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        <!-- Stock bajo -->
        <div class="stock-card">
          <div class="stock-card-header warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Stock bajo 1(<?= count($productosBajoStock) ?>)</span>
          </div>
          <div class="stock-card-body">
            <?php if (empty($productosBajoStock)): ?>
              <div class="no-items">
                <i class="fas fa-thumbs-up" style="color: var(--success-color); font-size: 24px; margin-bottom: 10px;"></i>
                <p>Todos los productos tienen stock adecuado</p>
              </div>
            <?php else: ?>
              <?php foreach ($productosBajoStock as $producto): ?>
                <div class="stock-item warning">
                  <div class="stock-item-name"><?= htmlspecialchars($producto['nombre']) ?></div>
                  <div class="stock-item-stock warning">Stock: <?= $producto['stock'] ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <!-- ===================== FIN SECCION STOCK BAJO ===================== -->
  </div>

  <!-- ===================== COLUMNA DERECHA ===================== -->
  <div class="column right">
    <h2>Inventario de Productos</h2>
    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
      <select id="filtroEstado" class="control" onchange="filtrarGastos()" style="flex: 1;">
        <option value="">Todos los estados</option>
        <option value="activo">Activos</option>
        <option value="desactivado">Desactivados</option>
      </select>
      <input type="text" id="buscarProducto" class="search-input" placeholder="Buscar producto..." onkeyup="filtrarProductos()" style="flex: 2;">
    </div>

    <?php if (isset($_GET['alert'])): ?>
      <script>
        swal({
          icon: '<?= $_GET['alert'] === 'error' ? 'error' : 'success' ?>',
          title: '<?= 
            $_GET['alert'] === 'added' ? '¡Producto agregado!' : 
            ($_GET['alert'] === 'updated' ? '¡Producto actualizado!' : 
            ($_GET['alert'] === 'deleted' ? '¡Producto eliminado!' : 
            '¡Operación completada!')) ?>',
          timer: 3000
        });
      </script>
    <?php endif; ?>

    <table id="tablaProductos">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>P. Compra</th>
          <th>P. Venta</th>
          <th>Stock</th>
          <th>Ganancia</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $totalInventario = 0;
        $totalGanancia = 0;
        while($producto = $result->fetch_assoc()):
          $ganancia = $producto['precio'] - $producto['precio_compra'];
          $totalInventario += ($producto['precio_compra'] * $producto['stock']);
          $totalGanancia += ($ganancia * $producto['stock']);
          if ($producto['stock'] == 0) $claseStock = "badge-stock-critico";
          elseif ($producto['stock'] == 1) $claseStock = "badge-stock-bajo";
          else $claseStock = "badge-stock-normal";
        ?>
        <tr id="fila-producto-<?= $producto['id'] ?>" class="producto-row <?= $producto['estado'] == 'desactivado' ? 'producto-inactivo' : '' ?>">
          <td><?= $producto['id'] ?></td>
          <td><?= htmlspecialchars($producto['nombre']) ?></td>
          <td>$<?= number_format($producto['precio_compra'], 2, ',', '.') ?></td>
          <td>$<?= number_format($producto['precio'], 2, ',', '.') ?></td>
          <td>
            <span class="badge <?= $claseStock ?>">
              <?= $producto['stock'] ?>
            </span>
          </td>
          <td>$<?= number_format($ganancia, 2, ',', '.') ?></td>
          <td>
            <button class="btn-action btn-edit" onclick="editarProducto(<?= $producto['id'] ?>)">
              <i class="fas fa-edit"></i>
            </button>
            <select class="control" style="width: 120px; padding: 5px;" onchange="cambiarEstado(<?= $producto['id'] ?>, this.value)">
              <option value="activo" <?= $producto['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
              <option value="desactivado" <?= $producto['estado'] == 'desactivado' ? 'selected' : '' ?>>Desactivado</option>
            </select>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// ===================== VARIABLES =====================
let contadorFormularios = 0;

// ===================== VALIDACION PRECIO VENTA > COMPRA =====================
function validarPrecioVentaMayor(input) {
  const form = input.closest('.producto-form');
  const precioCompraInput = form.querySelector('.precio-compra-input');
  const precioVentaInput = form.querySelector('.precio-venta-input');
  const errorMsg = form.querySelector('#precioVentaError');
  const compra = parseFloat(precioCompraInput.value) || 0;
  const venta = parseFloat(precioVentaInput.value) || 0;
  if (venta <= compra) {
    precioVentaInput.classList.add('error');
    errorMsg.textContent = "El precio de venta debe ser mayor al de compra";
    errorMsg.classList.add('show');
    return false;
  } else {
    precioVentaInput.classList.remove('error');
    errorMsg.classList.remove('show');
    return true;
  }
}
function agregarEventosValidacion(form) {
  const precioCompraInput = form.querySelector('.precio-compra-input');
  const precioVentaInput = form.querySelector('.precio-venta-input');
  precioCompraInput.addEventListener('input', function() { validarPrecioVentaMayor(this); });
  precioVentaInput.addEventListener('input', function() { validarPrecioVentaMayor(this); });
}

// ===================== AGREGAR FORMULARIO PRODUCTO =====================
function agregarFormularioProducto() {
  contadorFormularios++;
  const container = document.getElementById('productosContainer');
  const plantilla = document.getElementById('plantillaProducto');
  const nuevoForm = plantilla.cloneNode(true);
  nuevoForm.classList.remove('hidden');
  nuevoForm.id = '';
  nuevoForm.querySelector('.producto-numero').textContent = contadorFormularios;
  container.appendChild(nuevoForm);
  agregarEventosValidacion(nuevoForm);
  nuevoForm.querySelector('.precio-compra-input').addEventListener('input', calcularTotales);
  nuevoForm.querySelector('.precio-venta-input').addEventListener('input', calcularTotales);
  nuevoForm.querySelector('.stock-input').addEventListener('input', calcularTotales);
  renumerarFormulariosProductos();
  calcularTotales();
}

// ===================== ELIMINAR FORMULARIO PRODUCTO =====================
function eliminarFormularioProducto(btn) {
  const form = btn.closest('.producto-form');
  form.remove();
  renumerarFormulariosProductos();
  calcularTotales();
}

// ===================== RENUMERAR FORMULARIOS =====================
function renumerarFormulariosProductos() {
  const forms = document.querySelectorAll('.producto-form:not(.hidden)');
  forms.forEach((form, index) => {
    form.querySelector('.producto-numero').textContent = index + 1;
  });
  contadorFormularios = forms.length;
}

// ===================== CALCULAR TOTALES =====================
function calcularTotales() {
  let totalInventario = 0;
  let totalGanancia = 0;
  const forms = document.querySelectorAll('.producto-form:not(.hidden)');
  forms.forEach(form => {
    const precioCompra = parseFloat(form.querySelector('.precio-compra-input').value) || 0;
    const precioVenta = parseFloat(form.querySelector('.precio-venta-input').value) || 0;
    const stock = parseInt(form.querySelector('.stock-input').value) || 0;
    const valorInventario = precioCompra * stock;
    const gananciaUnitaria = precioVenta - precioCompra;
    const gananciaPotencial = gananciaUnitaria * stock;
    totalInventario += valorInventario;
    totalGanancia += gananciaPotencial;
  });
  document.getElementById('totalInventario').textContent = totalInventario.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  document.getElementById('totalGanancia').textContent = totalGanancia.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// ===================== FILTROS =====================
function filtrarGastos() {
  const estado = document.getElementById("filtroEstado").value.toLowerCase();
  const filas = document.querySelectorAll("#tablaProductos tbody tr");
  filas.forEach(fila => {
    const estadoFila = fila.querySelector("select").value.toLowerCase();
    fila.style.display = (estado === "" || estadoFila === estado) ? "" : "none";
  });
}
function filtrarProductos() {
  const busqueda = document.getElementById("buscarProducto").value.toLowerCase();
  const filas = document.querySelectorAll("#tablaProductos tbody tr");
  filas.forEach(fila => {
    const textoFila = fila.textContent.toLowerCase();
    fila.style.display = textoFila.includes(busqueda) ? "" : "none";
  });
}

// ===================== CAMBIAR ESTADO =====================
function cambiarEstado(idProducto, estado) {
  swal({
    title: "Actualizando estado...",
    text: "Por favor espere",
    button: false,
    closeOnClickOutside: false,
    closeOnEsc: false
  });
  fetch('cambiar_estado_producto.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `id=${idProducto}&estado=${estado}`
  })
  .then(response => {
    if (!response.ok) throw new Error('Error en la red');
    return response.json();
  })
  .then(data => {
    swal.close();
    if (data.success) {
      const fila = document.getElementById(`fila-producto-${idProducto}`);
      if (estado === 'desactivado') fila.classList.add('producto-inactivo');
      else fila.classList.remove('producto-inactivo');
      swal("Éxito", "Estado actualizado correctamente", "success");
    } else {
      swal("Error", data.error || "Error al actualizar el estado", "error");
      setTimeout(() => location.reload(), 2000);
    }
  })
  .catch(error => {
    swal.close();
    console.error('Error:', error);
    swal("Error", "Error de conexión al actualizar el estado", "error");
    setTimeout(() => location.reload(), 2000);
  });
}

// ===================== EDITAR Y ELIMINAR =====================
function editarProducto(idProducto) {
  window.location.href = `editar_producto.php?id=${idProducto}`;
}
function confirmarEliminacion(id, tipo) {
  swal({
    title: "¿Estás seguro?",
    text: `¿Quieres eliminar este ${tipo}? Esta acción no se puede deshacer.`,
    icon: "warning",
    buttons: {cancel: "Cancelar", confirm: "Sí, eliminar"},
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) window.location.href = `eliminar_${tipo}.php?id=${id}`;
  });
}

// ===================== INICIALIZACION =====================
document.addEventListener('DOMContentLoaded', function() {
  agregarFormularioProducto();
  document.getElementById("formProductos").addEventListener("submit", function(e) {
    e.preventDefault();
    let formulariosValidos = true;
    const formularios = document.querySelectorAll('.producto-form:not(.hidden)');
    if (formularios.length === 0) {
      swal("Error", "Debe agregar al menos un producto", "error");
      return;
    }
    formularios.forEach(form => {
      const nombreInput = form.querySelector('input[type="text"]');
      const precioCompraInput = form.querySelector('.precio-compra-input');
      const precioVentaInput = form.querySelector('.precio-venta-input');
      const stockInput = form.querySelector('.stock-input');
      if (!nombreInput.value.trim()) {
        nombreInput.classList.add('error');
        formulariosValidos = false;
      } else nombreInput.classList.remove('error');
      if (!precioCompraInput.value || parseFloat(precioCompraInput.value) <= 0) {
        precioCompraInput.classList.add('error');
        formulariosValidos = false;
      } else precioCompraInput.classList.remove('error');
      if (!precioVentaInput.value || parseFloat(precioVentaInput.value) <= 0) {
        precioVentaInput.classList.add('error');
        formulariosValidos = false;
      } else precioVentaInput.classList.remove('error');
      if (parseFloat(precioVentaInput.value) <= parseFloat(precioCompraInput.value)) {
        precioVentaInput.classList.add('error');
        formulariosValidos = false;
        swal("Error", "El precio de venta debe ser mayor al precio de compra", "error");
        return;
      }
      if (stockInput.value === "" || parseInt(stockInput.value) < 0) {
        stockInput.classList.add('error');
        formulariosValidos = false;
      } else stockInput.classList.remove('error');
    });
    if (!formulariosValidos) {
      swal("Error", "Por favor complete todos los campos requeridos correctamente", "error");
      return;
    }
    const totalInventario = parseFloat(document.getElementById('totalInventario').textContent.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
    const totalGanancia = parseFloat(document.getElementById('totalGanancia').textContent.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
    swal({
      title: "¿Guardar productos?",
      text: `Valor del inventario: $${totalInventario.toFixed(2)}\nGanancia potencial: $${totalGanancia.toFixed(2)}`,
      icon: "question",
      buttons: {cancel: "Cancelar", confirm: "Guardar"}
    }).then((willSave) => {
      if (willSave) {
        const submitBtn = this.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.value = "Guardando...";
        this.submit();
      }
    });
  });
});
</script>

<?php $conn->close(); ?>
