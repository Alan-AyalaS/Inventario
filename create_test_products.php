<?php
// Incluir solo las clases necesarias
require_once("core/app/model/ProductData.php");
require_once("core/app/model/CategoryData.php");
require_once("core/app/model/OperationData.php");
require_once("core/app/model/OperationTypeData.php");
require_once("core/app/model/Executor.php");

// Verificar si el usuario está logueado
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: index.php");
    exit;
}

// Función para generar nombres de productos aleatorios
function generateProductName($category) {
    $prefixes = ['Basic', 'Premium', 'Pro', 'Elite', 'Super', 'Ultra', 'Mega', 'Gold', 'Silver'];
    $suffixes = ['Lite', 'Standard', 'Deluxe', 'Plus', 'Max', 'Pro', 'Elite'];
    
    $prefix = $prefixes[array_rand($prefixes)];
    $suffix = $suffixes[array_rand($suffixes)];
    
    return $prefix . " " . $category->name . " " . $suffix;
}

// Función para generar códigos de barras únicos
function generateBarcode() {
    return "TEST" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Obtener todas las categorías
$categories = CategoryData::getAll();

$total_products = 0;
$success_count = 0;
$error_count = 0;
$errors = array();

echo "<h2>Generando productos de prueba...</h2>";

foreach($categories as $category) {
    for($i = 0; $i < 10; $i++) {
        try {
            // Crear el producto
            $p = new ProductData();
            $p->barcode = generateBarcode();
            $p->name = generateProductName($category);
            $p->description = "Producto de prueba generado automáticamente";
            $p->price_in = rand(100, 1000);
            $p->price_out = $p->price_in * 1.3; // 30% de ganancia
            $p->unit = "unidad";
            $p->user_id = $_SESSION["user_id"];
            $p->category_id = $category->id;
            $p->inventary_min = rand(5, 20);
            $p->presentation = "0";
            
            // 70% de probabilidad de tener stock inicial
            $cantidad = (rand(1, 100) <= 70) ? rand(1, 200) : 0;
            $p->availability = $cantidad;
            
            // Agregar el producto
            $sql = "INSERT INTO product (barcode, name, description, price_in, price_out, user_id, presentation, unit, category_id, inventary_min, availability, created_at) ";
            $sql .= "VALUES ('" . $p->barcode . "', '" . $p->name . "', '" . $p->description . "', " . $p->price_in . ", " . $p->price_out . ", " . $p->user_id . ", '" . $p->presentation . "', '" . $p->unit . "', " . $p->category_id . ", " . $p->inventary_min . ", " . $p->availability . ", NOW())";
            
            $result = Executor::doit($sql);
            if($result) {
                $total_products++;
                
                // Obtener el ID del producto recién creado
                $sql = "SELECT id FROM product WHERE barcode = '" . $p->barcode . "' ORDER BY id DESC LIMIT 1";
                $query = Executor::doit($sql);
                if($query[0]->num_rows > 0) {
                    $product_id = $query[0]->fetch_assoc()['id'];
                    
                    if($cantidad > 0) {
                        // Crear la operación de entrada
                        $op = new OperationData();
                        $op->product_id = $product_id;
                        $op->operation_type_id = 1; // 1 = entrada
                        $op->q = $cantidad;
                        $op->is_oficial = 1;
                        $op->created_at = "NOW()";
                        
                        $sql = "INSERT INTO operation (product_id, operation_type_id, q, is_oficial, created_at) ";
                        $sql .= "VALUES (" . $op->product_id . ", " . $op->operation_type_id . ", " . $op->q . ", " . $op->is_oficial . ", NOW())";
                        
                        if(Executor::doit($sql)) {
                            echo "✅ Producto creado: " . $p->name . " (Stock inicial: " . $cantidad . ")<br>";
                        } else {
                            throw new Exception("Error al crear operación de entrada");
                        }
                    } else {
                        echo "✅ Producto creado: " . $p->name . " (Sin stock inicial)<br>";
                    }
                    
                    $success_count++;
                } else {
                    throw new Exception("No se pudo obtener el ID del producto creado");
                }
            } else {
                throw new Exception("Error al crear el producto en la base de datos");
            }
        } catch(Exception $e) {
            $error_count++;
            $errors[] = "Error al crear producto: " . $p->name . " - " . $e->getMessage();
            echo "❌ Error al crear producto: " . $p->name . " - " . $e->getMessage() . "<br>";
        }
    }
}

echo "<h3>Resumen:</h3>";
echo "Total de productos a crear: " . $total_products . "<br>";
echo "Productos creados exitosamente: " . $success_count . "<br>";
echo "Errores: " . $error_count . "<br>";

if(count($errors) > 0) {
    echo "<h4>Detalles de errores:</h4>";
    foreach($errors as $error) {
        echo $error . "<br>";
    }
}

echo "<br><a href='index.php?view=inventary'>Volver al inventario</a>";
?> 