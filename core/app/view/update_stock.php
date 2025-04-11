<?php
require_once __DIR__ . "/../../controller/Core.php";
require_once __DIR__ . "/../../controller/Database.php";
require_once __DIR__ . "/../../controller/Executor.php";
require_once __DIR__ . "/../../controller/Model.php";
require_once __DIR__ . "/../model/ProductData.php";

header('Content-Type: application/json');

try {
    if (!isset($_POST['product_id']) || !isset($_POST['new_stock'])) {
        throw new Exception('Faltan parámetros requeridos');
    }

    $product_id = intval($_POST['product_id']);
    $new_stock = floatval($_POST['new_stock']);

    if ($product_id <= 0 || $new_stock < 0) {
        throw new Exception('Datos inválidos');
    }

    $product = ProductData::getById($product_id);
    if (!$product) {
        throw new Exception('Producto no encontrado');
    }

    // Actualizar el stock
    $product->availability = $new_stock;
    $product->total = $new_stock;
    $result = $product->update();

    if ($result[0]) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error al actualizar el stock');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 