<?php
// Inicializar variables al principio
$order = isset($_GET["order"]) ? $_GET["order"] : "desc";
$products = ProductData::getAll($order);
$categories = CategoryData::getAll();

// Obtener productos según los filtros
if(isset($_GET["category_id"]) && $_GET["category_id"] != "") {
	$products = ProductData::getAllByCategoryId($_GET["category_id"]);
}

if(isset($_GET["search"]) && $_GET["search"] != "") {
	$search_products = [];
	foreach($products as $product) {
		if(stripos($product->name, $_GET["search"]) !== false || 
		   stripos($product->id, $_GET["search"]) !== false) {
			$search_products[] = $product;
		}
	}
	$products = $search_products;
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

		<?php if(isset($_COOKIE["prdadd"])):?>
			<div class="alert alert-success alert-dismissible fade show" role="alert" id="addAlert">
				<strong>¡Éxito!</strong> El producto "<?php echo $_COOKIE["prdadd"]; ?>" se ha creado correctamente.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeAddAlert()"></button>
			</div>
			<script>
				function closeAddAlert() {
					document.getElementById('addAlert').style.transition = 'opacity 0.5s';
					document.getElementById('addAlert').style.opacity = '0';
					setTimeout(function() {
						document.getElementById('addAlert').style.display = 'none';
					}, 500);
				}
				
				// Cierre automático después de 5 segundos
				setTimeout(closeAddAlert, 5000);
				
				// Eliminar la cookie inmediatamente para evitar que la alerta aparezca nuevamente
				document.cookie = "prdadd=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/";
			</script>
			<?php 
			// Eliminar la cookie desde PHP también para mayor seguridad
			setcookie("prdadd", "", time()-3600, "/");
			?>
		<?php endif; ?>

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
                                    <?php foreach($categories as $category): 
                                        $categoryColor = isset($_COOKIE['category_color_' . $category->id]) 
                                            ? $_COOKIE['category_color_' . $category->id] 
                                            : '#28a745';
                                    ?>
                                        <div class="custom-option" data-value="<?php echo $category->id; ?>" data-color="<?php echo $categoryColor; ?>">
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
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET["search"]) ? $_GET["search"] : ''; ?>" placeholder="Buscar productos...">
                            <button class="btn btn-primary" type="button" id="searchBtn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
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
                    <div class="col-md-1">
                        <div class="input-group">
                            <input type="number" class="form-control" id="limit" name="limit" min="1" value="<?php echo isset($_GET['limit']) ? $_GET['limit'] : 100; ?>" style="width: 80px;">
                            <button class="btn btn-primary" type="button" onclick="applyLimitFilter()">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="clearFiltersBtn" style="display: none;" onclick="clearFilters()">Limpiar filtros</button>
                    <div class="ms-auto">
                        <a href="index.php?view=inventary&order=<?php echo $order == 'desc' ? 'asc' : 'desc'; ?><?php 
                            if(isset($_GET['category_id'])) echo '&category_id='.$_GET['category_id'];
                            if(isset($_GET['availability'])) echo '&availability='.$_GET['availability'];
                            if(isset($_GET['search'])) echo '&search='.$_GET['search'];
                            if(isset($_GET['date_filter'])) echo '&date_filter='.$_GET['date_filter'];
                            if(isset($_GET['limit'])) echo '&limit='.$_GET['limit'];
                            if(isset($_GET['page'])) echo '&page='.$_GET['page'];
                        ?>" class="btn btn-secondary">
                            <i class="bi bi-sort-<?php echo $order == 'desc' ? 'down' : 'up'; ?>"></i> 
                            <?php echo $order == 'desc' ? 'Más recientes primero' : 'Más antiguos primero'; ?>
                        </a>
                    </div>
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
        <div class="modal fade" id="adjustModal" tabindex="-1" aria-labelledby="adjustModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adjustModalLabel">Ajustar Inventario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="adjustForm" method="post" action="index.php?view=adjustinventory">
                            <input type="hidden" name="product_id" id="productId">
                            <input type="hidden" name="operation_type" id="operationType">
                            
                            <div class="mb-3">
                                <label for="talla" class="form-label">Talla</label>
                                <select class="form-select" id="talla" name="talla" required>
                                    <option value="">Seleccione una talla</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                    <option value="16">16</option>
                                    <option value="18">18</option>
                                    <option value="20">20</option>
                                    <option value="22">22</option>
                                    <option value="24">24</option>
                                    <option value="26">26</option>
                                    <option value="28">28</option>
                                    <option value="6">6</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="submitAdjustForm()">Ajustar</button>
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
                    for($i=0;$i<$npaginas;$i++) {
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
                    <div class="clearfix"></div>
                    <br><table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th style="width: 80px;">Codigo</th>
                                <th style="width: 150px;">Nombre</th>
                                <th style="width: 120px;">Categoría</th>
                                <th style="width: 100px;">Precio de Entrada</th>
                                <th style="width: 100px;">Precio de Salida</th>
                                <th style="width: 80px;">Unidad</th>
                                <th style="width: 80px;">Disponible</th>
                                <th style="width: 80px;">Talla</th>
                                <th style="width: 80px;">Total</th>
                                <th style="width: 100px;">Mínima en Inventario</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($curr_products as $product): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="product-checkbox" value="<?php echo $product->id; ?>">
                                </td>
                                <td><?php echo $product->id; ?></td>
                                <td><?php echo $product->name; ?></td>
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
                                <td class="text-center">
                                    <?php 
                                    // Obtener todas las operaciones del producto
                                    $operations = OperationData::getAllByProductId($product->id);
                                    $tallas = [];
                                    $total = 0;
                                    
                                    // Agrupar por talla
                                    foreach($operations as $op) {
                                        $talla = $op->talla ?? '1';
                                        if(!isset($tallas[$talla])) {
                                            $tallas[$talla] = 0;
                                        }
                                        if($op->operation_type_id == 1) { // Entrada
                                            $tallas[$talla] += $op->q;
                                        } else { // Salida
                                            $tallas[$talla] -= $op->q;
                                        }
                                        $total += $op->operation_type_id == 1 ? $op->q : -$op->q;
                                    }
                                    
                                    // Determinar el color según el total
                                    $min_q = $product->inventary_min;
                                    $percentage = 0;
                                    if($min_q > 0) {
                                        $percentage = ($total / $min_q) * 100;
                                    }
                                    
                                    if($total <= 0) {
                                        $color = '#dc3545'; // Rojo si no hay stock
                                    } else if($percentage <= 50) {
                                        $color = '#dc3545'; // Rojo si está por debajo del 50% del mínimo
                                    } else if($percentage <= 80) {
                                        $color = '#ffc107'; // Amarillo si está entre 50% y 80% del mínimo
                                    } else {
                                        $color = '#28a745'; // Verde si está por encima del 80% del mínimo
                                    }
                                    ?>
                                    <span class="badge" style="background-color: <?php echo $color; ?>; color: white; padding: 5px 10px; border-radius: 4px; font-size: 14px;" data-bs-toggle="tooltip" data-bs-html="true" title="<?php 
                                        $tooltip = '';
                                        foreach($tallas as $talla => $cantidad) {
                                            if($cantidad > 0) {
                                                $tooltip .= "Talla $talla: $cantidad<br>";
                                            }
                                        }
                                        echo $tooltip;
                                    ?>">
                                        <?php echo $total; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    // Obtener la talla del producto
                                    $talla_producto = '';
                                    foreach($operations as $op) {
                                        if($op->talla) {
                                            $talla_producto = $op->talla;
                                            break;
                                        }
                                    }
                                    echo $talla_producto ?: '1';
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    // Calcular el total de todas las tallas
                                    $total_tallas = 0;
                                    foreach($tallas as $cantidad) {
                                        if($cantidad > 0) {
                                            $total_tallas += $cantidad;
                                        }
                                    }
                                    echo $total_tallas;
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
                            <?php endforeach; ?>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Variable global para almacenar el mensaje de la operación
let lastOperationMessage = '';

// Variables globales para los datos
let allProducts = <?php echo json_encode($products); ?>;
let categoriesData = <?php echo json_encode($categories); ?>;
let filteredProducts = [...allProducts];

function showAdjustModal(productId, operationType) {
    document.getElementById('productId').value = productId;
    document.getElementById('operationType').value = operationType;
    document.getElementById('adjustModalLabel').textContent = 
        operationType === 'add' ? 'Agregar al Inventario' : 'Restar del Inventario';
    var modal = new bootstrap.Modal(document.getElementById('adjustModal'));
    modal.show();
}

function submitAdjustForm() {
    const form = document.getElementById('adjustForm');
    const talla = document.getElementById('talla').value;
    const quantity = document.getElementById('quantity').value;
    
    if(!talla) {
        alert('Por favor seleccione una talla');
        return;
    }
    
    if(!quantity || quantity <= 0) {
        alert('Por favor ingrese una cantidad válida');
        return;
    }
    
    form.submit();
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

// Función para actualizar el color de la categoría
function updateCategoryColor(select) {
    const selectedOption = select.options[select.selectedIndex];
    const color = selectedOption.dataset.color || '#000000';
    const customSelect = document.querySelector('#customCategorySelect');
    if (customSelect) {
        customSelect.style.setProperty('--select-color', color);
    }
}

// Inicializar el select personalizado de categoría
document.addEventListener('DOMContentLoaded', function() {
    const customCategorySelect = document.querySelector('#customCategorySelect');
    const customCategoryTrigger = customCategorySelect.querySelector('.custom-select__trigger');
    const customCategoryOptions = customCategorySelect.querySelectorAll('.custom-option');
    const originalCategorySelect = document.getElementById('category_id');
    
    if (!customCategorySelect || !customCategoryTrigger || !originalCategorySelect) return;
    
    // Abrir/cerrar el select
    customCategoryTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        customCategorySelect.classList.toggle('open');
    });
    
    // Seleccionar una opción
    customCategoryOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();
            const value = option.dataset.value;
            const text = option.textContent;
            const color = option.dataset.color;
            
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
            
            // Actualizar el botón de limpiar filtros
            updateClearFiltersButton();
            
            // Aplicar el filtro
            const url = new URL(window.location.href);
            url.searchParams.set('category_id', value);
            window.location.href = url.toString();
        });

        // Aplicar el color al hover
        option.addEventListener('mouseover', (e) => {
            if (option.dataset.value !== "") {
                const color = option.dataset.color;
                option.style.setProperty('--hover-color', color);
                option.style.backgroundColor = color;
                option.style.color = 'white';
            }
        });

        option.addEventListener('mouseout', (e) => {
            if (option.dataset.value !== "") {
                option.style.backgroundColor = 'white';
                option.style.color = '#000';
            }
        });
    });
    
    // Cerrar el select al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!customCategorySelect.contains(e.target)) {
            customCategorySelect.classList.remove('open');
        }
    });
});

