<?php
// Incluir los archivos necesarios directamente
require_once("core/app/model/ProductData.php");
require_once("core/app/model/CategoryData.php");
require_once("core/app/lib/Executor.php");
require_once("core/app/lib/Model.php");

// Función para generar un nombre aleatorio de producto
function generateProductName($category) {
    $prefixes = ['Super', 'Mega', 'Ultra', 'Pro', 'Elite', 'Premium', 'Gold', 'Silver', 'Basic', 'Standard'];
    $suffixes = ['Plus', 'Max', 'Lite', 'Pro', 'Elite', 'Gold', 'Silver', 'Basic', 'Standard', 'Deluxe'];
    
    $prefix = $prefixes[array_rand($prefixes)];
    $suffix = $suffixes[array_rand($suffixes)];
    
    return $prefix . ' ' . $category . ' ' . $suffix;
}

// Función para generar un código de barras aleatorio
function generateBarcode() {
    return 'PRD' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}

// Obtener todas las categorías
$categories = CategoryData::getAll();

if (empty($categories)) {
    echo "No hay categorías disponibles. Por favor, crea algunas categorías primero.\n";
    exit;
}

$success_count = 0;
$error_count = 0;

foreach ($categories as $category) {
    echo "Generando productos para la categoría: " . $category->name . "\n";
    
    for ($i = 1; $i <= 10; $i++) {
        $product = new ProductData();
        $product->barcode = generateBarcode();
        $product->name = generateProductName($category->name);
        $product->description = "Producto de ejemplo para la categoría " . $category->name;
        $product->price_in = rand(100, 1000);
        $product->price_out = $product->price_in * 1.3; // 30% de ganancia
        $product->unit = "unidad";
        $product->user_id = 1; // Asumiendo que el usuario con ID 1 existe
        $product->presentation = "1";
        $product->category_id = $category->id;
        $product->inventary_min = rand(5, 20);
        $product->is_active = 1;
        
        try {
            if ($product->add()) {
                $success_count++;
                echo "✓ Producto creado: " . $product->name . "\n";
            } else {
                $error_count++;
                echo "✗ Error al crear producto: " . $product->name . "\n";
            }
        } catch (Exception $e) {
            $error_count++;
            echo "✗ Error al crear producto: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nResumen:\n";
echo "Productos creados exitosamente: " . $success_count . "\n";
echo "Errores: " . $error_count . "\n";
?> 