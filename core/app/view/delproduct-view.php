<?php

$product = ProductData::getById($_GET["id"]);
$product_name = $product->name; // Guardar el nombre del producto antes de eliminarlo

$operations = OperationData::getAllByProductId($_GET["id"]);

foreach ($operations as $op) {
	$op->del();
}

$product->del();

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