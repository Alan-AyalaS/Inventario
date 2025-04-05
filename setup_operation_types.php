<?php
require_once('core/controller/Core.php');
require_once('core/controller/Database.php');
require_once('core/controller/Executor.php');
require_once('core/controller/Model.php');
require_once('core/app/model/OperationTypeData.php');

// Para mostrar contenido en la terminal
header('Content-Type: text/plain');

// Inicializar Core
Core::$root="";

echo "Iniciando configuración de tipos de operación...\n";

// Verificar si existe la tabla
$db = Database::getCon();
if($db === null) {
    echo "ERROR: No se pudo conectar a la base de datos.\n";
    exit;
}

echo "Conexión a la base de datos establecida.\n";

$table_exists = $db->query("SHOW TABLES LIKE 'operation_type'");
if($table_exists && $table_exists->num_rows == 0) {
    echo "La tabla 'operation_type' no existe. Creando tabla...\n";
    
    $sql = "CREATE TABLE operation_type (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    )";
    
    if($db->query($sql)) {
        echo "Tabla 'operation_type' creada con éxito.\n";
    } else {
        echo "Error al crear la tabla: " . $db->error . "\n";
        exit;
    }
} else {
    echo "Tabla 'operation_type' ya existe.\n";
}

// Insertar directamente los tipos usando consultas SQL
$check = $db->query("SELECT * FROM operation_type WHERE name='entrada'");
if($check && $check->num_rows == 0) {
    echo "Tipo 'entrada' no encontrado. Creando...\n";
    $db->query("INSERT INTO operation_type (id, name) VALUES (1, 'entrada')");
    echo "Tipo 'entrada' creado.\n";
} else {
    echo "Tipo 'entrada' ya existe.\n";
    // Asegurar que tenga el ID correcto
    $db->query("UPDATE operation_type SET id=1 WHERE name='entrada'");
}

$check = $db->query("SELECT * FROM operation_type WHERE name='salida'");
if($check && $check->num_rows == 0) {
    echo "Tipo 'salida' no encontrado. Creando...\n";
    $db->query("INSERT INTO operation_type (id, name) VALUES (2, 'salida')");
    echo "Tipo 'salida' creado.\n";
} else {
    echo "Tipo 'salida' ya existe.\n";
    // Asegurar que tenga el ID correcto
    $db->query("UPDATE operation_type SET id=2 WHERE name='salida'");
}

echo "Verificando la tabla operation_type:\n";
$result = $db->query("SELECT * FROM operation_type");
if($result) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Nombre: " . $row['name'] . "\n";
    }
} else {
    echo "Error al consultar la tabla: " . $db->error . "\n";
}

echo "Configuración completada. Ahora debe funcionar correctamente el ajuste de inventario.\n";
?> 