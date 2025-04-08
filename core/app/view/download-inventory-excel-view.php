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

// Obtener todos los productos y ordenarlos por nombre exacto
$products = ProductData::getAll();
usort($products, function($a, $b) {
    return strcmp(trim($a->name), trim($b->name));
});

// Obtener fecha y hora actual
$current_datetime = date("d-m-Y_H-i-s");

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer los encabezados (solo los que aparecen en el front)
$sheet->setCellValue('A1', 'Código');
$sheet->setCellValue('B1', 'Nombre');
$sheet->setCellValue('C1', 'Talla');
$sheet->setCellValue('D1', 'Categoría');
$sheet->setCellValue('E1', 'Precio de Compra');
$sheet->setCellValue('F1', 'Precio de Venta');
$sheet->setCellValue('G1', 'Unidad');
$sheet->setCellValue('H1', 'Mínimo en Inventario');
$sheet->setCellValue('I1', 'Disponible');
$sheet->setCellValue('J1', 'Total de Unidades');

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

$sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

// Función para determinar si una talla es de adulto (letra) o niño (número)
function isSizeAdult($size) {
    return preg_match('/^[A-Za-z]+$/', trim($size));
}

// Llenar los datos
$row = 2;
$current_product_name = '';
$group_start_row = 2;
$group_total = 0;
$products_in_group = 0;

foreach($products as $product) {
    $available = OperationData::getQYesF($product->id);
    $product_name = trim($product->name);
    
    // Si cambia el nombre del producto
    if($current_product_name !== '' && $current_product_name !== $product_name) {
        // Manejar el grupo anterior
        if($products_in_group > 1) {
            // Si había más de un producto en el grupo, fusionar y mostrar total del grupo
            $sheet->mergeCells('J' . $group_start_row . ':J' . ($row - 1));
            $sheet->setCellValue('J' . $group_start_row, $group_total);
            $sheet->getStyle('J' . $group_start_row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        } else {
            // Si solo había un producto, mostrar su disponible como total
            $sheet->setCellValue('J' . $group_start_row, $group_total);
        }
        
        // Reiniciar contadores para el nuevo grupo
        $group_start_row = $row;
        $group_total = $available;
        $products_in_group = 1;
    } else if($current_product_name === $product_name) {
        // Mismo producto, sumar al grupo
        $group_total += $available;
        $products_in_group++;
    } else {
        // Primer producto
        $group_total = $available;
        $products_in_group = 1;
    }
    
    $current_product_name = $product_name;
    
    $sheet->setCellValue('A' . $row, $product->id);
    $sheet->setCellValue('B' . $row, $product->name);
    $sheet->setCellValue('C' . $row, $product->size);
    $sheet->setCellValue('D' . $row, $product->getCategory()->name);
    $sheet->setCellValue('E' . $row, $product->price_in);
    $sheet->setCellValue('F' . $row, $product->price_out);
    $sheet->setCellValue('G' . $row, $product->unit);
    $sheet->setCellValue('H' . $row, $product->inventary_min);
    $sheet->setCellValue('I' . $row, $available);
    
    // Colorear la celda de disponible según el nivel de inventario
    if($available <= $product->inventary_min/2) {
        $sheet->getStyle('I' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DC3545'); // Rojo
        $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('FFFFFF');
    } elseif($available <= $product->inventary_min) {
        $sheet->getStyle('I' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFC107'); // Amarillo
    }
    
    $row++;
}

// Manejar el último grupo
if($products_in_group > 1) {
    $sheet->mergeCells('J' . $group_start_row . ':J' . ($row - 1));
    $sheet->setCellValue('J' . $group_start_row, $group_total);
    $sheet->getStyle('J' . $group_start_row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
} else {
    $sheet->setCellValue('J' . $group_start_row, $group_total);
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
$sheet->getStyle('A2:J' . $lastRow)->applyFromArray($dataStyle);

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(15); // Código
$sheet->getColumnDimension('B')->setWidth(40); // Nombre
$sheet->getColumnDimension('C')->setWidth(15); // Talla
$sheet->getColumnDimension('D')->setWidth(20); // Categoría
$sheet->getColumnDimension('E')->setWidth(15); // Precio Compra
$sheet->getColumnDimension('F')->setWidth(15); // Precio Venta
$sheet->getColumnDimension('G')->setWidth(15); // Unidad
$sheet->getColumnDimension('H')->setWidth(20); // Mínimo
$sheet->getColumnDimension('I')->setWidth(15); // Disponible
$sheet->getColumnDimension('J')->setWidth(20); // Total de Unidades

// Formato de moneda solo para precios
$sheet->getStyle('E2:F' . $lastRow)->getNumberFormat()->setFormatCode('$#,##0.00');

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="inventario_' . $current_datetime . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 