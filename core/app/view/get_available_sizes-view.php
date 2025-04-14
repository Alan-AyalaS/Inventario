<?php
// Evitar que se incluya el template HTML
$is_ajax = true;

// Establecer headers para JSON
header('Content-Type: application/json');
header('X-Requested-With: XMLHttpRequest');

if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de producto no proporcionado']);
    exit;
}

$product_id = intval($_GET['product_id']);
$product = ProductData::getById($product_id);

if (!$product) {
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
    exit;
}

// Obtener todas las tallas de productos con el mismo nombre y categoría
$sql = "SELECT size as talla, availability as cantidad 
        FROM product 
        WHERE name = ? AND category_id = ? AND availability > 0";
$params = [$product->name, $product->category_id];

// Si es un jersey, agregar el filtro por tipo de jersey
if ($product->category_id == 1) {
    $sql .= " AND jersey_type = ?";
    $params[] = $product->jersey_type;
}

$query = Executor::doit($sql, $params);
$tallas = $query[0]->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'sizes' => $tallas
]);
exit; // Asegurarnos de que no se incluya nada más después de esto
?> 