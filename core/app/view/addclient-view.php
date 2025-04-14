<?php

if(count($_POST)>0){
	$user = new PersonData();
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->address1 = empty($_POST["address1"]) ? "Desconocido" : $_POST["address1"];
	$user->city = $_POST["city"];
	$user->state = $_POST["state"];
	$user->zip_code = empty($_POST["zip_code"]) ? "Desconocido" : $_POST["zip_code"];
	$user->email1 = $_POST["email1"];
	$user->phone1 = $_POST["phone1"];
	$user->add_client();

print "<script>window.location='index.php?view=clients';</script>";


}


?>