<?php
// Iniciar el contador de tiempo
$start_time = microtime(true);

// Configurar la respuesta como JSON
header('Content-Type: application/json');

require_once __DIR__ . "/core/app/model/ProductData.php";
require_once __DIR__ . "/core/app/model/OperationData.php";
require_once __DIR__ . "/core/app/model/CategoryData.php";
require_once __DIR__ . "/core/controller/Database.php";
require_once __DIR__ . "/core/controller/Executor.php";
require_once __DIR__ . "/core/controller/Core.php";

try {
    // Crear productos de prueba
    $categories = [
        ['id' => 1, 'name' => 'Jerseys'],
        ['id' => 2, 'name' => 'Gorras'],
        ['id' => 3, 'name' => 'Tenis'],
        ['id' => 4, 'name' => 'Balones'],
        ['id' => 5, 'name' => 'Variado']
    ];

    $created = 0;
    $errors = [];
    $products = [];

    foreach ($categories as $category) {
        for ($i = 1; $i <= 10; $i++) {
            // Generar disponibilidad aleatoria entre 0 y 200
            $availability = rand(0, 200);
            
            // Crear el producto
            $product = new ProductData();
            $product->name = strtolower($category['name']) . " " . $i;
            $product->category_id = $category['id'];
            $product->price_in = number_format(rand(50, 150), 2);
            $product->price_out = number_format(rand(100, 200), 2);
            $product->unit = 'pz';
            $product->inventary_min = rand(5, 15);
            $product->barcode = "TEST" . rand(1000, 9999);
            $product->description = "Producto de prueba " . $i;
            $product->user_id = 1; // ID del usuario que crea el producto
            $product->presentation = "0";
            $product->is_active = 1;
            
            // Guardar el producto
            $product_id = $product->add();
            
            if ($product_id) {
                // Crear una operación inicial con la disponibilidad aleatoria
                $operation = new OperationData();
                $operation->product_id = $product_id;
                $operation->q = $availability;
                $operation->operation_type_id = 1; // 1 = entrada
                $operation->created_at = date('Y-m-d H:i:s');
                $operation->add();
                $created++;
                $products[] = [
                    'id' => $product_id,
                    'name' => $product->name,
                    'price' => $product->price_out,
                    'stock' => $availability,
                    'category' => $product->category_id,
                    'is_active' => $product->is_active
                ];
            } else {
                $errors[] = "Error al crear el producto: " . $product->name;
            }
        }
    }

    // Calcular el tiempo total de ejecución
    $total_time = microtime(true) - $start_time;

    // Preparar la respuesta JSON
    $response = [
        'success' => true,
        'total_products' => $created,
        'total_operations' => $created,
        'execution_time' => number_format($total_time, 2),
        'products' => $products,
        'errors' => $errors
    ];

    // Enviar la respuesta JSON
    echo json_encode($response);
} catch (Exception $e) {
    // En caso de error, devolver un JSON con el error
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
    echo json_encode($response);
} 