<?php

if(count($_POST)>0){
	// Obtener el producto original primero
	$original_product = ProductData::getById($_POST["product_id"]);
	if(!$original_product) {
		setcookie("prdupd_error", "No se encontró el producto a actualizar", time() + 3600, "/");
		header("Location: index.php?view=inventary");
		exit;
	}

	// Verificar si hay cambios en los campos críticos
	$category_id = $_POST["category_id"] != "" ? $_POST["category_id"] : "NULL";
	$new_jersey_type = isset($_POST["tipo_jersey"]) ? $_POST["tipo_jersey"] : "";
	
	$values_changed = (
		$_POST["name"] !== $original_product->name || 
		$category_id !== $original_product->category_id ||
		$new_jersey_type !== $original_product->jersey_type
	);

	// Validar duplicados solo si cambiaron valores críticos
	if($values_changed) {
		$category = CategoryData::getById($category_id);
		$categoryName = $category ? strtolower(trim($category->name)) : '';
		
		if($categoryName === 'jersey' && !empty($new_jersey_type)) {
			$existing_product = ProductData::existsByNameAndCategory($_POST["name"], $category_id, $new_jersey_type);
			if($existing_product && $existing_product->id != $original_product->id) {
				setcookie("prdupd_error", "Ya existe un jersey de tipo '{$new_jersey_type}' con el nombre '{$_POST["name"]}'", time() + 3600, "/");
				header("Location: index.php?view=editproduct&id=".$original_product->id);
				exit;
			}
		} else if($category_id != "NULL") {
			$existing_product = ProductData::existsByNameAndCategory($_POST["name"], $category_id);
			if($existing_product && $existing_product->id != $original_product->id) {
				setcookie("prdupd_error", "Ya existe un producto con el nombre '{$_POST["name"]}' en la categoría '$categoryName'", time() + 3600, "/");
				header("Location: index.php?view=editproduct&id=".$original_product->id);
				exit;
			}
		}
	}

	// Actualizar los valores del producto
	$original_product->barcode = $_POST["barcode"];
	$original_product->name = $_POST["name"];
	$original_product->price_in = $_POST["price_in"];
	$original_product->price_out = $_POST["price_out"];
	$original_product->unit = $_POST["unit"];
	$original_product->description = $_POST["description"];
	$original_product->presentation = $_POST["presentation"];
	$original_product->inventary_min = $_POST["inventary_min"];
	$original_product->category_id = $category_id;
	$original_product->jersey_type = $new_jersey_type;
	$original_product->is_active = isset($_POST["is_active"]) ? 1 : 0;
	$original_product->user_id = $_SESSION["user_id"];

	// Si hay una imagen nueva
	if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
		$image = new Upload($_FILES["image"]);
		if($image->uploaded){
			$image->Process("storage/products/");
			if($image->processed){
				$original_product->image = $image->file_dst_name;
				$original_product->update_with_image();
			} else {
				setcookie("prdupd_error", "Error al procesar la imagen");
				header("Location: index.php?view=editproduct&id=".$original_product->id);
				exit;
			}
		} else {
			setcookie("prdupd_error", "Error al subir la imagen");
			header("Location: index.php?view=editproduct&id=".$original_product->id);
			exit;
		}
	} else {
		$original_product->update();
	}

	setcookie("prdupd","true");
	
	// Construir la URL de redirección con los parámetros de filtro
	$redirect_url = "index.php?view=inventary";
	$filter_params = array("category_id", "availability", "size", "date_filter", "search", "limit", "jerseyType", "page");

	foreach($filter_params as $param) {
		if(isset($_GET[$param]) && $_GET[$param] != "") {
			$redirect_url .= "&" . $param . "=" . urlencode($_GET[$param]);
		}
	}

	header("Location: " . $redirect_url);
	exit;
}


?>