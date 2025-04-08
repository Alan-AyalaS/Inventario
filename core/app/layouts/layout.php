<!DOCTYPE html>
<!--
* CoreUI - Free Bootstrap Admin Template
* @version v4.2.1
* @link https://coreui.io
* Copyright (c) 2022 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://coreui.io/license)
-->
<!-- Breadcrumb-->
<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title>Inventio Lite</title>
    <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="assets/css/vendors/simplebar.css">
    <!-- Main styles for this application-->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.23.0/themes/prism.css">
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-icons/bootstrap-icons.css">
    <script type="text/javascript" src="assets/jquery/jquery.min.js"></script>
    <link href="vendors/@coreui/chartjs/css/coreui-chartjs.css" rel="stylesheet">
  </head>
  <body>
<?php
require_once 'core/app/model/ConfigurationData.php';
$configs = ConfigurationData::getAll();
$system_title = "INVENTIO LITE"; // Valor por defecto
$providers_enabled = false;
$clients_enabled = false;
$sells_enabled = true;
$sell_enabled = true;
$box_enabled = true;
$reports_enabled = true;
$purchases_enabled = false;

foreach($configs as $conf) {
    if($conf->short == "active_providers" && $conf->val == 1) {
        $providers_enabled = true;
    }
    if($conf->short == "active_clients" && $conf->val == 1) {
        $clients_enabled = true;
    }
    if($conf->short == "title") {
        $system_title = $conf->val;
    }
    if($conf->short == "active_sells" && $conf->val == 0) {
        $sells_enabled = false;
    }
    if($conf->short == "active_sell" && $conf->val == 0) {
        $sell_enabled = false;
    }
    if($conf->short == "active_box" && $conf->val == 0) {
        $box_enabled = false;
    }
    if($conf->short == "active_reports" && $conf->val == 0) {
        $reports_enabled = false;
    }
    if($conf->short == "active_purchases" && $conf->val == 1) {
        $purchases_enabled = true;
    }
}

// Definir $current_user si el usuario está logueado
if(isset($_SESSION["user_id"])) {
    require_once 'core/app/model/UserData.php';
    $current_user = UserData::getById($_SESSION["user_id"]);
}
?>
<?php if(!isset($_SESSION["user_id"])):?>
<div class="bg-light min-vh-100 d-flex flex-row align-items-center">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card-group d-block d-md-flex row">
<div class="card col-md-12 p-4 mb-0">
<div class="card-body">
<h1><?php echo $system_title; ?></h1>
<br>
<p class="text-medium-emphasis">Iniciar Sesion al Sistema</p>
<form method="post" action="./?action=processlogin">
<div class="input-group mb-3"><span class="input-group-text">
<svg class="icon">
<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
</svg></span>
<input class="form-control" type="text" name="username" placeholder="Email o Usuario">
</div>
<div class="input-group mb-4"><span class="input-group-text">
<svg class="icon">
<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
</svg></span>
<input class="form-control" name="password" type="password" placeholder="Password">
</div>
<div class="row">
<div class="col-6">
<button class="btn btn-primary px-4" type="submit">Iniciar Sesion</button>
</div>
<!--
<div class="col-6 text-end">
<button class="btn btn-link px-0" type="button">Forgot password?</button>
</div>
-->
</div>
</form>
<br><br><br>

</div>
</div>

</div>
</div>
</div>
</div>
</div>
<?php else:?>
    <div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">

<div class="sidebar-brand d-none d-md-flex">
<div class="sidebar-brand-full" width="118" height="46" alt="CoreUI Logo">

<h4><a href="./" style="color: white;"><?php echo $system_title; ?></a></h4>

</div>
<div class="sidebar-brand-narrow" width="46" height="46" alt="CoreUI Logo">
<h4><a href="./" style="color: white;"><?php echo substr($system_title, 0, 1); ?><b><?php echo substr($system_title, -1); ?></b></a></h4>

