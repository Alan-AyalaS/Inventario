<?php
require_once('core/controller/Core.php');
require_once('core/controller/Database.php');
require_once('core/controller/Executor.php');
require_once('core/controller/Model.php');

// Para mostrar contenido en la terminal
header('Content-Type: text/plain');

// Inicializar Core
Core::$root="";

echo "Verificando la estructura de la tabla 'operation'...\n";

$db = Database::getCon();
if($db === null) {
    echo "ERROR: No se pudo conectar a la base de datos.\n";
    exit;
}

$table_exists = $db->query("SHOW TABLES LIKE 'operation'");
if($table_exists && $table_exists->num_rows == 0) {
    echo "La tabla 'operation' no existe.\n";
    exit;
}

$result = $db->query("SHOW COLUMNS FROM operation");
if($result) {
    echo "Estructura de la tabla 'operation':\n";
    echo "-------------------------------------\n";
    echo "Campo | Tipo | NULL | Default \n";
    echo "-------------------------------------\n";
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . " | " . 
             ($row['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . " | " . 
             $row['Default'] . "\n";
    }
    echo "-------------------------------------\n";
} else {
    echo "Error al consultar la estructura: " . $db->error . "\n";
}

// Verificar las últimas operaciones
echo "\nÚltimas 5 operaciones registradas:\n";
echo "-------------------------------------\n";
$ops = $db->query("SELECT o.*, p.name as product_name, t.name as type_name 
                  FROM operation o 
                  JOIN product p ON o.product_id = p.id 
                  JOIN operation_type t ON o.operation_type_id = t.id 
                  ORDER BY o.created_at DESC LIMIT 5");

if($ops && $ops->num_rows > 0) {
    echo "ID | Producto | Cantidad | Tipo | Fecha\n";
    echo "-------------------------------------\n";
    while($op = $ops->fetch_assoc()) {
        echo $op['id'] . " | " . 
             $op['product_name'] . " | " . 
             $op['q'] . " | " . 
             $op['type_name'] . " | " . 
             $op['created_at'] . "\n";
    }
} else {
    echo "No hay operaciones registradas o error: " . $db->error . "\n";
}

echo "\nVerificación completada.\n";
?> 