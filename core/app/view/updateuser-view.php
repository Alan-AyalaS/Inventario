<?php

if(count($_POST)>0){
	$is_admin=0;
	if(isset($_POST["is_admin"])){$is_admin=1;}
	$is_active=0;
	if(isset($_POST["is_active"])){$is_active=1;}
	$user = UserData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->username = $_POST["username"];
	$user->email = $_POST["email"];
	$user->is_admin=$is_admin;
	$user->is_active=$is_active;

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
				// Eliminar la imagen anterior si existe
				if($user->image != "" && file_exists($target_dir . $user->image)) {
					unlink($target_dir . $user->image);
				}
				$user->image = $filename;
			}
		}
	}

	$user->update();

	// Actualizar la imagen en la sesión si es el usuario actual
	if($user->id == $_SESSION["user_id"]) {
		$_SESSION["user_image"] = $user->image;
	}

	if($_POST["password"]!=""){
		$user->password = sha1(md5($_POST["password"]));
		$user->update_passwd();
		print "<script>alert('Se ha actualizado el password');</script>";
	}

	print "<script>window.location='index.php?view=users';</script>";
}


?>