<?php
// Evitar cualquier output antes del JSON
ob_start();

// Incluir las clases necesarias
require_once 'core/app/model/ProductData.php';
require_once 'core/app/model/OperationData.php';

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Verificar que todos los datos requeridos estén presentes
if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !isset($_POST['operation_type'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

try {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    $operation_type = $_POST['operation_type'];

    // Validar que la cantidad sea positiva
    if ($quantity <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor que 0']);
        exit;
    }

    // Obtener el producto
    $product = ProductData::getById($product_id);
    if (!$product) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }

    // Calcular el nuevo stock
    $current_stock = $product->availability;
    $new_stock = $operation_type === 'add' ? 
        $current_stock + $quantity : 
        $current_stock - $quantity;

    // Validar que el stock no sea negativo
    if ($new_stock < 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No hay suficiente stock disponible']);
        exit;
    }

    // Actualizar el stock
    $product->availability = $new_stock;
    $result = $product->update();

    if ($result) {
        // Registrar la operación en el historial
        $op = new OperationData();
        $op->product_id = $product_id;
        $op->operation_type_id = $operation_type === 'add' ? 1 : 2; // 1 para entrada, 2 para salida
        $op->q = $quantity;
        $op->operation_type = $operation_type;
        $op->add();

        ob_end_clean();
        echo json_encode([
            'success' => true, 
            'message' => 'Stock actualizado correctamente',
            'new_stock' => $new_stock
        ]);
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el stock en la base de datos']);
    }
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 