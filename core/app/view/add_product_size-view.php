<?php
// Asegurarnos de que no haya salida antes del JSON
ob_start();

// Asegurarnos de que PHP reporte todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Verificar que todos los datos necesarios estén presentes
    $required_fields = ['action', 'original_product_id', 'size', 'quantity'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("El campo {$field} es requerido");
        }
    }

    if ($_POST['action'] !== 'add_size') {
        throw new Exception('Acción no válida');
    }

    // Obtener los datos del producto original
    $original_product = ProductData::getById($_POST['original_product_id']);
    if (!$original_product) {
        throw new Exception('Producto original no encontrado');
    }

    // Crear el nuevo producto con los mismos datos pero diferente talla
    $product = new ProductData();
    
    // Copiar TODOS los campos relevantes del producto original
    $product->barcode = $original_product->barcode;
    $product->name = $original_product->name;
    $product->description = $original_product->description;
    $product->price_in = $original_product->price_in;
    $product->price_out = $original_product->price_out;
    $product->unit = $original_product->unit;
    $product->user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : $original_product->user_id;
    $product->presentation = $original_product->presentation;
    $product->category_id = $original_product->category_id;
    $product->inventary_min = $original_product->inventary_min;
    $product->is_active = $original_product->is_active;
    $product->jersey_type = $original_product->jersey_type;
    $product->image = $original_product->image;
    
    // Establecer los nuevos valores
    $product->size = $_POST['size'];
    $product->total = 0;
    $product->availability = intval($_POST['quantity']);

    // Validar que no exista un producto con el mismo nombre, categoría, tipo de jersey y talla
    $category = CategoryData::getById($product->category_id);
    if ($category && strtolower($category->name) === 'jersey') {
        $existing = ProductData::getByNameCategoryTypeAndSize(
            $product->name,
            $product->category_id,
            $product->jersey_type,
            $product->size
        );
        if ($existing) {
            throw new Exception("Ya existe un jersey de tipo '{$product->jersey_type}' con el nombre '{$product->name}' en talla {$product->size}");
        }
    }

    // Guardar el nuevo producto
    $result = $product->add();
    if (!$result) {
        throw new Exception('Error al crear el producto en la base de datos');
    }
    
    // Obtener el ID del producto recién creado
    $product_id = $result[1];

    // Crear la operación de entrada inicial
    $op = new OperationData();
    $op->product_id = $product_id;
    $op->q = intval($_POST['quantity']);
    $op->operation_type_id = 1; // 1 para entrada
    $op->sell_id = null;
    $op->created_at = date("Y-m-d H:i:s");
    $op->talla = $_POST['size'];
    
    if (!$op->add()) {
        // Si falla la operación, eliminar el producto creado
        ProductData::delById($product_id);
        throw new Exception('Error al registrar la operación de entrada');
    }

    // Actualizar los totales del grupo
    if ($category && strtolower($category->name) === 'jersey') {
        // Para jerseys, obtener productos del mismo nombre, categoría y tipo
        $sql = "SELECT * FROM product WHERE name = \"$product->name\" AND category_id = $product->category_id AND jersey_type = \"$product->jersey_type\"";
    } else {
        // Para otros productos, obtener productos del mismo nombre y categoría
        $sql = "SELECT * FROM product WHERE name = \"$product->name\" AND category_id = $product->category_id";
    }
    
    $query = Executor::doit($sql);
    $group_products = Model::many($query[0], new ProductData());
    
    // Calcular el total del grupo
    $group_total = 0;
    foreach ($group_products as $group_product) {
        $group_total += $group_product->availability;
    }
    
    // Actualizar el total en todos los productos del grupo
    foreach ($group_products as $group_product) {
        $group_product->total = $group_total;
        $group_product->update();
    }

    // Establecer cookie para mostrar mensaje de éxito
    setcookie("prdadd", $product->name, time()+3600, "/");

    // Limpiar cualquier salida anterior
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Enviar la respuesta JSON
    echo json_encode([
        'success' => true,
        'message' => 'Producto creado correctamente',
        'product_id' => $product_id,
        'product_name' => $product->name
    ]);

} catch (Exception $e) {
    // Limpiar cualquier salida anterior
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    error_log("Error en add_product_size: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Asegurarnos de que no haya más salida después del JSON
exit();
?> 