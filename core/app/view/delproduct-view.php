<?php

$product = ProductData::getById($_GET["id"]);
$product_name = $product->name; // Guardar el nombre del producto antes de eliminarlo

// Obtener todos los productos con el mismo nombre
$sql = "SELECT * FROM product WHERE name = '$product->name'";
$query = Executor::doit($sql);
$related_products = Model::many($query[0], new ProductData());

// Primero eliminar todas las operaciones asociadas al producto
$sql = "DELETE FROM operation WHERE product_id = $product->id";
Executor::doit($sql);

// Luego eliminar el producto
$product->del();

// Si hay más de un producto en el grupo
if(count($related_products) > 1) {
	// Calcular el nuevo total sumando la disponibilidad de los productos restantes
	$total_availability = 0;
	foreach($related_products as $related_product) {
		if($related_product->id != $product->id) { // Excluir el producto que se está eliminando
			$total_availability += $related_product->availability;
		}
	}
	
	// Actualizar el total en todos los productos del grupo
	foreach($related_products as $related_product) {
		if($related_product->id != $product->id) { // Excluir el producto que se está eliminando
			$related_product->total = $total_availability;
			$related_product->update();
		}
	}
}

// Establecer una cookie para mostrar una alerta de éxito
// Expira en 60 segundos, lo que da tiempo suficiente para cargar la página pero no se queda mucho tiempo
setcookie("prddel", $product_name, time() + 60, "/");

// Construir la URL de redirección con los parámetros de filtro
$redirectUrl = "./index.php?view=inventary";

// Agregar los parámetros de filtro si existen
if (isset($_GET["category_id"])) {
    $redirectUrl .= "&category_id=" . $_GET["category_id"];
}
if (isset($_GET["date_filter"])) {
    $redirectUrl .= "&date_filter=" . $_GET["date_filter"];
}
if (isset($_GET["limit"])) {
    $redirectUrl .= "&limit=" . $_GET["limit"];
}
if (isset($_GET["page"])) {
    $redirectUrl .= "&page=" . $_GET["page"];
}

// Redirigir a la página de inventario con los filtros
Core::redir($redirectUrl);
?>