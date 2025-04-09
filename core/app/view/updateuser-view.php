<?php

if(count($_POST)>0 && isset($_POST["user_id"])){
	$user = UserData::getById($_POST["user_id"]);

	if ($user) {
		// 1. Actualizar campos básicos (los que SIEMPRE vienen del formulario)
		$user->name = $_POST["name"];
		$user->lastname = $_POST["lastname"];
		$user->username = $_POST["username"]; // Asegurarse que no esté duplicado (validación backend)
		$user->email = $_POST["email"];       // Asegurarse que no esté duplicado (validación backend)

		// IMPORTANTE: No tocar is_admin ni is_active aquí, se preservan los valores existentes

		$password_updated = false;
		$password_error = null;

		// 2. PROCESAR CAMBIO DE CONTRASEÑA
		
		// ESCENARIO A: Cambio desde el modal de perfil (requiere actual y confirmación)
		if(isset($_POST["new_password"]) && $_POST["new_password"] != "") {
			if (isset($_POST["current_password"]) && $_POST["current_password"] != "" && isset($_POST["confirm_password"])) {
				$current_password_matches = false;
				// Verificar actual con verify
				if (password_verify($_POST["current_password"], $user->password)) {
					$current_password_matches = true;
				}
				// Verificar actual con sha1(md5) para migración
				else if ($user->password == sha1(md5($_POST["current_password"])) ) {
					$current_password_matches = true;
				}
				
				if ($current_password_matches) {
					if ($_POST["new_password"] == $_POST["confirm_password"]) {
						$user->password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
						$password_updated = true;
					} else {
						$password_error = "La nueva contraseña y la confirmación no coinciden (desde perfil).";
					}
				} else {
					$password_error = "La contraseña actual introducida es incorrecta (desde perfil).";
				}
			} else {
				 $password_error = "Debes introducir la contraseña actual y confirmar la nueva para cambiarla (desde perfil).";
			}
		}
		// ESCENARIO B: Cambio desde la edición de admin (no requiere verificación de contraseña actual)
		if(isset($_POST["password"]) && !empty($_POST["password"])){
			$user->password = password_hash($_POST["password"], PASSWORD_DEFAULT);
		}
		// NOTA: Si ni new_password ni password están seteados o están vacíos, no se hace nada con la contraseña.

		// 3. Procesar la imagen (si se subió una nueva)
		if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
			$target_dir = "assets/img/avatars/";
			if(!file_exists($target_dir)) {
				mkdir($target_dir, 0777, true);
			}
			$imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
			$allowed_types = ["jpg", "png", "jpeg", "gif"]; // Tipos permitidos
			
			if(in_array($imageFileType, $allowed_types)){
				$filename = uniqid('user_'.$user->id.'_') . "." . $imageFileType;
				$target_file = $target_dir . $filename;
				
				$check = getimagesize($_FILES["image"]["tmp_name"]);
				if($check !== false) {
					if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
						// Eliminar la imagen anterior si existe y es diferente
						if($user->image != "" && $user->image != $filename && file_exists($target_dir . $user->image)) {
							unlink($target_dir . $user->image);
						}
						$user->image = $filename;
					} else {
						 // Error al mover archivo (opcional: guardar mensaje de error)
					}
				} else {
					// Archivo no es imagen (opcional: guardar mensaje de error)
				}
			} else {
				 // Tipo de archivo no permitido (opcional: guardar mensaje de error)
			}
		}

		// 4. Guardar los cambios en la BD (solo si no hubo error de contraseña)
		if ($password_error === null) {
			 $user->update();
			 
			 // Actualizar la imagen y nombre en la sesión si es el usuario actual
			 if($user->id == $_SESSION["user_id"]) {
				$_SESSION["user_image"] = $user->image;
				$_SESSION["user_name"] = $user->username; // Actualizar username en sesión también
				$_SESSION["profile_update_success"] = "Perfil actualizado correctamente.";
			 } else {
				 // Si un admin editó a otro user (esto viene de otra vista)
				 $_SESSION["user_update_success"] = "Usuario actualizado correctamente.";
			 }
			 
			 // Redirigir según el origen
			 if(isset($_POST["is_admin_edit"]) && $_POST["is_admin_edit"] == "true"){
				 Core::redir("./?view=users");
			 }else{
				 Core::redir("./?view=profile");
			 }

		} else {
			 // Hubo error de contraseña: Guardar error en sesión y redirigir atrás
			 $_SESSION["profile_update_error"] = $password_error;
			 Core::redir("./?view=profile");
		}

	} else {
		// Usuario no encontrado
		$_SESSION["profile_update_error"] = "Error: Usuario no encontrado.";
		Core::redir("./?view=profile");
	}

} else {
	// No se recibieron datos POST o user_id
	 $_SESSION["profile_update_error"] = "Error: Datos inválidos.";
	 Core::redir("./?view=profile");
}

?>