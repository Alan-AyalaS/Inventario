<?php
// Desactivar la visualización de errores
error_reporting(0);
ini_set('display_errors', 0);

// Limpiar cualquier salida previa
ob_clean();

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

try {
    // Incluir los archivos necesarios con las rutas corregidas
    require_once __DIR__ . "/../../controller/Core.php";
    require_once __DIR__ . "/../../controller/Database.php";
    require_once __DIR__ . "/../../controller/Executor.php";
    require_once __DIR__ . "/../../controller/Model.php";
    require_once __DIR__ . "/../model/OperationData.php";
    require_once __DIR__ . "/../model/OperationTypeData.php";
    require_once __DIR__ . "/../model/ProductData.php";

    if(isset($_POST["product_id"]) && isset($_POST["quantity"]) && isset($_POST["operation_type"])) {
        $product_id = $_POST["product_id"];
        $quantity = floatval($_POST["quantity"]);
        $operation_type = $_POST["operation_type"];
        
        // Verificar que los tipos de operación existan
        $entrada = OperationTypeData::getByName("entrada");
        $salida = OperationTypeData::getByName("salida");
        
        if(!$entrada || !$salida) {
            throw new Exception("No se encontraron los tipos de operación necesarios");
        }
        
        // Obtener el ID del tipo de operación
        $operation_type_id = $operation_type === 'add' ? 
            $entrada->id : 
            $salida->id;
        
        // Crear la operación
        $op = new OperationData();
        $op->product_id = $product_id;
        $op->operation_type_id = $operation_type_id;
        $op->q = $quantity;
        $op->sell_id = null;
        $op->is_oficial = 1;
        $op->created_at = "NOW()";
        
        $result = $op->add();
        
        if($result && $result[0]){
            // Obtener el nombre del producto
            $product = ProductData::getById($product_id);
            $product_name = $product ? $product->name : 'Producto';
            
            // Calcular la nueva disponibilidad basada en todas las operaciones
            $operations = OperationData::getAllByProductId($product_id);
            $newAvailability = 0;
            foreach ($operations as $op) {
                if ($op->operation_type_id == 1) { // Entrada
                    $newAvailability += $op->q;
                } else { // Salida
                    $newAvailability -= $op->q;
                }
            }
            $product->updateAvailability($newAvailability);
            
            // Crear el mensaje según el tipo de operación
            $message = $operation_type === 'add' ? 
                "Se agregaron {$quantity} unidades al producto '{$product_name}'" : 
                "Se restaron {$quantity} unidades del producto '{$product_name}'";
            
            // Establecer la cookie con una duración de 1 minuto
            setcookie('inventoryAlert', $message, time() + 60, '/', '', false, true);
            
            $response = [
                'success' => true,
                'message' => $message
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error al actualizar el inventario'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'No se recibieron datos POST'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ];
}

// Limpiar cualquier salida no deseada
ob_clean();

// Enviar la respuesta JSON
echo json_encode($response);

// Terminar la ejecución
exit;
?> 