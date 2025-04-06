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
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="index.php?view=newproduct" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Agregar Producto
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important;">
                    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="background-color: #28a745; border: none;">
                    <li><a class="dropdown-item text-white" href="index.php?view=download-inventory-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item text-white" href="index.php?view=download-inventory-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>
        <br>

<!-- Filtros -->
<div class="row">
    <div class="col-md-12">
        <form method="get" action="index.php" class="form-inline d-flex align-items-end">
            <input type="hidden" name="view" value="inventary">
            <div class="form-group me-2">
                <label for="category_id" class="me-2">Categoría:</label>
                <div class="custom-select-wrapper">
                    <div class="custom-select" id="customCategorySelect">
                        <div class="custom-select__trigger">
                            <?php
                            $categories = CategoryData::getAll();
                            $selectedCategoryName = 'Todas las categorías';
                            if (isset($_GET["category_id"]) && $_GET["category_id"] != "") {
                                foreach ($categories as $category) {
                                    if ($category->id == $_GET["category_id"]) {
                                        $selectedCategoryName = htmlspecialchars($category->name);
                                        break;
                                    }
                                }
                            }
                            ?>
                            <span><?php echo $selectedCategoryName; ?></span>
                            <div class="arrow"></div>
                        </div>
                        <div class="custom-options">
                            <?php
                            // Primero, preparamos los datos
                            $options = [];
                            $options[] = ['value' => '', 'text' => 'Todas las categorías', 'color' => '#6c757d', 'selected' => !isset($_GET["category_id"]) || $_GET["category_id"] == ""];
                            
                            foreach($categories as $category) {
                                $selected = (isset($_GET["category_id"]) && $_GET["category_id"] == $category->id);
                                $options[] = [
                                    'value' => $category->id,
                                    'text' => htmlspecialchars($category->name),
                                    'color' => '#28a745', // Color por defecto, será actualizado por JavaScript
                                    'selected' => $selected
                                ];
                            }
                            
                            // Generar las opciones
                            foreach($options as $option) {
                                $selectedClass = $option['selected'] ? 'selected' : '';
                                printf(
                                    '<div class="custom-option %s" data-value="%s" data-color="%s">%s</div>',
                                    $selectedClass,
                                    $option['value'],
                                    $option['color'],
                                    $option['text']
                                );
                            }
                            ?>
                        </div>
                    </div>
                    <select id="category_id" name="category_id" style="display: none;">
                        <?php
                        foreach($options as $option) {
                            printf(
                                '<option value="%s"%s>%s</option>',
                                $option['value'],
                                $option['selected'] ? ' selected' : '',
                                $option['text']
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group me-2">
                <label for="availability" class="me-2">Disponibilidad:</label>
                <div class="custom-select-wrapper">
                    <div class="custom-select" id="customAvailabilitySelect">
                        <div class="custom-select__trigger">
                            <?php
                            $availabilityOptions = [
                                ['value' => '', 'text' => 'Todas las cantidades', 'selected' => !isset($_GET["availability"])],
                                ['value' => '0', 'text' => 'Sin stock (0)', 'selected' => isset($_GET["availability"]) && $_GET["availability"] === '0'],
                                ['value' => '1-10', 'text' => 'Stock bajo (1-10)', 'selected' => isset($_GET["availability"]) && $_GET["availability"] === '1-10'],
                                ['value' => '11-50', 'text' => 'Stock medio (11-50)', 'selected' => isset($_GET["availability"]) && $_GET["availability"] === '11-50'],
                                ['value' => '51-100', 'text' => 'Stock alto (51-100)', 'selected' => isset($_GET["availability"]) && $_GET["availability"] === '51-100'],
                                ['value' => '100+', 'text' => 'Stock muy alto (100+)', 'selected' => isset($_GET["availability"]) && $_GET["availability"] === '100+']
                            ];
                            
                            $selectedText = 'Todas las cantidades';
                            if (isset($_GET["availability"])) {
                                foreach ($availabilityOptions as $option) {
                                    if ($option['value'] === $_GET["availability"]) {
                                        $selectedText = $option['text'];
                                        break;
                                    }
                                }
                            }
                            ?>
                            <span><?php echo htmlspecialchars($selectedText); ?></span>
                            <div class="arrow"></div>
                        </div>
                        <div class="custom-options">
                            <?php
                            foreach($availabilityOptions as $option) {
                                $selectedClass = $option['selected'] ? 'selected' : '';
                                printf(
                                    '<div class="custom-option %s" data-value="%s">%s</div>',
                                    $selectedClass,
                                    $option['value'],
                                    $option['text']
                                );
                            }
                            ?>
                        </div>
                    </div>
                    <select id="availability" name="availability" style="display: none;">
                        <?php
                        foreach($availabilityOptions as $option) {
                            printf(
                                '<option value="%s"%s>%s</option>',
                                $option['value'],
                                $option['selected'] ? ' selected' : '',
                                $option['text']
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group me-2">
                <label for="search" class="me-2">Buscar:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : ''; ?>" placeholder="Buscar productos...">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
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
                <input type="number" name="limit" id="limit" class="form-control" value="<?php echo isset($_GET["limit"]) ? $_GET["limit"] : '10'; ?>" min="1" style="width: 80px;" onchange="this.form.submit()">
            </div>
            <button type="submit" class="btn btn-primary" style="display: none;">Aplicar</button>
            <a href="index.php?view=inventary" class="btn btn-secondary" id="clearFiltersBtn" style="display: none;">Limpiar filtros</a>
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

<!-- Modal de confirmación para eliminar todos los productos -->
<div id="deleteAllModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar todos los productos? Esta acción no se puede deshacer.</p>
        <div class="modal-buttons">
            <button onclick="document.getElementById('deleteAllModal').style.display='none'">Cancelar</button>
            <button onclick="confirmDeleteAll()">Confirmar</button>
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

// Obtener productos según los filtros
if(isset($_GET["search"]) && $_GET["search"] != "") {
	$products = ProductData::getLike($_GET["search"]);
} else if(isset($_GET["category_id"]) && $_GET["category_id"] != "") {
	$products = ProductData::getAllByCategoryId($_GET["category_id"]);
} else {
	$products = ProductData::getAll();
}

// Filtrar por disponibilidad si está seleccionado
if(isset($_GET["availability"]) && $_GET["availability"] != "") {
	$filtered_products = [];
	foreach($products as $product) {
		$q = OperationData::getQYesF($product->id);
		switch($_GET["availability"]) {
			case '0':
				if($q == 0) $filtered_products[] = $product;
				break;
			case '1-10':
				if($q >= 1 && $q <= 10) $filtered_products[] = $product;
				break;
			case '11-50':
				if($q >= 11 && $q <= 50) $filtered_products[] = $product;
				break;
			case '51-100':
				if($q >= 51 && $q <= 100) $filtered_products[] = $product;
				break;
			case '100+':
				if($q > 100) $filtered_products[] = $product;
				break;
		}
	}
	$products = $filtered_products;
}

if(count($products)>0){
	// Calcular el número total de páginas
	$total_records = count($products);
	$npaginas = ceil($total_records / $limit);

	// Asegurarse de que la página actual no exceda el número total de páginas
	if ($page > $npaginas) {
		$page = $npaginas;
	}

	// Si hay una categoría seleccionada, mostrar todos los productos
	if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
		$curr_products = $products;
		$npaginas = 1;
		$page = 1;
	} else {
		// Obtener los productos para la página actual
		if ($page == 1) {
			$curr_products = array_slice($products, 0, $limit);
		} else {
			$start_index = ($page - 1) * $limit;
			$curr_products = array_slice($products, $start_index, $limit);
		}
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
				<span class="badge" style="background-color: <?php echo $categoryColor; ?>; color: white; padding: 5px 10px; border-radius: 4px;" data-category-id="<?php echo $product->category_id; ?>">
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
    
    // Enviar datos al servidor
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?view=adjustinventory', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                // Obtener solo la parte JSON de la respuesta
                let jsonResponse = xhr.responseText;
                const htmlIndex = jsonResponse.indexOf('<!DOCTYPE html>');
                if (htmlIndex !== -1) {
                    jsonResponse = jsonResponse.substring(0, htmlIndex);
                }
                
                const response = JSON.parse(jsonResponse.trim());
                console.log('Respuesta procesada:', response);
                
                if (response.success) {
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('adjustInventoryModal')).hide();
                    
                    // Guardar el mensaje en sessionStorage
                    sessionStorage.setItem('inventoryAlert', response.message);
                    
                    // Recargar la página
                    window.location.href = 'index.php?view=inventary';
                } else {
                    alert('Error: ' + (response.message || 'Error al ajustar el inventario'));
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

// Verificar si hay una alerta pendiente al cargar la página
window.addEventListener('load', function() {
    setTimeout(function() {
        const alertMessage = sessionStorage.getItem('inventoryAlert');
        if (alertMessage) {
            // Crear y mostrar la alerta
            const alertDiv = document.createElement('div');
            // Determinar el color de la alerta según el tipo de operación
            const isSubtraction = alertMessage.includes('restaron');
            alertDiv.className = `alert alert-${isSubtraction ? 'warning' : 'success'} alert-dismissible fade show`;
            alertDiv.role = 'alert';
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '300px';
            alertDiv.innerHTML = `
                <strong>¡${isSubtraction ? 'Aviso' : 'Éxito'}!</strong> ${alertMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Agregar la alerta al body
            document.body.appendChild(alertDiv);
            
            // Configurar la animación de desvanecimiento
            setTimeout(() => {
                alertDiv.style.transition = 'opacity 0.5s';
                alertDiv.style.opacity = '0';
                setTimeout(() => {
                    alertDiv.remove();
                }, 500);
            }, 3000);
            
            // Eliminar el mensaje del sessionStorage
            sessionStorage.removeItem('inventoryAlert');
        }
    }, 500); // Esperar 500ms después de que la página se cargue completamente
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

// Inicializar todos los componentes cuando la página cargue
window.addEventListener('load', function() {
    // Inicializar selección múltiple
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    const confirmDeleteSelectedBtn = document.getElementById('confirmDeleteSelected');
    let lastChecked = null;

    // Seleccionar/deseleccionar todos
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });
    }

    // Actualizar botón de eliminar seleccionados
    function updateDeleteButton() {
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        if (deleteSelectedBtn) {
            deleteSelectedBtn.disabled = selectedCount === 0;
        }
    }

    // Manejar selección con Shift
    function handleCheckboxClick(e) {
        if (!lastChecked) {
            lastChecked = this;
            updateDeleteButton();
            return;
        }

        if (e.shiftKey) {
            const checkboxesArray = Array.from(checkboxes);
            const start = checkboxesArray.indexOf(lastChecked);
            const end = checkboxesArray.indexOf(this);
            
            const startIndex = Math.min(start, end);
            const endIndex = Math.max(start, end);
            
            const isChecked = this.checked;
            
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
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            $('#deleteSelectedModal').modal('show');
        });
    }

    // Eliminar productos seleccionados
    if (confirmDeleteSelectedBtn) {
        confirmDeleteSelectedBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                .map(checkbox => checkbox.value);

            console.log('Enviando solicitud para eliminar productos:', selectedIds);

            // Enviar solicitud para eliminar
            fetch('index.php?view=deleteproducts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_ids=' + encodeURIComponent(JSON.stringify(selectedIds))
            })
            .then(response => {
                console.log('Estado de la respuesta:', response.status);
                console.log('Tipo de contenido:', response.headers.get('content-type'));
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.text().then(text => {
                    console.log('Respuesta recibida:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        throw new Error('La respuesta del servidor no es válida: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Datos procesados:', data);
                if (data.success) {
                    // Guardar el mensaje en localStorage para mostrarlo después de recargar
                    const count = selectedIds.length;
                    localStorage.setItem('deleteSuccessMessage', `Se eliminaron exitosamente ${count} producto${count > 1 ? 's' : ''}.`);
                    // Recargar la página
                    window.location.href = window.location.href;
                } else {
                    alert('Error al eliminar los productos: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                alert('Error al eliminar los productos: ' + error.message);
            });

            $('#deleteSelectedModal').modal('hide');
        });
    }

    // Verificar si hay mensaje de éxito en localStorage
    setTimeout(function() {
        const successMessage = localStorage.getItem('deleteSuccessMessage');
        if (successMessage) {
            // Crear y mostrar la alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>¡Eliminado!</strong> ${successMessage}
            `;
            
            // Agregar la alerta al principio del card-body
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(alertDiv, cardBody.firstChild);
            }
            
            // Eliminar el mensaje del localStorage
            localStorage.removeItem('deleteSuccessMessage');
            
            // Cerrar automáticamente la alerta después de 3 segundos
            setTimeout(() => {
                const closeButton = alertDiv.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, 3000);
        }
    }, 500);

    // Inicializar otros componentes aquí...
    // ... (mantener el resto del código de inicialización)
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
            
            // Actualizar el borde del select personalizado
            const customSelect = document.querySelector('.custom-select');
            if (customSelect) {
                customSelect.style.borderColor = color;
            }
            
            // Actualizar los colores de las opciones
            document.querySelectorAll('.custom-option').forEach(option => {
                const optionCategoryId = option.dataset.value;
                if (optionCategoryId) {
                    const optionColor = localStorage.getItem('category_color_' + optionCategoryId) || '#28a745';
                    option.style.setProperty('--hover-color', optionColor);
                    option.style.backgroundColor = optionCategoryId === categoryId ? optionColor : 'white';
                    option.style.color = optionCategoryId === categoryId ? 'white' : '#000';
                }
            });
        }, 0);
    };
})();

// Inicializar el select personalizado de disponibilidad
window.addEventListener('load', function() {
    const customAvailabilitySelect = document.querySelector('#customAvailabilitySelect');
    const customAvailabilityTrigger = customAvailabilitySelect.querySelector('.custom-select__trigger');
    const customAvailabilityOptions = customAvailabilitySelect.querySelectorAll('.custom-option');
    const originalAvailabilitySelect = document.getElementById('availability');
    
    if (!customAvailabilitySelect || !customAvailabilityTrigger || !originalAvailabilitySelect) return;
    
    // Abrir/cerrar el select
    customAvailabilityTrigger.addEventListener('click', () => {
        customAvailabilitySelect.classList.toggle('open');
    });
    
    // Seleccionar una opción
    customAvailabilityOptions.forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value;
            const text = option.textContent;
            
            // Actualizar el select original
            originalAvailabilitySelect.value = value;
            
            // Actualizar el texto mostrado
            customAvailabilityTrigger.querySelector('span').textContent = text;
            
            // Actualizar las clases selected
            customAvailabilityOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            
            // Cerrar el select
            customAvailabilitySelect.classList.remove('open');
            
            // Disparar el evento change del select original
            originalAvailabilitySelect.dispatchEvent(new Event('change'));
            
            // Filtrar los productos
            filterProducts();
        });
    });
    
    // Cerrar el select al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!customAvailabilitySelect.contains(e.target)) {
            customAvailabilitySelect.classList.remove('open');
        }
    });
});

