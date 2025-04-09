<?php

// Asegurarnos de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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

if(isset($_SESSION["user_id"]) && $_SESSION["user_id"]!="") {
    print "<script>window.location='index.php?view=home';</script>";
}

// Mostrar mensajes de error de login si existen
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger" role="alert" style="margin: 20px auto; max-width: 400px; background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 15px; border-radius: 4px;">
            <i class="fa fa-exclamation-circle"></i> Error: Contraseña incorrecta.
          </div>';
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
        <?php setcookie("password_updated","",time()-18600); endif; ?>

        <div class="card">
            <div class="card-header" data-background-color="green">
                <h4 class="title">Acceder a Inventio Lite</h4>
            </div>
            <div class="card-content">
                <form accept-charset="UTF-8" role="form" method="post" action="./?action=processlogin">
                    <fieldset>
                        <div class="form-group">
                            <input class="form-control" placeholder="Usuario o Email" name="username" type="text" required>
                        </div>
                        <div class="form-group">
                            <input class="form-control" placeholder="Contraseña" name="password" type="password" required>
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