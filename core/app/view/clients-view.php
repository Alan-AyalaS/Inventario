<div class="row">
	<div class="col-md-12">

		<h1>Directorio de Clientes</h1>
	<div class="">
	<a href="index.php?view=newclient" class="btn btn-secondary"><i class='fa fa-smile-o'></i> Nuevo Cliente</a>
<?php
require_once 'core/app/model/ConfigurationData.php';
$configs = ConfigurationData::getAll();
$word_enabled = false;
$excel_enabled = false;
$pdf_enabled = false;

foreach($configs as $conf) {
    if($conf->short == "active_reports_word" && $conf->val == 1) $word_enabled = true;
    if($conf->short == "active_reports_excel" && $conf->val == 1) $excel_enabled = true;
    if($conf->short == "active_reports_pdf" && $conf->val == 1) $pdf_enabled = true;
}
?>

<div class="btn-group pull-right">
  <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important;">
    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end" style="background-color: #28a745; border: none;">
    <?php if($word_enabled): ?>
    <li><a class="dropdown-item text-white" href="index.php?view=download-clients" style="background-color: transparent !important; transition: color 0.3s ease;">Word 2007 (.docx)</a></li>
    <?php endif; ?>
    <?php if($excel_enabled): ?>
    <li><a class="dropdown-item text-white" href="index.php?view=download-clients-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
    <?php endif; ?>
    <?php if($pdf_enabled): ?>
    <li><a class="dropdown-item text-white" href="index.php?view=download-clients-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
    <?php endif; ?>
  </ul>
</div>
</div>	
<br>
<div class="card">
	<div class="card-header">
		CLIENTES
	</div>
		<div class="card-body">


		<?php

		$users = PersonData::getClients();
		if(count($users)>0){
			// si hay usuarios
			?>

			<table class="table table-bordered table-hover">
			<thead>
			<th>Nombre completo</th>
			<th>Dirección</th>
			<th>Ciudad/Municipio</th>
			<th>Estado</th>
			<th>Código Postal</th>
			<th>Email</th>
			<th>Teléfono</th>
			<th></th>
			</thead>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td><?php echo $user->name." ".$user->lastname; ?></td>
				<td><?php echo $user->address1; ?></td>
				<td><?php echo $user->city; ?></td>
				<td><?php echo $user->state; ?></td>
				<td><?php echo $user->zip_code; ?></td>
				<td><?php echo $user->email1; ?></td>
				<td><?php echo $user->phone1; ?></td>
				<td style="width:130px;">
				<a href="index.php?view=editclient&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
				<a href="index.php?view=delclient&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>
				</td>
				</tr>
				<?php

			}
echo "</table>";


		}else{
			echo "<p class='alert alert-danger'>No hay clientes</p>";
		}


		?>
		</div>
</div>


	</div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.dropdown-item:hover {
  color:rgb(24, 56, 31) !important;
  background-color: white !important;
}
</style>