// Inicializar el select personalizado de categoría
window.addEventListener('load', function() {
    const customCategorySelect = document.querySelector('#customCategorySelect');
    const customCategoryTrigger = customCategorySelect.querySelector('.custom-select__trigger');
    const customCategoryOptions = customCategorySelect.querySelectorAll('.custom-option');
    const originalCategorySelect = document.getElementById('category_id');
    
    if (!customCategorySelect || !customCategoryTrigger || !originalCategorySelect) return;
    
    // Abrir/cerrar el select
    customCategoryTrigger.addEventListener('click', () => {
        customCategorySelect.classList.toggle('open');
    });
    
    // Seleccionar una opción
    customCategoryOptions.forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value;
            const text = option.textContent;
            
            // Actualizar el select original
            originalCategorySelect.value = value;
            
            // Actualizar el texto mostrado
            customCategoryTrigger.querySelector('span').textContent = text;
            
            // Actualizar las clases selected
            customCategoryOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            
            // Cerrar el select
            customCategorySelect.classList.remove('open');
            
            // Actualizar los colores
            updateCategoryColor(originalCategorySelect);
            
            // Filtrar los productos inmediatamente
            filterProducts();
        });
    });
    
    // Cerrar el select al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!customCategorySelect.contains(e.target)) {
            customCategorySelect.classList.remove('open');
        }
    });
    
    // Inicializar el color
    updateCategoryColor(originalCategorySelect);
});

