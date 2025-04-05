<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION["user_id"])) {
    die("Debes iniciar sesión para ejecutar este script.");
}

// Incluir los archivos necesarios
include "core/app/model/ProductData.php";
include "core/app/model/CategoryData.php";
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";

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
    die("No hay categorías disponibles. Por favor, crea algunas categorías primero.");
}

$success_count = 0;
$error_count = 0;
$results = [];

foreach ($categories as $category) {
    $results[] = "Generando productos para la categoría: " . $category->name;
    
    for ($i = 1; $i <= 10; $i++) {
        $product = new ProductData();
        $product->barcode = generateBarcode();
        $product->name = generateProductName($category->name);
        $product->description = "Producto de ejemplo para la categoría " . $category->name;
        $product->price_in = rand(100, 1000);
        $product->price_out = $product->price_in * 1.3; // 30% de ganancia
        $product->unit = "unidad";
        $product->user_id = $_SESSION["user_id"]; // Usar el ID del usuario actual
        $product->presentation = "1";
        $product->category_id = $category->id;
        $product->inventary_min = rand(5, 20);
        $product->is_active = 1;
        
        try {
            if ($product->add()) {
                $success_count++;
                $results[] = "✓ Producto creado: " . $product->name;
            } else {
                $error_count++;
                $results[] = "✗ Error al crear producto: " . $product->name;
            }
        } catch (Exception $e) {
            $error_count++;
            $results[] = "✗ Error al crear producto: " . $e->getMessage();
        }
    }
}

$results[] = "\nResumen:";
$results[] = "Productos creados exitosamente: " . $success_count;
$results[] = "Errores: " . $error_count;

// Mostrar resultados en formato HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador de Productos</title>
    <link href="res/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="res/font-awesome/css/font-awesome.min.css">
    <style>
        body { padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Resultados de la Generación de Productos</h1>
        <div class="card">
            <div class="card-body">
                <?php foreach ($results as $result): ?>
                    <div class="<?php echo strpos($result, '✓') !== false ? 'success' : (strpos($result, '✗') !== false ? 'error' : ''); ?>">
                        <pre><?php echo $result; ?></pre>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <br>
        <a href="index.php?view=inventary" class="btn btn-primary">Volver al Inventario</a>
    </div>
</body>
</html> 