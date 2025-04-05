<?php
require_once "core/app/model/Product.php";
require_once "core/app/model/Operation.php";

class ProductController {
    public function create_test_products() {
        try {
            // Verificar que la solicitud sea POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Obtener y decodificar los datos JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }
            
            if (!isset($data['products']) || !is_array($data['products'])) {
                throw new Exception('Datos de productos inválidos');
            }
            
            $success = true;
            $message = '';
            $created = 0;
            
            foreach ($data['products'] as $productData) {
                $product = new Product();
                $product->name = $productData['name'];
                $product->category_id = $productData['category_id'];
                $product->price_in = $productData['price_in'];
                $product->price_out = $productData['price_out'];
                $product->unit = $productData['unit'];
                $product->inventary_min = $productData['inventary_min'];
                
                // Guardar el producto
                $product_id = $product->add();
                
                if ($product_id) {
                    // Crear una operación inicial con la disponibilidad aleatoria
                    $operation = new Operation();
                    $operation->product_id = $product_id;
                    $operation->q = $productData['availability'];
                    $operation->operation_type_id = 1; // 1 = entrada
                    $operation->created_at = date('Y-m-d H:i:s');
                    $operation->add();
                    $created++;
                } else {
                    $success = false;
                    $message .= "Error al crear el producto: {$productData['name']}\n";
                }
            }
            
            // Establecer los headers correctos
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Content-Type');
            
            echo json_encode([
                'success' => $success,
                'message' => $success ? "Productos creados exitosamente: $created" : $message
            ]);
            
        } catch (Exception $e) {
            // Establecer los headers correctos
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST');
            header('Access-Control-Allow-Headers: Content-Type');
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

// Crear una instancia del controlador y ejecutar el método
if (isset($_GET['action']) && $_GET['action'] === 'create_test_products') {
    $controller = new ProductController();
    $controller->create_test_products();
    exit;
} 