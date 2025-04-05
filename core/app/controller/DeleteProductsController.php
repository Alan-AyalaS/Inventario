<?php
// Evitar cualquier salida antes de los headers
ob_start();

class DeleteProductsController {
    public function __construct() {
        if (!isset($_SESSION["user_id"])) {
            $this->sendJsonResponse(false, 'No autorizado');
            return;
        }
    }

    private function sendJsonResponse($success, $message) {
        // Limpiar cualquier salida previa
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Establecer los encabezados correctos
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('X-Content-Type-Options: nosniff');
        
        // Enviar la respuesta JSON
        echo json_encode([
            'success' => $success,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($data['product_ids']) || empty($data['product_ids'])) {
                    $this->sendJsonResponse(false, 'No se seleccionaron productos');
                    return;
                }

                $success = true;
                $message = '';
                $deleted_count = 0;
                $errors = [];

                foreach ($data['product_ids'] as $product_id) {
                    $product = ProductData::getById($product_id);
                    if ($product) {
                        try {
                            if ($product->del()) {
                                $deleted_count++;
                            } else {
                                $success = false;
                                $errors[] = "Error al eliminar el producto con ID: $product_id";
                                break;
                            }
                        } catch (Exception $e) {
                            $success = false;
                            $errors[] = "Error al eliminar el producto con ID: $product_id - " . $e->getMessage();
                            break;
                        }
                    } else {
                        $success = false;
                        $errors[] = "Producto con ID: $product_id no encontrado";
                        break;
                    }
                }

                if ($success) {
                    $this->sendJsonResponse(true, 'Se eliminaron ' . $deleted_count . ' productos correctamente');
                } else {
                    $this->sendJsonResponse(false, implode("\n", $errors));
                }
            } catch (Exception $e) {
                $this->sendJsonResponse(false, 'Error: ' . $e->getMessage());
            }
        } else {
            $this->sendJsonResponse(false, 'Método no permitido');
        }
    }
}
?> 