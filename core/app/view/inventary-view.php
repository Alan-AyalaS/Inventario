<div class="row">
	<div class="col-md-12">
<!-- Single button -->

		<h1><i class="glyphicon glyphicon-stats"></i> Inventario de Productos</h1>
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
		<?php if(isset($_COOKIE["prddel"])):?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert" id="deleteAlert">
				<strong>¡Eliminado!</strong> El producto "<?php echo $_COOKIE["prddel"]; ?>" ha sido eliminado correctamente.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeDeleteAlert()"></button>
			</div>
			<script>
				function closeDeleteAlert() {
					document.getElementById('deleteAlert').style.transition = 'opacity 0.5s';
					document.getElementById('deleteAlert').style.opacity = '0';
					setTimeout(function() {
						document.getElementById('deleteAlert').style.display = 'none';
					}, 500);
				}
				
				// Cierre automático después de 5 segundos
				setTimeout(closeDeleteAlert, 5000);
				
				// Eliminar la cookie inmediatamente para evitar que la alerta aparezca nuevamente
				document.cookie = "prddel=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/";
			</script>
			<?php 
			// Eliminar la cookie desde PHP también para mayor seguridad
			setcookie("prddel", "", time()-3600, "/");
			?>
		<?php endif; ?>
<div class="">
	<a href="index.php?view=newproduct" class="btn btn-secondary">Agregar Producto</a>
<div class="btn-group pull-right">
  <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important;">
    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end" style="background-color: #28a745; border: none;">
    <li><a class="dropdown-item text-white" href="index.php?view=download-inventory-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
    <li><a class="dropdown-item text-white" href="index.php?view=download-inventory-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
  </ul>
</div>
		<div class="clearfix"></div>
		<br>

<!-- Filtros -->
<div class="row">
    <div class="col-md-12">
        <form method="get" action="" class="form-inline d-flex align-items-end">
            <input type="hidden" name="view" value="inventary">
            <div class="form-group me-2">
                <label for="category_id" class="me-2">Categoría:</label>
                <?php
                // Primero, preparamos los datos
                $categories = CategoryData::getAll();
                $options = [];
                $options[] = ['value' => '', 'text' => 'Todas las categorías', 'color' => '#6c757d', 'selected' => false];
                
                foreach($categories as $category) {
                    $selected = (isset($_GET["category_id"]) && $_GET["category_id"] == $category->id);
                    $options[] = [
                        'value' => $category->id,
                        'text' => htmlspecialchars($category->name),
                        'color' => '#28a745', // Color por defecto, será actualizado por JavaScript
                        'selected' => $selected
                    ];
                }
                
                // Luego, generamos el HTML del select
                echo '<select class="form-control" id="category_id" name="category_id">';
                foreach($options as $option) {
                    printf(
                        '<option value="%s" data-color="%s"%s>%s</option>',
                        $option['value'],
                        $option['color'],
                        $option['selected'] ? ' selected' : '',
                        $option['text']
                    );
                }
                echo '</select>';
                ?>
            </div>
            <div class="form-group me-2">
                <label for="date_filter" class="me-2">Fecha:</label>
                <select name="date_filter" id="date_filter" class="form-control">
                    <option value="">Todas las fechas</option>
                    <option value="this_week" <?php if(isset($_GET["date_filter"]) && $_GET["date_filter"]=="this_week"){ echo "selected"; } ?>>Esta semana</option>
                    <option value="this_month" <?php if(isset($_GET["date_filter"]) && $_GET["date_filter"]=="this_month"){ echo "selected"; } ?>>Este mes</option>
                    <option value="last_3_months" <?php if(isset($_GET["date_filter"]) && $_GET["date_filter"]=="last_3_months"){ echo "selected"; } ?>>Últimos 3 meses</option>
                    <option value="last_6_months" <?php if(isset($_GET["date_filter"]) && $_GET["date_filter"]=="last_6_months"){ echo "selected"; } ?>>Últimos 6 meses</option>
                    <option value="this_year" <?php if(isset($_GET["date_filter"]) && $_GET["date_filter"]=="this_year"){ echo "selected"; } ?>>Este año</option>
                </select>
            </div>
            <div class="form-group me-2">
                <label for="limit" class="me-2">Mostrar:</label>
                <input type="number" name="limit" id="limit" class="form-control" value="<?php echo isset($_GET["limit"]) ? $_GET["limit"] : '10'; ?>" min="1" style="width: 80px;">
            </div>
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <?php if(isset($_GET["category_id"]) || isset($_GET["date_filter"])): ?>
            <a href="index.php?view=inventary" class="btn btn-secondary">Limpiar filtros</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Botón para eliminar seleccionados -->
