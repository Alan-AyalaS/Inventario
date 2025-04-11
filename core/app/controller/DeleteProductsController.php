<?php
// Asegurarnos de que no haya salida previa
while (ob_get_level()) {
    ob_end_clean();
}

// Asegurarnos de que los archivos requeridos se carguen correctamente
require_once(__DIR__ . "/../model/ProductData.php");
require_once(__DIR__ . "/../model/OperationData.php");

class DeleteProductsController {
    public function index() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['product_ids'])) {
                    throw new Exception('No se recibieron IDs de productos');
                }

                $product_ids = json_decode($_POST['product_ids'], true);
                
                if (!is_array($product_ids)) {
                    throw new Exception('Formato de IDs inválido');
                }

                foreach ($product_ids as $id) {
                    $product = ProductData::getById($id);
                    if ($product) {
                        // Primero eliminar todas las operaciones asociadas
                        $sql = "DELETE FROM operation WHERE product_id = $id";
                        Executor::doit($sql);
                        
                        // Luego eliminar el producto
                        $sql = "DELETE FROM product WHERE id = $id";
                        Executor::doit($sql);
                    }
                }

                // Establecer cookie de éxito
                setcookie("prddel", "Productos eliminados exitosamente", time()+3600, "/");
            }
        } catch (Exception $e) {
            error_log("Error al eliminar productos: " . $e->getMessage());
        }
        
        // Redirigir a la vista de inventario
        header("Location: index.php?view=inventary");
        exit;
    }
} 