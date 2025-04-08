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

// Definir el orden específico de las categorías
$category_order = ['Jersey', 'Tenis', 'Gorra', 'Balon', 'Variado'];

// Crear la primera hoja con todos los productos
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Todos los Productos');

// Establecer los encabezados para la hoja de todos los productos
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

// Función para aplicar estilos a los encabezados
function applyHeaderStyle($sheet) {
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 12
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => '2C3E50'
            ]
        ]
    ];
    
    $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
    $sheet->getRowDimension(1)->setRowHeight(40); // Aumentar altura para texto envuelto
}

// Función para aplicar estilos a las celdas de datos
function applyDataStyle($sheet, $startRow, $endRow) {
    $dataStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '95A5A6']
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    
    // Estilo zebra para las filas
    for ($row = $startRow; $row <= $endRow; $row++) {
        if ($row % 2 == 0) {
            $sheet->getStyle('A'.$row.':J'.$row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F8F9FA');
        }
    }
    
    $sheet->getStyle('A'.$startRow.':J'.$endRow)->applyFromArray($dataStyle);
    
    // Altura de fila para datos
    for ($row = $startRow; $row <= $endRow; $row++) {
        $sheet->getRowDimension($row)->setRowHeight(25);
    }
}

// Función para ajustar anchos de columna y aplicar formatos
function applyColumnFormats($sheet, $lastRow) {
    // Ajustar anchos de columna
    $sheet->getColumnDimension('A')->setWidth(12);  // Código
    $sheet->getColumnDimension('B')->setWidth(45);  // Nombre
    $sheet->getColumnDimension('C')->setWidth(12);  // Talla
    $sheet->getColumnDimension('D')->setWidth(15);  // Categoría
    $sheet->getColumnDimension('E')->setWidth(18);  // Precio de Compra
    $sheet->getColumnDimension('F')->setWidth(18);  // Precio de Venta
    $sheet->getColumnDimension('G')->setWidth(12);  // Unidad
    $sheet->getColumnDimension('H')->setWidth(20);  // Mínimo en Inventario
    $sheet->getColumnDimension('I')->setWidth(15);  // Disponible
    $sheet->getColumnDimension('J')->setWidth(20);  // Total de Unidades
    
    // Formato de moneda para precios
    if ($lastRow > 1) {
        $sheet->getStyle('E2:F' . $lastRow)->getNumberFormat()->setFormatCode('$#,##0.00');
        
        // Alineación de columnas
        $sheet->getStyle('A2:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Ajustar texto en nombre y categoría
        $sheet->getStyle('B2:B'.$lastRow)->getAlignment()->setWrapText(true);
    }
}

// Llenar los datos de todos los productos
$row = 2;
$current_product_name = '';
$group_start_row = 2;
$group_total = 0;
$products_in_group = 0;

foreach($products as $product) {
    $available = OperationData::getQYesF($product->id);
    $product_name = trim($product->name);
    
    // Lógica de agrupación existente
    if($current_product_name !== '' && $current_product_name !== $product_name) {
        if($products_in_group > 1) {
            $sheet->mergeCells('J' . $group_start_row . ':J' . ($row - 1));
            $sheet->setCellValue('J' . $group_start_row, $group_total);
            $sheet->getStyle('J' . $group_start_row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        } else {
            $sheet->setCellValue('J' . $group_start_row, $group_total);
        }
        
        $group_start_row = $row;
        $group_total = $available;
        $products_in_group = 1;
    } else if($current_product_name === $product_name) {
        $group_total += $available;
        $products_in_group++;
    } else {
        $group_total = $available;
        $products_in_group = 1;
    }
    
    $current_product_name = $product_name;
    
    // Llenar datos en la fila
    $sheet->setCellValue('A' . $row, $product->id);
    $sheet->setCellValue('B' . $row, $product->name);
    $sheet->setCellValue('C' . $row, $product->size);
    $sheet->setCellValue('D' . $row, $product->getCategory()->name);
    $sheet->setCellValue('E' . $row, $product->price_in);
    $sheet->setCellValue('F' . $row, $product->price_out);
    $sheet->setCellValue('G' . $row, $product->unit);
    $sheet->setCellValue('H' . $row, $product->inventary_min);
    $sheet->setCellValue('I' . $row, $available);
    
    // Colorear niveles de inventario
    if($available <= $product->inventary_min/2) {
        $sheet->getStyle('I' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DC3545');
        $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('FFFFFF');
    } elseif($available <= $product->inventary_min) {
        $sheet->getStyle('I' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFC107');
    }
    
    $row++;
}

// Manejar el último grupo de la hoja general
if($products_in_group > 1) {
    $sheet->mergeCells('J' . $group_start_row . ':J' . ($row - 1));
    $sheet->setCellValue('J' . $group_start_row, $group_total);
    $sheet->getStyle('J' . $group_start_row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
} else {
    $sheet->setCellValue('J' . $group_start_row, $group_total);
}

// Después de procesar los productos en la hoja general
applyHeaderStyle($sheet);
if ($row > 2) {
    applyDataStyle($sheet, 2, $row - 1);
}
applyColumnFormats($sheet, $row - 1);

// Crear hojas para cada categoría en el orden especificado
foreach($category_order as $category) {
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($category);
    
    // Establecer los encabezados
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
    
    // Filtrar productos por categoría
    $categoryProducts = array_filter($products, function($product) use ($category) {
        return $product->getCategory()->name === $category;
    });
    
    // Llenar los datos
    $row = 2;
    $current_product_name = '';
    $group_start_row = 2;
    $group_total = 0;
    $products_in_group = 0;
    
    foreach($categoryProducts as $product) {
        $available = OperationData::getQYesF($product->id);
        $product_name = trim($product->name);
        
        // Lógica de agrupación existente
        if($current_product_name !== '' && $current_product_name !== $product_name) {
            if($products_in_group > 1) {
                $sheet->mergeCells('J' . $group_start_row . ':J' . ($row - 1));
                $sheet->setCellValue('J' . $group_start_row, $group_total);
                $sheet->getStyle('J' . $group_start_row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            } else {
                $sheet->setCellValue('J' . $group_start_row, $group_total);
            }
            
            $group_start_row = $row;
            $group_total = $available;
            $products_in_group = 1;
        } else if($current_product_name === $product_name) {
            $group_total += $available;
            $products_in_group++;
        } else {
            $group_total = $available;
            $products_in_group = 1;
        }
        
        $current_product_name = $product_name;
        
        // Llenar datos en la fila
        $sheet->setCellValue('A' . $row, $product->id);
        $sheet->setCellValue('B' . $row, $product->name);
        $sheet->setCellValue('C' . $row, $product->size);
        $sheet->setCellValue('D' . $row, $product->getCategory()->name);
        $sheet->setCellValue('E' . $row, $product->price_in);
        $sheet->setCellValue('F' . $row, $product->price_out);
        $sheet->setCellValue('G' . $row, $product->unit);
        $sheet->setCellValue('H' . $row, $product->inventary_min);
        $sheet->setCellValue('I' . $row, $available);
        
        // Colorear niveles de inventario
        if($available <= $product->inventary_min/2) {
            $sheet->getStyle('I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('DC3545');
            $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('FFFFFF');
        } elseif($available <= $product->inventary_min) {
            $sheet->getStyle('I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFC107');
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
    
    // Después de procesar los productos en cada hoja de categoría
    applyHeaderStyle($sheet);
    if ($row > 2) {
        applyDataStyle($sheet, 2, $row - 1);
    }
    applyColumnFormats($sheet, $row - 1);
}

// Establecer la primera hoja (Todos los Productos) como activa
$spreadsheet->setActiveSheetIndex(0);

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="inventario_' . $current_datetime . '.xlsx"');
header('Cache-Control: max-age=0');

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 