<?php
// =======================
// INCLUDES Y CONFIGURACIÓN
// =======================
include '../includes/header.php';
include '../config/config.php';

// =======================
// FECHAS Y PERÍODOS
// =======================
$hoy = date('Y-m-d');
$semana_actual_inicio = date('Y-m-d', strtotime('monday this week'));
$mes_actual_inicio = date('Y-m-01');
$trimestre_actual_inicio = date('Y-m-01', strtotime('-2 months'));
$anio_actual_inicio = date('Y-01-01');

// Filtros por defecto (mes actual)
$fecha_inicio = $mes_actual_inicio;
$fecha_fin = date('Y-m-t');

// =======================
// FILTROS POR GET
// =======================
if (isset($_GET['periodo'])) {
    switch ($_GET['periodo']) {
        case 'semana':
            $fecha_inicio = $semana_actual_inicio;
            $fecha_fin = $hoy;
            break;
        case 'mes':
            $fecha_inicio = $mes_actual_inicio;
            $fecha_fin = date('Y-m-t');
            break;
        case 'trimestre':
            $fecha_inicio = $trimestre_actual_inicio;
            $fecha_fin = $hoy;
            break;
        case 'custom':
            if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
                $fecha_inicio = $_GET['fecha_inicio'];
                $fecha_fin = $_GET['fecha_fin'];
            }
            break;
    }
}

// =======================
// CONSULTAS PRINCIPALES
// =======================

// Total ventas (incluyendo envío)
$query_ventas = "
    SELECT 
        (SELECT SUM(subtotal) FROM detalle_ventas dv
            JOIN ventas v ON dv.venta_id = v.id
            WHERE v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                AND v.estado = 'pagada') +
        (SELECT SUM(envio) FROM ventas
            WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
                AND estado = 'pagada')
        AS total_ventas
";
$total_ventas = $conn->query($query_ventas)->fetch_assoc()['total_ventas'] ?? 0;


// Total compras (reinversión)
$query_compras = "
    SELECT SUM(monto) AS total 
    FROM compras 
    WHERE estado = 'pagada' 
    AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
";
$total_compras = $conn->query($query_compras)->fetch_assoc()['total'] ?? 0;

// Gastos fijos
$query_gastos_fijos = "
    SELECT SUM(monto) as total 
    FROM gastos 
    WHERE tipo='fijo' and estado = 'pagada'
    AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
";
$total_gastos_fijos = $conn->query($query_gastos_fijos)->fetch_assoc()['total'] ?? 0;

// Gastos variables
$query_gastos_variables = "
    SELECT SUM(monto) as total 
    FROM gastos 
    WHERE tipo='variable' and estado = 'pagada'
    AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
";
$total_gastos_variables = $conn->query($query_gastos_variables)->fetch_assoc()['total'] ?? 0;

// Total gastos y beneficio neto
$total_gastos = $total_gastos_fijos + $total_gastos_variables;
$beneficio = $total_ventas - ($total_compras + $total_gastos);

// =======================
// VENTAS DIARIAS
// =======================
$query_ventas_diarias = "
    SELECT fecha, SUM(total) as total 
    FROM ventas 
    WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' and estado = 'pagada'
    GROUP BY fecha 
    ORDER BY fecha
";
$res_ventas_diarias = $conn->query($query_ventas_diarias);
$ventas_diarias = [];
while ($row = $res_ventas_diarias->fetch_assoc()) {
    $ventas_diarias[$row['fecha']] = (float)$row['total'];
}

// =======================
// PATRONES DE CATEGORÍAS
// =======================
$patrones_yerba = ['baldo', 'canarias', 'rei verde', 'pindare', 'verdecita'];
$patrones_mate = [
    'torpedo', 'camionero', 'imperial', 'bombilla', 'termo', 'sobres', 'latas',
    'grabados', 'base', 'canasta', 'matepa', 'mochila', 'porta', 'criollo', 'dif', 'envio','difusor'
];

// Inicializar categorías
$categorias = [
    'yerbas' => ['ventas' => 0, 'beneficio' => 0, 'reinversion' => 0],
    'mates'  => ['ventas' => 0, 'beneficio' => 0, 'reinversion' => 0],
    'otros'  => ['ventas' => 0, 'beneficio' => 0, 'reinversion' => 0]
];

