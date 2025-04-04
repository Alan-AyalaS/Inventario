<?php
require_once 'core/controller/Core.php';
require_once 'core/controller/Database.php';
require_once 'core/controller/Executor.php';

Core::$root="";

$db = new Database();
$sql = "INSERT INTO configuration (name, short, kind, val) VALUES 
('Activar Ventas', 'active_sells', 1, 1),
('Activar Vender', 'active_sell', 1, 1),
('Activar Caja', 'active_box', 1, 1),
('Activar Reportes', 'active_reports', 1, 1),
('Activar Compras', 'active_purchases', 1, 1)
ON DUPLICATE KEY UPDATE val=VALUES(val)";

try {
    Executor::doit($sql);
    echo "Configuraciones agregadas exitosamente.";
} catch (Exception $e) {
    echo "Error al agregar configuraciones: " . $e->getMessage();
}
?> 