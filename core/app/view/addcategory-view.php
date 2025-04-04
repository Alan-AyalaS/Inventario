<?php

if(count($_POST)>0){
	// Depuración: mostrar los datos recibidos
	echo "<pre>";
	echo "Datos POST recibidos:\n";
	print_r($_POST);
	echo "</pre>";

	$category = new CategoryData();
	$category->name = $_POST["name"];
	
	// Depuración: mostrar los datos antes de insertar
	echo "<pre>";
	echo "Datos de la categoría antes de insertar:\n";
	echo "Nombre: " . $category->name . "\n";
	echo "</pre>";
	
	// Intentar insertar y mostrar resultado
	$result = $category->add();
	
	// Depuración: mostrar resultado de la inserción
	echo "<pre>";
	echo "Resultado de la inserción:\n";
	var_dump($result);
	echo "</pre>";
	
	// Verificar si la inserción fue exitosa
	if($result && $result[0]) {
		print "<script>window.location='index.php?view=categories';</script>";
	} else {
		echo "<div class='alert alert-danger'>Error al crear la categoría. Por favor, intente nuevamente.</div>";
		// Mostrar el error de MySQL si está disponible
		if(isset($result[1])) {
			echo "<div class='alert alert-danger'>Error MySQL: " . $result[1] . "</div>";
		}
	}
}


?>