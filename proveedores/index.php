<?php include '../includes/header.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/vamates3/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        .container { display: flex; gap: 30px; padding: 20px; font-family: 'Roboto', sans-serif; }
        .column.left, .column.right {
            flex: 1; background: #fff; border: 1px solid #e0e0e0; border-radius: var(--border-radius);
            padding: 25px; box-shadow: var(--box-shadow); max-height: 90vh; overflow-y: auto;
        }
        h2 { color: var(--dark-color); margin-bottom: 20px; font-weight: 500; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; }
        .control { width: 100%; padding: 10px 15px; margin: 8px 0 15px; border: 1px solid #ddd; border-radius: var(--border-radius); box-sizing: border-box; font-size: 14px; transition: border-color 0.3s; }
        .control:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2); }
        .form-container { background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: var(--border-radius); padding: 20px; margin-bottom: 25px; }
        .btn-submit { background-color: var(--primary-color); color: white; border: none; padding: 12px 20px; border-radius: var(--border-radius); cursor: pointer; font-size: 16px; font-weight: 500; transition: background-color 0.3s; width: 100%; }
        .btn-submit:hover { background-color: var(--secondary-color); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        th, td { border: 1px solid #e0e0e0; padding: 12px; text-align: left; }
        th { background-color: var(--primary-color); color: white; font-weight: 500; position: sticky; top: 0; }
        tr:nth-child(even) { background-color: #fafafa; }
        tr:hover { background-color: #f0e6f6; }
        .containerBusq { margin-bottom: 15px; position: relative; }
        .search-input { width: 100%; padding: 10px 15px 10px 40px; font-size: 14px; border-radius: var(--border-radius); border: 1px solid #ddd; box-sizing: border-box; transition: all 0.3s; }
        .search-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #777; }
        .btn-action { background: none; border: none; cursor: pointer; padding: 5px; margin: 0 3px; font-size: 16px; transition: transform 0.2s; }
        .btn-action:hover { transform: scale(1.1); }
        .btn-edit { color: var(--primary-color); }
        .btn-delete { color: var(--danger-color); }
        .proveedor-inactivo { opacity: 0.7; background-color: #f9f9f9; }
        .proveedor-inactivo td { color: #999; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-activo { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .badge-inactivo { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>
<div class="container">
    <!-- Columna Izquierda: Formulario para Agregar Proveedores -->
    <div class="column left">
        <h2>Agregar Proveedor</h2>
        <form action="add_proveedor.php" method="post" class="form-container">
            <input class="control" type="text" name="nombre" placeholder="Nombre del proveedor" required>
            <textarea class="control" name="detalle" placeholder="Detalle del proveedor" rows="3"></textarea>
            <input class="btn-submit" type="submit" value="Guardar Proveedor">
        </form>
    </div>
    <!-- Columna Derecha: Tabla de Proveedores -->
    <div class="column right">
        <h2>Listado de Proveedores</h2>
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <select id="filtroEstado" class="control" onchange="filtrarProveedores()" style="flex: 1;">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="desactivado">Inactivos</option>
            </select>
            <input type="text" id="buscarProveedor" class="search-input" placeholder="Buscar proveedor..." onkeyup="filtrarProveedores()" style="flex: 2;">
        </div>
        <?php 
        include '../config/config.php';
        // Mostrar alertas si existen
        if (isset($_GET['alert'])): 
            $mensaje = '';
            $tipo = 'success';
            switch($_GET['alert']) {
                case 'added': $mensaje = '¡Proveedor agregado correctamente!'; break;
                case 'updated': $mensaje = '¡Proveedor actualizado correctamente!'; break;
                case 'deleted': $mensaje = '¡Proveedor desactivado correctamente!'; break;
                case 'error': $mensaje = '¡Error al procesar la solicitud!'; $tipo = 'error'; break;
            }
        ?>
        <script>
            swal({
                icon: '<?= $tipo ?>',
                title: '<?= $mensaje ?>',
                timer: 3000
            });
        </script>
        <?php endif; ?>

        <?php
        $sql = "SELECT * FROM proveedores ORDER BY nombre ASC";
        $result = $conn->query($sql);
        ?>
        <table id="tablaProveedores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Detalle</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($proveedor = $result->fetch_assoc()): ?>
                <tr id="fila-proveedor-<?= $proveedor['id'] ?>" class="<?= $proveedor['estado'] == 'desactivado' ? 'proveedor-inactivo' : '' ?>">
                    <td><?= $proveedor['id'] ?></td>
                    <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
                    <td><?= htmlspecialchars($proveedor['detalle']) ?></td>
                    <td>
                        <span class="badge <?= $proveedor['estado'] == 'activo' ? 'badge-activo' : 'badge-inactivo' ?>">
                            <?= ucfirst($proveedor['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarProveedor(<?= $proveedor['id'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <select class="control" onchange="cambiarEstado(<?= $proveedor['id'] ?>, this.value)">
                            <option value="activo" <?= $proveedor['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="desactivado" <?= $proveedor['estado'] == 'desactivado' ? 'selected' : '' ?>>Desactivado</option>
                        </select>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function cambiarEstado(idProveedor, estado) {
    const fila = document.getElementById(`fila-proveedor-${idProveedor}`);
    const select = fila.querySelector('select');
    const estadoOriginal = select.value;
    swal({
        title: "Actualizando estado...",
        text: "Por favor espere",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false
    });
    fetch('cambiar_estado_proveedor.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${idProveedor}&estado=${estado}`
    })
    .then(response => response.json())
    .then(data => {
        swal.close();
        if (data.success) {
            const badge = fila.querySelector('.badge');
            if (estado === 'desactivado') {
                fila.classList.add('proveedor-inactivo');
                badge.className = 'badge badge-inactivo';
                badge.textContent = 'Desactivado';
            } else {
                fila.classList.remove('proveedor-inactivo');
                badge.className = 'badge badge-activo';
                badge.textContent = 'Activo';
            }
            swal("Éxito", "Estado actualizado", "success");
        } else {
            select.value = estadoOriginal;
            swal("Error", data.error || "Error al actualizar", "error");
        }
    })
    .catch(error => {
        swal.close();
        select.value = estadoOriginal;
        swal("Error", "Error de conexión", "error");
    });
}


function editarProveedor(id) {
    // Lógica para editar proveedor
    window.location.href = `editar_proveedor.php?id=${id}`;
}
function filtrarProveedores() {
    const filtroEstado = document.getElementById('filtroEstado').value.toLowerCase();
    const buscarProveedor = document.getElementById('buscarProveedor').value.toLowerCase();
    const tabla = document.getElementById('tablaProveedores').getElementsByTagName('tbody')[0];
    const filas = tabla.getElementsByTagName('tr');

    for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        const nombre = fila.cells[1].textContent.toLowerCase();
        const detalle = fila.cells[2].textContent.toLowerCase();
        // Obtener el estado real desde el atributo de clase o desde el select
        let estado = '';
        const select = fila.querySelector('select');
        if (select) {
            estado = select.value.toLowerCase();
        }

        const coincideEstado = !filtroEstado || estado === filtroEstado;
        const coincideBusqueda = !buscarProveedor || nombre.includes(buscarProveedor) || detalle.includes(buscarProveedor);

        if (coincideEstado && coincideBusqueda) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    }
}
</script>
</body>
