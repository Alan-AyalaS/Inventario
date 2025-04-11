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

    // Actualizar tanto la disponibilidad como el total
    $product->availability = $new_stock;
    $product->total = $new_stock;
    
    // Asegurarse de que todos los campos necesarios estén establecidos
    if (!isset($product->description)) $product->description = "";
    if (!isset($product->jersey_type)) $product->jersey_type = "";
    if (!isset($product->is_active)) $product->is_active = 1;
    
    $result = $product->update();

    if ($result) {
        // Obtener la categoría del producto
        $category = $product->getCategory();
        $category_name = $category ? strtolower(trim($category->name)) : '';

        // Obtener todos los productos del mismo grupo
        if ($category_name === 'jersey') {
            // Para jerseys, agrupar por nombre, categoría y tipo de jersey
            $sql = "SELECT * FROM product WHERE name = \"$product->name\" AND category_id = $product->category_id AND jersey_type = \"$product->jersey_type\"";
        } else {
            // Para otras categorías, agrupar solo por nombre y categoría
            $sql = "SELECT * FROM product WHERE name = \"$product->name\" AND category_id = $product->category_id";
        }
        
        $query = Executor::doit($sql);
        $group_products = Model::many($query[0], new ProductData());
        
        // Calcular el nuevo total del grupo
        $group_total = 0;
        foreach ($group_products as $group_product) {
            $group_total += $group_product->availability;
        }
        
        // Actualizar el total en todos los productos del grupo
        foreach ($group_products as $group_product) {
            $group_product->total = $group_total;
            $group_product->update();
        }

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
            'new_stock' => $new_stock,
            'group_total' => $group_total,
            'debug' => [
                'availability' => $product->availability,
                'total' => $product->total,
                'group_products_count' => count($group_products),
                'category' => $category_name
            ]
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