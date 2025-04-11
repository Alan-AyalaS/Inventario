<?php
// Incluir las clases necesarias
require_once 'core/app/model/ProductData.php';
require_once 'core/app/model/OperationData.php';

// Verificar que todos los datos requeridos estén presentes
if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !isset($_POST['operation_type'])) {
    setcookie("stock_error", "Faltan datos requeridos", time() + 3600, "/");
    header("Location: index.php?view=inventary");
    exit;
}

try {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    $operation_type = $_POST['operation_type'];

    // Validar que la cantidad sea positiva
    if ($quantity <= 0) {
        setcookie("stock_error", "La cantidad debe ser mayor que 0", time() + 3600, "/");
        header("Location: index.php?view=inventary");
        exit;
    }

    // Obtener el producto
    $product = ProductData::getById($product_id);
    if (!$product) {
        setcookie("stock_error", "Producto no encontrado", time() + 3600, "/");
        header("Location: index.php?view=inventary");
        exit;
    }

    // Calcular el nuevo stock
    $current_stock = $product->availability;
    $new_stock = $operation_type === 'add' ? 
        $current_stock + $quantity : 
        $current_stock - $quantity;

    // Validar que el stock no sea negativo
    if ($new_stock < 0) {
        setcookie("stock_error", "No hay suficiente stock disponible", time() + 3600, "/");
        header("Location: index.php?view=inventary");
        exit;
    }

    // Actualizar tanto la disponibilidad como el total
    $product->availability = $new_stock;
    $product->total = $new_stock;
    
    // Asegurarse de que todos los campos necesarios estén establecidos
    if (!isset($product->description)) $product->description = "";
    if (!isset($product->jersey_type)) $product->jersey_type = "";
    if (!isset($product->is_active)) $product->is_active = 1;
    
    $result = $product->update();

    if ($result) {
        // Obtener la categoría del producto
        $category = $product->getCategory();
        $category_name = $category ? strtolower(trim($category->name)) : '';

        // Obtener todos los productos del mismo grupo
        if ($category_name === 'jersey') {
            // Para jerseys, agrupar por nombre, categoría y tipo de jersey
            $sql = "SELECT * FROM product WHERE name = \"$product->name\" AND category_id = $product->category_id AND jersey_type = \"$product->jersey_type\"";
        } else {
            // Para otras categorías, agrupar solo por nombre y categoría
            $sql = "SELECT * FROM product WHERE name = \"$product->name\" AND category_id = $product->category_id";
        }
        
        $query = Executor::doit($sql);
        $group_products = Model::many($query[0], new ProductData());
        
        // Calcular el nuevo total del grupo
        $group_total = 0;
        foreach ($group_products as $group_product) {
            $group_total += $group_product->availability;
        }
        
        // Actualizar el total en todos los productos del grupo
        foreach ($group_products as $group_product) {
            $group_product->total = $group_total;
            $group_product->update();
        }

        // Registrar la operación en el historial
        $op = new OperationData();
        $op->product_id = $product_id;
        $op->operation_type_id = $operation_type === 'add' ? 1 : 2; // 1 para entrada, 2 para salida
        $op->q = $quantity;
        $op->operation_type = $operation_type;
        $op->add();

        // Establecer mensaje de éxito personalizado
        $message = sprintf(
            "%s %d unidad(es) %s al producto %s talla %s", 
            $operation_type === 'add' ? "Se agregaron" : "Se restaron",
            $quantity,
            $operation_type === 'add' ? "a" : "de",
            $product->name,
            $product->size
        );
        
        setcookie("stock_updated", $message, time() + 3600, "/");
        setcookie("stock_operation", $operation_type, time() + 3600, "/");

        // Construir URL de redirección con parámetros existentes
        $redirect_url = "index.php?view=inventary";
        if(isset($_POST['category_id'])) $redirect_url .= "&category_id=" . $_POST['category_id'];
        if(isset($_POST['availability'])) $redirect_url .= "&availability=" . $_POST['availability'];
        if(isset($_POST['size'])) $redirect_url .= "&size=" . $_POST['size'];
        if(isset($_POST['date_filter'])) $redirect_url .= "&date_filter=" . $_POST['date_filter'];
        if(isset($_POST['search'])) $redirect_url .= "&search=" . $_POST['search'];
        if(isset($_POST['limit'])) $redirect_url .= "&limit=" . $_POST['limit'];
        if(isset($_POST['jerseyType'])) $redirect_url .= "&jerseyType=" . $_POST['jerseyType'];
        if(isset($_POST['page'])) $redirect_url .= "&page=" . $_POST['page'];

        header("Location: " . $redirect_url);
        exit;
    } else {
        setcookie("stock_error", "Error al actualizar el stock en la base de datos", time() + 3600, "/");
        header("Location: index.php?view=inventary");
        exit;
    }
} catch (Exception $e) {
    setcookie("stock_error", "Error: " . $e->getMessage(), time() + 3600, "/");
    header("Location: index.php?view=inventary");
    exit;
}
?> 