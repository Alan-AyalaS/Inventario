<?php
// Evitar que se cargue el layout principal
define('NO_LAYOUT', true);

// Limpiar cualquier salida anterior
ob_clean();

require_once 'core/app/model/PersonData.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Obtener todos los proveedores
$providers = PersonData::getProviders();

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer los encabezados
$sheet->setCellValue('A1', 'Nombre');
$sheet->setCellValue('B1', 'Dirección');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'Teléfono');

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
foreach($providers as $provider) {
    $sheet->setCellValue('A' . $row, trim($provider->name . " " . $provider->lastname));
    $sheet->setCellValue('B' . $row, trim($provider->address1));
    $sheet->setCellValue('C' . $row, trim($provider->email1));
    $sheet->setCellValue('D' . $row, trim($provider->phone1));
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
$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(20);

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="directorio_proveedores_' . $current_datetime . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 