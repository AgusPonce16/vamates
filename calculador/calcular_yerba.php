<?php 
// Incluye el encabezado común del sitio
include '../includes/header.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Yerba</title>
    <!-- FontAwesome para iconos -->
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <!-- Fuente Roboto de Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS para colores y estilos reutilizables */
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

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: var(--dark-color);
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }

        .control {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tr:hover {
            background-color: #f0e6f6;
        }

        .totales {
            font-size: 1.2em;
            font-weight: 500;
            margin: 20px 0;
            padding: 15px;
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

        .btn-delete {
            color: var(--danger-color);
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .input-group .control {
            flex: 1;
        }

        .input-group .btn {
            flex: 0 0 auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-calculator"></i> Calculadora de Yerba</h2>

    <!-- Selector de yerba -->
    <div class="input-group">
        <select id="yerbaSelect" class="control">
            <option value="">Seleccioná un tipo de yerba</option>
            <?php
            // Conexión a la base de datos y consulta de productos
            include '../config/config.php';
            $productos = $conn->query("
                SELECT * FROM productos 
                WHERE nombre LIKE '%Baldo%' 
                OR nombre LIKE '%Canarias%' 
                OR nombre LIKE '%Rei verde%' 
                OR nombre LIKE '%Pindare%'
                OR nombre LIKE '%verdecita%'
                ORDER BY nombre ASC
            ");

            // Genera las opciones del select con los productos encontrados
            while ($prod = $productos->fetch_assoc()) {
                echo "<option value='{$prod['id']}' data-precio='{$prod['precio_compra']}' data-nombre='{$prod['nombre']}'>
                        {$prod['nombre']} - $ {$prod['precio_compra']} (Stock: {$prod['stock']})
                      </option>";
            }
            ?>
        </select>
    </div>

    <!-- Selector de presentación y cantidad -->
    <div class="input-group">
        <select id="presentacionSelect" class="control">
            <option value="1">1 kg</option>
            <option value="0.5">500 g</option>
        </select>

        <input type="number" id="cantidadInput" class="control" placeholder="Cantidad" min="1" value="1">
        
        <button type="button" class="btn" id="btnAgregarYerba">
            <i class="fas fa-plus"></i> Agregar
        </button>
    </div>

    <!-- Tabla de yerbas agregadas -->
    <table id="tablaYerbas">
        <thead>
            <tr>
                <th>Yerba</th>
                <th>Presentación</th>
                <th>Paquetes</th>
                <th>Kilos</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Totales -->
    <div class="totales">
        <div><strong>Total Kg:</strong> <span id="totalKg">0.00</span> kg</div>
        <div><strong>Total Gasto:</strong> $<span id="totalPrecio">0.00</span></div>
    </div>
</div>

<script>
/**
 * Array para almacenar las yerbas agregadas
 * Cada elemento es un objeto: { nombre, presentacion, cantidad, subtotal, kilos }
 */
let yerbas = [];

/**
 * Agrega una yerba a la lista o suma cantidades si ya existe
 */
function agregarYerba() {
    const select = document.getElementById("yerbaSelect");
    const option = select.options[select.selectedIndex];
    const presentacion = parseFloat(document.getElementById("presentacionSelect").value);
    const cantidad = parseInt(document.getElementById("cantidadInput").value);

    // Validación de selección y cantidad
    if (!option.value || isNaN(cantidad) || cantidad <= 0) {
        alert("Seleccioná una yerba válida y cantidad.");
        return;
    }

    const nombre = option.dataset.nombre;
    const precio = parseFloat(option.dataset.precio);
    const subtotal = precio * cantidad;
    const kilos = presentacion * cantidad;

    // Busca si ya existe la yerba con la misma presentación
    const index = yerbas.findIndex(y => y.nombre === nombre && y.presentacion === presentacion);
    if (index >= 0) {
        // Suma cantidades y totales si ya existe
        yerbas[index].cantidad += cantidad;
        yerbas[index].subtotal += subtotal;
        yerbas[index].kilos += kilos;
    } else {
        // Agrega nueva yerba
        yerbas.push({ nombre, presentacion, cantidad, subtotal, kilos });
    }

    actualizarTabla();
    document.getElementById("cantidadInput").value = "1";
    select.focus();
}

/**
 * Actualiza la tabla HTML con las yerbas agregadas y los totales
 */
function actualizarTabla() {
    const tbody = document.querySelector("#tablaYerbas tbody");
    tbody.innerHTML = "";
    let totalKg = 0;
    let totalPrecio = 0;

    yerbas.forEach((y, i) => {
        totalKg += y.kilos;
        totalPrecio += y.subtotal;
        tbody.innerHTML += `
            <tr>
                <td>${y.nombre}</td>
                <td>${y.presentacion == 1 ? "1 kg" : "500 g"}</td>
                <td>${y.cantidad}</td>
                <td>${y.kilos.toFixed(2)}</td>
                <td>$${y.subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn-action btn-delete" onclick="quitarYerba(${i})" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
    });

    document.getElementById("totalKg").textContent = totalKg.toFixed(2);
    document.getElementById("totalPrecio").textContent = totalPrecio.toFixed(2);
}

/**
 * Elimina una yerba de la lista por su índice
 * @param {number} index - Índice de la yerba a eliminar
 */
function quitarYerba(index) {
    yerbas.splice(index, 1);
    actualizarTabla();
}

/**
 * Inicializa los eventos de la interfaz
 */
function inicializarEventos() {
    // Permitir agregar con Enter en el input de cantidad
    document.getElementById("cantidadInput").addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            agregarYerba();
        }
    });

    // Botón de agregar yerba
    document.getElementById("btnAgregarYerba").addEventListener("click", agregarYerba);
}

// Inicializa los eventos al cargar la página
window.addEventListener("DOMContentLoaded", inicializarEventos);
</script>
</body>
</html>