<div class="row mt-3">
    <div class="col-md-12">
        <button type="button" class="btn btn-danger" id="deleteSelected" disabled>
            <i class="fas fa-trash"></i> Eliminar seleccionados
        </button>
    </div>
</div>

<!-- Modal para ajustar inventario -->
<div class="modal fade" id="adjustInventoryModal" tabindex="-1" aria-labelledby="adjustInventoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="adjustInventoryModalLabel">Ajustar Inventario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="adjustInventoryForm" onsubmit="submitAdjustment(); return false;">
          <input type="hidden" id="productId" name="product_id">
          <input type="hidden" id="operationType" name="operation_type">
          <div class="mb-3">
            <label for="quantity" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="0.01" step="0.01" required>
          </div>
          <button type="submit" style="display: none;">Submit</button>
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

<!-- Modal de confirmación para eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">¿Está seguro que desea eliminar el producto <strong id="productNameToDelete"></strong>?</p>
        <p class="text-danger mt-3 mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Esta acción no se puede deshacer. Se eliminarán también todas las operaciones de inventario asociadas a este producto.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Eliminar producto</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal para eliminar productos seleccionados -->
<div class="modal fade" id="deleteSelectedModal" tabindex="-1" role="dialog" aria-labelledby="deleteSelectedModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSelectedModalLabel">Eliminar productos seleccionados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar los productos seleccionados? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSelected">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
	<div class="card-header">INVENTARIO
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

// Imprimir las variables de fecha y categoría para debug
error_log("Filtro de fecha: " . (isset($_GET['date_filter']) ? $_GET['date_filter'] : 'no establecido'));
error_log("Filtro de categoría: " . (isset($_GET['category_id']) ? $_GET['category_id'] : 'no establecido'));

// Verificar si tenemos ambos filtros (categoría y fecha)
if(isset($_GET['date_filter']) && $_GET['date_filter'] != "" && isset($_GET['category_id']) && $_GET['category_id'] != "") {
    $category_id = intval($_GET['category_id']);
    
    switch($_GET['date_filter']) {
        case 'this_week':
            $products = ProductData::getThisWeekByCategory($category_id);
            break;
        case 'this_month':
            $products = ProductData::getThisMonthByCategory($category_id);
            break;
        case 'last_3_months':
            $products = ProductData::getLast3MonthsByCategory($category_id);
            break;
        case 'last_6_months':
            $products = ProductData::getLast6MonthsByCategory($category_id);
            break;
        case 'this_year':
            $products = ProductData::getThisYearByCategory($category_id);
            break;
        default:
            $products = ProductData::getAllByCategoryId($category_id);
    }
    // Depuración
    error_log("Productos recuperados con ambos filtros: " . count($products));
}
// Si solo tenemos filtro de fecha
else if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
    // Depuración: mostrar todos los productos sin filtrar
    $all_products = ProductData::getAll();
    $all_categories = array();
    foreach($all_products as $p) {
        if(isset($p->category_id) && $p->category_id != null) {
            if(!isset($all_categories[$p->category_id])) {
                $all_categories[$p->category_id] = 1;
            } else {
                $all_categories[$p->category_id]++;
            }
        } else {
            if(!isset($all_categories['null'])) {
                $all_categories['null'] = 1;
            } else {
                $all_categories['null']++;
            }
        }
    }
    error_log("TODOS LOS PRODUCTOS: " . count($all_products));
    error_log("TODAS LAS CATEGORÍAS: " . json_encode($all_categories));
    
    switch($_GET['date_filter']) {
        case 'this_week':
            $products = ProductData::getThisWeek();
            break;
        case 'this_month':
            $products = ProductData::getThisMonth();
            break;
        case 'last_3_months':
            $products = ProductData::getLast3Months();
            break;
        case 'last_6_months':
            $products = ProductData::getLast6Months();
            break;
        case 'this_year':
            $products = ProductData::getThisYear();
            break;
        default:
            $products = ProductData::getAll();
    }
    // Depuración
    error_log("Productos recuperados solo con filtro de fecha: " . count($products));
}
// Si solo tenemos filtro de categoría
else if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
    $category_id = intval($_GET['category_id']);
    $products = ProductData::getAllByCategoryId($category_id);
    // Depuración
    error_log("Productos recuperados solo con filtro de categoría: " . count($products));
}
// Sin filtros
else {
    $products = ProductData::getAll();
    // Depuración
    error_log("Productos recuperados sin filtros: " . count($products));
}

