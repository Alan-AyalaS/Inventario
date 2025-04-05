<?php

$category = Categorydata::getById($_GET["id"]);
$category_name = $category->name; // Guardar el nombre de la categoría antes de eliminarla

$products = ProductData::getAllByCategoryId($category->id);
foreach ($products as $product) {
	$product->del_category();
}

$category->del();

// Establecer una cookie para mostrar una alerta de éxito
// Expira en 60 segundos, lo que da tiempo suficiente para cargar la página pero no se queda mucho tiempo
setcookie("catdel", $category_name, time() + 60, "/");

Core::redir("./index.php?view=categories");

?>