<?php
// Asegurarnos de que no haya output antes de los headers
ob_start();

// Habilitar la visualización de errores para debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Definir la ruta base
    define('ROOT_PATH', dirname(dirname(__FILE__)));
    
    // Incluir el autoload y los modelos necesarios
    if (!file_exists(ROOT_PATH . '/core/autoload.php')) {
        throw new Exception("No se pudo encontrar el archivo autoload.php");
    }
    require_once ROOT_PATH . '/core/autoload.php';
    
    // Incluir los modelos necesarios
    require_once ROOT_PATH . '/core/app/model/ProductData.php';
    require_once ROOT_PATH . '/core/app/model/CategoryData.php';
    require_once ROOT_PATH . '/core/app/model/OperationData.php';
    require_once ROOT_PATH . '/core/controller/Database.php';

    if (!isset($_GET['product_id'])) {
        throw new Exception('ID de producto no proporcionado');
    }

    $product_id = intval($_GET['product_id']);
    $product = ProductData::getById($product_id);

    if (!$product) {
        throw new Exception('Producto no encontrado');
    }

    // Definir las tallas disponibles por tipo de jersey
    $tallas_por_tipo = [
        'adulto' => ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '6XL', '8XL'],
        'dama' => ['S', 'M', 'L', 'XL', 'XXL'],
        'niño' => ['16', '18', '20', '22', '24', '26']
    ];

    // Obtener las tallas ya registradas para este producto
    $db = new Database();
    $con = $db->connect();
    
    $sql = "SELECT DISTINCT size FROM product WHERE name = ? AND category_id = ?";
    $params = [$product->name, $product->category_id];
    
    if ($product->category_id == 1) { // Si es jersey
        $sql .= " AND jersey_type = ?";
        $params[] = $product->jersey_type;
    }
    
    $stmt = $con->prepare($sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tallas_registradas = [];
    while ($row = $result->fetch_assoc()) {
        $tallas_registradas[] = $row['size'];
    }

    // Obtener las tallas disponibles según el tipo de jersey
    $tallas_disponibles = [];
    if ($product->category_id == 1) { // Si es jersey
        $tipo_jersey = $product->jersey_type;
        if (isset($tallas_por_tipo[$tipo_jersey])) {
            // Filtrar las tallas que no están registradas
            $tallas_disponibles = array_diff($tallas_por_tipo[$tipo_jersey], $tallas_registradas);
        }
    }

    // Limpiar cualquier output anterior
    ob_clean();
    
    // Enviar respuesta
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'sizes' => array_values($tallas_disponibles) // array_values para reindexar el array
    ]);

} catch (Throwable $e) {
    // Limpiar cualquier output anterior
    ob_clean();
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 