// Inicializar el select personalizado de disponibilidad
document.addEventListener('DOMContentLoaded', function() {
    const customAvailabilitySelect = document.querySelector('#customAvailabilitySelect');
    const customAvailabilityTrigger = customAvailabilitySelect.querySelector('.custom-select__trigger');
    const customAvailabilityOptions = customAvailabilitySelect.querySelectorAll('.custom-option');
    const originalAvailabilitySelect = document.getElementById('availability');
    
    if (!customAvailabilitySelect || !customAvailabilityTrigger || !originalAvailabilitySelect) return;
    
    // Abrir/cerrar el select
    customAvailabilityTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        customAvailabilitySelect.classList.toggle('open');
    });
    
    // Seleccionar una opción
    customAvailabilityOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();
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
            
            // Actualizar el botón de limpiar filtros
            updateClearFiltersButton();
            
            // Aplicar el filtro
            const url = new URL(window.location.href);
            url.searchParams.set('availability', value);
            window.location.href = url.toString();
        });
    });
    
    // Cerrar el select al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!customAvailabilitySelect.contains(e.target)) {
            customAvailabilitySelect.classList.remove('open');
        }
    });
});

// Evento para el botón de búsqueda
document.getElementById('searchBtn').addEventListener('click', function() {
    const searchTerm = document.getElementById('search').value;
    const url = new URL(window.location.href);
    
    // Mantener los filtros existentes
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const dateFilter = document.getElementById('date_filter').value;
    const limit = document.getElementById('limit').value;
    
    // Actualizar la URL manteniendo los filtros
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    } else {
        url.searchParams.delete('search');
    }
    
    // Mantener los otros filtros
    if (categoryId) url.searchParams.set('category_id', categoryId);
    if (availability) url.searchParams.set('availability', availability);
    if (dateFilter) url.searchParams.set('date_filter', dateFilter);
    if (limit) url.searchParams.set('limit', limit);
    
    window.location.href = url.toString();
});

