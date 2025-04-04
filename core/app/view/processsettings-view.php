<?php
// Desactivar la redirección automática
$success = true;
$errors = [];

try {
	$configurations = ConfigurationData::getAll();
	
	foreach($configurations as $conf){
		if(isset($_POST[$conf->short])) {
			$value = $_POST[$conf->short];
			if($conf->kind==1){ // Si es un checkbox
				$value = ($value == 1) ? 1 : 0;
			}
			$conf->val = $value;
			$conf->update();
		} else {
			$errors[] = "No se recibió el valor para la configuración: " . $conf->name . " (short: " . $conf->short . ")";
			$success = false;
		}
	}
	
} catch(Exception $e) {
	$errors[] = "Error al procesar los cambios: " . $e->getMessage();
	$success = false;
}

// Mostrar los errores en una página HTML completa
?>
<!DOCTYPE html>
<html>
<head>
	<title>Procesando Configuración</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
	<div class="container mt-5">
		<?php if($success): ?>
			<div class="alert alert-success">
				<h4>¡Cambios guardados correctamente!</h4>
				<p>Los cambios se han aplicado correctamente.</p>
				<a href="index.php?view=settings" class="btn btn-primary">Volver a Ajustes</a>
			</div>
		<?php else: ?>
			<div class="alert alert-danger">
				<h4>Se encontraron los siguientes errores:</h4>
				<ul>
					<?php foreach($errors as $error): ?>
						<li><?php echo $error; ?></li>
					<?php endforeach; ?>
				</ul>
				<a href="index.php?view=settings" class="btn btn-primary">Volver a Ajustes</a>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>
?> 