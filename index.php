<?php
/**
* @author evilnapsis
**/

define("ROOT", dirname(__FILE__));

$debug= false;
if($debug){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}

include "core/autoload.php";

ob_start();
session_start();
Core::$root="";

// si quieres que se muestre las consultas SQL debes descomentar la siguiente linea
// Core::$debug_sql = true;

$lb = new Lb();
$lb->start();

// Obtener configuración de proveedores
$configs = ConfigurationData::getAll();
$providers_enabled = false;
foreach($configs as $conf) {
    if($conf->short == "active_providers" && $conf->val == 1) {
        $providers_enabled = true;
        break;
    }
}

// Modificar el menú de catálogos
$lb->addMenu("Catálogos", array(
    "Categorías" => "index.php?view=categories",
    "Productos" => "index.php?view=products",
    "Proveedores" => "index.php?view=providers"
), $providers_enabled);

?>