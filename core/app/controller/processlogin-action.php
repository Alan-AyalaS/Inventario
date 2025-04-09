<?php
// Asegurarnos de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

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
			header("Location: ./");
			exit();
		} else {
			header("Location: ./?view=login&error=1");
			exit();
		}
	} else {
		header("Location: ./?view=login&error=1");
		exit();
	}
} else {
	header("Location: ./?view=login&error=1");
	exit();
}
?> 