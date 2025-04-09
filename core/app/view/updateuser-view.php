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
		// Generar un nombre único para la nueva imagen
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
		
		// Guardar información de depuración en la sesión
		$_SESSION["debug_info"] = array(
			"update_info" => array(
				"user_id" => $user->id,
				"session_id" => $_SESSION["user_id"],
				"user_image" => $user->image,
				"session_image" => $_SESSION["user_image"],
				"image_path" => $target_dir . $user->image,
				"image_exists" => file_exists($target_dir . $user->image) ? 'true' : 'false'
			)
		);
	}

	// Manejar cambio de contraseña desde el modal de perfil
	if(isset($_POST["new_password"]) && $_POST["new_password"] != "") {
		// Verificar que se haya proporcionado la contraseña actual
		if(!isset($_POST["current_password"]) || $_POST["current_password"] == "") {
			$_SESSION["success"] = "Debes proporcionar tu contraseña actual para cambiarla";
			Core::redir("./?view=profile");
			exit;
		}

		// Verificar que la contraseña actual sea correcta
		$current_password = sha1(md5($_POST["current_password"]));
		if($current_password != $user->password) {
			$_SESSION["success"] = "La contraseña actual no es correcta";
			Core::redir("./?view=profile");
			exit;
		}

		// Verificar que las nuevas contraseñas coincidan
		if($_POST["new_password"] != $_POST["confirm_password"]) {
			$_SESSION["success"] = "Las nuevas contraseñas no coinciden";
			Core::redir("./?view=profile");
			exit;
		}

		// Actualizar la contraseña
		$user->password = sha1(md5($_POST["new_password"]));
		$user->update_passwd();
		$_SESSION["success"] = "Contraseña actualizada correctamente";
		Core::redir("./?view=profile");
		exit;
	}

	// Manejar cambio de contraseña desde la vista de administrador
	if(isset($_POST["password"]) && $_POST["password"] != ""){
		$user->password = sha1(md5($_POST["password"]));
		$user->update_passwd();
		$_SESSION["success"] = "Contraseña actualizada correctamente";
	}

	$_SESSION["success"] = "Perfil actualizado correctamente";
	Core::redir("./?view=profile");
}


?>