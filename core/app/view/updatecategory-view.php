<?php

if(count($_POST)>0){
	$user = CategoryData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->update();

	$category_id = $_POST["user_id"];
	$category_color = isset($_POST["color"]) ? $_POST["color"] : "#28a745";
	
	// Establecer cookie y guardar en localStorage mediante JavaScript
	setcookie("cat_updated", "true", time() + 60);
	setcookie("category_color_".$category_id, $category_color, time() + 365 * 24 * 60 * 60);
	
	echo "<script>
	localStorage.setItem('category_color_".$category_id."', '".$category_color."');
	window.location='index.php?view=categories';
	</script>";
}


?>