// Evento para aplicar la búsqueda al presionar Enter
document.getElementById('search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const searchTerm = document.getElementById('search').value;
        const url = new URL(window.location.href);
        
        // Mantener los filtros existentes
        const categoryId = document.getElementById('category_id').value;
        const availability = document.getElementById('availability').value;
        const dateFilter = document.getElementById('date_filter').value;
        const limit = document.getElementById('limit').value;
        
        // Actualizar la URL manteniendo los filtros
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        
        // Mantener los otros filtros
        if (categoryId) url.searchParams.set('category_id', categoryId);
        if (availability) url.searchParams.set('availability', availability);
        if (dateFilter) url.searchParams.set('date_filter', dateFilter);
        if (limit) url.searchParams.set('limit', limit);
        
        window.location.href = url.toString();
    }
});

// Evento para el input de búsqueda (solo para mostrar/ocultar el botón de limpiar)
document.getElementById('search').addEventListener('input', function() {
    updateClearFiltersButton();
});

// Función para actualizar la visibilidad del botón de limpiar filtros
function updateClearFiltersButton() {
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    if (clearFiltersBtn) {
        const searchTerm = document.getElementById('search').value;
        const categoryId = document.getElementById('category_id').value;
        const availability = document.getElementById('availability').value;
        const dateFilter = document.getElementById('date_filter').value;
        
        // Mostrar el botón si hay algún filtro activo
        clearFiltersBtn.style.display = (searchTerm || categoryId || availability || dateFilter) ? '' : 'none';
    }
}

