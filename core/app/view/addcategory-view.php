<?php

if(count($_POST)>0){
	$category = new CategoryData();
	$category->name = $_POST["name"];
	
	// Intentar insertar
	$result = $category->add();
	
	// Verificar si la inserción fue exitosa
	if($result && $result[0]) {
		// Establecer una cookie para mostrar una alerta de éxito
		// Expira en 60 segundos, lo que da tiempo suficiente para cargar la página pero no se queda mucho tiempo
		setcookie("catadd", $category->name, time() + 60, "/");
		
		// Almacenar el ID y color de la categoría en una cookie para ser leída por JavaScript
		$new_category_id = $result[1];
		$color = isset($_POST["color"]) ? $_POST["color"] : "#28a745";
		setcookie("category_color_" . $new_category_id, $color, time() + 31536000, "/"); // 1 año de expiración
		
		print "<script>
		// Guardar color en localStorage para uso persistente
		localStorage.setItem('category_color_" . $new_category_id . "', '" . $color . "');
		window.location='index.php?view=categories';
		</script>";
	} else {
		echo "<div class='alert alert-danger'>Error al crear la categoría. Por favor, intente nuevamente.</div>";
		// Mostrar el error de MySQL si está disponible
		if(isset($result[1])) {
			echo "<div class='alert alert-danger'>Error MySQL: " . $result[1] . "</div>";
		}
	}
}

?>