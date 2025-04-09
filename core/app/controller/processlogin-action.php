<?php
session_start();
require_once("core/app/model/UserData.php");

if(isset($_POST["username"]) && isset($_POST["password"])){
	$user = UserData::getByUsername($_POST["username"]);
	if($user!=null && $user->is_active){
		if(password_verify($_POST["password"], $user->password)){
			$_SESSION["user_id"]=$user->id;
			$_SESSION["user_name"]=$user->username;
			$_SESSION["user_image"]=$user->image;
			$_SESSION["is_admin"]=$user->is_admin;
			$_SESSION["user_lastname"]=$user->lastname;
			$_SESSION["user_fullname"]=$user->name." ".$user->lastname;
			$_SESSION["user_email"]=$user->email;
			Core::redir("./");
		} else {
			$_SESSION["login_error"] = "Error: Contraseña incorrecta.";
			Core::redir("./?view=login");
		}
	} else {
		$_SESSION["login_error"] = "Error: Usuario no encontrado o inactivo.";
		Core::redir("./?view=login");
	}
} else {
	$_SESSION["login_error"] = "Error: Datos incompletos.";
	Core::redir("./?view=login");
}
?> 