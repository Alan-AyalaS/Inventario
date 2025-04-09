<?php

if(count($_POST)>0){
	$is_admin=0;
	if(isset($_POST["is_admin"])){$is_admin=1;}
	
	$user = new UserData();
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->username = $_POST["username"];
	$user->email = $_POST["email"];
	$user->is_admin=$is_admin;
	$user->password = password_hash($_POST["password"], PASSWORD_DEFAULT);
	$user->is_active = isset($_POST["is_active"]) ? 1 : 0;
	
	// Procesar la imagen
	if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
		$target_dir = "assets/img/avatars/";
		if(!file_exists($target_dir)) {
			mkdir($target_dir, 0777, true);
		}
		$imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
		$filename = uniqid() . "." . $imageFileType;
		$target_file = $target_dir . $filename;
		
		// Verificar si es una imagen real
		$check = getimagesize($_FILES["image"]["tmp_name"]);
		if($check !== false) {
			if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
				$user->image = $filename;
			}
		}
	} else {
		// Asignar imagen por defecto
		$user->image = "default-avatar-icon.jpg";
	}
	
	try {
		$user->add();
		print "<script>window.location='index.php?view=users';</script>";
	} catch (Exception $e) {
		echo "<div class='alert alert-danger'>Error al crear usuario: " . $e->getMessage() . "</div>";
	}
}

?>