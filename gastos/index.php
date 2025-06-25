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

    .container {
        display: flex;
        gap: 30px;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
    }
    .hidden {
    display: none;
    }


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

    h2 {
        color: var(--dark-color);
        margin-bottom: 20px;
        font-weight: 500;
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
    }

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

    .btn-submit:hover {
        background-color: var(--secondary-color);
    }

    .btn-submit:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

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

    .btn-agregar:hover {
        background-color: var(--secondary-color);
    }

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

    tr:nth-child(even) {
        background-color: #fafafa;
    }

    tr:hover {
        background-color: #f0e6f6;
    }

    .containerBusq {
        margin-bottom: 15px;
        position: relative;
    }

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

    .total-display {
        font-size: 1.2em;
        font-weight: 500;
        margin: 15px 0;
        padding: 12px;
        background-color: #e8f5e9;
        border-radius: var(--border-radius);
        text-align: center;
    }

    .btn-action {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        margin: 0 3px;
        font-size: 16px;
        transition: transform 0.2s;
    }

    .btn-action:hover {
        transform: scale(1.1);
    }

    .btn-edit {
        color: var(--primary-color);
    }

    .btn-delete {
        color: var(--danger-color);
    }

    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-fijo {
        background-color: #d1f5e9;
        color: #00796b;
    }

    .badge-variable {
        background-color: #e3f2fd;
        color: #1565c0;
    }

    /* NUEVOS ESTILOS PARA FECHAS HORIZONTALES */
    .fecha-header {
        background-color: #f0f0f0 !important;
        color: #333 !important;
        font-weight: bold !important;
        font-size: 15px !important;
        text-align: center !important;
        padding: 15px !important;
        border: none !important;
        border-top: 2px solid #ddd !important;
        border-bottom: 2px solid #ddd !important;
    }

    .fecha-header td {
        border: none !important;
        background-color: #f0f0f0 !important;
        color: #333 !important;
        font-weight: bold !important;
    }

    /* Las filas de gastos no necesitan estilos especiales adicionales */
    .gasto-row:hover {
        background-color: #f0e6f6 !important;
    }
    
    .estado-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .estado-pagada {
        background-color: #d4edda;
        color: #155724;
    }
    
    .estado-pendiente {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .estado-cancelada {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Estilos para mostrar errores de validación */
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

    .error-message.show {
        display: block;
    }

    /* Estilos para los formularios de gastos */
    .gasto-form {
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
</style>

<div class="container">
    <!-- Columna Izquierda: Formulario para Agregar Gastos -->
    <div class="column left">
        <h2>Agregar Gastos</h2>
        
        <form id="formGastos" action="add_gastos.php" method="post">
            <div id="gastosContainer">
                <!-- Los formularios de gastos se agregarán aca dinámicamente -->
            </div>
            
            <button type="button" class="btn-agregar" onclick="agregarFormularioGasto()">
                <i class="fas fa-plus"></i> Agregar otro gasto
            </button>
            
            <input class="btn-submit" type="submit" value="Guardar Gastos">
        </form>
        
        <!-- Plantilla oculta para nuevos formularios de gastos -->
        <div class="gasto-form hidden" id="plantillaGasto">
            <button type="button" class="btn-eliminar-form" onclick="eliminarFormularioGasto(this)">×</button>
            <h3>Gasto #<span class="gasto-numero">1</span></h3>
            
            <input class="control" type="date" name="fecha[]" required>
            <div class="error-message" id="fechaError">La fecha debe ser de 2025 en adelante</div>
            
            <input class="control" type="text" name="descripcion[]" placeholder="Descripción" required>
            <input class="control monto-input" type="number" step="0.01" name="monto[]" placeholder="Monto" min = "1" required>
            
            <label for="tipo">Tipo:</label>
            <select name="tipo[]" class="control" required>
                <option value="" disabled selected>-- Seleccione tipo --</option>
                <option value="fijo">Fijo</option>
                <option value="variable">Variable</option>
            </select>
            
            <label for="categoria">Categoría:</label>
            <select name="categoria[]" class="control" required>
                <option value="" disabled selected>-- Seleccione categoría --</option>
                <option value="servicios">Servicios</option>
                <option value="transporte">Transporte</option>
                <option value="comida">Comida</option>
                <option value="boludeces">Boludeces</option>
                <option value="utilidades">Utilidades</option>
                <option value="envios">Envios</option>
                <option value="combustible">Combustible</option>
                <option value="devoluciones">Devoluciones</option>
                <option value="educacion">Educacion</option>
            </select>
            
            <label for="estado">Estado:</label>
            <select name="estado[]" class="control" required>
                <option value="pendiente" selected>Pendiente</option>
                <option value="pagada">Pagada</option>
            </select>
        </div>
        
        <div class="total-display">
            <strong>Total a gastar: $<span id="totalGastos">0.00</span></strong>
        </div>
    </div>

    <!-- Columna Derecha: Tabla de Gastos -->
    <div class="column right">
        <h2>Historial de Gastos</h2>

        <div style="display: flex; gap: 10px; margin-bottom: 15px;">

            <select id="filtroEstado" class="control" onchange="filtrarGastos()" style="flex: 1;">
                <option value="">Todos los estados</option>
                <option value="pagada">Pagados</option>
                <option value="pendiente">Pendientes</option>
                <option value="cancelada">Cancelados</option>
            </select>
            <input type="text" id="buscarGasto" class="search-input" placeholder="Buscar gasto..." onkeyup="filtrarGastos()" style="flex: 2;">
        </div>

        <?php if (isset($_GET['alert'])): ?>
            <script>
                swal({
                    icon: '<?= $_GET['alert'] === 'error' ? 'error' : 'success' ?>',
                    title: '<?= 
                        $_GET['alert'] === 'added' ? '¡Gasto agregado!' : 
                        ($_GET['alert'] === 'updated' ? '¡Gasto actualizado!' : 
                        ($_GET['alert'] === 'state_changed' ? '¡Estado actualizado!' : 
                        '¡Operación completada!')) ?>',
                    timer: 3000
                });
            </script>
        <?php endif; ?>

        <table id="tablaGastos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Conexión y consulta de gastos
                include '../config/config.php';
                
                // Consulta agrupada por fecha
                $sql = "SELECT * FROM gastos ORDER BY fecha DESC, id DESC";
                $result = $conn->query($sql);
                
                $fechaActual = '';
                $numeroColumnas = 7; 
                $totalGastado = 0;
                $totalPagado = 0;
                $totalPendiente = 0;
                $totalCancelado = 0;
                
                while($gasto = $result->fetch_assoc()):
                    $fechaGasto = $gasto['fecha'];
                    $claseEstado = "estado-" . $gasto['estado'];
                    $claseTipo = "badge-" . $gasto['tipo'];
                    
                    // Sumar al total según el estado
                    if ($gasto['estado'] == 'pagada') {
                        $totalPagado += $gasto['monto'];
                    } elseif ($gasto['estado'] == 'pendiente') {
                        $totalPendiente += $gasto['monto'];
                    } elseif ($gasto['estado'] == 'cancelada') {
                        $totalCancelado += $gasto['monto'];
                    }
                    
                    $totalGastado += $gasto['monto'];
                    
                    // Si es una nueva fecha, mostrar el header de fecha
                    if ($fechaActual !== $fechaGasto) {
                        $fechaActual = $fechaGasto;
                        $fechaFormateada = date('d/m/Y', strtotime($fechaGasto));
                        echo "<tr class='fecha-header'>
                                <td colspan='{$numeroColumnas}'>📅 {$fechaFormateada}</td>
                            </tr>";
                    }
                ?>
                <tr id="fila-gasto-<?= $gasto['id'] ?>" class="gasto-row" data-estado="<?= $gasto['estado'] ?>" data-categoria="<?= $gasto['categoria'] ?>" data-tipo="<?= $gasto['tipo'] ?>">
                    <td><?= $gasto['id'] ?></td>
                    <td><?= htmlspecialchars($gasto['descripcion']) ?></td>
                    <td>$<?= number_format($gasto['monto'], 2, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $claseTipo ?>">
                            <?= ucfirst($gasto['tipo']) ?>
                        </span>
                    </td>
                    <td><?= ucfirst($gasto['categoria']) ?></td>
                    <td>
                        <span class="estado-badge <?= $claseEstado ?>">
                            <?= ucfirst($gasto['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <!-- Botón Editar -->
                        <button class="btn-action btn-edit" onclick="editarGasto(<?= $gasto['id'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <!-- Selector de Estado -->
                        <select onchange="cambiarEstado(<?= $gasto['id'] ?>, this.value)" 
                                class="control" style="width: auto; padding: 5px;">
                            <option value="pendiente" <?= $gasto['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="pagada" <?= $gasto['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                            <option value="cancelada" <?= $gasto['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="total-display">
            <div>Total Gastado: $<?= number_format($totalGastado, 2, ',', '.') ?></div>
            <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                <span style="color: #155724;">Pagados: $<?= number_format($totalPagado, 2, ',', '.') ?></span>
                <span style="color: #856404;">Pendientes: $<?= number_format($totalPendiente, 2, ',', '.') ?></span>
                <span style="color: #721c24;">Cancelados: $<?= number_format($totalCancelado, 2, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let contadorFormularios = 0;

// Función para validar fecha
function validarFecha(fecha) {
    const fechaSeleccionada = new Date(fecha);
    const año = fechaSeleccionada.getFullYear();
    return año >= 2025;
}

// Función para mostrar/ocultar error
function mostrarError(inputId, errorId, mostrar) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);
    
    if (mostrar) {
        input.classList.add('error');
        error.classList.add('show');
    } else {
        input.classList.remove('error');
        error.classList.remove('show');
    }
}

function filtrarGastos() {
    const estado = document.getElementById("filtroEstado").value.toLowerCase();
    const busqueda = document.getElementById("buscarGasto").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaGastos tbody tr");

    filas.forEach(fila => {
        // Saltear las filas de fecha
        if (fila.classList.contains('fecha-header')) {
            return;
        }
        
        const textoFila = fila.textContent.toLowerCase();
        const estadoGasto = fila.dataset.estado || "";

        const coincideBusqueda = textoFila.includes(busqueda);
        const coincideEstado = estado === "" || estadoGasto.includes(estado);

        fila.style.display = (coincideBusqueda && coincideEstado) ? "" : "none";
    });
    
    // Ocultar headers de fecha que no tengan gastos visibles
    const fechasHeaders = document.querySelectorAll("#tablaGastos tbody .fecha-header");
    fechasHeaders.forEach(fechaHeader => {
        let siguienteFila = fechaHeader.nextElementSibling;
        let tieneGastosVisibles = false;
        
        // Verificar si hay gastos visibles después de este header de fecha
        while (siguienteFila && !siguienteFila.classList.contains('fecha-header')) {
            if (siguienteFila.style.display !== 'none') {
                tieneGastosVisibles = true;
                break;
            }
            siguienteFila = siguienteFila.nextElementSibling;
        }
        
        fechaHeader.style.display = tieneGastosVisibles ? "" : "none";
    });
}

// Función para agregar un nuevo formulario de gasto
function agregarFormularioGasto() {
    contadorFormularios++;
    const container = document.getElementById('gastosContainer');
    const plantilla = document.getElementById('plantillaGasto');
    const nuevoForm = plantilla.cloneNode(true);

    nuevoForm.classList.remove('hidden');
    nuevoForm.id = '';
    nuevoForm.querySelector('.gasto-numero').textContent = contadorFormularios;

    // Establecer fecha actual por defecto
    const today = new Date().toISOString().split('T')[0];
    nuevoForm.querySelector('input[type="date"]').value = today;

    container.appendChild(nuevoForm);
    
    // Agregar evento para calcular total cuando cambie el monto
    nuevoForm.querySelector('.monto-input').addEventListener('change', calcularTotalGastos);
    
    // Agregar evento para validar fecha
    nuevoForm.querySelector('input[type="date"]').addEventListener('change', function() {
        const fechaValida = validarFecha(this.value);
        mostrarError(this.id, 'fechaError', !fechaValida);
    });
    
    // Renumerar formularios
    renumerarFormulariosGastos();
    calcularTotalGastos();
}

// Función para eliminar un formulario de gasto
function eliminarFormularioGasto(btn) {
    const form = btn.closest('.gasto-form');
    form.remove();
    renumerarFormulariosGastos();
    calcularTotalGastos();
}

// Función para renumerar los formularios de gastos
function renumerarFormulariosGastos() {
    const forms = document.querySelectorAll('.gasto-form:not(.hidden)');
    forms.forEach((form, index) => {
        form.querySelector('.gasto-numero').textContent = index + 1;
    });
    contadorFormularios = forms.length;
}

// Función para calcular el total de todos los gastos
function calcularTotalGastos() {
    let total = 0;
    const montos = document.querySelectorAll('.monto-input');
    
    montos.forEach(input => {
        if (input.value && !isNaN(parseFloat(input.value))) {
            total += parseFloat(input.value);
        }
    });
    
    document.getElementById('totalGastos').textContent = total.toFixed(2);
}

function cambiarEstado(idGasto, nuevoEstado) {
    const selectElement = event.target;
    const originalValue = selectElement.value;
    selectElement.disabled = true;
    
    fetch('actualizar_estado_gasto.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: idGasto,
            estado: nuevoEstado
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Error al actualizar estado');
        }
        
        // Actualizar visualización
        const fila = document.querySelector(`#fila-gasto-${idGasto}`);
        fila.dataset.estado = nuevoEstado;
        
        const badge = fila.querySelector('.estado-badge');
        badge.className = `estado-badge estado-${nuevoEstado}`;
        badge.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
        
        swal({
            title: "¡Éxito!",
            text: "Estado actualizado correctamente",
            icon: "success",
            timer: 2000
        });
    })
    .catch(error => {
        console.error('Error:', error);
        selectElement.value = originalValue;
        swal({
            title: "Error",
            text: error.message || "Error al actualizar el estado",
            icon: "error"
        });
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

// Función para editar gasto
function editarGasto(id) {
    window.location.href = `editar_gasto.php?id=${id}`;
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Agregar el primer formulario de gasto
    agregarFormularioGasto();
    
    // Validación del submit del formulario
    document.getElementById("formGastos").addEventListener("submit", function(e) {
        e.preventDefault();
        
        // Validar que todos los formularios tengan datos válidos
        let formulariosValidos = true;
        const formularios = document.querySelectorAll('.gasto-form:not(.hidden)');
        
        formularios.forEach(form => {
            const fechaInput = form.querySelector('input[type="date"]');
            const descripcionInput = form.querySelector('input[type="text"]');
            const montoInput = form.querySelector('.monto-input');
            const tipoSelect = form.querySelector('select[name="tipo[]"]');
            const categoriaSelect = form.querySelector('select[name="categoria[]"]');
            
            // Validar fecha
            if (!validarFecha(fechaInput.value)) {
                mostrarError(fechaInput.id, 'fechaError', true);
                formulariosValidos = false;
            }
            
            // Validar otros campos
            if (!descripcionInput.value.trim()) {
                descripcionInput.classList.add('error');
                formulariosValidos = false;
            } else {
                descripcionInput.classList.remove('error');
            }
            
            if (!montoInput.value || isNaN(parseFloat(montoInput.value)) || parseFloat(montoInput.value) <= 0) {
                montoInput.classList.add('error');
                formulariosValidos = false;
            } else {
                montoInput.classList.remove('error');
            }
            
            if (!tipoSelect.value) {
                tipoSelect.classList.add('error');
                formulariosValidos = false;
            } else {
                tipoSelect.classList.remove('error');
            }
            
            if (!categoriaSelect.value) {
                categoriaSelect.classList.add('error');
                formulariosValidos = false;
            } else {
                categoriaSelect.classList.remove('error');
            }
        });
        
        if (!formulariosValidos) {
            swal("Error", "Por favor complete todos los campos requeridos en todos los gastos", "error");
            return;
        }
        
        // Calcular total
        const total = parseFloat(document.getElementById('totalGastos').textContent) || 0;
        
        // Confirmar antes de guardar
        swal({
            title: "¿Guardar gastos?",
            text: `Total a gastar: $${total.toFixed(2)}`,
            icon: "question",
            buttons: {
                cancel: "Cancelar",
                confirm: "Guardar"
            }
        }).then((willSave) => {
            if (willSave) {
                // Deshabilitar el botón para evitar múltiples clics
                const submitBtn = this.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.value = "Guardando...";
                
                // Enviar el formulario
                this.submit();
            }
        });
    });
});
</script>
<?php $conn->close(); ?>