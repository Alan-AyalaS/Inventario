<?php
// Configuración de la base de datos
$db_host = "localhost"; // Cambiar por el host de producción
$db_user = "inventario_user"; // Cambiar por el usuario de producción
$db_pass = "TU_CONTRASEÑA_SEGURA"; // Cambiar por una contraseña segura
$db_name = "inventario"; // Cambiar si es necesario

// Configuración de caracteres
$db_charset = "utf8mb4";

// Intentar establecer la conexión
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Verificar la conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Establecer el conjunto de caracteres
    if (!$conn->set_charset($db_charset)) {
        throw new Exception("Error al establecer el conjunto de caracteres: " . $conn->error);
    }
    
} catch (Exception $e) {
    // Registrar el error en un archivo de log en producción
    error_log("Error de base de datos: " . $e->getMessage());
    
    // Mostrar un mensaje genérico en producción
    die("Error al conectar con la base de datos. Por favor, contacte al administrador.");
}
?> 