// Función para limpiar filtros
function clearFilters() {
    // Ocultar el botón de limpiar filtros
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    if (clearFiltersBtn) {
        clearFiltersBtn.style.display = 'none';
    }
    
    // Recargar la página sin filtros
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('category_id');
    url.searchParams.delete('availability');
    url.searchParams.delete('date_filter');
    url.searchParams.delete('limit');
    window.location.href = url.toString();
}

// Función para filtrar productos
function filterProducts() {
    // Obtener valores actuales de los filtros
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    
    // Resetear productos filtrados
    filteredProducts = [...allProducts];
    
    // Aplicar filtros secuencialmente
    if (searchTerm) {
        filteredProducts = filteredProducts.filter(product => 
            product.name.toLowerCase().includes(searchTerm) || 
            product.id.toString().includes(searchTerm)
        );
    }
    
    if (categoryId) {
        filteredProducts = filteredProducts.filter(product => 
            product.category_id == categoryId
        );
    }
    
    if (availability) {
        filteredProducts = filteredProducts.filter(product => {
            const q = parseFloat(product.availability) || 0;
            switch(availability) {
                case '0': return q === 0;
                case '1-10': return q >= 1 && q <= 10;
                case '11-50': return q >= 11 && q <= 50;
                case '51-100': return q >= 51 && q <= 100;
                case '100+': return q > 100;
                default: return true;
            }
        });
    }
    
    // Actualizar la tabla inmediatamente
    updateTableWithClientData();
    
    // Mostrar u ocultar el botón de limpiar filtros
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    if (clearFiltersBtn) {
        clearFiltersBtn.style.display = (searchTerm || categoryId || availability) ? '' : 'none';
    }
}