if(count($products)>0){

// Calcular el número total de páginas
$total_records = count($products);
$npaginas = ceil($total_records / $limit);

// Asegurarse de que la página actual no exceda el número total de páginas
if ($page > $npaginas) {
    $page = $npaginas;
}

// Obtener los productos para la página actual
if ($page == 1) {
    $curr_products = array_slice($products, 0, $limit);
} else {
    $start_index = ($page - 1) * $limit;
    $curr_products = array_slice($products, $start_index, $limit);
}

	?>

	<h3>Pagina <?php echo $page." de ".$npaginas; ?></h3>
<div class="btn-group pull-right">
<?php
$px=$page-1;
if($px>0):
    // Construir la URL con los parámetros de filtro actuales
    $url = "index.php?view=inventary&limit=$limit&page=".($px);
    if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
        $url .= "&category_id=".$_GET['category_id'];
    }
    if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
        $url .= "&date_filter=".$_GET['date_filter'];
    }
?>
<a class="btn btn-sm btn-default" href="<?php echo $url; ?>"><i class="glyphicon glyphicon-chevron-left"></i> Atras </a>
<?php endif; ?>

<?php 
$px=$page+1;
if($px<=$npaginas):
    // Construir la URL con los parámetros de filtro actuales
    $url = "index.php?view=inventary&limit=$limit&page=".($px);
    if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
        $url .= "&category_id=".$_GET['category_id'];
    }
    if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
        $url .= "&date_filter=".$_GET['date_filter'];
    }