// =======================
// TOP PRODUCTOS Y CATEGORIZACIÓN
// =======================
$query_top_productos = "
    SELECT p.nombre, SUM(dv.cantidad) as cantidad, SUM(dv.subtotal) as total, p.precio_compra
    FROM detalle_ventas dv
    JOIN productos p ON dv.producto_id = p.id
    JOIN ventas v ON dv.venta_id = v.id
    WHERE v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    AND v.estado = 'pagada'
    GROUP BY p.id 
    ORDER BY cantidad DESC 
    LIMIT 100
";
$result = $conn->query($query_top_productos);
$top_productos = [];
while ($row = $result->fetch_assoc()) {
    $top_productos[] = $row;
    $nombre = strtolower($row['nombre']);
    $total_venta = $row['total'];
    $costo_total = $row['precio_compra'] * $row['cantidad'];
    $beneficio_prod = $total_venta - $costo_total;

    // Clasificar producto
    $cat = 'otros';
    foreach ($patrones_yerba as $pat) {
        if (str_contains($nombre, $pat)) {
            $cat = 'yerbas';
            break;
        }
    }
    if ($cat === 'otros') {
        foreach ($patrones_mate as $pat) {
            if (str_contains($nombre, $pat)) {
                $cat = 'mates';
                break;
            }
        }
    }

    $categorias[$cat]['ventas']     += $total_venta;
    $categorias[$cat]['beneficio']  += $beneficio_prod;
    $categorias[$cat]['reinversion']+= $costo_total;
}

// =======================
// MÁRGENES POR CATEGORÍA
// =======================
$margen_yerbas = $categorias['yerbas']['ventas'] > 0 ?
    ($categorias['yerbas']['beneficio'] / $categorias['yerbas']['ventas']) * 100 : 0;
