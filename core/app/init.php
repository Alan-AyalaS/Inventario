<?php

/// en caso de que el parametro action este definido evitamos que se muestre
/// el layout por defecto y ejecutamos el action sin mostrar nada de vista
// print_r($_GET);
if(!isset($_GET["action"])){
	// Si es la vista de login, manejarla de manera especial
	if(isset($_GET["view"]) && $_GET["view"] == "processlogin"){
		include "core/app/controller/processlogin-action.php";
	} else {
		Module::loadLayout("index");
	}
}else{
	Action::load($_GET["action"]);
}

?>