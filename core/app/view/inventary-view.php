<?php
// Inicializar variables al principio
$products = ProductData::getAll();
$categories = CategoryData::getAll();

// Obtener productos según los filtros
if(isset($_GET["search"]) && $_GET["search"] != "") {
	$products = ProductData::getLike($_GET["search"]);
} else if(isset($_GET["category_id"]) && $_GET["category_id"] != "") {
	$products = ProductData::getAllByCategoryId($_GET["category_id"]);
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
?>
<div class="row">
	<div class="col-md-12">
<!-- Single button -->

		<h1><i class="glyphicon glyphicon-stats"></i> Inventario de Productos 
			<small class="text-muted">
				(<?php echo count($products); ?> productos registrados)
			</small>
		</h1>
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
        <div class="form-inline d-flex align-items-end">
            <div class="form-group me-2">
                <label for="category_id" class="me-2">Categoría:</label>
                <div class="custom-select-wrapper">
                    <div class="custom-select" id="customCategorySelect">
                        <div class="custom-select__trigger">
                            <?php
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
                            <div class="custom-option" data-value="" data-color="#6c757d">Todas las categorías</div>
                            <?php foreach($categories as $category): ?>
                                <div class="custom-option" data-value="<?php echo $category->id; ?>" data-color="#28a745">
                                    <?php echo htmlspecialchars($category->name); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <select id="category_id" name="category_id" style="display: none;">
                        <option value="">Todas las categorías</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo (isset($_GET["category_id"]) && $_GET["category_id"] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
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
                                ['value' => '', 'text' => 'Todas las cantidades'],
                                ['value' => '0', 'text' => 'Sin stock (0)'],
                                ['value' => '1-10', 'text' => 'Stock bajo (1-10)'],
                                ['value' => '11-50', 'text' => 'Stock medio (11-50)'],
                                ['value' => '51-100', 'text' => 'Stock alto (51-100)'],
                                ['value' => '100+', 'text' => 'Stock muy alto (100+)']
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
                            <?php foreach($availabilityOptions as $option): ?>
                                <div class="custom-option" data-value="<?php echo $option['value']; ?>">
                                    <?php echo htmlspecialchars($option['text']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <select id="availability" name="availability" style="display: none;">
                        <?php foreach($availabilityOptions as $option): ?>
                            <option value="<?php echo $option['value']; ?>" <?php echo (isset($_GET["availability"]) && $_GET["availability"] === $option['value']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($option['text']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group me-2">
                <label for="search" class="me-2">Buscar:</label>
                <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET["search"]) ? $_GET["search"] : ''; ?>" placeholder="Buscar productos..." oninput="filterProducts()">
            </div>
            <div class="form-group me-2">
                <label for="date_filter" class="me-2">Fecha:</label>
                <select name="date_filter" id="date_filter" class="form-control" onchange="filterProducts()">
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
                <input type="number" name="limit" id="limit" class="form-control" min="1" max="<?php echo count($products); ?>" value="<?php echo isset($_GET["limit"]) ? $_GET["limit"] : 100; ?>" onchange="updateLimit(this.value)">
            </div>
            <button type="button" class="btn btn-secondary" id="clearFiltersBtn" style="display: none;" onclick="clearFilters()">Limpiar filtros</button>
        </div>
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
			// Verificar si hay filtros activos (excluyendo el campo "Mostrar")
			$has_filters = false;
			
			// Verificar búsqueda
			if(isset($_GET["search"]) && $_GET["search"] != "") {
				$has_filters = true;
			}
			
			// Verificar categoría (solo si no es "Todas las categorías")
			if(isset($_GET["category_id"]) && $_GET["category_id"] != "") {
				$has_filters = true;
			}
			
			// Verificar disponibilidad
			if(isset($_GET["availability"]) && $_GET["availability"] != "") {
				$has_filters = true;
			}
			
			// Verificar fecha
			if(isset($_GET["date_filter"]) && $_GET["date_filter"] != "") {
				$has_filters = true;
			}
			
			// Verificar si el único parámetro es el campo "Mostrar"
			$only_limit = count($_GET) == 2 && isset($_GET["view"]) && isset($_GET["limit"]);
			
			// Verificar si se seleccionó "Todas las categorías"
			$all_categories = isset($_GET["category_id"]) && $_GET["category_id"] == "";
			
			// Determinar si se debe mostrar la alerta
			$show_alert = $has_filters && !$only_limit && !$all_categories;
			
			// Si se seleccionó "Todas las categorías", no mostrar la alerta
			if ($all_categories) {
				$show_alert = false;
			}
			?>
			<div id="filterAlert" class="alert alert-info" <?php echo $show_alert ? '' : 'style="display: none;"'; ?>>
				<?php
				$total_products = count($products);
				if(isset($_GET["search"]) && $_GET["search"] != "") {
					echo "Mostrando $total_products productos que coinciden con la búsqueda";
				} else if(isset($_GET["category_id"]) && $_GET["category_id"] != "") {
					echo "Mostrando $total_products productos de la categoría seleccionada";
				} else if(isset($_GET["availability"]) && $_GET["availability"] != "") {
					echo "Mostrando $total_products productos con la disponibilidad seleccionada";
				} else if(isset($_GET["date_filter"]) && $_GET["date_filter"] != "") {
					echo "Mostrando $total_products productos del período seleccionado";
				} else {
					echo "Mostrando todos los $total_products productos";
				}
				?>
			</div>

			<?php
			$page = 1;
			if(isset($_GET["page"])){
				$page=$_GET["page"];
			}
			$limit = 100; // Valor predeterminado
			if(isset($_GET["limit"]) && $_GET["limit"]!=""){
				$limit = intval($_GET["limit"]);
			}
			// Asegurar que el límite nunca sea 0
			if($limit <= 0) {
				$limit = 100;
			}

			// Verificar si el límite es diferente al total de productos
			$total_products = count($products);
			$is_full_list = ($limit == $total_products);

			// Si el límite es diferente al total y hay filtros, aplicar filtros en el servidor
			if (!$is_full_list && (isset($_GET['category_id']) || isset($_GET['search']) || isset($_GET['availability']) || isset($_GET['date_filter']))) {
				$curr_products = $products;
				$npaginas = 1;
				$page = 1;
			} else {
				// Obtener los productos para la página actual
				$start_index = ($page - 1) * $limit;
				$curr_products = array_slice($products, $start_index, $limit);
			}

			if(count($products)>0){
				// Calcular el número total de páginas
				$total_records = count($products);
				$npaginas = ceil($total_records / $limit);

				// Asegurarse de que la página actual no exceda el número total de páginas
				if ($page > $npaginas) {
					$page = $npaginas;
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

// Almacenar todos los productos en una variable global
let allProducts = <?php echo json_encode($products); ?>;
let currentProducts = allProducts;

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

// Función para filtrar productos
function filterProducts() {
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const dateFilter = document.getElementById('date_filter').value;
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const filterAlert = document.getElementById('filterAlert');
    
    // Mostrar u ocultar el botón de limpiar filtros
    if (searchTerm || categoryId || availability || dateFilter) {
        clearFiltersBtn.style.display = '';
    } else {
        clearFiltersBtn.style.display = 'none';
    }
    
    // Filtrar los productos
    let filteredProducts = allProducts.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchTerm);
        const matchesCategory = !categoryId || product.category_id === categoryId;
        const matchesAvailability = filterByAvailability(product, availability);
        const matchesDate = !dateFilter || product.date === dateFilter;
        
        return matchesSearch && matchesCategory && matchesAvailability && matchesDate;
    });
    
    // Actualizar la tabla
    updateTable(filteredProducts);
    
    // Actualizar la alerta de filtrado
    if (filterAlert) {
        filterAlert.style.display = 'block';
        if (searchTerm) {
            filterAlert.textContent = `Mostrando ${filteredProducts.length} productos que coinciden con la búsqueda`;
        } else if (categoryId) {
            filterAlert.textContent = `Mostrando ${filteredProducts.length} productos de la categoría seleccionada`;
        } else if (availability) {
            filterAlert.textContent = `Mostrando ${filteredProducts.length} productos con la disponibilidad seleccionada`;
        } else if (dateFilter) {
            filterAlert.textContent = `Mostrando ${filteredProducts.length} productos del período seleccionado`;
        } else {
            filterAlert.textContent = `Mostrando todos los ${filteredProducts.length} productos`;
        }
    }
}

// Inicializar la tabla con todos los productos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateTable(allProducts);
});

// Función para actualizar la tabla con los productos filtrados
function updateTable(filteredProducts) {
    const tbody = document.querySelector('tbody');
    const limit = parseInt(document.getElementById('limit').value) || 100;
    const currentPage = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
    
    // Calcular el número total de páginas
    const totalPages = Math.ceil(filteredProducts.length / limit);
    
    // Asegurar que la página actual no exceda el número total de páginas
    const page = Math.min(currentPage, totalPages);
    
    // Calcular el rango de productos a mostrar
    const startIndex = (page - 1) * limit;
    const endIndex = Math.min(startIndex + limit, filteredProducts.length);
    const productsToShow = filteredProducts.slice(startIndex, endIndex);
    
    // Limpiar la tabla
    tbody.innerHTML = '';
    
    // Agregar los productos filtrados
    productsToShow.forEach(product => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input product-checkbox" value="${product.id}">
            </td>
            <td>${product.id}</td>
            <td><a href="index.php?view=producthistory&id=${product.id}" style="text-decoration: none; color: inherit;">${product.name}</a></td>
            <td>
                <span class="badge" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 4px;" data-category-id="${product.category_id}">
                    ${product.category_name}
                </span>
            </td>
            <td>${product.price_in}</td>
            <td>${product.price_out}</td>
            <td>${product.unit}</td>
            <td>${product.available}</td>
            <td>${product.inventary_min}</td>
            <td>
                <button type="button" class="btn btn-sm btn-success" onclick="showAdjustModal(${product.id}, 'add')">
                    <i class="bi bi-plus-circle"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="showAdjustModal(${product.id}, 'subtract')">
                    <i class="bi bi-dash-circle"></i>
                </button>
                <a href="index.php?view=editproduct&id=${product.id}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal(${product.id}, '${product.name.replace(/'/g, "\\'")}')">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    // Actualizar la paginación
    updatePagination(filteredProducts.length, page, totalPages);
}

// Función para actualizar la paginación
function updatePagination(totalProducts, currentPage, totalPages) {
    const paginationContainer = document.querySelector('.btn-group.pull-right');
    if (!paginationContainer) return;
    
    // Limpiar la paginación existente
    paginationContainer.innerHTML = '';
    
    // Agregar botón de página anterior
    if (currentPage > 1) {
        const prevButton = document.createElement('a');
        prevButton.className = 'btn btn-sm btn-default';
        prevButton.href = '#';
        prevButton.innerHTML = '<i class="glyphicon glyphicon-chevron-left"></i> Atras';
        prevButton.onclick = (e) => {
            e.preventDefault();
            changePage(currentPage - 1);
        };
        paginationContainer.appendChild(prevButton);
    }
    
    // Agregar números de página
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('a');
        pageButton.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-default'}`;
        pageButton.href = '#';
        pageButton.textContent = i;
        pageButton.onclick = (e) => {
            e.preventDefault();
            changePage(i);
        };
        paginationContainer.appendChild(pageButton);
    }
    
    // Agregar botón de página siguiente
    if (currentPage < totalPages) {
        const nextButton = document.createElement('a');
        nextButton.className = 'btn btn-sm btn-default';
        nextButton.href = '#';
        nextButton.innerHTML = 'Adelante <i class="glyphicon glyphicon-chevron-right"></i>';
        nextButton.onclick = (e) => {
            e.preventDefault();
            changePage(currentPage + 1);
        };
        paginationContainer.appendChild(nextButton);
    }
    
    // Actualizar el texto de la página actual
    const pageInfo = document.querySelector('h3');
    if (pageInfo) {
        pageInfo.textContent = `Pagina ${currentPage} de ${totalPages}`;
    }
}

