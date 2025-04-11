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

                // Eliminar cada producto
                foreach ($product_ids as $product_id) {
                    $product = ProductData::getById($product_id);
                    if ($product) {
                        // Obtener todos los productos del mismo grupo
                        $sql = "SELECT * FROM product WHERE name = '$product->name' AND category_id = $product->category_id";
                        if ($product->jersey_type) {
                            $sql .= " AND jersey_type = '$product->jersey_type'";
                        }
                        $query = Executor::doit($sql);
                        $group_products = Model::many($query[0], new ProductData());
                        
                        // Primero eliminar todas las operaciones asociadas al producto
                        $sql = "DELETE FROM operation WHERE product_id = $product_id";
                        Executor::doit($sql);
                        
                        // Luego eliminar el producto
                        $product->del();
                        
                        // Si hay más de un producto en el grupo
                        if (count($group_products) > 1) {
                            // Calcular el nuevo total sumando la disponibilidad de los productos restantes
                            $total_availability = 0;
                            foreach ($group_products as $group_product) {
                                if ($group_product->id != $product_id) { // Excluir el producto que se está eliminando
                                    $total_availability += $group_product->availability;
                                }
                            }
                            
                            // Actualizar el total en todos los productos del grupo
                            foreach ($group_products as $group_product) {
                                if ($group_product->id != $product_id) { // Excluir el producto que se está eliminando
                                    $group_product->total = $total_availability;
                                    $group_product->update();
                                }
                            }
                        }
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