// Función para filtrar productos por disponibilidad
function filterByAvailability(product, availability) {
    if (!availability) return true;
    
    const stock = parseInt(product.querySelector('td:nth-child(8)').textContent);
    
    switch(availability) {
        case '0':
            return stock === 0;
        case '1-10':
            return stock >= 1 && stock <= 10;
        case '11-50':
            return stock >= 11 && stock <= 50;
        case '51-100':
            return stock >= 51 && stock <= 100;
        case '100+':
            return stock > 100;
        default:
            return true;
    }
}

// Modificar la función de filtrado para incluir la disponibilidad
function filterProducts() {
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const dateFilter = document.getElementById('date_filter').value;
    const limit = document.getElementById('limit').value;
    const products = document.querySelectorAll('tbody tr');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    
    console.log('=== INICIO DE FILTRADO ===');
    console.log('Parámetros de filtro:');
    console.log('- Categoría seleccionada:', categoryId);
    console.log('- Límite:', limit);
    console.log('- Total de productos en la tabla:', products.length);
    
    // Mostrar u ocultar el botón de limpiar filtros
    if (searchTerm || categoryId || availability || dateFilter) {
        clearFiltersBtn.style.display = '';
    } else {
        clearFiltersBtn.style.display = 'none';
    }
    
    let visibleCount = 0;
    let categoryMatchCount = 0;
    
    // Primero, mostrar todos los productos para poder filtrarlos
    products.forEach(product => {
        product.style.display = '';
    });
    
    // Luego, aplicar los filtros
    products.forEach((product, index) => {
        const name = product.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const categoryCell = product.querySelector('td:nth-child(4)');
        const badge = categoryCell.querySelector('.badge');
        const productCategoryId = badge ? badge.getAttribute('data-category-id') : '';
        const date = product.querySelector('td:nth-child(9)').textContent;
        const matchesSearch = name.includes(searchTerm);
        
        // Debug logs detallados
        console.log(`=== Producto ${index + 1} ===`);
        console.log('Nombre:', name);
        console.log('ID Categoría del producto:', productCategoryId);
        console.log('ID Categoría seleccionada:', categoryId);
        console.log('Tipo de dato productCategoryId:', typeof productCategoryId);
        console.log('Tipo de dato categoryId:', typeof categoryId);
        
        // Verificar si la categoría coincide
        const matchesCategory = !categoryId || productCategoryId === categoryId;
        
        if (matchesCategory) {
            categoryMatchCount++;
        }
        
        console.log('¿Coincide categoría?:', matchesCategory);
        console.log('================');
        
        const matchesAvailability = filterByAvailability(product, availability);
        const matchesDate = !dateFilter || date === dateFilter;
        
        if (matchesSearch && matchesCategory && matchesAvailability && matchesDate) {
            product.style.display = '';
            visibleCount++;
        } else {
            product.style.display = 'none';
        }
    });
    
    // Debug: Mostrar estadísticas
    console.log('=== ESTADÍSTICAS DE FILTRADO ===');
    console.log('Total de productos:', products.length);
    console.log('Productos que coinciden con la categoría:', categoryMatchCount);
    console.log('Productos visibles después del filtrado:', visibleCount);
    
    // Actualizar la URL con los parámetros de filtro
    const params = new URLSearchParams(window.location.search);
    params.set('view', 'inventary');
    if (searchTerm) params.set('search', searchTerm);
    if (categoryId) params.set('category_id', categoryId);
    if (availability) params.set('availability', availability);
    if (dateFilter) params.set('date_filter', dateFilter);
    if (limit) params.set('limit', limit);
    
    // Mantener la URL limpia si no hay filtros
    if (!searchTerm && !categoryId && !availability && !dateFilter) {
        window.history.replaceState({}, '', 'index.php?view=inventary');
    } else {
        window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
    }
    
    // Si hay una categoría seleccionada, actualizar el límite para mostrar todos los productos
    if (categoryId) {
        console.log('Actualizando límite para categoría:', categoryId);
        console.log('Productos visibles:', visibleCount);
        document.getElementById('limit').value = visibleCount;
        // Ocultar la paginación cuando se filtra por categoría
        const pagination = document.querySelector('.btn-group.pull-right');
        if (pagination) {
            pagination.style.display = 'none';
            console.log('Paginación ocultada');
        }
    } else {
        // Mostrar la paginación cuando no hay filtro de categoría
        const pagination = document.querySelector('.btn-group.pull-right');
        if (pagination) {
            pagination.style.display = '';
            console.log('Paginación mostrada');
        }
        // Restaurar el límite por defecto
        document.getElementById('limit').value = '10';
    }
    
    console.log('=== FIN DE FILTRADO ===');
}

