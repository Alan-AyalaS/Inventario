<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Iniciar el buffer de salida
ob_start();

require_once 'core/app/model/PersonData.php';
require_once 'core/app/model/ConfigurationData.php';

// Obtener la configuración del sistema
$configs = ConfigurationData::getAll();
$system_name = "Inventario Jersey"; // Valor por defecto
foreach($configs as $conf) {
    if($conf->short == "system_name") {
        $system_name = $conf->val;
        break;
    }
}

// Obtener todos los clientes
$clients = PersonData::getClients();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Configurar headers para descarga de archivo Word
header("Content-Type: application/msword");
header("Content-Disposition: attachment; filename=directorio_clientes_{$current_datetime}.doc");
header("Pragma: no-cache");
header("Expires: 0");

// Generar el contenido del documento
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Directorio de Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { text-align: center; }
        .date { text-align: right; margin-bottom: 20px; }
        tr:hover td { color: #0066cc; }
    </style>
</head>
<body>
    <h1><?php echo $system_name; ?></h1>
    <h2>Directorio de Clientes</h2>
    <p class="date">Generado el: <?php echo date("d/m/Y H:i:s"); ?></p>

    <table>
        <tr>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Email</th>
            <th>Teléfono</th>
        </tr>
        <?php foreach($clients as $client): ?>
        <tr>
            <td><?php echo $client->name . " " . $client->lastname; ?></td>
            <td><?php echo $client->address1; ?></td>
            <td><?php echo $client->email1; ?></td>
            <td><?php echo $client->phone1; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php
// Obtener el contenido del buffer
$content = ob_get_clean();

// Limpiar cualquier salida anterior
ob_clean();

// Enviar el contenido
echo $content;
exit; 