<?php
header('Content-Type: application/json');

// Incluir configuración de base de datos
include '../config/config.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$productos = $input['productos'] ?? [];

if (empty($productos)) {
    echo json_encode(['success' => false, 'message' => 'No se enviaron productos para verificar']);
    exit;
}

try {
    $errores = [];
    $productosVerificados = [];
    
    foreach ($productos as $item) {
        $producto_id = intval($item['producto_id']);
        $cantidad_solicitada = intval($item['cantidad']);
        
        if ($producto_id <= 0 || $cantidad_solicitada <= 0) {
            continue; // Saltar productos inválidos
        }
        
        // Consultar información del producto
        $stmt = $conn->prepare("SELECT id, nombre, stock, estado FROM productos WHERE id = ?");
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $errores[] = "Producto con ID {$producto_id} no encontrado";
            continue;
        }
        
        $producto = $result->fetch_assoc();
        
        // Verificar si el producto está activo
        if ($producto['estado'] !== 'activo') {
            $errores[] = "El producto '{$producto['nombre']}' (ID: {$producto['id']}) no está disponible";
            continue;
        }
        
        // Verificar stock disponible
        if ($producto['stock'] < $cantidad_solicitada) {
            if ($producto['stock'] <= 0) {
                $errores[] = "El producto '{$producto['nombre']}' (ID: {$producto['id']}) está agotado (stock: {$producto['stock']})";
            } else {
                $errores[] = "El producto '{$producto['nombre']}' (ID: {$producto['id']}) tiene stock insuficiente. Disponible: {$producto['stock']}, solicitado: {$cantidad_solicitada}";
            }
            continue;
        }
        
        // Verificar si quedaría en stock negativo
        $stock_resultante = $producto['stock'] - $cantidad_solicitada;
        if ($stock_resultante < 0) {
            $errores[] = "El producto '{$producto['nombre']}' (ID: {$producto['id']}) quedaría en stock negativo. Stock actual: {$producto['stock']}, cantidad solicitada: {$cantidad_solicitada}";
            continue;
        }
        
        // Si llega aquí, el producto está OK
        $productosVerificados[] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'stock_actual' => $producto['stock'],
            'cantidad_solicitada' => $cantidad_solicitada,
            'stock_resultante' => $stock_resultante
        ];
        
        $stmt->close();
    }
    
    // Respuesta
    if (!empty($errores)) {
        echo json_encode([
            'success' => false,
            'message' => 'Problemas de stock detectados',
            'errores' => $errores,
            'productos_verificados' => $productosVerificados
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Todos los productos tienen stock suficiente',
            'productos_verificados' => $productosVerificados
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la verificación: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>