<?php
session_start();

$user = UserData::getByMail($_POST["username"]);
if($user==null){
	$user = UserData::getByUsername($_POST["username"]);
}
if($user!=null){
	if($user->password==sha1(md5($_POST["password"]))){
		// Debug info
		echo "<!-- User object dump: ";
		print_r($user);
		echo " -->";
		echo "<!-- User image: " . $user->image . " -->";
		echo "<!-- User image type: " . gettype($user->image) . " -->";
		echo "<!-- User image empty: " . (empty($user->image) ? 'true' : 'false') . " -->";
		echo "<!-- User image null: " . (is_null($user->image) ? 'true' : 'false') . " -->";
		
		$_SESSION["user_id"]=$user->id;
		$_SESSION["user_name"]=$user->name;
		$_SESSION["user_lastname"]=$user->lastname ? $user->lastname : '';
		$_SESSION["user_image"]=$user->image;
		
		// Debug session
		echo "<!-- Session after setting: ";
		print_r($_SESSION);
		echo " -->";
		echo "<!-- Session image: " . $_SESSION["user_image"] . " -->";
		echo "<!-- Session image type: " . gettype($_SESSION["user_image"]) . " -->";
		echo "<!-- Session image empty: " . (empty($_SESSION["user_image"]) ? 'true' : 'false') . " -->";
		echo "<!-- Session image null: " . (is_null($_SESSION["user_image"]) ? 'true' : 'false') . " -->";
		
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