<?php
// Asegurarnos de que no haya salida previa
while (ob_get_level()) {
    ob_end_clean();
}

// Asegurarnos de que los archivos requeridos se carguen correctamente
require_once(__DIR__ . "/../model/ProductData.php");

class DeleteProductsController {
    public function index() {
        try {
            // Establecer el tipo de contenido como JSON
            header('Content-Type: application/json');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_POST['product_ids'])) {
                throw new Exception('No se recibieron IDs de productos');
            }

            $product_ids = json_decode($_POST['product_ids'], true);
            
            if (!is_array($product_ids)) {
                throw new Exception('IDs de productos no válidos');
            }

            $success = true;
            $message = '';
            $deleted_ids = [];

            foreach ($product_ids as $id) {
                try {
                    // Verificar si el producto existe antes de intentar eliminarlo
                    $product = ProductData::getById($id);
                    if (!$product) {
                        throw new Exception('Producto con ID ' . $id . ' no encontrado');
                    }

                    // Usar el método del() que maneja la eliminación de operaciones asociadas
                    $result = $product->del();
                    if (!$result) {
                        throw new Exception('Error al eliminar el producto con ID: ' . $id);
                    }
                    $deleted_ids[] = $id;
                } catch (Exception $e) {
                    $success = false;
                    $message = $e->getMessage();
                    break;
                }
            }

            if ($success) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Productos eliminados exitosamente',
                    'deleted_ids' => $deleted_ids
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => $message,
                    'deleted_ids' => $deleted_ids
                ]);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
} 