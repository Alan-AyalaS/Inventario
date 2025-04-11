<?php
// Asegurarnos de que no haya salida previa
while (ob_get_level()) {
    ob_end_clean();
}

// Asegurarnos de que los archivos requeridos se carguen correctamente
require_once(__DIR__ . "/../model/ProductData.php");
require_once(__DIR__ . "/../model/OperationData.php");
require_once(__DIR__ . "/../../controller/Executor.php");

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
            setcookie("prddel", "Error al eliminar productos: " . $e->getMessage(), time()+3600, "/");
        }
        
        // Construir URL de redirección con los filtros actuales
        $redirect_url = 'index.php?view=inventary';
        
        // Parámetros de filtro a mantener
        $filter_params = [
            'category_id', 'availability', 'size', 'date_filter', 
            'search', 'limit', 'jerseyType', 'page'
        ];
        
        // Agregar cada parámetro de filtro que exista
        foreach ($filter_params as $param) {
            if (isset($_POST[$param]) && $_POST[$param] !== '') {
                $redirect_url .= '&' . $param . '=' . urlencode($_POST[$param]);
            }
        }
        
        header("Location: " . $redirect_url);
        exit;
    }
} 