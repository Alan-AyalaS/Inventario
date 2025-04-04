<div class="row">
	<div class="col-md-12">
		<h1><i class='glyphicon glyphicon-shopping-cart'></i> Lista de Ventas</h1>
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important;">
				<i class="fa fa-download"></i> Descargar <span class="caret"></span>
			</button>
			<ul class="dropdown-menu dropdown-menu-end" style="background-color: #28a745; border: none;">
				<li><a class="dropdown-item text-white" href="index.php?view=download-sells" style="background-color: transparent !important; transition: color 0.3s ease;">Word 2007 (.docx)</a></li>
				<li><a class="dropdown-item text-white" href="index.php?view=download-sells-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
				<li><a class="dropdown-item text-white" href="index.php?view=download-sells-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
			</ul>
		</div>
		<div class="clearfix"></div>


<?php

$products = SellData::getSells();

if(count($products)>0){

	?>

<div class="card">
	<div class="card-header">
		VENTAS
	</div>
		<div class="card-body">

<table class="table table-bordered table-hover	">
	<thead>
		<th></th>
		<th>Producto</th>
		<th>Total</th>
		<th>Fecha</th>
		<th></th>
	</thead>
	<?php foreach($products as $sell):?>

	<tr>
		<td style="width:30px;">
		<a href="index.php?view=onesell&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-link"><i class="bi bi-eye"></i></a></td>

		<td>

<?php
$operations = OperationData::getAllProductsBySellId($sell->id);
echo count($operations);
?>
		<td>

<?php
$total= $sell->total-$sell->discount;
	/*foreach($operations as $operation){
		$product  = $operation->getProduct();
		$total += $operation->q*$product->price_out;
	}*/
		echo "<b>$ ".number_format($total)."</b>";

?>			

		</td>
		<td><?php echo $sell->created_at; ?></td>
		<td style="width:30px;"><a href="index.php?view=delsell&id=<?php echo $sell->id; ?>" class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></a></td>
	</tr>

<?php endforeach; ?>

</table>

<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay ventas</h2>
		<p>No se ha realizado ninguna venta.</p>
	</div>
	<?php
}

?>
		</div>
</div>


<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>

<style>
.dropdown-item:hover {
  color:rgb(24, 56, 31) !important;
  background-color: white !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>