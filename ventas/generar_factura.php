<?php
require '../config/config.php';
require __DIR__ . '/libraries/fpdf/fpdf.php';

class FacturaPDF extends FPDF {
    // Cabecera de página
    function Header() {
        // Logo
        $this->Image('../img/vamates.jpg', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, 'FACTURA DE VENTA', 0, 0, 'C');
        // Salto de línea
        $this->Ln(30);
    }

    // Pie de página
    function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Pagina '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

if (isset($_GET['id_venta'])) {
    $id_venta = $_GET['id_venta'];
    
    // Primero obtenemos los datos de la venta
    $query_venta = "SELECT v.id, v.fecha, v.total, v.envio, v.estado, v.numero_factura
            FROM ventas v 
            WHERE v.id = ?";
    $stmt_venta = $conn->prepare($query_venta);
    $stmt_venta->bind_param("i", $id_venta);
    $stmt_venta->execute();
    $result_venta = $stmt_venta->get_result();
    $venta = $result_venta->fetch_assoc();

    // Si no tiene número de factura, lo generamos y actualizamos
    if (empty($venta['numero_factura'])) {
        $numero_factura = 'FAC-' . date('Y') . '-' . str_pad($venta['id'], 5, '0', STR_PAD_LEFT);
        $update_query = "UPDATE ventas SET numero_factura = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("si", $numero_factura, $id_venta);
        $stmt_update->execute();
        
        // Volvemos a obtener los datos para tener el número actualizado
        $stmt_venta->execute();
        $result_venta = $stmt_venta->get_result();
        $venta = $result_venta->fetch_assoc();
    }

    // Obtener detalles de la venta
    $query_detalles = "SELECT dv.*, p.nombre as producto_nombre 
                        FROM detalle_ventas dv 
                        LEFT JOIN productos p ON dv.producto_id = p.id 
                        WHERE dv.venta_id = ?";
    $stmt_detalles = $conn->prepare($query_detalles);
    $stmt_detalles->bind_param("i", $id_venta);
    $stmt_detalles->execute();
    $detalles_venta = $stmt_detalles->get_result();
    
    // Crear PDF
    $pdf = new FacturaPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Número de factura y fecha
    $pdf->Cell(0, 10, 'Factura #: ' . $venta['numero_factura'], 0, 1);
    $pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y', strtotime($venta['fecha'])), 0, 1);

    $pdf->Ln(10);


// Tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Producto', 1, 0, 'C');
$pdf->Cell(20, 10, 'Cant.', 1, 0, 'C');
$pdf->Cell(25, 10, 'Precio', 1, 0, 'C');
$pdf->Cell(25, 10, 'Desc. %', 1, 0, 'C');
$pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$total = 0;
$total_descuento = 0;
while ($detalle = $detalles_venta->fetch_assoc()) {
    $precio = isset($detalle['precio_unitario']) ? $detalle['precio_unitario'] : (isset($detalle['precio']) ? $detalle['precio'] : 0);
    $cantidad = isset($detalle['cantidad']) ? $detalle['cantidad'] : 0;
    $descuento = isset($detalle['descuento']) ? floatval($detalle['descuento']) : 0; // porcentaje
    $subtotal_sin_desc = $cantidad * $precio;
    $monto_descuento = $subtotal_sin_desc * ($descuento / 100);
    $subtotal = $subtotal_sin_desc - $monto_descuento;
    $total += $subtotal;
    $total_descuento += $monto_descuento;

    $pdf->Cell(60, 10, $detalle['producto_nombre'], 1);
    $pdf->Cell(20, 10, $cantidad, 1, 0, 'C');
    $pdf->Cell(25, 10, '$' . number_format($precio, 2), 1, 0, 'R');
    $pdf->Cell(25, 10, $descuento > 0 ? $descuento . '%' : '-', 1, 0, 'C');
    $pdf->Cell(40, 10, '$' . number_format($subtotal, 2), 1, 1, 'R');
}

// Mostrar total descuento si hubo alguno
if ($total_descuento > 0) {
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(130, 10, 'Total descuento aplicado', 1, 0, 'R');
    $pdf->Cell(50, 10, '-$' . number_format($total_descuento, 2), 1, 1, 'R');
}

// Detalle del envío
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(130, 10, 'Envio', 1, 0, 'R');
$pdf->Cell(50, 10, '$' . number_format($venta['envio'], 2), 1, 1, 'R');
$total_final = $total + floatval($venta['envio']);

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 10, 'TOTAL:', 1, 0, 'R');
$pdf->Cell(50, 10, '$' . number_format($total_final, 2), 1, 1, 'R');





    // Salida del PDF
    $pdf->Output('D', 'Factura_' . $venta['numero_factura'] . '.pdf');
}
?>