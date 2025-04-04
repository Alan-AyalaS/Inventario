<div class="row">
	<div class="col-md-12">

		<h1>Productos</h1>
		<?php if(isset($_COOKIE["prdupd"])):?>
			<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
				<strong>¡Éxito!</strong> El producto se ha actualizado correctamente.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeAlert()"></button>
			</div>
			<script>
				function closeAlert() {
					document.getElementById('successAlert').style.transition = 'opacity 0.5s';
					document.getElementById('successAlert').style.opacity = '0';
					setTimeout(function() {
						document.getElementById('successAlert').style.display = 'none';
					}, 500);
				}
				
				// Cierre automático después de 5 segundos
				setTimeout(closeAlert, 5000);
			</script>
			<?php setcookie("prdupd","",time()-18600); endif; ?>
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

<!-- Modal para ajustar inventario -->
<div class="modal fade" id="adjustInventoryModal" tabindex="-1" aria-labelledby="adjustInventoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adjustInventoryModalLabel">Ajustar Inventario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="adjustInventoryForm" onsubmit="return false;">
          <input type="hidden" id="productId" name="product_id">
          <input type="hidden" id="operationType" name="operation_type">
          <div class="mb-3">
            <label for="quantity" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="0.01" step="0.01" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="submitAdjustment()">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Alerta dinámica -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="inventoryAlert" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-check-circle-fill me-2"></i>
        <span id="alertMessage"></span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<div class="card">
	<div class="card-header">
		PRODUCTOS
	</div>
		<div class="card-body">

<?php
$page=1;
if(isset($_GET["page"]) && $_GET["page"]!="" && $_GET["page"]!=$page){
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
		<td style="width:130px;">
			<button type="button" class="btn btn-sm btn-success" onclick="showAdjustModal(<?php echo $product->id; ?>, 'add')">
				<i class="bi bi-plus-circle"></i>
			</button>
			<button type="button" class="btn btn-sm btn-danger" onclick="showAdjustModal(<?php echo $product->id; ?>, 'subtract')">
				<i class="bi bi-dash-circle"></i>
			</button>
			<a href="index.php?view=editproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning">
				<i class="bi bi-pencil"></i>
			</a>
			<a href="index.php?view=delproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-danger">
				<i class="bi bi-trash"></i>
			</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showAdjustModal(productId, operationType) {
    document.getElementById('productId').value = productId;
    document.getElementById('operationType').value = operationType;
    document.getElementById('adjustInventoryModalLabel').textContent = 
        operationType === 'add' ? 'Agregar al Inventario' : 'Restar del Inventario';
    var modal = new bootstrap.Modal(document.getElementById('adjustInventoryModal'));
    modal.show();
}

function showInventoryAlert(message) {
    const toast = document.getElementById('inventoryAlert');
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.textContent = message;
    
    // Cambiar el color según el tipo de operación
    if (message.includes('agregados')) {
        toast.className = 'toast align-items-center text-white bg-success border-0';
    } else {
        toast.className = 'toast align-items-center text-white bg-danger border-0';
    }
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function submitAdjustment() {
    const form = document.getElementById('adjustInventoryForm');
    const formData = new FormData(form);
    const quantity = formData.get('quantity');
    const operationType = formData.get('operation_type');
    
    fetch('index.php?view=adjustinventory', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if(data.success) {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('adjustInventoryModal'));
                modal.hide();
                
                // Guardar el mensaje en una cookie
                const operationText = operationType === 'add' ? 'agregados' : 'eliminados';
                const message = `Se han ${operationText} ${quantity} unidades correctamente`;
                document.cookie = `inventoryAlert=${encodeURIComponent(message)}; path=/`;
                
                // Recargar la página inmediatamente
                location.reload();
            } else {
                showInventoryAlert('Error: ' + data.message);
            }
        } catch (e) {
            console.error('Error al parsear JSON:', text);
            showInventoryAlert('Error al procesar la respuesta del servidor');
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        showInventoryAlert('Ocurrió un error al procesar la solicitud');
    });
}

// Función para obtener el valor de una cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
}

// Mostrar alerta si existe la cookie
document.addEventListener('DOMContentLoaded', function() {
    const message = getCookie('inventoryAlert');
    if (message) {
        // Mostrar la alerta
        showInventoryAlert(message);
        
        // Eliminar la cookie
        document.cookie = 'inventoryAlert=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
});

// Agregar evento para manejar la tecla Enter
document.getElementById('quantity').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        submitAdjustment();
    }
});
</script>