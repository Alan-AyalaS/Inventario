<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Limpiar cualquier salida anterior
ob_clean();

require_once 'core/app/model/ProductData.php';
require_once 'core/app/model/OperationData.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Obtener todos los productos
$products = ProductData::getAll();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer los encabezados
$sheet->setCellValue('A1', 'Código');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Disponible');

// Estilo para los encabezados
$headerStyle = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'F2F2F2',
        ],
    ],
];

$sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

// Llenar los datos
$row = 2;
foreach($products as $product) {
    $available = OperationData::getQYesF($product->id);
    
    $sheet->setCellValue('A' . $row, $product->id);
    $sheet->setCellValue('B' . $row, $product->name);
    $sheet->setCellValue('C' . $row, $available);
    
    // Colorear la celda de disponible según el nivel de inventario
    if($available <= $product->inventary_min/2) {
        $sheet->getStyle('C' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DC3545'); // Rojo
        $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('FFFFFF');
    } elseif($available <= $product->inventary_min) {
        $sheet->getStyle('C' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFC107'); // Amarillo
    }
    
    $row++;
}

// Estilo para las celdas de datos
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
    ],
];

$lastRow = $row - 1;
$sheet->getStyle('A2:C' . $lastRow)->applyFromArray($dataStyle);

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(50);
$sheet->getColumnDimension('C')->setWidth(20);

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="inventario_' . $current_datetime . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 