<?php

if(!isset($_POST['user_id']) || !isset($_POST['name']) || !isset($_POST['lastname']) || !isset($_POST['username']) || !isset($_POST['email'])){
	Core::redir("./index.php?view=users");
}

$user = UserData::getById($_POST['user_id']);
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

Core::redir("./index.php?view=users");
?>