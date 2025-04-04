<div class="row">
	<div class="col-md-12">

		<h1>Productos</h1>
<div class="">
	<a href="index.php?view=newproduct" class="btn btn-secondary">Agregar Producto</a>
<div class="btn-group pull-right">
  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href="report/products-word.php">Word 2007 (.docx)</a></li>
  </ul>
</div>
</div>
<br>

<div class="card">
	<div class="card-header">
		PRODUCTOS
	</div>
		<div class="card-body">

<?php
$page = 1;
if(isset($_GET["page"])){
	$page=$_GET["page"];
}
$limit=10;
if(isset($_GET["limit"]) && $_GET["limit"]!="" && $_GET["limit"]!=$limit){
	$limit=$_GET["limit"];
}

$products = ProductData::getAll();
if(count($products)>0){

if($page==1){
$curr_products = ProductData::getAllByPage($products[0]->id,$limit);
}else{
$curr_products = ProductData::getAllByPage($products[($page-1)*$limit]->id,$limit);

}
$npaginas = floor(count($products)/$limit);
 $spaginas = count($products)%$limit;

if($spaginas>0){ $npaginas++;}

	?>

	<h3>Pagina <?php echo $page." de ".$npaginas; ?></h3>
<div class="btn-group pull-right">
<?php
$px=$page-1;
if($px>0):
?>
<a class="btn btn-sm btn-secondary" href="<?php echo "index.php?view=products&limit=$limit&page=".($px); ?>"><i class="glyphicon glyphicon-chevron-left"></i> Atras </a>
<?php endif; ?>

<?php 
$px=$page+1;
if($px<=$npaginas):
?>
<a class="btn btn-sm btn-secondary" href="<?php echo "index.php?view=products&limit=$limit&page=".($px); ?>">Adelante <i class="glyphicon glyphicon-chevron-right"></i></a>
<?php endif; ?>
</div>
<div class="clearfix"></div>
<br><table class="table table-bordered table-hover">
	<thead>
		<th>Codigo</th>
		<th>Nombre</th>
		<th>Precio de Entrada</th>
		<th>Precio de Salida</th>
		<th>Unidad</th>
		<th>Presentacion</th>
		<th>Disponible</th>
		<th>Minima en Inventario</th>
		<th></th>
	</thead>
	<?php 
	foreach($curr_products as $product):
		$q=OperationData::getQYesF($product->id);
	?>
	<tr class="<?php if($q<=$product->inventary_min/2){ echo "danger";}else if($q<=$product->inventary_min){ echo "warning";}?>">
		<td><?php echo $product->id; ?></td>
		<td><?php echo $product->name; ?></td>
		<td><?php echo $product->price_in; ?></td>
		<td><?php echo $product->price_out; ?></td>
		<td><?php echo $product->unit; ?></td>
		<td><?php echo $product->presentation; ?></td>
		<td>
			<?php 
			$available = OperationData::getQYesF($product->id);
			$min_q = $product->inventary_min;
			// Calcular qué tan cerca está del mínimo (100% = en el mínimo, 0% = muy por encima)
			$percentage = ($min_q / $available) * 100;
			
			// Determinar el color según el porcentaje
			$color = '#28a745'; // Verde por defecto
			if($percentage >= 80) {
				$color = '#dc3545'; // Rojo si está muy cerca del mínimo (80% o más)
			} else if($percentage >= 60) {
				$color = '#fd7e14'; // Naranja si está cerca del mínimo (60-80%)
			} else if($percentage >= 40) {
				$color = '#ffc107'; // Amarillo si está moderadamente cerca (40-60%)
			}
			
			// Aplicar el estilo con el color calculado
			echo "<span style='background-color: $color; color: white; padding: 5px 10px; border-radius: 5px;'>$available</span>";
			?>
		</td>
		<td><?php echo $product->inventary_min; ?></td>
		<td style="width:93px;">
		<a href="index.php?view=editproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
		<a href="index.php?view=delproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
<div class="btn-group pull-right">
<?php

for($i=0;$i<$npaginas;$i++){
	echo "<a href='index.php?view=products&limit=$limit&page=".($i+1)."' class='btn btn-secondary btn-sm'>".($i+1)."</a> ";
}
?>
</div>
<form class="form-inline">
	<label for="limit">Limite</label>
	<input type="hidden" name="view" value="products">
	<input type="number" value=<?php echo $limit?> name="limit" style="width:60px;" class="form-control">
</form>

<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay productos</h2>
		<p>No se han agregado productos a la base de datos, puedes agregar uno dando click en el boton <b>"Agregar Producto"</b>.</p>
	</div>
	<?php
}

?>

		</div>
</div>

<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>