<?php
require_once('core/controller/Core.php');
require_once('core/controller/Database.php');
require_once('core/controller/Executor.php');
require_once('core/controller/Model.php');
require_once('core/app/model/OperationTypeData.php');

// Inicializar Core
Core::$root="";

// Mostrar los tipos de operación
$operation_types = OperationTypeData::getAll();

if(count($operation_types) > 0) {
    echo "<h3>Tipos de Operación</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th></tr>";
    
    foreach($operation_types as $type) {
        echo "<tr>";
        echo "<td>" . $type->id . "</td>";
        echo "<td>" . $type->name . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Verificar tipo "entrada"
    $entrada = OperationTypeData::getByName("entrada");
    if($entrada != null) {
        echo "<p>Tipo 'entrada' encontrado con ID: " . $entrada->id . "</p>";
    } else {
        echo "<p>¡ADVERTENCIA! Tipo 'entrada' no encontrado</p>";
    }
    
    // Verificar tipo "salida"
    $salida = OperationTypeData::getByName("salida");
    if($salida != null) {
        echo "<p>Tipo 'salida' encontrado con ID: " . $salida->id . "</p>";
    } else {
        echo "<p>¡ADVERTENCIA! Tipo 'salida' no encontrado</p>";
    }
    
} else {
    echo "<p>No se encontraron tipos de operación.</p>";
    
    // Crear los tipos básicos
    echo "<p>Creando tipos básicos...</p>";
    
    $entrada = new OperationTypeData();
    $entrada->name = "entrada";
    $entrada->add();
    
    $salida = new OperationTypeData();
    $salida->name = "salida";
    $salida->add();
    
    echo "<p>Tipos básicos creados. Por favor, recarga la página.</p>";
}
?> 