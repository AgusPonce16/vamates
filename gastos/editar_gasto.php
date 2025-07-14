<?php 
include '../includes/header.php';
include '../config/config.php';

// Verificar que se reciba el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error');
    exit;
}

$id_gasto = intval($_GET['id']);

// Obtener datos del gasto
$sql_gasto = "SELECT * FROM gastos WHERE id = ?";
$stmt = $conn->prepare($sql_gasto);
$stmt->bind_param("i", $id_gasto);
$stmt->execute();
$gasto = $stmt->get_result()->fetch_assoc();

if (!$gasto) {
    header('Location: index.php?alert=error');
    exit;
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $monto = floatval($_POST['monto'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    
    // Validar datos
    if (empty($fecha) || empty($descripcion) || $monto <= 0 || empty($tipo) || empty($categoria)) {
        header("Location: editar_gasto.php?id=$id_gasto&alert=error");
        exit;
    }
    
    // Actualizar en la base de datos
    $stmt = $conn->prepare("UPDATE gastos SET fecha = ?, descripcion = ?, monto = ?, tipo = ?, categoria = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $fecha, $descripcion, $monto, $tipo, $categoria, $id_gasto);
    
    if ($stmt->execute()) {
        header("Location: index.php?alert=updated");
    } else {
        header("Location: editar_gasto.php?id=$id_gasto&alert=error");
    }
    
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/vamates/assets/css/styles.css">
    <link rel="stylesheet" href="/vamates/assets/css/editar/edit.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<div class="edit-container">
    <div class="edit-form">
        <div class="edit-header-section">
            <h2>Editar Gasto #<?= $gasto['id'] ?></h2>
            <a href="index.php" class="edit-btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (isset($_GET['alert']) && $_GET['alert'] == 'error'): ?>
            <div class="edit-alert edit-alert-error">
                Error al actualizar el gasto. Por favor, verifica los datos.
            </div>
        <?php endif; ?>

        <div class="edit-info-box">
            <h4>Información del Gasto</h4>
            <p><strong>Fecha original:</strong> <?= date('d/m/Y', strtotime($gasto['fecha'])) ?></p>
            <p><strong>Descripción original:</strong> <?= htmlspecialchars($gasto['descripcion']) ?></p>
            <p><strong>Monto original:</strong> $<?= number_format($gasto['monto'], 2, ',', '.') ?></p>
        </div>
        
        <form action="editar_gasto.php?id=<?= $id_gasto ?>" method="post">
            <input type="hidden" name="id_gasto" value="<?= $id_gasto ?>">
            
            <div class="edit-form-group">
                <label for="fecha">Fecha</label>
                <input class="edit-control" type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($gasto['fecha']) ?>" required>
            </div>
            
            <div class="edit-form-group">
                <label for="descripcion">Descripción</label>
                <input class="edit-control" type="text" name="descripcion" id="descripcion" value="<?= htmlspecialchars($gasto['descripcion']) ?>" required>
            </div>
            
            <div class="edit-form-group">
                <label for="monto">Monto</label>
                <input class="edit-control" type="number" step="0.01" name="monto" id="monto" value="<?= htmlspecialchars($gasto['monto']) ?>" required>
            </div>
            
            <div class="edit-form-group">
                <label for="tipo">Tipo</label>
                <select class="edit-control" name="tipo" id="tipo" required>
                    <option value="fijo" <?= $gasto['tipo'] == 'fijo' ? 'selected' : '' ?>>Fijo</option>
                    <option value="variable" <?= $gasto['tipo'] == 'variable' ? 'selected' : '' ?>>Variable</option>
                </select>
            </div>
            
            <div class="edit-form-group">
                <label for="categoria">Categoría</label>
                <select class="edit-control" name="categoria" id="categoria" required>
                    <option value="Servicios" <?= $gasto['categoria'] == 'Servicios' ? 'selected' : '' ?>>Servicios</option>
                    <option value="Transporte" <?= $gasto['categoria'] == 'Transporte' ? 'selected' : '' ?>>Transporte</option>
                    <option value="Comida" <?= $gasto['categoria'] == 'Comida' ? 'selected' : '' ?>>Comida</option>
                    <option value="Boludeces" <?= $gasto['categoria'] == 'Boludeces' ? 'selected' : '' ?>>Boludeces</option>
                    <option value="Utilidades" <?= $gasto['categoria'] == 'Utilidades' ? 'selected' : '' ?>>Utilidades</option>
                    <option value="Envíos" <?= $gasto['categoria'] == 'Envíos' ? 'selected' : '' ?>>Envíos</option>
                    <option value="Combustible" <?= $gasto['categoria'] == 'Combustible' ? 'selected' : '' ?>>Combustible</option>
                    <option value="Devoluciones" <?= $gasto['categoria'] == 'Devoluciones' ? 'selected' : '' ?>>Devoluciones</option>
                    <option value="Educacion" <?= $gasto['categoria'] == 'Educacion' ? 'selected' : '' ?>>Educacion</option>
                </select>
            </div>
            
            <button type="submit" class="edit-btn-submit">
                <i class="fas fa-save"></i> Actualizar Gasto
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar envío del formulario
    document.querySelector("form").addEventListener("submit", function(e) {
        const monto = parseFloat(document.getElementById("monto").value);
        
        if (isNaN(monto) || monto <= 0) {
            e.preventDefault();
            swal("Error", "El monto debe ser un número positivo", "error");
            return;
        }
        
        // Validar fecha
        const fechaInput = document.getElementById("fecha");
        if (!fechaInput.value) {
            e.preventDefault();
            swal("Error", "Debes seleccionar una fecha válida", "error");
            return;
        }
        
        // Deshabilitar el botón para evitar múltiples clics
        const submitBtn = this.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    });
});
</script>
<?php 
$conn->close();
?>