// Función para crear productos de prueba
function createTestProducts() {
    const categories = [
        { id: 1, name: 'Jerseys' },
        { id: 2, name: 'Gorras' },
        { id: 3, name: 'Tenis' },
        { id: 4, name: 'Balones' },
        { id: 5, name: 'Variado' }
    ];

    const products = [];
    
    // Crear 10 productos por categoría
    categories.forEach(category => {
        for (let i = 1; i <= 10; i++) {
            // Generar disponibilidad aleatoria entre 0 y 200
            const availability = Math.floor(Math.random() * 201);
            
            products.push({
                name: `${category.name.toLowerCase()} ${i}`,
                category_id: category.id,
                price_in: (Math.random() * 100 + 50).toFixed(2),
                price_out: (Math.random() * 100 + 100).toFixed(2),
                unit: 'pz',
                inventary_min: Math.floor(Math.random() * 10 + 5),
                availability: availability
            });
        }
    });

    // Enviar los productos al servidor
    fetch('core/app/controller/ProductController.php?action=create_test_products', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ products })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`Error en la respuesta del servidor: ${response.status} ${response.statusText}\n${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error al crear productos: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear productos: ' + error.message);
    });
}

// Agregar botón para crear productos de prueba
window.addEventListener('load', function() {
    const createTestProductsBtn = document.createElement('button');
    createTestProductsBtn.className = 'btn btn-primary';
    createTestProductsBtn.textContent = 'Crear Productos de Prueba';
    createTestProductsBtn.onclick = () => {
        window.location.href = 'create_test_products.php';
    };
    
    const cardHeader = document.querySelector('.card-header');
    if (cardHeader) {
        cardHeader.appendChild(createTestProductsBtn);
    }
});

// Función para obtener el valor de una cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Función para obtener el artículo correcto según la categoría
function getArticleForCategory(category) {
    switch(category.toLowerCase()) {
        case 'balón':
            return 'el';
        case 'tenis':
            return 'los';
        case 'variado':
            return 'el';
        default:
            return 'la';
    }
}

// Verificar si hay una alerta de producto creado
if (getCookie('productCreated') === 'true') {
    const productName = decodeURIComponent(getCookie('productName') || '');
    const productCategory = getCookie('productCategory') || '';
    const article = getArticleForCategory(productCategory);
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        <strong>¡Éxito!</strong> ${article} ${productCategory} "${productName}" se ha creado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Eliminar la alerta después de 3 segundos
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => {
            alertDiv.remove();
        }, 150);
    }, 3000);
    
    // Eliminar las cookies
    document.cookie = "productCreated=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "productName=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "productCategory=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

// Función para mostrar/ocultar el botón de limpiar filtros
function updateClearFiltersButton() {
    const searchInput = document.getElementById('search');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const categorySelect = document.getElementById('category_id');
    const availabilitySelect = document.getElementById('availability');
    const dateFilter = document.getElementById('date_filter');
    
    // Mostrar el botón si hay algún filtro activo
    if (searchInput.value.trim() !== '' || 
        categorySelect.value !== '' || 
        availabilitySelect.value !== '' || 
        dateFilter.value !== '') {
        clearFiltersBtn.style.display = '';
    } else {
        clearFiltersBtn.style.display = 'none';
    }
}

// Agregar evento al campo de búsqueda
document.getElementById('search').addEventListener('input', updateClearFiltersButton);

// Agregar eventos a los otros filtros
document.getElementById('category_id').addEventListener('change', updateClearFiltersButton);
document.getElementById('availability').addEventListener('change', updateClearFiltersButton);
document.getElementById('date_filter').addEventListener('change', updateClearFiltersButton);

// Función para limpiar todos los filtros excepto "mostrar"
function clearFilters() {
    const limitValue = document.getElementById('limit').value;
    // Redirigir a la página base con solo el parámetro de límite
    window.location.href = `index.php?view=inventary&limit=${limitValue}`;
}

// Asignar la función al botón de limpiar filtros
document.getElementById('clearFiltersBtn').addEventListener('click', function(e) {
    e.preventDefault();
    clearFilters();
});

// Verificar si hay filtros activos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateClearFiltersButton();
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

<style>
/* Estilos para el select personalizado */
.custom-select-wrapper {
    position: relative;
    width: 200px;
}

.custom-select {
    position: relative;
    display: flex;
    flex-direction: column;
    border: 2px solid #6c757d;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
}
/* Comentario de prueba */

.custom-select__trigger {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 400;
    color: #000;
    background-color: white;
    cursor: pointer;
}

.arrow {
    position: relative;
    height: 10px;
    width: 10px;
}

.arrow::before, .arrow::after {
    content: "";
    position: absolute;
    bottom: 0px;
    width: 0.15rem;
    height: 100%;
    transition: all 0.3s;
}

.arrow::before {
    left: -5px;
    transform: rotate(45deg);
    background-color: #6c757d;
}

.arrow::after {
    left: 5px;
    transform: rotate(-45deg);
    background-color: #6c757d;
}

.custom-select.open .arrow::before {
    left: -5px;
    transform: rotate(-45deg);
}

.custom-select.open .arrow::after {
    left: 5px;
    transform: rotate(45deg);
}

.custom-options {
    position: absolute;
    display: block;
    top: 100%;
    left: 0;
    right: 0;
    border: 2px solid #6c757d;
    border-top: 0;
    background: #fff;
    transition: all 0.3s;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    z-index: 2;
    height: auto;
    max-height: none;
    overflow: visible;
}

.custom-select.open .custom-options {
    opacity: 1;
    visibility: visible;
    pointer-events: all;
}

.custom-option {
    position: relative;
    display: block;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 400;
    color: #000;
    background-color: white;
    cursor: pointer;
    transition: all 0.3s;
}

/* Estilo para todas las opciones excepto "Todas las categorías" */
.custom-option[data-value]:not([data-value=""]) {
    color: #000;
}

.custom-option[data-value]:not([data-value=""]):hover {
    color: white !important;
    background-color: var(--hover-color) !important;
}

.custom-option[data-value]:not([data-value=""]).selected {
    color: white !important;
    background-color: var(--hover-color) !important;
}

.custom-option.selected::after {
    content: "✓";
    position: absolute;
    right: 10px;
    color: white !important;
}

/* Estilo específico para "Todas las categorías" */
.custom-option[data-value=""] {
    color: #6c757d !important;
}

.custom-option[data-value=""]:hover {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
}

.custom-option[data-value=""].selected {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
}

.custom-option[data-value=""].selected::after {
    color: #6c757d !important;
}
</style>