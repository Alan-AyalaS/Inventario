<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Limpiar cualquier salida anterior
ob_clean();
ob_start();

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

// Obtener todos los clientes
$clients = PersonData::getClients();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear el PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($system_name);
$pdf->SetTitle('Directorio de Clientes');

// Configurar márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Configurar auto saltos de página
$pdf->SetAutoPageBreak(TRUE, 15);

// Agregar una página
$pdf->AddPage();

// Configurar fuente
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, $system_name, 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Directorio de Clientes', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Generado el: ' . date("d/m/Y H:i:s"), 0, 1, 'R');
$pdf->Ln(10);

// Encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(60, 10, 'Nombre', 1, 0, 'C');
$pdf->Cell(50, 10, 'Dirección', 1, 0, 'C');
$pdf->Cell(40, 10, 'Email', 1, 0, 'C');
$pdf->Cell(40, 10, 'Teléfono', 1, 1, 'C');

// Datos de los clientes
$pdf->SetFont('helvetica', '', 10);
foreach($clients as $client) {
    $pdf->Cell(60, 10, $client->name . " " . $client->lastname, 1, 0, 'L');
    $pdf->Cell(50, 10, $client->address1, 1, 0, 'L');
    $pdf->Cell(40, 10, $client->email1, 1, 0, 'L');
    $pdf->Cell(40, 10, $client->phone1, 1, 1, 'L');
}

// Limpiar el buffer y enviar el PDF
ob_end_clean();
$pdf->Output("directorio_clientes_{$current_datetime}.pdf", 'D');
exit; 