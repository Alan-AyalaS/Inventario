	<?php
	$found=false;
$products = ProductData::getAll();
$products_array = array();
foreach($products as $product){
	$q = $product->availability;
	if($q <= $product->inventary_min){
		$products_array[] = $product;
	}
}

require_once 'core/app/model/ConfigurationData.php';
$configs = ConfigurationData::getAll();
$providers_enabled = false;
$clients_enabled = false;
foreach($configs as $conf) {
    if($conf->short == "active_providers" && $conf->val == 1) {
        $providers_enabled = true;
    }
    if($conf->short == "active_clients" && $conf->val == 1) {
        $clients_enabled = true;
    }
}
?>
<div class="row">
	<div class="col-md-12">
		<h1>Bienvenido a Inventio Lite</h1>
</div>
</div>

                    <div class="row">
                      <div class="col-6 col-lg-3">
                        <div class="card">
                          <div class="card-body p-3 d-flex align-items-center">
                            <div class="bg-primary text-white p-3 me-3">
                              <svg class="icon icon-xl">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-smile"></use>
                              </svg>
                            </div>
                            <div>
                              <div class="fs-6 fw-semibold text-primary"><?php echo count(ProductData::getAll());?></div>
                              <div class="text-medium-emphasis text-uppercase fw-semibold small">INVENTARIO</div>
                            </div>
                          </div>
                          <div class="card-footer px-3 py-2"><a class="btn-block text-medium-emphasis d-flex justify-content-between align-items-center" href="./?view=inventary"><span class="small fw-semibold">IR A INVENTARIO</span>
                              <svg class="icon">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-chevron-right"></use>
                              </svg></a></div>
                        </div>
                      </div>
                      <!-- /.col-->
                      <?php if($clients_enabled): ?>
                      <div class="col-6 col-lg-3">
                        <div class="card">
                          <div class="card-body p-3 d-flex align-items-center">
                            <div class="bg-info text-white p-3 me-3">
                              <svg class="icon icon-xl">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                              </svg>
                            </div>
                            <div>
                              <div class="fs-6 fw-semibold text-info"><?php echo count(PersonData::getClients());?></div>
                              <div class="text-medium-emphasis text-uppercase fw-semibold small">IR A CLIENTES</div>
                            </div>
                          </div>
                          <div class="card-footer px-3 py-2"><a class="btn-block text-medium-emphasis d-flex justify-content-between align-items-center" href="./?view=clients"><span class="small fw-semibold">IR A CLIENTES</span>
                              <svg class="icon">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-chevron-right"></use>
                              </svg></a></div>
                        </div>
                      </div>
                      <?php endif; ?>
                      <?php if($providers_enabled): ?>
                      <div class="col-6 col-lg-3">
                        <div class="card">
                          <div class="card-body p-3 d-flex align-items-center">
                            <div class="bg-warning text-white p-3 me-3">
                              <svg class="icon icon-xl">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-truck"></use>
                              </svg>
                            </div>
                            <div>
                              <div class="fs-6 fw-semibold text-warning"><?php echo count(PersonData::getProviders());?></div>
                              <div class="text-medium-emphasis text-uppercase fw-semibold small">IR A PROVEEDORES</div>
                            </div>
                          </div>
                          <div class="card-footer px-3 py-2"><a class="btn-block text-medium-emphasis d-flex justify-content-between align-items-center" href="./?view=providers"><span class="small fw-semibold">IR A PROVEEDORES</span>
                              <svg class="icon">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-chevron-right"></use>
                              </svg></a></div>
                        </div>
                      </div>
                      <?php endif; ?>
                      <div class="col-6 col-lg-3">
                        <div class="card">
                          <div class="card-body p-3 d-flex align-items-center">
                            <div class="bg-danger text-white p-3 me-3">
                              <svg class="icon icon-xl">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                              </svg>
                            </div>
                            <div>
                              <div class="fs-6 fw-semibold text-danger"><?php echo count(CategoryData::getAll());?></div>
                              <div class="text-medium-emphasis text-uppercase fw-semibold small">CATEGORIAS</div>
                            </div>
                          </div>
                          <div class="card-footer px-3 py-2"><a class="btn-block text-medium-emphasis d-flex justify-content-between align-items-center" href="./?view=categories"><span class="small fw-semibold">IR A CATEGORIAS</span>
                              <svg class="icon">
                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-chevron-right"></use>
                              </svg></a></div>
                        </div>
                      </div>
                    </div>

<br>
<div class="row">
	<div class="col-md-12">
<div class="card">
  <div class="card-header">ALERTAS DE INVENTARIO
  </div>
    <div class="card-body">



<?php 

if(count($products_array)>0){?>
<br><table class="table table-bordered table-hover">
	<thead>
		<th >Codigo</th>
		<th>Nombre del producto</th>
		<th>En Stock</th>
		<th></th>
	</thead>
	<?php
foreach($products_array as $product):
	$q = $product->availability;
	?>
	<tr class="<?php if($q==0){ echo "danger"; }else if($q<=$product->inventary_min/2){ echo "danger"; } else if($q<=$product->inventary_min){ echo "warning"; } ?>">
		<td><?php echo $product->id; ?></td>
		<td><?php echo $product->name; ?></td>
		<td><?php echo $q; ?></td>
		<td>
		<?php if($q==0){ echo "<span class='label label-danger'>No hay existencias.</span>";}else if($q<=$product->inventary_min/2){ echo "<span class='label label-danger'>Quedan muy pocas existencias.</span>";} else if($q<=$product->inventary_min){ echo "<span class='label label-warning'>Quedan pocas existencias.</span>";} ?>
		</td>
	</tr>
<?php
endforeach;
?>
</table>

<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay alertas</h2>
		<p>Por el momento no hay alertas de inventario, estas se muestran cuando el inventario ha alcanzado el nivel minimo.</p>
	</div>
	<?php
}

?>
    </div>
</div>
	</div>
</div>

<!-- Botón de Debug Admin -->
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#adminDebugModal" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    Debug Admin
</button>

<!-- Modal de Debug Admin -->
<div class="modal fade" id="adminDebugModal" tabindex="-1" role="dialog" aria-labelledby="adminDebugModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminDebugModalLabel">Debug - Menú Administración</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre><?php 
                    // Obtener el usuario actual
                    $current_user = UserData::getById($_SESSION["user_id"]);
                    
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
                            '¿La condición if($is_admin) está presente?' => 'Sí',
                            '¿El archivo layout.php está cargado?' => 'Sí',
                            '¿La variable $current_user está disponible en home-view?' => isset($current_user) ? 'Sí' : 'No',
                            '¿El valor de is_admin es el mismo en layout y home-view?' => isset($current_user) ? ($current_user->is_admin == "1" ? 'Sí' : 'No') : 'not set'
                        )
                    );
                    echo json_encode($debug_info, JSON_PRETTY_PRINT);
                ?></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios para el modal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>