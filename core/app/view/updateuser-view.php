<?php

if(!isset($_POST['user_id'])){
	Core::redir("./index.php?view=users");
}

$user = UserData::getById($_POST['user_id']);

// Si se está cambiando la contraseña desde el modal de perfil
if(isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
	// Verificar que la contraseña actual sea correcta
	if(!password_verify($_POST['current_password'], $user->password)) {
		$_SESSION['error'] = "La contraseña actual no es correcta";
		Core::redir("./?view=profile");
		exit;
	}

	// Verificar que las nuevas contraseñas coincidan
	if($_POST['new_password'] != $_POST['confirm_password']) {
		$_SESSION['error'] = "Las nuevas contraseñas no coinciden";
		Core::redir("./?view=profile");
		exit;
	}

	// Actualizar solo la contraseña
	$user->password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
	$user->update_passwd();
	$_SESSION['success'] = "Contraseña actualizada correctamente";
	Core::redir("./?view=profile");
	exit;
}

// Si se está actualizando desde la vista de administrador
if(isset($_POST['name']) && isset($_POST['lastname']) && isset($_POST['username']) && isset($_POST['email'])) {
	$user->name = $_POST['name'];
	$user->lastname = $_POST['lastname'];
	$user->username = $_POST['username'];
	$user->email = $_POST['email'];

	if(isset($_FILES["image"])){
		$image = new Upload($_FILES["image"]);
		if($image->uploaded){
			$image->Process("assets/img/avatars/");
			if($image->processed){
				$user->image = $image->file_dst_name;
			}
		}
	}

	if(isset($_POST['password']) && $_POST['password']!=""){
		$user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	}

	$user->update();
}

Core::redir("./index.php?view=users");
?>