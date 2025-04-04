<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Limpiar cualquier salida anterior
ob_clean();

require_once 'core/app/model/PersonData.php';
require_once 'core/app/model/ConfigurationData.php';
require_once 'plugins/tcpdf/tcpdf.php';

// Obtener la configuración del sistema
$configs = ConfigurationData::getAll();
$system_name = "Inventario Jersey"; // Valor por defecto
foreach($configs as $conf) {
    if($conf->short == "system_name") {
        $system_name = $conf->val;
        break;
    }
}

// Obtener todos los proveedores
$providers = PersonData::getProviders();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear nuevo documento PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Establecer información del documento
$pdf->SetCreator($system_name);
$pdf->SetAuthor($system_name);
$pdf->SetTitle('Directorio de Proveedores');

// Eliminar header y footer por defecto
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Agregar una página
$pdf->AddPage();

// Establecer fuente
$pdf->SetFont('helvetica', 'B', 16);

// Título
$pdf->Cell(0, 10, $system_name, 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Directorio de Proveedores', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Generado el: ' . date("d/m/Y H:i:s"), 0, 1, 'C');

// Agregar espacio
$pdf->Ln(10);

// Establecer fuente para la tabla
$pdf->SetFont('helvetica', 'B', 10);

// Encabezados de la tabla
$header = array('Nombre', 'Dirección', 'Email', 'Teléfono');
$w = array(50, 60, 50, 30);

// Establecer color de fondo para los encabezados
$pdf->SetFillColor(242, 242, 242);

// Imprimir encabezados
for($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Establecer fuente para los datos
$pdf->SetFont('helvetica', '', 9);

// Imprimir datos
foreach($providers as $provider) {
    $pdf->Cell($w[0], 7, trim($provider->name . " " . $provider->lastname), 1);
    $pdf->Cell($w[1], 7, trim($provider->address1), 1);
    $pdf->Cell($w[2], 7, trim($provider->email1), 1);
    $pdf->Cell($w[3], 7, trim($provider->phone1), 1);
    $pdf->Ln();
}

// Salida del PDF
$pdf->Output('directorio_proveedores_' . $current_datetime . '.pdf', 'D');
exit; 