?>
<a class="btn btn-sm btn-default" href="<?php echo $url; ?>">Adelante <i class="glyphicon glyphicon-chevron-right"></i></a>
<?php endif; ?>
</div>
<div class="clearfix"></div>
<br><table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th style="width: 50px;">
				<input type="checkbox" id="selectAll" class="form-check-input">
			</th>
			<th>Codigo</th>
			<th>Nombre</th>
			<th>Categoría</th>
			<th>Precio de Entrada</th>
			<th>Precio de Salida</th>
			<th>Unidad</th>
			<th>Disponible</th>
			<th>Minima en Inventario</th>
			<th style="width: 130px;">Acciones</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($curr_products as $product):
		$q=OperationData::getQYesF($product->id);
		?>
		<tr class="<?php if($q<=$product->inventary_min/2){ echo "danger";}else if($q<=$product->inventary_min){ echo "warning";}?>">
			<td>
				<input type="checkbox" class="form-check-input product-checkbox" value="<?php echo $product->id; ?>">
			</td>
			<td><?php echo $product->id; ?></td>
			<td><a href="index.php?view=producthistory&id=<?php echo $product->id; ?>" style="text-decoration: none; color: inherit;"><?php echo $product->name; ?></a></td>
			<td>
				<?php 
				$categoryColor = '#6c757d'; // Color por defecto
				$categoryName = 'Sin categoría';
				
				if (!empty($product->category_id)) {
					$categoryColor = isset($_COOKIE['category_color_' . $product->category_id]) 
						? $_COOKIE['category_color_' . $product->category_id] 
						: '#6c757d';
					
					// Obtener el nombre de la categoría
					$category = CategoryData::getById($product->category_id);
					if ($category) {
						$categoryName = $category->name;
					}
				}
				?>
				<span class="badge" style="background-color: <?php echo $categoryColor; ?>; color: white; padding: 5px 10px; border-radius: 4px;">
					<?php echo htmlspecialchars($categoryName); ?>
				</span>
			</td>
			<td><?php echo $product->price_in; ?></td>
			<td><?php echo $product->price_out; ?></td>
			<td><?php echo $product->unit; ?></td>
			<td>
				<?php 
				$available = OperationData::getQYesF($product->id);
				$min_q = $product->inventary_min;
				// Calcular qué tan cerca está del mínimo (100% = en el mínimo, 0% = muy por encima)
				$color = '#28a745'; // Verde por defecto
				
				if ($available <= 0) {
					// Si no hay inventario disponible, mostrar en rojo
					$color = '#dc3545';
				} else {
					$percentage = ($min_q / $available) * 100;
					
					// Determinar el color según el porcentaje
					if($percentage >= 80) {
						$color = '#dc3545'; // Rojo si está muy cerca del mínimo (80% o más)
					} else if($percentage >= 60) {
						$color = '#fd7e14'; // Naranja si está cerca del mínimo (60-80%)
					} else if($percentage >= 40) {
						$color = '#ffc107'; // Amarillo si está moderadamente cerca (40-60%)
					}
				}
				
				// Aplicar el estilo con el color calculado
				echo "<span style='background-color: $color; color: white; padding: 5px 10px; border-radius: 5px;'>$available</span>";
				?>
			</td>
			<td><?php echo $product->inventary_min; ?></td>
			<td>
				<button type="button" class="btn btn-sm btn-success" onclick="showAdjustModal(<?php echo $product->id; ?>, 'add')">
					<i class="bi bi-plus-circle"></i>
				</button>
				<button type="button" class="btn btn-sm btn-danger" onclick="showAdjustModal(<?php echo $product->id; ?>, 'subtract')">
					<i class="bi bi-dash-circle"></i>
				</button>
				<a href="index.php?view=editproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning">
					<i class="bi bi-pencil"></i>
				</a>
				<button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(<?php echo $product->id; ?>, '<?php echo addslashes($product->name); ?>')">
					<i class="bi bi-trash"></i>
				</button>
			</td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<div class="btn-group pull-right">
<?php
for($i=0;$i<$npaginas;$i++){
    // Construir la URL con los parámetros de filtro actuales
    $url = "index.php?view=inventary&limit=$limit&page=".($i+1);
    if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
        $url .= "&category_id=".$_GET['category_id'];
    }
    if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
        $url .= "&date_filter=".$_GET['date_filter'];
    }
    
    $active_class = ($page == ($i+1)) ? 'btn-primary' : 'btn-default';
    echo "<a href='$url' class='btn $active_class btn-sm'>".($i+1)."</a> ";
}
?>
</div>

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
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Variable global para almacenar el mensaje de la operación
let lastOperationMessage = '';

function showAdjustModal(productId, operationType) {
    document.getElementById('productId').value = productId;
    document.getElementById('operationType').value = operationType;
    document.getElementById('adjustInventoryModalLabel').textContent = 
        operationType === 'add' ? 'Agregar al Inventario' : 'Restar del Inventario';
    
    // Resetear el valor de cantidad
    document.getElementById('quantity').value = '';
    
    var modal = new bootstrap.Modal(document.getElementById('adjustInventoryModal'));
    modal.show();
    
    // Enfocar el campo de cantidad después de mostrar el modal
    setTimeout(() => {
        document.getElementById('quantity').focus();
    }, 500);
}

