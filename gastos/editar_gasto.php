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
    <link rel="stylesheet" href="/vamates3/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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
            max-width: 1100px; /* antes 800px */
            margin: 0 auto;
            padding: 20px;
            font-family: 'Roboto', sans-serif;
        }

        .edit-form {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            width: 100%; /* nuevo */
        }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-color);
    }

    h2 {
        color: var(--dark-color);
        margin: 0;
        font-weight: 500;
    }

    .btn-back {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s;
    }

    .btn-back:hover {
        background-color: #5a6268;
        color: white;
        text-decoration: none;
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

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
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

    .info-gasto {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 20px;
    }

    .info-gasto h4 {
        margin: 0 0 10px 0;
        color: #1976d2;
    }

    .alert {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="container">
    <div class="edit-form">
        <div class="header-section">
            <h2>Editar Gasto #<?= $gasto['id'] ?></h2>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (isset($_GET['alert']) && $_GET['alert'] == 'error'): ?>
            <div class="alert alert-error">
                Error al actualizar el gasto. Por favor, verifica los datos.
            </div>
        <?php endif; ?>

        <div class="info-gasto">
            <h4>Información del Gasto</h4>
            <p><strong>Fecha original:</strong> <?= date('d/m/Y', strtotime($gasto['fecha'])) ?></p>
            <p><strong>Descripción original:</strong> <?= htmlspecialchars($gasto['descripcion']) ?></p>
            <p><strong>Monto original:</strong> $<?= number_format($gasto['monto'], 2, ',', '.') ?></p>
        </div>
        
        <form action="editar_gasto.php?id=<?= $id_gasto ?>" method="post">
            <input type="hidden" name="id_gasto" value="<?= $id_gasto ?>">
            
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input class="control" type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($gasto['fecha']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input class="control" type="text" name="descripcion" id="descripcion" value="<?= htmlspecialchars($gasto['descripcion']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="monto">Monto</label>
                <input class="control" type="number" step="0.01" name="monto" id="monto" value="<?= htmlspecialchars($gasto['monto']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select class="control" name="tipo" id="tipo" required>
                    <option value="fijo" <?= $gasto['tipo'] == 'fijo' ? 'selected' : '' ?>>Fijo</option>
                    <option value="variable" <?= $gasto['tipo'] == 'variable' ? 'selected' : '' ?>>Variable</option>
                </select>
            </div>
            <!-- Reemplaza la sección del select de categoría con este código: -->
            <div class="form-group">
                <label for="categoria">Categoría</label>
                <select class="control" name="categoria" id="categoria" required>
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

            
            <button type="submit" class="btn-submit">
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