</div>
</div>









      <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link" href="./">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-home"></use>
            </svg> INICIO</a></li>
        <?php if($sell_enabled): ?>
        <li class="nav-item"><a class="nav-link" href="./?view=sell">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-money"></use>
            </svg> VENDER</a>
        </li>
        <?php endif; ?>
        <?php if($sells_enabled): ?>
        <li class="nav-item"><a class="nav-link" href="./?view=sells">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-cart"></use>
            </svg> VENTAS</a>
        </li>
        <?php endif; ?>
        <?php if($box_enabled): ?>
        <li class="nav-item"><a class="nav-link" href="./?view=box">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
            </svg> CAJA</a>
        </li>
        <?php endif; ?>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-folder-open"></use>
            </svg> CATALOGOS</a>
          <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="./?view=categories"><span class="nav-icon"></span> CATEGORIAS  </a></li>
            <?php if($clients_enabled): ?>
            <li class="nav-item"><a class="nav-link" href="./?view=clients"><span class="nav-icon"></span> CLIENTES  </a></li>
            <?php endif; ?>
            <?php if($providers_enabled): ?>
            <li class="nav-item"><a class="nav-link" href="./?view=providers"><span class="nav-icon"></span> PROVEEDORES  </a></li>
            <?php endif; ?>
          </ul>
        </li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg> INVENTARIO</a>
          <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="./?view=inventary"><span class="nav-icon"></span> INVENTARIO</a></li>
            <?php if($purchases_enabled): ?>
            <li class="nav-item"><a class="nav-link" href="./?view=re"><span class="nav-icon"></span> HACER COMPRA  </a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=res"><span class="nav-icon"></span> COMPRAS  </a></li>
            <?php endif; ?>
          </ul>
        </li>
        <?php if($reports_enabled): ?>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-chart"></use>
            </svg> REPORTES</a>
          <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="./?view=reports"><span class="nav-icon"></span> MOVIMIENTOS</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=sellreports"><span class="nav-icon"></span> REPORTE DE VENTAS  </a></li>
          </ul>
        </li>
        <?php endif; ?>
<!--
        <li class="nav-title">Components</li>
