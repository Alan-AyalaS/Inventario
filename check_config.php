<?php
define("ROOT", dirname(__FILE__));
include "core/autoload.php";
include "core/app/model/ConfigurationData.php";

Core::$root="";

$configs = ConfigurationData::getAll();
echo "Configuraciones encontradas:\n";
foreach($configs as $conf) {
    echo "Short: " . $conf->short . ", Valor: " . $conf->val . "\n";
}
?> 