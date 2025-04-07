<?php
// Desactivar la visualización de errores
error_reporting(0);
ini_set('display_errors', 0);

// Limpiar cualquier salida previa
ob_clean();

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
        
        if($result[0]) {
            // Actualizar la disponibilidad del producto
            $product = ProductData::getById($product_id);
            if($product) {
                // Obtener la disponibilidad actual
                $current_availability = $product->availability;
                
                // Calcular la nueva disponibilidad según el tipo de operación
                $new_availability = $operation_type === 'add' ? 
                    $current_availability + $quantity : 
                    $current_availability - $quantity;
                
                // Actualizar la disponibilidad y el total
                $product->availability = $new_availability;
                $product->total = $new_availability;
                
                // Actualizar el producto
                $update_result = $product->update();
                
                if(!$update_result[0]) {
                    throw new Exception("Error al actualizar el producto");
                }
            }
            
            // Redirigir a la página de inventario
            header("Location: index.php?view=inventary");
            exit;
        } else {
            throw new Exception("Error al guardar la operación");
        }
    } else {
        throw new Exception("Faltan parámetros requeridos");
    }
} catch(Exception $e) {
    // Redirigir a la página de inventario con un mensaje de error
    header("Location: index.php?view=inventary&error=" . urlencode($e->getMessage()));
    exit;
}
?> 