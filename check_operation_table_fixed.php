<?php
require_once('core/controller/Core.php');
require_once('core/controller/Database.php');
require_once('core/controller/Executor.php');
require_once('core/controller/Model.php');

// Inicializar Core
Core::$root="";

echo "Verificando la estructura de la tabla 'operation'...\n";

$db = Database::getCon();
if($db === null) {
    echo "ERROR: No se pudo conectar a la base de datos.\n";
    exit;
}

// Verificar estructura
echo "Estructura de la tabla 'operation':\n";
$columns = $db->query("DESCRIBE operation");
if($columns) {
    while($col = $columns->fetch_assoc()) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")" . 
             ($col['Null'] === 'NO' ? ' NOT NULL' : '') . 
             ($col['Default'] !== NULL ? ' DEFAULT ' . $col['Default'] : '') . "\n";
    }
} else {
    echo "Error al consultar la estructura: " . $db->error . "\n";
}

// Probar la función add del modelo OperationData
require_once('core/app/model/OperationData.php');
require_once('core/app/model/ProductData.php');
require_once('core/app/model/OperationTypeData.php');

echo "\nProbando registro directo en la tabla:\n";
$result = $db->query("INSERT INTO operation (product_id, q, operation_type_id, sell_id, is_oficial, created_at) 
                     VALUES (1, 10, 1, NULL, 1, NOW())");
if($result) {
    $id = $db->insert_id;
    echo "Operación de prueba insertada correctamente con ID: $id\n";
} else {
    echo "Error al insertar operación de prueba: " . $db->error . "\n";
}

// Revisar operaciones
echo "\nOperaciones en la tabla:\n";
$ops = $db->query("SELECT * FROM operation ORDER BY id DESC LIMIT 5");
if($ops) {
    while($op = $ops->fetch_assoc()) {
        echo "- ID: " . $op['id'] . 
             ", Producto: " . $op['product_id'] . 
             ", Cantidad: " . $op['q'] . 
             ", Tipo: " . $op['operation_type_id'] . 
             ", Fecha: " . $op['created_at'] . "\n";
    }
} else {
    echo "Error al consultar operaciones: " . $db->error . "\n";
}

echo "\nVerificación completada.\n";
?> 