// Función para actualizar la tabla con los datos del cliente
function updateTableWithClientData() {
    const tbody = document.querySelector('#inventoryTable tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    // Obtener parámetros de paginación
    const urlParams = new URLSearchParams(window.location.search);
    const limit = parseInt(urlParams.get('limit')) || 100;
    const currentPage = parseInt(urlParams.get('page')) || 1;

    // Calcular índices para la paginación
    const startIndex = (currentPage - 1) * limit;
    const endIndex = startIndex + limit;
    const currentProducts = filteredProducts.slice(startIndex, endIndex);

    // Actualizar paginación
    const totalPages = Math.ceil(filteredProducts.length / limit);
    const paginationContainer = document.querySelector('.btn-group.pull-right');
    if (paginationContainer) {
        let paginationHTML = '';
        
        // Botón Atrás
        if (currentPage > 1) {
            const prevPage = currentPage - 1;
            paginationHTML += `<a class="btn btn-sm btn-default" href="index.php?view=inventary&limit=$limit&page=${prevPage}"><i class="glyphicon glyphicon-chevron-left"></i> Atras </a>`;
        }

        // Números de página
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'btn-primary' : 'btn-default';
            paginationHTML += `<a class="btn ${activeClass} btn-sm" href="index.php?view=inventary&limit=$limit&page=${i}">${i}</a>`;
        }

        // Botón Adelante
        if (currentPage < totalPages) {
            const nextPage = currentPage + 1;
            paginationHTML += `<a class="btn btn-sm btn-default" href="index.php?view=inventary&limit=$limit&page=${nextPage}">Adelante <i class="glyphicon glyphicon-chevron-right"></i></a>`;
        }

        paginationContainer.innerHTML = paginationHTML;
    }

    // Actualizar texto de página actual
    const pageInfo = document.querySelector('h3');
    if (pageInfo) {
        pageInfo.textContent = `Pagina ${currentPage} de ${totalPages}`;
    }

    // Actualizar tabla con productos
    currentProducts.forEach(product => {
        const tr = document.createElement('tr');
        const q = parseFloat(product.availability) || 0;
        const minQ = parseFloat(product.inventary_min) || 0;
        
        // Determinar clase de la fila según disponibilidad
        if(q <= minQ/2) {
            tr.className = 'danger';
        } else if(q <= minQ) {
            tr.className = 'warning';
        }

        // Obtener nombre y color de la categoría
        let categoryName = 'Sin categoría';
        let categoryColor = '#6c757d';
        
        if (product.category_id) {
            const category = categoriesData.find(c => c.id == product.category_id);
            if (category) {
                categoryName = category.name;
                categoryColor = localStorage.getItem('category_color_' + category.id) || '#28a745';
            }
        }

        // Determinar color de disponibilidad
        let availabilityColor = '#28a745';
        if (q <= 0) {
            availabilityColor = '#dc3545';
        } else {
            const percentage = (minQ / q) * 100;
            if(percentage <= 50) {
                availabilityColor = '#dc3545';
            } else if(percentage <= 80) {
                availabilityColor = '#ffc107';
            } else {
                availabilityColor = '#28a745';
            }
        }

        tr.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input product-checkbox" value="${product.id}">
            </td>
            <td>${product.id}</td>
            <td><a href="index.php?view=producthistory&id=${product.id}" style="text-decoration: none; color: inherit;">${product.name}</a></td>
            <td>
                <span class="badge" style="background-color: ${categoryColor}; color: white; padding: 5px 10px; border-radius: 4px;" data-category-id="${product.category_id || ''}">
                    ${categoryName}
                </span>
            </td>
            <td>${product.price_in}</td>
            <td>${product.price_out}</td>
            <td>${product.unit}</td>
            <td>
                <span style="background-color: ${availabilityColor}; color: white; padding: 5px 10px; border-radius: 5px;">
                    ${q}
                </span>
            </td>
            <td>
                <?php 
                // Obtener la talla del producto
                $talla_producto = '';
                foreach($operations as $op) {
                    if($op->talla) {
                        $talla_producto = $op->talla;
                        break;
                    }
                }
                echo $talla_producto ?: '1';
                ?>
            </td>
            <td>
                <?php 
                // Calcular el total de todas las tallas
                $total_tallas = 0;
                foreach($tallas as $cantidad) {
                    if($cantidad > 0) {
                        $total_tallas += $cantidad;
                    }
                }
                echo $total_tallas;
                ?>
            </td>
            <td><?php echo $product->inventary_min; ?></td>
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

    // Actualizar alerta de filtrado
    const filterAlert = document.getElementById('filterAlert');
    if (filterAlert) {
        const totalFiltered = filteredProducts.length;
        let filterMessage = '';
        
        if (document.getElementById('search').value) {
            filterMessage = `Mostrando ${totalFiltered} productos que coinciden con la búsqueda`;
        } else if (document.getElementById('category_id').value) {
            const categoryId = document.getElementById('category_id').value;
            const category = categoriesData.find(c => c.id == categoryId);
            const categoryName = category ? category.name : 'Todas las categorías';
            filterMessage = `Mostrando ${totalFiltered} productos de la categoría "${categoryName}"`;
        } else if (document.getElementById('availability').value) {
            let availabilityText = '';
            switch(document.getElementById('availability').value) {
                case '0': availabilityText = 'Sin stock (0)'; break;
                case '1-10': availabilityText = 'Stock bajo (1-10)'; break;
                case '11-50': availabilityText = 'Stock medio (11-50)'; break;
                case '51-100': availabilityText = 'Stock alto (51-100)'; break;
                case '100+': availabilityText = 'Stock muy alto (100+)'; break;
            }
            filterMessage = `Mostrando ${totalFiltered} productos con ${availabilityText}`;
        } else {
            filterMessage = `Mostrando todos los ${totalFiltered} productos`;
        }
        
        filterAlert.style.display = 'block';
        filterAlert.textContent = filterMessage;
    }
}

// Inicializar la tabla al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateTableWithClientData();
    updateClearFiltersButton();
});