function submitAdjustment() {
    const productId = document.getElementById('productId').value;
    const operationType = document.getElementById('operationType').value;
    const quantity = document.getElementById('quantity').value;
    
    if (!quantity || isNaN(quantity) || quantity <= 0) {
        alert('Por favor, ingrese una cantidad válida');
        return;
    }
    
    console.log('Enviando ajuste:', {
        product_id: productId,
        operation_type: operationType,
        quantity: quantity
    });
    
    // Enviar datos al servidor
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?action=adjust_inventory', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        console.log('Respuesta del servidor:', xhr.responseText);
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                console.log('Respuesta procesada:', response);
                
                if (response.success) {
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('adjustInventoryModal')).hide();
                    
                    // Guardar el mensaje para mostrarlo después de recargar
                    lastOperationMessage = response.message || 'Inventario actualizado correctamente';
                    
                    // Almacenar el mensaje en sessionStorage para recuperarlo después de la recarga
                    sessionStorage.setItem('inventoryMessage', lastOperationMessage);
                    
                    // Recargar la página para mostrar los cambios
                    location.reload();
                    
                } else {
                    alert('Error: ' + (response.message || 'Error al ajustar el inventario'));
                    console.error('Detalles del error:', response.debug || response);
                }
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                console.error('Texto recibido:', xhr.responseText);
                alert('Error en la respuesta del servidor. Revise la consola para más detalles.');
            }
        } else {
            alert('Error de comunicación con el servidor');
            console.error('Error de comunicación:', xhr.status, xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Error de red al realizar la solicitud');
        alert('Error de red al realizar la solicitud');
    };
    xhr.send(`product_id=${productId}&operation_type=${operationType}&quantity=${quantity}`);
}

// Verificar si hay un mensaje pendiente al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const message = sessionStorage.getItem('inventoryMessage');
    if (message) {
        // Limpiar el mensaje de sessionStorage
        sessionStorage.removeItem('inventoryMessage');
        
        // Mostrar la notificación
        document.getElementById('alertMessage').textContent = message;
        const toast = new bootstrap.Toast(document.getElementById('inventoryAlert'));
        toast.show();
    }
    
    // Agregar evento para manejar la tecla Enter en el campo de cantidad
    document.getElementById('quantity').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitAdjustment();
        }
    });
});

function showDeleteModal(productId, productName) {
    document.getElementById('productNameToDelete').textContent = productName;
    
    // Construir la URL con los parámetros de filtro actuales
    let deleteUrl = 'index.php?view=delproduct&id=' + productId;
    
    // Agregar los parámetros de filtro si existen
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('category_id')) {
        deleteUrl += '&category_id=' + urlParams.get('category_id');
    }
    if (urlParams.has('date_filter')) {
        deleteUrl += '&date_filter=' + urlParams.get('date_filter');
    }
    if (urlParams.has('limit')) {
        deleteUrl += '&limit=' + urlParams.get('limit');
    }
    if (urlParams.has('page')) {
        deleteUrl += '&page=' + urlParams.get('page');
    }
    
    document.getElementById('confirmDeleteBtn').href = deleteUrl;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Funcionalidad para selección múltiple
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    const confirmDeleteSelectedBtn = document.getElementById('confirmDeleteSelected');
    let lastChecked = null;

    // Seleccionar/deseleccionar todos
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
    });

    // Actualizar botón de eliminar seleccionados
    function updateDeleteButton() {
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        deleteSelectedBtn.disabled = selectedCount === 0;
    }

    // Manejar selección con Shift
    function handleCheckboxClick(e) {
        if (e.shiftKey && lastChecked) {
            const checkboxesArray = Array.from(checkboxes);
            const start = checkboxesArray.indexOf(lastChecked);
            const end = checkboxesArray.indexOf(this);
            
            const startIndex = Math.min(start, end);
            const endIndex = Math.max(start, end);
            
            const isChecked = lastChecked.checked;
            
            for (let i = startIndex; i <= endIndex; i++) {
                checkboxesArray[i].checked = isChecked;
            }
        }
        
        lastChecked = this;
        updateDeleteButton();
    }

    // Actualizar botón cuando se selecciona/deselecciona un producto
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('click', handleCheckboxClick);
    });

    // Mostrar modal de confirmación
    deleteSelectedBtn.addEventListener('click', function() {
        $('#deleteSelectedModal').modal('show');
    });

    // Eliminar productos seleccionados
    confirmDeleteSelectedBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
            .map(checkbox => checkbox.value);

        console.log('Enviando solicitud para eliminar productos:', selectedIds);

        // Enviar solicitud para eliminar
        fetch('index.php?view=deleteproducts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ product_ids: selectedIds })
        })
        .then(response => {
            console.log('Estado de la respuesta:', response.status);
            console.log('Tipo de contenido:', response.headers.get('content-type'));
            
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            
            return response.text().then(text => {
                console.log('Texto de respuesta:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    console.error('Texto recibido:', text);
                    throw new Error('La respuesta no es un JSON válido. Texto recibido: ' + text.substring(0, 100));
                }
            });
        })
        .then(data => {
            console.log('Datos procesados:', data);
            if (data.success) {
                // Guardar el mensaje en sessionStorage para mostrarlo después de recargar
                sessionStorage.setItem('deleteSuccessMessage', data.message);
                
                // Recargar la página
                window.location.reload();
            } else {
                alert('Error al eliminar los productos: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert('Error al eliminar los productos. Detalles: ' + error.message);
        });

        $('#deleteSelectedModal').modal('hide');
    });

    // Mostrar alerta de éxito después de recargar la página
    const successMessage = sessionStorage.getItem('deleteSuccessMessage');
    if (successMessage) {
        // Crear y mostrar la alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            <strong>¡Éxito!</strong> ${successMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insertar la alerta al principio del card-body
        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.insertBefore(alertDiv, cardBody.firstChild);
        }
        
        // Eliminar el mensaje del sessionStorage
        sessionStorage.removeItem('deleteSuccessMessage');
        
        // Cerrar automáticamente la alerta después de 5 segundos
        setTimeout(() => {
            const closeButton = alertDiv.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    }
});