-->
        <!-- DEBUG INFO -->
        <!-- Current user: <?php print_r($current_user); ?> -->
        <!-- is_admin value: <?php echo isset($current_user) ? $current_user->is_admin : 'not set'; ?> -->
        <!-- is_admin type: <?php echo isset($current_user) ? gettype($current_user->is_admin) : 'not set'; ?> -->
        <!-- Condition result: <?php echo isset($current_user) && $current_user->is_admin == "1" ? 'true' : 'false'; ?> -->
        <?php 
        // Definir $is_admin antes de usarla
        $is_admin = false;
        if(isset($current_user)) {
            $is_admin = ($current_user->is_admin == "1");
        }
        ?>
        <!-- DEBUG: is_admin = <?php var_dump($is_admin); ?> -->
        <?php if($is_admin): ?>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
            </svg> ADMINISTRACION</a>
          <ul class="nav-group-items">
            <li class="nav-item"><a class="nav-link" href="./?view=users&opt=all"><span class="nav-icon"></span> USUARIOS</a></li>
            <li class="nav-item"><a class="nav-link" href="./?view=settings&opt=all"><span class="nav-icon"></span> AJUSTES</a></li>
          </ul>
        </li>
        <?php endif; ?>
        <!--
        <li class="nav-item mt-auto"><a class="nav-link" href="https://coreui.io/docs/templates/installation/" target="_blank">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-description"></use>
            </svg> Docs</a></li>
        <li class="nav-item"><a class="nav-link nav-link-danger" href="https://coreui.io/pro/" target="_top">
            <svg class="nav-icon">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-layers"></use>
            </svg> Try CoreUI
            <div class="fw-semibold">PRO</div>
          </a></li>
        -->
      </ul>
      <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
      <header class="header header-sticky mb-4">
        <div class="container-fluid">
          <button class="header-toggler px-md-0 me-md-3" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
              <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
          </button><a class="header-brand d-md-none" href="#">
            <svg width="118" height="46" alt="CoreUI Logo">
              <use xlink:href="assets/brand/coreui.svg#full"></use>
            </svg></a>
            <!--
          <ul class="header-nav d-none d-md-flex">
            <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
          </ul>
        -->
          <ul class="header-nav ms-auto">
            <!--
            <li class="nav-item"><a class="nav-link" href="#">
                <svg class="icon icon-lg">
                  <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                </svg></a></li>
              -->
          </ul>
          <ul class="header-nav ms-3">
            <li class="nav-item dropdown">
              <div class="d-flex align-items-center">
                <span class="me-2 text-dark"><?php echo isset($_SESSION["user_name"]) ? $_SESSION["user_name"] : "Usuario"; ?></span>
                <button class="btn btn-link nav-link py-0 dropdown-toggle" data-coreui-toggle="dropdown" aria-expanded="false">
                  <div class="avatar avatar-md">
                    <!-- DEBUG INFO -->
                    <!-- User image in session: <?php echo isset($_SESSION['user_image']) ? $_SESSION['user_image'] : 'not set'; ?> -->
                    <!-- User image path: <?php echo isset($_SESSION['user_image']) && !empty($_SESSION['user_image']) ? 'assets/img/avatars/' . $_SESSION['user_image'] : 'default'; ?> -->
                    <!-- Image exists: <?php echo file_exists('assets/img/avatars/' . (isset($_SESSION['user_image']) ? $_SESSION['user_image'] : 'default-avatar-icon.jpg')) ? 'true' : 'false'; ?> -->
                    <!-- Image path: <?php echo 'assets/img/avatars/' . (isset($_SESSION['user_image']) ? $_SESSION['user_image'] : 'default-avatar-icon.jpg'); ?> -->
                    <!-- Session dump: <?php print_r($_SESSION); ?> -->
                    <img class="avatar-img" src="<?php echo isset($_SESSION['user_image']) && !empty($_SESSION['user_image']) ? 'assets/img/avatars/' . $_SESSION['user_image'] : 'assets/img/avatars/default-avatar-icon.jpg'; ?>" alt="<?php echo isset($_SESSION["user_name"]) ? $_SESSION["user_name"] : "Usuario"; ?>">
                  </div>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a class="dropdown-item" href="index.php?view=profile">
                    <svg class="icon me-2">
                        <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                    </svg> Perfil
                  </a>
                  <a class="dropdown-item" href="logout.php">
                    <svg class="icon me-2">
                        <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                    </svg> Cerrar Sesión
                  </a>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="header-divider"></div>
        <div class="container-fluid">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
              <li class="breadcrumb-item">
                <span>Home</span>
              </li>
              <li class="breadcrumb-item active"><span>Dashboard</span></li>
            </ol>
          </nav>
        </div>
        <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#debugModal">
            Debug Info
        </button>
      </header>
      <div class="body flex-grow-1 px-3">
        <div class="container-fluid">

          <?php View::load("index");?>

        </div>
      </div>
      <footer class="footer">
        <div><a href="https://evilnapsis.com/">Evilnapsis </a> © 2023.</div>
        <div class="ms-auto">Version <b>4.1</b></div> 
      </footer>
    </div>
    <?php endif; ?>
    <!-- CoreUI and necessary plugins-->
    <script src="vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="vendors/simplebar/js/simplebar.min.js"></script>
    <!-- Plugins and scripts required by this view-->
    <script src="vendors/chart.js/js/chart.min.js"></script>
    <script src="vendors/@coreui/chartjs/js/coreui-chartjs.js"></script>
    <script src="vendors/@coreui/utils/js/coreui-utils.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos los dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-coreui-toggle="dropdown"]'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
          return new coreui.Dropdown(dropdownToggleEl)
        });
      });
    </script>

    <!-- Modal de depuración -->
    <div class="modal fade" id="debugModal" tabindex="-1" role="dialog" aria-labelledby="debugModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="debugModalLabel">Información de Depuración</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre><?php 
                        $debug_info = array(
                            '1. Información del Usuario Actual' => array(
                                'ID' => $_SESSION["user_id"],
                                'Nombre' => $_SESSION["user_name"],
                                'is_admin' => isset($current_user) ? $current_user->is_admin : 'not set',
                                'Tipo de is_admin' => isset($current_user) ? gettype($current_user->is_admin) : 'not set'
                            ),
                            '2. Estado de la Condición' => array(
                                '¿$current_user está definido?' => isset($current_user) ? 'Sí' : 'No',
                                'Valor de is_admin' => isset($current_user) ? $current_user->is_admin : 'not set',
                                'Tipo de is_admin' => isset($current_user) ? gettype($current_user->is_admin) : 'not set'
                            ),
                            '3. Comparaciones detalladas' => array(
                                'is_admin === 1' => isset($current_user) ? ($current_user->is_admin === 1 ? 'Sí' : 'No') : 'not set',
                                'is_admin === "1"' => isset($current_user) ? ($current_user->is_admin === "1" ? 'Sí' : 'No') : 'not set',
                                'is_admin == 1' => isset($current_user) ? ($current_user->is_admin == 1 ? 'Sí' : 'No') : 'not set',
                                'is_admin == "1"' => isset($current_user) ? ($current_user->is_admin == "1" ? 'Sí' : 'No') : 'not set',
                                'is_admin == true' => isset($current_user) ? ($current_user->is_admin == true ? 'Sí' : 'No') : 'not set',
                                'is_admin == false' => isset($current_user) ? ($current_user->is_admin == false ? 'Sí' : 'No') : 'not set',
                                'is_admin === true' => isset($current_user) ? ($current_user->is_admin === true ? 'Sí' : 'No') : 'not set',
                                'is_admin === false' => isset($current_user) ? ($current_user->is_admin === false ? 'Sí' : 'No') : 'not set',
                                'is_admin === "0"' => isset($current_user) ? ($current_user->is_admin === "0" ? 'Sí' : 'No') : 'not set',
                                'is_admin === 0' => isset($current_user) ? ($current_user->is_admin === 0 ? 'Sí' : 'No') : 'not set',
                                'is_admin == 0' => isset($current_user) ? ($current_user->is_admin == 0 ? 'Sí' : 'No') : 'not set',
                                'is_admin == "0"' => isset($current_user) ? ($current_user->is_admin == "0" ? 'Sí' : 'No') : 'not set'
                            ),
                            '4. Conversión a número' => array(
                                'is_admin convertido a int' => isset($current_user) ? (int)$current_user->is_admin : 'not set',
                                'is_admin_num === 1' => isset($current_user) ? ((int)$current_user->is_admin === 1 ? 'Sí' : 'No') : 'not set'
                            ),
                            '5. Variables de Sesión' => $_SESSION,
                            '6. Objeto Usuario Completo' => isset($current_user) ? (array)$current_user : 'not set',
                            '7. Consulta SQL' => 'SELECT * FROM user WHERE id = ' . (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 'not set'),
                            '8. Verificación del Menú' => array(
                                '¿Existe el menú de administración en el código?' => 'Sí',
                                'Posición del menú en el código' => '2184'
                            ),
                            '9. Información del Layout' => array(
                                'Valor de is_admin en layout' => isset($current_user) ? $current_user->is_admin : 'not set',
                                'Tipo de is_admin en layout' => isset($current_user) ? gettype($current_user->is_admin) : 'not set',
                                'Resultado de la condición is_admin' => isset($current_user) && ($current_user->is_admin == "1" || $current_user->is_admin == 1) ? 'true' : 'false',
                                'Código de la condición' => '$is_admin = isset($current_user) && ($current_user->is_admin === "1" || $current_user->is_admin === 1);'
                            ),
                            '10. Información Adicional' => array(
                                '¿Dónde se define $current_user?' => 'En el archivo layout.php',
                                '¿Cuándo se define $current_user?' => 'Antes de mostrar el menú',
                                '¿Qué valor tiene $is_admin?' => isset($is_admin) ? ($is_admin ? 'true' : 'false') : 'not set',
                                '¿La condición if($is_admin) se evalúa como true?' => isset($is_admin) ? ($is_admin ? 'Sí' : 'No') : 'not set',
                                '¿El menú de administración está visible?' => isset($is_admin) && $is_admin ? 'Sí' : 'No',
                                '¿El código del menú está presente?' => 'Sí',
                                '¿La condición if($is_admin) está presente?' => 'Sí'
                            )
                        );
                        echo json_encode($debug_info, JSON_PRETTY_PRINT);
                    ?></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>