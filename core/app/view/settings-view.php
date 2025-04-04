<div class="row">
<div class="col-md-12">
<h1>Configuración</h1>

<?php

$configurations = ConfigurationData::getAll();

?>

<?php if(count($configurations)>0):?>
<br>
<div class="card">
	<div class="card-header">
		<h4 class="card-title">Configuración del Sistema</h4>
	</div>
	<div class="card-body">
		<form method="post" action="index.php?view=processsettings">
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre</th>
					<th>Valor</th>
				</thead>
				<?php foreach($configurations as $conf):?>
				<tr>
					<td><?php echo $conf->name; ?></td>
					<td>
						<?php if($conf->kind==1):?>
							<input type="hidden" name="<?php echo $conf->short; ?>" value="0">
							<input type="checkbox" name="<?php echo $conf->short; ?>" value="1" <?php echo ($conf->val == 1) ? 'checked' : ''; ?>>
						<?php else:?>
							<input type="text" name="<?php echo $conf->short; ?>" value="<?php echo $conf->val; ?>" class="form-control">
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Guardar Cambios</button>
			</div>
		</form>
	</div>
</div>

<?php endif; ?>

</div>
</div>
