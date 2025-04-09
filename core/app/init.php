<?php
// Asegurarnos de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

/// en caso de que el parametro action este definido evitamos que se muestre
/// el layout por defecto y ejecutamos el action sin mostrar nada de vista
// print_r($_GET);
if(!isset($_GET["action"])){
	Module::loadLayout("index");
} else {
	// Si es la acción de login, manejarla de manera especial
	if($_GET["action"] == "processlogin"){
		include "core/app/controller/processlogin-action.php";
	} else {
		Action::load($_GET["action"]);
	}
}

?>