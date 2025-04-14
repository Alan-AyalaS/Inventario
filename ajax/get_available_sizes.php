<?php
// Asegurarnos de que no haya output antes de los headers
ob_start();

// Habilitar la visualización de errores para debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función para devolver respuesta JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    echo json_encode($data);
    exit;
}

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

    // Recopilar información de debugging
    $debug_info = [
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'category_id' => $product->category_id,
            'jersey_type' => isset($product->jersey_type) ? $product->jersey_type : null,
            'size' => isset($product->size) ? $product->size : null
        ]
    ];

    // Construir la consulta SQL
    $sql = "SELECT DISTINCT p.size as talla, SUM(p.availability) as cantidad 
            FROM product p 
            WHERE p.name = ? 
            AND p.category_id = ? 
            AND p.availability > 0";
    
    $params = [$product->name, $product->category_id];

    // Si es un jersey, agregar el filtro por tipo de jersey
    if ($product->category_id == 1) {
        $sql .= " AND p.jersey_type = ?";
        $params[] = $product->jersey_type;
    }

    // Agregar GROUP BY al final
    $sql .= " GROUP BY p.size";

    // Agregar información de la consulta al debug
    $debug_info['sql'] = [
        'query' => $sql,
        'params' => $params
    ];

    // Intentar ejecutar la consulta
    try {
        // Obtener la conexión a la base de datos
        $db = new Database();
        $con = $db->connect();
        if (!$con) {
            throw new Exception('No se pudo conectar a la base de datos');
        }

        // Preparar la consulta
        $stmt = $con->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error al preparar la consulta: ' . $con->error);
        }

        // Vincular los parámetros
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        // Ejecutar la consulta
        if (!$stmt->execute()) {
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        // Obtener los resultados
        $result = $stmt->get_result();
        $tallas = [];
        while ($row = $result->fetch_assoc()) {
            $tallas[] = $row;
        }

        $debug_info['status'] = 'Tallas obtenidas exitosamente';
        $debug_info['tallas_count'] = count($tallas);
        $debug_info['tallas'] = $tallas;

    } catch (Exception $e) {
        throw new Exception('Error en la consulta SQL: ' . $e->getMessage());
    }

    // Limpiar cualquier output anterior
    ob_clean();
    
    // Enviar respuesta con información de debugging
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'sizes' => $tallas,
        'debug_info' => $debug_info
    ]);

} catch (Throwable $e) {
    // Limpiar cualquier output anterior
    ob_clean();
    
    // Enviar respuesta de error con información detallada
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug_info' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'type' => get_class($e)
        ]
    ]);
}
?> 