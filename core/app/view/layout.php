<?php
$purchases_enabled = false;
foreach($configs as $conf) {
    if($conf->short == "active_purchases" && $conf->val == 1) {
        $purchases_enabled = true;
        break;
    }
}

// Obtener el usuario actual y verificar si es administrador
$current_user = UserData::getById($_SESSION["user_id"]);
$is_admin = isset($current_user) && ($current_user->is_admin === "1" || $current_user->is_admin === 1);

// Debug info
echo "<!-- Debug Admin Check -->\n";
echo "<!-- is_admin value: " . $current_user->is_admin . " -->\n";
echo "<!-- is_admin type: " . gettype($current_user->is_admin) . " -->\n";
echo "<!-- is_admin check result: " . ($is_admin ? 'true' : 'false') . " -->\n";
?>

<!-- Botón de Debug Info -->
<button type="button" class="btn btn-info" data-toggle="modal" data-target="#debugInfoModal">
  Debug Info
</button>

<!-- Incluir el modal de debug info -->
<?php include("debug-info-view.php"); ?>

                    <li class="nav-item"><a class="nav-link" href="./?view=inventary">
                        <svg class="nav-icon">
                          <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-layers"></use>
                        </svg> Inventario</a>
                        <ul class="nav-group-items">
                          <li class="nav-item"><a class="nav-link" href="./?view=inventary"><span class="nav-icon"></span> Inventario</a></li>
                          <?php if($purchases_enabled): ?>
                          <li class="nav-item"><a class="nav-link" href="./?view=purchases"><span class="nav-icon"></span> Compras</a></li>
                          <li class="nav-item"><a class="nav-link" href="./?view=make-purchase"><span class="nav-icon"></span> Hacer Compra</a></li>
                          <?php endif; ?>
                        </ul>
                    </li>

                    <?php if($is_admin): ?>
                    <li class="nav-item"><a class="nav-link" href="./?view=users">
                        <svg class="nav-icon">
                          <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                        </svg> Administración</a>
                        <ul class="nav-group-items">
                          <li class="nav-item"><a class="nav-link" href="./?view=users"><span class="nav-icon"></span> Usuarios</a></li>
                          <li class="nav-item"><a class="nav-link" href="./?view=categories"><span class="nav-icon"></span> Categorías</a></li>
                          <li class="nav-item"><a class="nav-link" href="./?view=products"><span class="nav-icon"></span> Productos</a></li>
                          <li class="nav-item"><a class="nav-link" href="./?view=clients"><span class="nav-icon"></span> Clientes</a></li>
                          <li class="nav-item"><a class="nav-link" href="./?view=providers"><span class="nav-icon"></span> Proveedores</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="assets/img/avatars/<?php echo $_SESSION["user_image"]; ?>" class="img-avatar" alt="<?php echo $_SESSION["user_name"]; ?>">
                            <?php echo $_SESSION["user_name"]; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="./?view=profile">
                                <svg class="nav-icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                                </svg> Mi Perfil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="./?view=logout">
                                <svg class="nav-icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                                </svg> Cerrar Sesión
                            </a>
                        </div>
                    </li> 

                    <?php 
                    // Definir $is_admin antes de usarla
                    $is_admin = false;
                    if(isset($current_user)) {
                        $is_admin = ($current_user->is_admin == "1");
                    }
                    ?>
                    <?php if($is_admin): ?>
<!--
                    <li class="nav-title">Components</li>
-->
                    <?php endif; ?> 