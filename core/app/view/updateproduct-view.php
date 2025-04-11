<?php

if(count($_POST)>0){
	$product = ProductData::getById($_POST["product_id"]);

	$product->barcode = $_POST["barcode"];
	$product->name = $_POST["name"];
	$product->price_in = $_POST["price_in"];
	$product->price_out = $_POST["price_out"];
	$product->unit = $_POST["unit"];

	$product->description = $_POST["description"];
	$product->presentation = $_POST["presentation"];
	$product->inventary_min = $_POST["inventary_min"];
	$category_id="NULL";
	if($_POST["category_id"]!=""){ $category_id=$_POST["category_id"];}

	$is_active=0;
	if(isset($_POST["is_active"])){ $is_active=1;}

	$product->is_active=$is_active;
	$product->category_id=$category_id;

	// Manejar tipo_jersey con valor por defecto
	$product->jersey_type = isset($_POST["tipo_jersey"]) ? $_POST["tipo_jersey"] : "";

	$product->user_id = $_SESSION["user_id"];

	// Si hay una imagen nueva
	if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
		$image = new Upload($_FILES["image"]);
		if($image->uploaded){
			$image->Process("storage/products/");
			if($image->processed){
				$product->image = $image->file_dst_name;
				$product->update_with_image(); // Usar el método correcto para actualizar con imagen
			} else {
				// Error procesando la imagen
				setcookie("prdupd_error", "Error al procesar la imagen");
				header("Location: index.php?view=editproduct&id=".$product->id);
				exit;
			}
		} else {
			// Error subiendo la imagen
			setcookie("prdupd_error", "Error al subir la imagen");
			header("Location: index.php?view=editproduct&id=".$product->id);
			exit;
		}
	} else {
		// No hay imagen nueva, actualizar sin imagen
		$product->update();
	}

	setcookie("prdupd","true");
	
	// Redirigir a la vista de inventario
	header("Location: index.php?view=inventary");
	exit;
}


?>