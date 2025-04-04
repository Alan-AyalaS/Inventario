<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

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

// Configurar headers para descarga de archivo Excel
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=directorio_clientes_{$current_datetime}.xlsx");
header("Pragma: no-cache");
header("Expires: 0");

// Crear el contenido del Excel
$excel = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<?mso-application progid=\"Excel.Sheet\"?>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">
 <Styles>
  <Style ss:ID=\"Default\" ss:Name=\"Normal\">
   <Alignment ss:Vertical=\"Bottom\"/>
   <Borders/>
   <Font ss:FontName=\"Calibri\" x:Family=\"Swiss\" ss:Size=\"11\" ss:Color=\"#000000\"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID=\"s62\">
   <Font ss:FontName=\"Calibri\" x:Family=\"Swiss\" ss:Size=\"11\" ss:Color=\"#000000\"
    ss:Bold=\"1\"/>
  </Style>
 </Styles>
 <Worksheet ss:Name=\"Directorio de Clientes\">
  <Table>
   <Row>
    <Cell ss:StyleID=\"s62\"><Data ss:Type=\"String\">Nombre</Data></Cell>
    <Cell ss:StyleID=\"s62\"><Data ss:Type=\"String\">Dirección</Data></Cell>
    <Cell ss:StyleID=\"s62\"><Data ss:Type=\"String\">Email</Data></Cell>
    <Cell ss:StyleID=\"s62\"><Data ss:Type=\"String\">Teléfono</Data></Cell>
   </Row>";

foreach($clients as $client) {
    $excel .= "
   <Row>
    <Cell><Data ss:Type=\"String\">" . htmlspecialchars($client->name . " " . $client->lastname) . "</Data></Cell>
    <Cell><Data ss:Type=\"String\">" . htmlspecialchars($client->address1) . "</Data></Cell>
    <Cell><Data ss:Type=\"String\">" . htmlspecialchars($client->email1) . "</Data></Cell>
    <Cell><Data ss:Type=\"String\">" . htmlspecialchars($client->phone1) . "</Data></Cell>
   </Row>";
}

$excel .= "
  </Table>
 </Worksheet>
</Workbook>";

echo $excel;
exit; 