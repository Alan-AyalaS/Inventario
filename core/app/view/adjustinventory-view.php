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

    if(count($_POST)>0){
        // Verificar que todos los datos necesarios estén presentes
        if(!isset($_POST["product_id"]) || !isset($_POST["quantity"]) || !isset($_POST["operation_type"])) {
            throw new Exception("Faltan datos requeridos en la solicitud");
        }

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
            $response = [
                'success' => true,
                'message' => 'Inventario actualizado correctamente'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Error al actualizar el inventario: ' . ($result[1] ?? 'Error desconocido')
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