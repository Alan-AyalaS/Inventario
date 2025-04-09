<?php

// Mostrar mensajes de error de login si existen
if (isset($_SESSION["login_error"])) {
    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION["login_error"]) . '</div>';
    unset($_SESSION["login_error"]);
}

// Cargar configuración (parece estar en layout.php, pero por si acaso)
if (!isset($configs)) {
    $configs = ConfigurationData::getAll();
    $system_title = "INVENTIO LITE";
    foreach($configs as $conf) {
        if($conf->short == "title") {
            $system_title = $conf->val;
            break;
        }
    }
}

if(isset($_SESSION["user_id"]) && $_SESSION["user_id"]!=""){
		print "<script>window.location='index.php?view=home';</script>";
}

?>
<br><br><br><br><br>
<div class="content">
<div class="container">
<div class="row">
    	<div class="col-md-4 col-md-offset-4">
    	<?php if(isset($_COOKIE['password_updated'])):?>
    		<div class="alert alert-success">
    		<p><i class='glyphicon glyphicon-off'></i> Se ha cambiado la contraseña exitosamente !!</p>
    		<p>Pruebe iniciar sesion con su nueva contraseña.</p>

    		</div>
    	<?php setcookie("password_updated","",time()-18600);
    	 endif; ?>






<div class="card">
                              <div class="card-header" data-background-color="green">
                                  <h4 class="title">Acceder a Inventio Lite</h4>
                              </div>
                <div class="card-content">
 
 <form accept-charset="UTF-8" role="form" method="post" action="./?view=processlogin">
                    <fieldset>
			    	  	<div class="form-group">
			    		    <input class="form-control" placeholder="Usuario o Email" name="username" type="text">
			    		</div>
			    		<div class="form-group">
			    			<input class="form-control" placeholder="Contraseña" name="password" type="password" value="">
			    		</div>
			    		<input class="btn btn-lg btn-primary btn-block" type="submit" value="Iniciar Sesion">
			    	</fieldset>
			      	</form>

                </div>
              </div>




		</div>
	</div>
	</div>
	</div>
<br><br><br><br><br><br><br><br><br><br><br><br>