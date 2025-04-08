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
		
		$_SESSION["user_id"]=$user->id;
		$_SESSION["user_name"]=$user->name;
		$_SESSION["user_lastname"]=$user->lastname;
		$_SESSION["user_image"]=$user->image ? $user->image : 'default-avatar-icon.jpg';
		
		// Debug session
		echo "<!-- Session after setting: ";
		print_r($_SESSION);
		echo " -->";
		echo "<!-- Session image: " . $_SESSION["user_image"] . " -->";
		
		print "<script>window.location='index.php';</script>";
	}else{
		print "<script>window.location='index.php?error=1';</script>";
	}
}else{
	print "<script>window.location='index.php?error=1';</script>";
}
?> 