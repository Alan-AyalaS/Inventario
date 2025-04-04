<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Limpiar cualquier salida anterior
ob_clean();

require_once 'core/app/model/ProductData.php';
require_once 'core/app/model/OperationData.php';
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

// Obtener todos los productos
$products = ProductData::getAll();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear nuevo documento PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Establecer información del documento
$pdf->SetCreator($system_name);
$pdf->SetAuthor($system_name);
$pdf->SetTitle('Inventario');

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
$pdf->Cell(0, 10, 'Inventario de Productos', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Generado el: ' . date("d/m/Y H:i:s"), 0, 1, 'C');

// Agregar espacio
$pdf->Ln(10);

// Establecer fuente para la tabla
$pdf->SetFont('helvetica', 'B', 10);

// Encabezados de la tabla
$header = array('Código', 'Nombre', 'Disponible');
$w = array(30, 100, 60);

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
foreach($products as $product) {
    $available = OperationData::getQYesF($product->id);
    
    // Determinar el color de fondo para la cantidad disponible
    if($available <= $product->inventary_min/2) {
        $pdf->SetFillColor(220, 53, 69); // Rojo
        $pdf->SetTextColor(255, 255, 255); // Texto blanco
    } elseif($available <= $product->inventary_min) {
        $pdf->SetFillColor(255, 193, 7); // Amarillo
        $pdf->SetTextColor(0, 0, 0); // Texto negro
    } else {
        $pdf->SetFillColor(255, 255, 255); // Blanco
        $pdf->SetTextColor(0, 0, 0); // Texto negro
    }
    
    $pdf->Cell($w[0], 7, $product->id, 1);
    $pdf->Cell($w[1], 7, $product->name, 1);
    $pdf->Cell($w[2], 7, $available, 1, 0, 'C', true);
    $pdf->Ln();
    
    // Restaurar colores por defecto
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
}

// Salida del PDF
$pdf->Output('inventario_' . $current_datetime . '.pdf', 'D');
exit; 