// Función para actualizar el color del select según la categoría seleccionada
const updateCategoryColor = (() => {
    let timeoutId;
    
    return (select) => {
        if (!select || !(select instanceof HTMLSelectElement)) return;
        
        // Cancelar cualquier actualización pendiente
        if (timeoutId) clearTimeout(timeoutId);
        
        timeoutId = setTimeout(() => {
            const selectedOption = select.options[select.selectedIndex];
            const categoryId = selectedOption.value;
            
            // Obtener el color de localStorage o usar el color por defecto
            const color = categoryId ? 
                localStorage.getItem('category_color_' + categoryId) || '#28a745' : 
                '#6c757d';
            
            // Actualizar solo el borde del select
            select.style.setProperty('border-color', color, 'important');
            
            // Actualizar los colores de todas las opciones
            Array.from(select.options).forEach(option => {
                const optionCategoryId = option.value;
                if (optionCategoryId) {
                    const optionColor = localStorage.getItem('category_color_' + optionCategoryId) || '#28a745';
                    option.style.color = optionColor;
                    option.style.backgroundColor = 'white';
                } else {
                    // Opción "Todas las categorías"
                    option.style.color = '#6c757d';
                    option.style.backgroundColor = 'white';
                }
            });
        }, 0);
    };
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('category_id');
    if (!categorySelect) return;
    
    // Aplicar el color inicial
    updateCategoryColor(categorySelect);
    
    // Agregar el evento change
    categorySelect.addEventListener('change', function() {
        updateCategoryColor(this);
    });
    
    // Agregar evento para mantener los colores al abrir el select
    categorySelect.addEventListener('click', function() {
        updateCategoryColor(this);
    });
    
    // Agregar evento para mantener los colores al enfocar el select
    categorySelect.addEventListener('focus', function() {
        updateCategoryColor(this);
    });
    
    // Agregar evento para mantener los colores cuando el select está abierto
    categorySelect.addEventListener('mousedown', function() {
        updateCategoryColor(this);
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

<style>
/* Estilos para el select de categorías */
#category_id {
    transition: all 0.3s ease !important;
    border-width: 2px !important;
    border-style: solid !important;
    outline: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}

#category_id:focus {
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25) !important;
}

/* Estilo para el placeholder */
#category_id option[value=""] {
    color: #6c757d !important;
}

/* Estilo para las opciones */
#category_id option {
    background-color: white !important;
}

/* Estilo para el select cuando está abierto */
#category_id:focus option {
    background-color: white !important;
}

/* Estilo para las opciones cuando se pasa el mouse */
#category_id option:hover {
    background-color: #f8f9fa !important;
}
</style>