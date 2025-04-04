<?php
$product = ProductData::getById($_GET["id"]);
$operations = OperationData::getAllByProductId($product->id);

// Calcular totales
$total_entradas = 0;
$total_salidas = 0;
foreach($operations as $operation) {
    if($operation->operation_type_id == 1) { // Entrada
        $total_entradas += $operation->q;
    } else { // Salida
        $total_salidas += $operation->q;
    }
}
$disponible = $total_entradas - $total_salidas;
?>
<div class="row">
	<div class="col-md-12">
		<h1>Historial del Producto</h1>
		<br>
		<div class="card">
			<div class="card-header">
				<h3>Información del Producto</h3>
			</div>
			<div class="card-body">
				<table class="table table-bordered">
					<tr>
						<td style="width:150px;">Código</td>
						<td><?php echo $product->id; ?></td>
					</tr>
					<tr>
						<td>Nombre</td>
						<td><?php echo $product->name; ?></td>
					</tr>
					<tr>
						<td>Precio de Entrada</td>
						<td><?php echo $product->price_in; ?></td>
					</tr>
					<tr>
						<td>Precio de Salida</td>
						<td><?php echo $product->price_out; ?></td>
					</tr>
					<tr>
						<td>Unidad</td>
						<td><?php echo $product->unit; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-4">
				<div class="card bg-success text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h3 class="mb-0"><?php echo $total_entradas; ?></h3>
								<p class="mb-0">Total Entradas</p>
							</div>
							<div class="icon-circle">
								<i class="bi bi-box-arrow-in-down" style="font-size: 2rem;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card bg-danger text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h3 class="mb-0"><?php echo $total_salidas; ?></h3>
								<p class="mb-0">Total Salidas</p>
							</div>
							<div class="icon-circle">
								<i class="bi bi-box-arrow-up" style="font-size: 2rem;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card bg-primary text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h3 class="mb-0"><?php echo $disponible; ?></h3>
								<p class="mb-0">Disponible</p>
							</div>
							<div class="icon-circle">
								<i class="bi bi-box" style="font-size: 2rem;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="card">
			<div class="card-header">
				<h3>Historial de Operaciones</h3>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-hover">
					<thead>
						<th>Fecha</th>
						<th>Tipo</th>
						<th>Cantidad</th>
						<th>Usuario</th>
					</thead>
					<?php foreach($operations as $operation):?>
					<tr>
						<td><?php echo $operation->created_at; ?></td>
						<td><?php echo $operation->operation_type_id==1?"Entrada":"Salida"; ?></td>
						<td><?php echo $operation->q; ?></td>
						<td>Administrador</td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<br>
		<a href="index.php?view=products" class="btn btn-default">Volver</a>
	</div>
</div>

<style>
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style> 