// Función para cambiar de página
function changePage(newPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('page', newPage);
    window.history.pushState({}, '', url);
    filterProducts();
}

// Función para actualizar el límite de productos mostrados
function updateLimit(newLimit) {
    const totalProducts = <?php echo count($products); ?>;
    const limitInput = document.getElementById('limit');
    
    // Asegurar que el valor esté dentro del rango
    if (newLimit < 1) {
        newLimit = totalProducts;
    } else if (newLimit > totalProducts) {
        newLimit = 1;
    }
    
    // Actualizar el valor del input
    limitInput.value = newLimit;
    
    // Actualizar la URL sin recargar la página
    const url = new URL(window.location.href);
    url.searchParams.set('limit', newLimit);
    url.searchParams.set('page', '1');
    window.history.pushState({}, '', url);
    
    // Aplicar los filtros actuales
    filterProducts();
}

// Función para limpiar todos los filtros
function clearFilters() {
    // Resetear los valores de los filtros
    document.getElementById('search').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('availability').value = '';
    document.getElementById('date_filter').value = '';
    
    // Actualizar los selects personalizados
    document.querySelector('#customCategorySelect .custom-select__trigger span').textContent = 'Todas las categorías';
    document.querySelector('#customAvailabilitySelect .custom-select__trigger span').textContent = 'Todas las cantidades';
    
    // Ocultar el botón de limpiar filtros
    document.getElementById('clearFiltersBtn').style.display = 'none';
    
    // Aplicar los filtros
    filterProducts();
}
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