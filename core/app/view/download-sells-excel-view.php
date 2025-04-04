<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Limpiar cualquier salida anterior
ob_clean();

require_once 'core/app/model/SellData.php';
require_once 'core/app/model/OperationData.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Obtener todas las ventas
$sells = SellData::getSells();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer los encabezados
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Cantidad de Productos');
$sheet->setCellValue('C1', 'Total');
$sheet->setCellValue('D1', 'Fecha');

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

$sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

// Llenar los datos
$row = 2;
foreach($sells as $sell) {
    $operations = OperationData::getAllProductsBySellId($sell->id);
    $total = $sell->total - $sell->discount;
    
    $sheet->setCellValue('A' . $row, $sell->id);
    $sheet->setCellValue('B' . $row, count($operations));
    $sheet->setCellValue('C' . $row, '$ ' . number_format($total, 2));
    $sheet->setCellValue('D' . $row, $sell->created_at);
    
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
$sheet->getStyle('A2:D' . $lastRow)->applyFromArray($dataStyle);

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(30);

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ventas_' . $current_datetime . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 