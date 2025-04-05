<?php
// Incluir solo las clases necesarias
require_once("core/app/model/Executor.php");

// Verificar si el usuario está logueado
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: index.php");
    exit;
}

// Obtener los productos de prueba
$sql = "SELECT id, name FROM product WHERE description = 'Producto de prueba generado automáticamente'";
$products = Executor::doit($sql);

if(!is_array($products)) {
    $products = array();
}

$total_products = count($products);
$deleted_products = 0;
$errors = 0;

echo "<h2>Eliminando productos de prueba...</h2>";

foreach($products as $product) {
    try {
        // Primero eliminar las operaciones asociadas
        $sql = "DELETE FROM operation WHERE product_id = " . $product['id'];
        Executor::doit($sql);
        
        // Luego eliminar el producto
        $sql = "DELETE FROM product WHERE id = " . $product['id'];
        Executor::doit($sql);
        
        echo "✅ Producto eliminado: " . $product['name'] . "<br>";
        $deleted_products++;
    } catch(Exception $e) {
        echo "❌ Error al eliminar producto: " . $product['name'] . " - " . $e->getMessage() . "<br>";
        $errors++;
    }
}

echo "<h3>Resumen:</h3>";
echo "Total de productos de prueba encontrados: " . $total_products . "<br>";
echo "Productos eliminados exitosamente: " . $deleted_products . "<br>";
echo "Errores: " . $errors . "<br>";

echo "<br><a href='index.php'>Volver al inicio</a>";
?> 