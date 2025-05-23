<?php
/**
* Inventario Jersey
* @author Jorge Luis Ch
* @time 2024
**/

// Incluir las clases del sistema
require_once('core/controller/Core.php');
require_once('core/controller/Database.php');
require_once('core/controller/Executor.php');
require_once('core/controller/Model.php');

require_once('core/app/model/ProductData.php');
require_once('core/app/model/OperationData.php');
require_once('core/app/model/OperationTypeData.php');

// Activar registro de errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inicializar Core
Core::$root="";

// Obtener conexión a la base de datos
$db = Database::getCon();

// Registrar información para depuración
$debug_info = [];
$debug_info['request'] = $_POST;

// Verificar que los datos necesarios estén presentes
if(isset($_POST['product_id']) && isset($_POST['operation_type']) && isset($_POST['quantity'])) {
    
    $product_id = intval($_POST['product_id']);
    $operation_type = $_POST['operation_type'];
    $quantity = floatval($_POST['quantity']);
    
    $debug_info['product_id'] = $product_id;
    $debug_info['operation_type'] = $operation_type;
    $debug_info['quantity'] = $quantity;
    
    // Verificar datos válidos
    if($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }
    
    $message = '';
    $operation_type_id = 0;
    
    if($operation_type === 'add') {
        // Operación de entrada al inventario - ID 1
        $operation_type_id = 1;
        $message = 'Se agregaron '.$quantity.' unidades al inventario';
        $debug_info['operation_type_id'] = 1;
    } 
    else if($operation_type === 'subtract') {
        // Verificar que haya suficiente inventario
        $current_stock = OperationData::getQYesF($product_id);
        $debug_info['current_stock'] = $current_stock;
        
        if($current_stock < $quantity) {
            echo json_encode(['success' => false, 'message' => 'No hay suficiente stock. Stock actual: '.$current_stock]);
            exit;
        }
        
        // Operación de salida del inventario - ID 2
        $operation_type_id = 2;
        $message = 'Se restaron '.$quantity.' unidades del inventario';
        $debug_info['operation_type_id'] = 2;
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Tipo de operación inválido']);
        exit;
    }
    
    // Insertar directamente en la base de datos usando SQL
    $sql = "INSERT INTO operation (product_id, q, operation_type_id, sell_id, is_oficial, created_at) 
            VALUES ($product_id, $quantity, $operation_type_id, NULL, 1, NOW())";
    
    $result = $db->query($sql);
    $debug_info['sql'] = $sql;
    $debug_info['sql_error'] = $db->error;
    
    // Verificar si se guardó correctamente
    if ($result) {
        $insert_id = $db->insert_id;
        $debug_info['insert_id'] = $insert_id;
        
        // Registro exitoso
        echo json_encode(['success' => true, 'message' => $message, 'debug' => $debug_info]);
    } else {
        // Error al guardar
        echo json_encode(['success' => false, 'message' => 'Error al guardar la operación en la base de datos: '.$db->error, 'debug' => $debug_info]);
    }
} 
else {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos', 'post' => $_POST]);
}
?> 