$margen_mates = $categorias['mates']['ventas'] > 0 ?
    ($categorias['mates']['beneficio'] / $categorias['mates']['ventas']) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Profesional - Mates y Yerbas</title>
    <!-- Bootstrap y Chart.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Estilos personalizados -->
    <style>
        :root {
            --yerba: #2e8b57;
            --mate: #8b4513;
            --primary: #4e73df;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        body { background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 0.5rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); margin-bottom: 1.5rem; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15); }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; padding: 1rem 1.35rem; font-weight: 600; color: var(--dark); }
        .kpi-card { border-left: 0.25rem solid; position: relative; overflow: hidden; }
        .kpi-card::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to right, rgba(255,255,255,0.1), rgba(255,255,255,0.3)); z-index: 1; opacity: 0; transition: opacity 0.3s; }
        .kpi-card:hover::after { opacity: 1; }
        .kpi-primary { border-left-color: var(--primary); }
        .kpi-success { border-left-color: var(--success); }
        .kpi-info { border-left-color: var(--info); }
        .kpi-warning { border-left-color: var(--warning); }
        .kpi-danger { border-left-color: var(--danger); }
        .kpi-yerba { border-left-color: var(--yerba); }
        .kpi-mate { border-left-color: var(--mate); }
        .kpi-value { font-size: 1.75rem; font-weight: 700; }
        .icon-circle { width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .chart-container { position: relative; height: 300px; min-height: 300px; }
        .nav-pills .nav-link { color: var(--dark); font-weight: 600; margin: 0 0.25rem; }
        .nav-pills .nav-link.active { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); }
        .tab-pane { padding: 1.5rem 0; }
        .progress { height: 1rem; border-radius: 0.5rem; }
        .bg-yerba { background-color: var(--yerba); }
        .bg-mate { background-color: var(--mate); }
        .text-yerba { color: var(--yerba); }
        .text-mate { color: var(--mate); }
        .table th { background-color: #f8f9fc; font-weight: 600; }
        .badge-yerba { background-color: rgba(46, 139, 87, 0.2); color: var(--yerba); }
        .badge-mate { background-color: rgba(139, 69, 19, 0.2); color: var(--mate); }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-end align-items-center mb-3" style="position: relative;">
        <!-- Botón de cerrar sesión -->
        <a href="../login/login.php" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
    </div>

    <!-- =======================
        HEADER Y FILTROS
    ======================== -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard de Gestión</h1>
        <div class="d-flex">
            <form method="GET" class="d-flex align-items-center">
                <div class="btn-group btn-group-sm me-3">
                    <button type="submit" name="periodo" value="semana" class="btn btn-outline-primary <?= isset($_GET['periodo']) && $_GET['periodo'] == 'semana' ? 'active' : '' ?>">Semanal</button>
                    <button type="submit" name="periodo" value="mes" class="btn btn-outline-primary <?= (!isset($_GET['periodo']) || $_GET['periodo'] == 'mes') ? 'active' : '' ?>">Mensual</button>
                    <button type="submit" name="periodo" value="trimestre" class="btn btn-outline-primary <?= isset($_GET['periodo']) && $_GET['periodo'] == 'trimestre' ? 'active' : '' ?>">Trimestral</button>
                </div>
                <div class="input-group input-group-sm ms-3" style="width: 250px;">
                    <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control">
                    <span class="input-group-text">a</span>
                    <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" class="form-control">
                    <button type="submit" name="periodo" value="custom" class="btn btn-primary">
                        <i class="bi bi-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =======================
        KPIs PRINCIPALES
    ======================== -->
    <div class="row">
        <?php
        // Helper para mostrar KPIs
        function kpi_card($color, $title, $value, $badge, $badge_text, $icon, $extra_class = '') {
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card kpi-<?= $color ?> h-100 <?= $extra_class ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-<?= $color ?> text-uppercase small font-weight-bold"><?= $title ?></div>
                                <div class="kpi-value"><?= $value ?></div>
                                <div class="mt-2">
                                    <?= $badge ?>
                                    <span class="text-muted small"><?= $badge_text ?></span>
                                </div>
                            </div>
                            <div class="icon-circle bg-<?= $color ?> text-white">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        // KPI: Ventas Totales
        kpi_card(
            'primary',
            'Ventas Totales + Envios',
            '$' . number_format($total_ventas, 2),
            '<span class="badge bg-primary text-white me-1"><i class="bi bi-arrow-up"></i> ' . number_format(($total_ventas - $total_compras) / max(1, $total_compras) * 100, 2) . '%</span>',
            'vs Compras',
            'bi-cash-stack'
        );
        // KPI: Yerbas
        kpi_card(
            'yerba',
            'Ventas Yerbas',
            '$' . number_format($categorias['yerbas']['ventas'], 2),
            '<span class="badge badge-yerba me-1">' . number_format($margen_yerbas, 2) . '% Margen</span>',
            number_format($categorias['yerbas']['ventas'] / max(1, $total_ventas) * 100, 2) . '% del total',
            'bi-cup-straw',
            '',
        );
        // KPI: Mates
        kpi_card(
            'mate',
            'Ventas Mates',
            '$' . number_format($categorias['mates']['ventas'], 2),
            '<span class="badge badge-mate me-1">' . number_format($margen_mates, 2) . '% Margen</span>',
            number_format($categorias['mates']['ventas'] / max(1, $total_ventas) * 100, 2) . '% del total',
            'bi-cup-hot'
        );
        // KPI: Beneficio Neto
        $beneficio_color = $beneficio >= 0 ? 'success' : 'danger';
        kpi_card(
            $beneficio_color,
            'Beneficio Neto',
            '$' . number_format($beneficio, 2),
            '<span class="badge bg-' . $beneficio_color . ' text-white me-1">' . ($beneficio >= 0 ? '+' : '') . number_format($beneficio / max(1, $total_ventas) * 100, 2) . '%</span>',
            'Margen',
            $beneficio >= 0 ? 'bi-graph-up' : 'bi-graph-down'
        );
        ?>
    </div>

    <!-- =======================
        SEGUNDA FILA DE KPIs
    ======================== -->
    <div class="row">
        <?php
        // KPI: Reinversión
        kpi_card(
            'info',
            'Reinversión',
            '$' . number_format($total_compras, 2),
            '<div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-info" role="progressbar"
                     style="width: ' . min(100, ($total_compras / max(1, $total_ventas)) * 100) . '%"
                    aria-valuenow="' . $total_compras . '"
                    aria-valuemin="0"
                    aria-valuemax="' . $total_ventas . '">
                </div>
            </div>
            <div class="text-muted small mt-1">' . number_format(($total_compras / max(1, $total_ventas)) * 100, 2) . '% de ventas</div>',
            '',
            'bi-arrow-repeat'
        );
        // KPI: Gastos Fijos
        kpi_card(
            'warning',
            'Gastos Fijos',
            '$' . number_format($total_gastos_fijos, 2),
            '<div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-warning" role="progressbar"
                     style="width: ' . min(100, ($total_gastos_fijos / max(1, $total_ventas)) * 100) . '%"
                    aria-valuenow="' . $total_gastos_fijos . '"
                    aria-valuemin="0"
                    aria-valuemax="' . $total_ventas . '">
                </div>
            </div>
            <div class="text-muted small mt-1">' . number_format(($total_gastos_fijos / max(1, $total_ventas)) * 100, 2) . '% de ventas</div>',
            '',
            'bi-house-gear'
        );
        // KPI: Gastos Variables
        kpi_card(
            'danger',
            'Gastos Variables',
            '$' . number_format($total_gastos_variables, 2),
            '<div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-danger" role="progressbar"
                     style="width: ' . min(100, ($total_gastos_variables / max(1, $total_ventas)) * 100) . '%"
                    aria-valuenow="' . $total_gastos_variables . '"
                    aria-valuemin="0"
                    aria-valuemax="' . $total_ventas . '">
                </div>
            </div>
            <div class="text-muted small mt-1">' . number_format(($total_gastos_variables / max(1, $total_ventas)) * 100, 2) . '% de ventas</div>',
            '',
            'bi-speedometer2'
        );
        // KPI: Gastos Totales
        $gastos_totales = $total_gastos + $total_compras;
        $gastos_color = $gastos_totales >= 0 ? 'danger' : 'success';
        kpi_card(
            $gastos_color,
            'Gastos Totales',
            '$' . number_format($gastos_totales, 2),
            '<span class="badge bg-' . $gastos_color . ' text-white me-1">' . number_format(($gastos_totales / max(1, $total_ventas)) * 100, 2) . '%</span>',
            'del total ventas',
            'bi-calculator'
        );
        ?>
    </div>

    <!-- =======================
        GRÁFICOS PRINCIPALES
    ======================== -->

    <div class="row">
        <!-- Gráfico de ventas diarias -->
        <div class="col-lg-8 mb-4">
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-graph-up me-2"></i>
                    Evolución de Ventas Diarias
                </h6>
                <?php
                // Calcular porcentaje de cambio diario respecto al día anterior
                $ventas_vals = array_values($ventas_diarias);
                $fechas_vals = array_keys($ventas_diarias);
                $porcentajes = [];
                for ($i = 0; $i < count($ventas_vals); $i++) {
                    if ($i === 0) {
                        $porcentajes[] = 0;
                    } else {
                        $prev = $ventas_vals[$i - 1];
                        $curr = $ventas_vals[$i];
                        $porcentajes[] = $prev > 0 ? (($curr - $prev) / $prev) * 100 : 0;
                    }
                }
                // Mostrar porcentajes con diseño mejorado
                echo '<div class="d-flex flex-wrap align-items-center mt-3 mb-2">';
                foreach ($porcentajes as $i => $pct) {
                    $color_class = $pct > 0 ? 'success' : ($pct < 0 ? 'danger' : 'secondary');
                    $bg_class = $pct > 0 ? 'rgba(25, 135, 84, 0.1)' : ($pct < 0 ? 'rgba(220, 53, 69, 0.1)' : 'rgba(108, 117, 125, 0.1)');
                    $border_class = $pct > 0 ? 'rgba(25, 135, 84, 0.3)' : ($pct < 0 ? 'rgba(220, 53, 69, 0.3)' : 'rgba(108, 117, 125, 0.3)');
                    $icon_pct = $pct > 0 ? 'bi-arrow-up' : ($pct < 0 ? 'bi-arrow-down' : 'bi-dot');
                    $fecha_label = isset($fechas_vals[$i]) ? date('d/m', strtotime($fechas_vals[$i])) : '';
                    
                    echo '<div class="me-2 mb-2" style="';
                    echo 'display: inline-flex; align-items: center; ';
                    echo 'padding: 6px 12px; border-radius: 12px; ';
                    echo 'font-size: 0.75rem; font-weight: 600; ';
                    echo 'background: ' . $bg_class . '; ';
                    echo 'border: 1px solid ' . $border_class . '; ';
                    echo 'color: var(--bs-' . $color_class . '); ';
                    echo 'backdrop-filter: blur(10px);';
                    echo '">';
                    echo '<span class="text-muted me-1" style="font-size: 0.7rem;">' . $fecha_label . '</span>';
                    echo '<i class="bi ' . $icon_pct . ' me-1"></i>';
                    if ($i === 0) {
                        echo '<span>—</span>';
                    } else {
                        echo '<span>' . ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%</span>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                
                // Calcular los 2 días de la semana con mayor promedio de ventas
                $dias_es = [
                    'Monday' => 'Lunes',
                    'Tuesday' => 'Martes',
                    'Wednesday' => 'Miércoles',
                    'Thursday' => 'Jueves',
                    'Friday' => 'Viernes',
                    'Saturday' => 'Sábado',
                    'Sunday' => 'Domingo'
                ];
                $dias_semana = [];
                foreach ($ventas_diarias as $fecha => $total) {
                    $dia_en = date('l', strtotime($fecha));
                    $dia = $dias_es[$dia_en] ?? $dia_en;
                    if (!isset($dias_semana[$dia])) $dias_semana[$dia] = [];
                    $dias_semana[$dia][] = $total;
                }
                $promedios = [];
                foreach ($dias_semana as $dia => $ventas) {
                    $promedios[$dia] = array_sum($ventas) / count($ventas);
                }
                arsort($promedios);
                $top_dias = array_slice(array_keys($promedios), 0, 2);
                
                // Insights mejorados
                echo '<div style="';
                echo 'background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); ';
                echo 'border-radius: 12px; padding: 15px; margin-top: 15px; ';
                echo 'border-left: 4px solid #3b82f6;';
                echo '">';
                echo '<div style="color: #1e293b; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">';
                echo '<i class="bi bi-lightbulb text-warning"></i>';
                echo '<span>Insights de Ventas</span>';
                echo '</div>';
                echo '<div style="display: flex; gap: 15px; flex-wrap: wrap;">';
                foreach ($top_dias as $dia) {
                    echo '<div style="';
                    echo 'background: white; padding: 10px 15px; border-radius: 8px; ';
                    echo 'box-shadow: 0 2px 8px rgba(0,0,0,0.1); ';
                    echo 'border: 2px solid #e2e8f0;';
                    echo '">';
                    echo '<div style="font-weight: 600; color: #1e293b; font-size: 0.9rem;">' . $dia . '</div>';
                    echo '<div style="color: #3b82f6; font-weight: 700; font-size: 0.8rem;">$' . number_format($promedios[$dia], 0) . ' promedio</div>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<div style="margin-top: 8px; color: #6b7280; font-size: 0.8rem;">';
                echo '<i class="bi bi-info-circle me-1"></i>';
                echo 'Los mejores días para promociones especiales';
                echo '</div>';
                echo '</div>';
                ?>
                </div>
                <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Período
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="?periodo=semana">
                        <i class="bi bi-calendar-week me-2"></i>Semanal
                    </a></li>
                    <li><a class="dropdown-item" href="?periodo=mes">
                        <i class="bi bi-calendar-month me-2"></i>Mensual
                    </a></li>
                    <li><a class="dropdown-item" href="?periodo=trimestre">
                        <i class="bi bi-calendar-range me-2"></i>Trimestral
                    </a></li>
                </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                <canvas id="ventasChart"></canvas>
                </div>
            </div>
            </div>
        </div>
        
        <!-- Gráfico de distribución mejorado -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribución de Ventas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <?php
                        // Calcular totales para porcentajes
                        $total_general = $categorias['yerbas']['ventas'] + $categorias['mates']['ventas'];
                        $query_envio = "SELECT SUM(envio) as total_envio FROM ventas WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                        $total_envio = $conn->query($query_envio)->fetch_assoc()['total_envio'] ?? 0;
                        $total_general += $total_envio;
                        
                        $items = [
                            ['label' => 'Yerbas', 'valor' => $categorias['yerbas']['ventas'], 'color' => '#2e8b57'],
                            ['label' => 'Mates', 'valor' => $categorias['mates']['ventas'], 'color' => '#8b4513'],
                            ['label' => 'Envío', 'valor' => $total_envio, 'color' => '#36b9cc']
                        ];
                        
                        foreach ($items as $item) {
                            $porcentaje = $total_general > 0 ? ($item['valor'] / $total_general) * 100 : 0;
                            echo '<div style="';
                            echo 'display: flex; justify-content: space-between; align-items: center; ';
                            echo 'padding: 10px 0; border-bottom: 1px solid #f1f5f9;';
                            echo '">';
                            echo '<div style="display: flex; align-items: center; gap: 8px; font-weight: 500;">';
                            echo '<i class="bi bi-square-fill" style="color: ' . $item['color'] . ';"></i>';
                            echo '<span>' . $item['label'] . '</span>';
                            echo '<small class="text-muted">(' . number_format($porcentaje, 1) . '%)</small>';
                            echo '</div>';
                            echo '<div style="font-weight: 600; color: #1e293b;">$' . number_format($item['valor'], 2) . '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =======================
        ANÁLISIS DETALLADO
    ======================== -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="pill" href="#topProductos">Top Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#analisisComparativo">Cuadro de gastos</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Top Productos -->
                        <div class="tab-pane fade show active" id="topProductos">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th>Cantidad</th>
                                            <th>Ventas</th>
                                            <th>Reinversión</th>
                                            <th>Beneficio</th>
                                            <th>Margen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_productos as $producto):
                                            $nombre = strtolower($producto['nombre']);
                                            $cat = 'otros';
                                            foreach ($patrones_yerba as $pat) {
                                                if (str_contains($nombre, $pat)) {
                                                    $cat = 'yerbas';
                                                    break;
                                                }
                                            }
                                            if ($cat === 'otros') {
                                                foreach ($patrones_mate as $pat) {
                                                    if (str_contains($nombre, $pat)) {
                                                        $cat = 'mates';
                                                        break;
                                                    }
                                                }
                                            }
                                            $reinversion = $producto['precio_compra'] * $producto['cantidad'];
                                            $beneficio = $producto['total'] - $reinversion;
                                            $margen = $producto['total'] > 0 ? ($beneficio / $producto['total']) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td><?= $producto['nombre'] ?></td>
                                            <td>
                                                <span class="badge <?= $cat === 'yerbas' ? 'badge-yerba' : ($cat === 'mates' ? 'badge-mate' : 'bg-secondary') ?>">
                                                    <?= ucfirst($cat) ?>
                                                </span>
                                            </td>
                                            <td><?= $producto['cantidad'] ?></td>
                                            <td>$<?= number_format($producto['total'], 2) ?></td>
                                            <td>$<?= number_format($reinversion, 2) ?></td>
                                            <td class="<?= $beneficio >= 0 ? 'text-success' : 'text-danger' ?>">
                                                $<?= number_format($beneficio, 2) ?>
                                            </td>
                                            <td class="<?= $margen >= 20 ? 'text-success' : ($margen >= 10 ? 'text-warning' : 'text-danger') ?>">
                                                <?= number_format($margen, 2) ?>%
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Tab Cuadro Comparativo Profesional -->
                        <div class="tab-pane fade" id="analisisComparativo">
                            <div class="table-responsive mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="m-0 font-weight-bold"><i class="bi bi-house-gear"></i> Gastos Fijos</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="chart-container">
                                                    <canvas id="gastosFijosBarChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header bg-danger text-white">
                                                <h6 class="m-0 font-weight-bold"><i class="bi bi-speedometer2"></i> Gastos Variables</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="chart-container">
                                                    <canvas id="gastosVariablesBarChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // =======================
    // DATOS PARA GRÁFICOS JS
    // =======================
    <?php
    // Inicializar arrays de fechas
    $fechas = array_keys($ventas_diarias);
    $ventas_yerbas = array_fill_keys($fechas, 0);
    $ventas_mates = array_fill_keys($fechas, 0);
    $ventas_envio = array_fill_keys($fechas, 0);

    // Consulta para ventas diarias por producto
    $query_ventas_cat = "
        SELECT v.fecha, p.nombre, dv.subtotal
        FROM detalle_ventas dv
        JOIN productos p ON dv.producto_id = p.id
        JOIN ventas v ON dv.venta_id = v.id
        WHERE v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
        AND v.estado = 'pagada'
    ";
    $res_ventas_cat = $conn->query($query_ventas_cat);
    while ($row = $res_ventas_cat->fetch_assoc()) {
        $fecha = $row['fecha'];
        $nombre = strtolower($row['nombre']);
        $subtotal = (float)$row['subtotal'];
        $cat = 'otros';
        foreach ($patrones_yerba as $pat) {
            if (str_contains($nombre, $pat)) {
                $cat = 'yerbas';
                break;
            }
        }
        if ($cat === 'otros') {
            foreach ($patrones_mate as $pat) {
                if (str_contains($nombre, $pat)) {
                    $cat = 'mates';
                    break;
                }
            }
        }
        if ($cat === 'yerbas') $ventas_yerbas[$fecha] += $subtotal;
        elseif ($cat === 'mates') $ventas_mates[$fecha] += $subtotal;
    }
    // Consulta para ventas diarias de envío
    $query_envio_diario = "SELECT fecha, SUM(envio) as envio FROM ventas WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' GROUP BY fecha";
    $res_envio_diario = $conn->query($query_envio_diario);
    while ($row = $res_envio_diario->fetch_assoc()) {
        $fecha = $row['fecha'];
        $ventas_envio[$fecha] = (float)$row['envio'];
    }
    ?>
// Gráfico de ventas diarias 
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($fechas) ?>,
            datasets: [
                {
                    label: 'Yerbas',
                    data: <?= json_encode(array_values($ventas_yerbas)) ?>,
                    borderColor: '#2e8b57',
                    backgroundColor: 'rgba(46, 139, 87, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#2e8b57',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Mates',
                    data: <?= json_encode(array_values($ventas_mates)) ?>,
                    borderColor: '#8b4513',
                    backgroundColor: 'rgba(139, 69, 19, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#8b4513',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Envío',
                    data: <?= json_encode(array_values($ventas_envio)) ?>,
                    borderColor: '#36b9cc',
                    backgroundColor: 'rgba(54, 185, 204, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#36b9cc',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Total Venta',
                    data: <?= json_encode(array_values($ventas_diarias)) ?>,
                    borderColor: '#1e293b',
                    backgroundColor: 'transparent',
                    fill: false,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: '#1e293b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    borderDash: [8, 4],
                    order: 0
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        },
                        font: {
                            size: 11
                        },
                        color: '#64748b'
                    }
                },
                x: {
                    stacked: false,
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        color: '#64748b'
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#fff'
                }
            }
        }
    });

    // Gráfico de distribución 
    const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
    new Chart(distribucionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Yerbas', 'Mates', 'Envío'],
            datasets: [{
                data: [
                    <?= $categorias['yerbas']['ventas'] ?>,
                    <?= $categorias['mates']['ventas'] ?>,
                    <?= $total_envio ?>
                ],
                backgroundColor: [
                    '#2e8b57', // Verde Yerba
                    '#8b4513', // Marrón Mate
                    '#36b9cc'  // Celeste para Envío
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
                hoverOffset: 8
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': $' + context.raw.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // Gastos Fijos Bar Chart (por categoría)
    const gastosFijosBarCtx = document.getElementById('gastosFijosBarChart').getContext('2d');
    <?php
    $gastos_fijos_cat = [];
    $query_gastos_fijos_cat = "SELECT categoria, SUM(monto) as total FROM gastos WHERE tipo='fijo' AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' GROUP BY categoria";
    $res_gastos_fijos_cat = $conn->query($query_gastos_fijos_cat);
    while ($row = $res_gastos_fijos_cat->fetch_assoc()) {
        $gastos_fijos_cat[$row['categoria'] ?: 'Sin categoría'] = (float)$row['total'];
    }
    ?>
    new Chart(gastosFijosBarCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($gastos_fijos_cat)) ?>,
            datasets: [{
                label: 'Gastos Fijos',
                data: <?= json_encode(array_values($gastos_fijos_cat)) ?>,
                backgroundColor: '#f6c23e'
            }]
        },
        options: {
            indexAxis: 'x',
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gastos Variables Bar Chart (por categoría)
    const gastosVariablesBarCtx = document.getElementById('gastosVariablesBarChart').getContext('2d');
    <?php
    $gastos_variables_cat = [];
    $query_gastos_variables_cat = "SELECT categoria, SUM(monto) as total FROM gastos WHERE tipo='variable' AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' GROUP BY categoria";
    $res_gastos_variables_cat = $conn->query($query_gastos_variables_cat);
    while ($row = $res_gastos_variables_cat->fetch_assoc()) {
        $gastos_variables_cat[$row['categoria'] ?: 'Sin categoría'] = (float)$row['total'];
    }
    ?>
    new Chart(gastosVariablesBarCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($gastos_variables_cat)) ?>,
            datasets: [{
                label: 'Gastos Variables',
                data: <?= json_encode(array_values($gastos_variables_cat)) ?>,
                backgroundColor: '#e74a3b'
            }]
        },
        options: {
            indexAxis: 'x',
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
<script src="js/dashboard-validations.js"></script>
</body>
</html>