// Función para aplicar el filtro de límite
function applyLimitFilter() {
    const limit = document.getElementById('limit').value;
    const url = new URL(window.location.href);
    url.searchParams.set('limit', limit);
    window.location.href = url.toString();
}

// Evento para el input de límite (solo para mostrar/ocultar el botón de limpiar)
document.getElementById('limit').addEventListener('input', function() {
    updateClearFiltersButton();
});

// Evento para aplicar el filtro al presionar Enter
document.getElementById('limit').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyLimitFilter();
    }
});

// Función para aplicar el orden manteniendo los filtros
function applyOrder(url) {
    // Obtener los valores actuales de los filtros
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const searchTerm = document.getElementById('search').value;
    const dateFilter = document.getElementById('date_filter').value;
    const limit = document.getElementById('limit').value;
    
    // Construir la URL con los parámetros actuales
    let newUrl = new URL(url);
    if (categoryId) newUrl.searchParams.set('category_id', categoryId);
    if (availability) newUrl.searchParams.set('availability', availability);
    if (searchTerm) newUrl.searchParams.set('search', searchTerm);
    if (dateFilter) newUrl.searchParams.set('date_filter', dateFilter);
    if (limit) newUrl.searchParams.set('limit', limit);
    
    // Redirigir a la nueva URL
    window.location.href = newUrl.toString();
    return false;
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
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

.custom-option[data-value]:not([data-value=""]) {
    --hover-color: attr(data-color);
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