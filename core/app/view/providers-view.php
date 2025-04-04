<div class="row">
	<div class="col-md-12">

		<h1>Directorio de Proveedores</h1>
<div class="">
	<a href="index.php?view=newprovider" class="btn btn-secondary"><i class='fa fa-truck'></i> Nuevo Proveedor</a>
<?php
require_once 'core/app/model/ConfigurationData.php';
$configs = ConfigurationData::getAll();
$word_enabled = false;
$excel_enabled = false;
$pdf_enabled = false;

foreach($configs as $conf) {
    if($conf->short == "enable_word_reports" && $conf->val == 1) $word_enabled = true;
    if($conf->short == "enable_excel_reports" && $conf->val == 1) $excel_enabled = true;
    if($conf->short == "enable_pdf_reports" && $conf->val == 1) $pdf_enabled = true;
}
?>
<div class="btn-group pull-right">
  <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important;">
    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end" style="background-color: #28a745; border: none;">
    <li><a class="dropdown-item text-white" href="index.php?view=download-providers" style="background-color: transparent !important; transition: color 0.3s ease;">Word 2007 (.docx)</a></li>
    <li><a class="dropdown-item text-white" href="index.php?view=download-providers-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
    <li><a class="dropdown-item text-white" href="index.php?view=download-providers-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
  </ul>
</div>
</div>
<br>
<div class="card">
	<div class="card-header">
		<h3 class="card-title">Proveedores</h3>
		<div class="card-tools">
			<div class="btn-group">
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-download"></i> Descargar <span class="caret"></span>
				</button>
				<div class="dropdown-menu" style="background-color: #343a40;">
					<li><a class="dropdown-item text-white" href="index.php?view=download-providers-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
					<li><a class="dropdown-item text-white" href="index.php?view=download-providers-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
				</div>
			</div>
			<a href="index.php?view=newprovider" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Nuevo Proveedor</a>
		</div>
	</div>
		<div class="card-body">


		<?php

		$users = PersonData::getProviders();
		if(count($users)>0){
			// si hay usuarios
			?>

			<table class="table table-bordered table-hover">
			<thead>
			<th>Nombre completo</th>
			<th>Direccion</th>
			<th>Email</th>
			<th>Telefono</th>
			<th></th>
			</thead>
			<?php
			foreach($users as $user){
				?>
				<tr>
				<td><?php echo $user->name." ".$user->lastname; ?></td>
				<td><?php echo $user->address1; ?></td>
				<td><?php echo $user->email1; ?></td>
				<td><?php echo $user->phone1; ?></td>
				<td style="width:130px;">
				<a href="index.php?view=editprovider&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
				<a href="index.php?view=delprovider&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>

				</td>
				</tr>
				<?php

			}
			echo "</table>";



		}else{
			echo "<p class='alert alert-danger'>No hay proveedores</p>";
		}


		?>

		</div>
</div>

	</div>
</div>

<style>
.dropdown-item:hover {
  color:rgb(24, 56, 31) !important;
  background-color: white !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>