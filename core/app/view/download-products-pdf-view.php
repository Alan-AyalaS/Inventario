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
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Establecer información del documento
$pdf->SetCreator($system_name);
$pdf->SetAuthor($system_name);
$pdf->SetTitle('Inventario de Productos');

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
$pdf->SetFont('helvetica', 'B', 9);

// Encabezados de la tabla
$header = array('Código', 'Nombre', 'Talla', 'Categoría', 'Precio Compra', 'Precio Venta', 'Unidad', 'Mínimo', 'Disponible', 'Total');
$w = array(20, 60, 20, 25, 25, 25, 20, 20, 25, 25);

// Establecer color de fondo para los encabezados
$pdf->SetFillColor(44, 62, 80); // Color oscuro profesional
$pdf->SetTextColor(255, 255, 255); // Texto blanco

// Imprimir encabezados
for($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Establecer fuente para los datos
$pdf->SetFont('helvetica', '', 8);

// Variables para el agrupamiento
$current_product_name = '';
$group_total = 0;
$products_in_group = 0;
$fill = false;

// Ordenar productos por nombre
usort($products, function($a, $b) {
    return strcmp(trim($a->name), trim($b->name));
});

// Imprimir datos
foreach($products as $product) {
    $available = OperationData::getQYesF($product->id);
    $product_name = trim($product->name);
    
    // Lógica de agrupación
    if($current_product_name !== '' && $current_product_name !== $product_name) {
        $group_total = $available;
        $products_in_group = 1;
    } else if($current_product_name === $product_name) {
        $group_total += $available;
        $products_in_group++;
    } else {
        $group_total = $available;
        $products_in_group = 1;
    }
    
    // Alternar colores de fondo
    $fill = !$fill;
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetTextColor(0, 0, 0);
    
    // Imprimir datos de la fila
    $pdf->Cell($w[0], 7, $product->id, 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 7, $product->name, 1, 0, 'L', $fill);
    $pdf->Cell($w[2], 7, $product->size, 1, 0, 'C', $fill);
    $pdf->Cell($w[3], 7, $product->getCategory()->name, 1, 0, 'C', $fill);
    $pdf->Cell($w[4], 7, '$'.number_format($product->price_in, 2), 1, 0, 'R', $fill);
    $pdf->Cell($w[5], 7, '$'.number_format($product->price_out, 2), 1, 0, 'R', $fill);
    $pdf->Cell($w[6], 7, $product->unit, 1, 0, 'C', $fill);
    $pdf->Cell($w[7], 7, $product->inventary_min, 1, 0, 'C', $fill);
    
    // Colorear la celda de disponible según el nivel
    if($available <= $product->inventary_min/2) {
        $pdf->SetFillColor(220, 53, 69); // Rojo
        $pdf->SetTextColor(255, 255, 255); // Texto blanco
    } elseif($available <= $product->inventary_min) {
        $pdf->SetFillColor(255, 193, 7); // Amarillo
        $pdf->SetTextColor(0, 0, 0); // Texto negro
    }
    $pdf->Cell($w[8], 7, $available, 1, 0, 'C', true);
    
    // Restaurar colores
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetTextColor(0, 0, 0);
    
    // Mostrar el total
    if($current_product_name !== $product_name || $current_product_name === '') {
        $pdf->Cell($w[9], 7, $available, 1, 0, 'C', $fill);
    } else {
        $pdf->Cell($w[9], 7, $group_total, 1, 0, 'C', $fill);
    }
    
    $pdf->Ln();
    $current_product_name = $product_name;
}

// Salida del PDF
$pdf->Output('inventario_productos_' . $current_datetime . '.pdf', 'D');
exit; 