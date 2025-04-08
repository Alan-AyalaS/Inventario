<?php
session_start();

$user = UserData::getByMail($_POST["username"]);
if($user==null){
	$user = UserData::getByUsername($_POST["username"]);
}
if($user!=null){
	if($user->password==sha1(md5($_POST["password"]))){
		// Debug info
		echo "<!-- DEBUG INFO -->\n";
		echo "<!-- User object dump: ";
		print_r($user);
		echo " -->\n";
		echo "<!-- User image from DB: " . $user->image . " -->\n";
		echo "<!-- User image type: " . gettype($user->image) . " -->\n";
		echo "<!-- User image empty: " . (empty($user->image) ? 'true' : 'false') . " -->\n";
		
		$_SESSION["user_id"]=$user->id;
		$_SESSION["user_name"]=$user->name;
		$_SESSION["user_lastname"]=$user->lastname ? $user->lastname : '';
		
		// Asegurarse de que la imagen se guarde en la sesión
		if(!empty($user->image)) {
			$_SESSION["user_image"] = $user->image;
		} else {
			$_SESSION["user_image"] = "default-avatar-icon.jpg";
		}
		
		// Debug session
		echo "<!-- Session after setting: ";
		print_r($_SESSION);
		echo " -->\n";
		echo "<!-- Session image: " . $_SESSION["user_image"] . " -->\n";
		
		// Redirigir después de mostrar la información de depuración
		header("Location: index.php");
		exit();
	}else{
		header("Location: index.php?error=1");
		exit();
	}
}else{
	header("Location: index.php?error=1");
	exit();
}
?> 