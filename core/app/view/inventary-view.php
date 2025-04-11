<?php
// Inicializar variables al principio
$order = isset($_GET["order"]) ? $_GET["order"] : "desc";
$products = ProductData::getAll($order);
$categories = CategoryData::getAll();

// Obtener productos según los filtros
if(isset($_GET["category_id"]) && $_GET["category_id"] != "") {
	$products = ProductData::getAllByCategoryId($_GET["category_id"], $order);
	// Si no hay productos en la categoría, mostrar todos los productos
	if(empty($products)) {
		$products = ProductData::getAll($order);
		// Establecer cookie para mostrar el mensaje
		setcookie("category_empty", "1", time()+3600);
		// Redirigir a todas las categorías manteniendo los filtros
		$url = "index.php?view=inventary";
		if(isset($_GET["order"])) $url .= "&order=".$_GET["order"];
		if(isset($_GET["search"])) $url .= "&search=".$_GET["search"];
		if(isset($_GET["availability"])) $url .= "&availability=".$_GET["availability"];
		if(isset($_GET["date_filter"])) $url .= "&date_filter=".$_GET["date_filter"];
		if(isset($_GET["limit"])) $url .= "&limit=".$_GET["limit"];
		if(isset($_GET["size"])) $url .= "&size=".$_GET["size"];
		header("Location: ".$url);
		exit;
	}
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

// Filtrar por talla si está seleccionado
if(isset($_GET["size"]) && $_GET["size"] != "") {
    $filtered_products = [];
    foreach($products as $product) {
        if($product->size == $_GET["size"]) {
            $filtered_products[] = $product;
        }
    }
    $products = $filtered_products;
}

// Filtrar por disponibilidad si está seleccionado
if(isset($_GET["availability"]) && $_GET["availability"] != "") {
	$filtered_products = [];
	foreach($products as $product) {
		$q = $product->availability; // Usar el valor correcto del stock
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

// Filtrar por tipo de jersey si está seleccionado y no es 'Todos'
if(isset($_GET["jerseyType"]) && $_GET["jerseyType"] != "") {
    $filtered_products = [];
    foreach($products as $product) {
        $jerseyType = $product->jersey_type;
        if ($jerseyType == 'nino') {
            $jerseyType = 'niño';
        }
        if($_GET["jerseyType"] == "" || $jerseyType == $_GET["jerseyType"]) {
            $filtered_products[] = $product;
        }
    }
    $products = $filtered_products;
}

// Obtener la categoría seleccionada
$selected_category = isset($_GET["category_id"]) ? $_GET["category_id"] : "";
$selected_category_name = "";
if($selected_category != "") {
    foreach($categories as $category) {
        if($category->id == $selected_category) {
            $selected_category_name = $category->name;
            break;
        }
    }
}

// Definir las tallas disponibles según la categoría
$available_sizes = [];
if($selected_category_name == "Jersey") {
    $available_sizes = [
        "adulto" => ["S", "M", "L", "XL", "XXL", "3XL", "4XL", "6XL", "8XL"],
        "niño" => ["16", "18", "20", "22", "24", "26", "28"]
    ];
} elseif($selected_category_name == "Tenis") {
    $available_sizes = [
        "tenis" => ["23.5", "24", "24.5", "25", "25.5", "26", "26.5", "27"]
    ];
} elseif($selected_category_name == "Variado") {
    // Para la categoría Variado, mostrar todas las tallas disponibles
    $available_sizes = [
        "adulto" => ["S", "M", "L", "XL", "XXL"],
        "niño" => ["16", "18", "20", "22", "24", "26", "28"],
        "tenis" => ["6", "7", "8", "9"]
    ];
} elseif(in_array($selected_category_name, ["Gorras", "Balón"])) {
    $available_sizes = [];
} else {
    // Cuando no hay categoría seleccionada, mostrar todas las tallas agrupadas
    $available_sizes = [
        "adulto" => ["S", "M", "L", "XL", "XXL", "3XL", "4XL", "6XL", "8XL"],
        "niño" => ["16", "18", "20", "22", "24", "26", "28"],
        "tenis" => ["23.5", "24", "24.5", "25", "25.5", "26", "26.5", "27"]
    ];
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
				<strong>¡Eliminado!</strong> <span id="deleteMessage"><?php echo $_COOKIE["prddel"]; ?></span>
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
        <!-- ===== INICIO DE SECCIÓN PROTEGIDA - NO MODIFICAR ===== -->
        <!-- Esta sección maneja los botones principales de la vista -->
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
        <!-- ===== FIN DE SECCIÓN PROTEGIDA ===== -->

        <!-- ===== INICIO DE SECCIÓN PROTEGIDA - NO MODIFICAR ===== -->
        <!-- Esta sección maneja los filtros principales -->
        <div class="form-inline d-flex flex-wrap align-items-end gap-2">
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="category_id" class="me-2">Categoría:</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>" <?php echo (isset($_GET["category_id"]) && $_GET["category_id"] == $category->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="availability" class="me-2">Disponibilidad:</label>
                <select id="availability" name="availability" class="form-control">
                    <option value="">Todas las cantidades</option>
                    <option value="0" <?php echo (isset($_GET["availability"]) && $_GET["availability"] === "0") ? 'selected' : ''; ?>>Sin stock (0)</option>
                    <option value="1-10" <?php echo (isset($_GET["availability"]) && $_GET["availability"] === "1-10") ? 'selected' : ''; ?>>Stock bajo (1-10)</option>
                    <option value="11-50" <?php echo (isset($_GET["availability"]) && $_GET["availability"] === "11-50") ? 'selected' : ''; ?>>Stock medio (11-50)</option>
                    <option value="51-100" <?php echo (isset($_GET["availability"]) && $_GET["availability"] === "51-100") ? 'selected' : ''; ?>>Stock alto (51-100)</option>
                    <option value="100+" <?php echo (isset($_GET["availability"]) && $_GET["availability"] === "100+") ? 'selected' : ''; ?>>Stock muy alto (100+)</option>
                </select>
            </div>

            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="size" class="me-2">Talla:</label>
                <select id="size" name="size" class="form-control">
                    <option value="">Todas las tallas</option>
                    <?php
                    if(isset($available_sizes)) {
                        foreach($available_sizes as $type => $sizes) {
                            foreach($sizes as $size) {
                                echo '<option value="' . $size . '"';
                                if(isset($_GET["size"]) && $_GET["size"] == $size) {
                                    echo ' selected';
                                }
                                echo '>' . $size . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="date_filter" class="me-2">Fecha:</label>
                <select id="date_filter" name="date_filter" class="form-control">
                    <option value="">Todas las fechas</option>
                    <option value="today" <?php echo (isset($_GET["date_filter"]) && $_GET["date_filter"] === "today") ? 'selected' : ''; ?>>Hoy</option>
                    <option value="week" <?php echo (isset($_GET["date_filter"]) && $_GET["date_filter"] === "week") ? 'selected' : ''; ?>>Esta semana</option>
                    <option value="month" <?php echo (isset($_GET["date_filter"]) && $_GET["date_filter"] === "month") ? 'selected' : ''; ?>>Este mes</option>
                </select>
            </div>

            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="search" class="me-2">Buscar:</label>
                <div class="input-group">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Buscar productos..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
    <label for="limit" class="me-2">Mostrar:</label>
    <div class="input-group">
        <input type="number" class="form-control" id="limit" name="limit" min="1" value="<?php echo isset($_GET['limit']) ? $_GET['limit'] : 100; ?>" style="width: 80px;">
        <button class="btn btn-primary" type="button" onclick="applyLimitFilter()">
            <i class="bi bi-filter"></i>
        </button>
    </div>
</div>

            <div class="form-group">
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                    <i class="bi bi-x-circle"></i> Limpiar Filtros
                </button>
            </div>
        </div>
        <!-- ===== FIN DE SECCIÓN PROTEGIDA ===== -->

<!-- Botón para eliminar seleccionados -->
<div class="row mt-3">
<div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="jerseyType" class="d-block">Tipo de Jersey:</label>
                <select id="jerseyType" name="jerseyType" class="form-control" style="width: 150px; margin-bottom: 10px;">
                    <option value="" <?php echo (!isset($_GET["jerseyType"]) || $_GET["jerseyType"] == "") ? 'selected' : ''; ?>>Todos</option>
                    <option value="adulto" <?php echo (isset($_GET["jerseyType"]) && $_GET["jerseyType"] == "adulto") ? 'selected' : ''; ?>>Adulto</option>
                    <option value="niño" <?php echo (isset($_GET["jerseyType"]) && $_GET["jerseyType"] == "niño") ? 'selected' : ''; ?>>Niño</option>
                    <option value="dama" <?php echo (isset($_GET["jerseyType"]) && $_GET["jerseyType"] == "dama") ? 'selected' : ''; ?>>Dama</option>
                </select>
            </div>
    <div class="col-md-12">
        <button type="button" class="btn btn-danger" id="deleteSelected" disabled style="margin-bottom: 15px;">
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
                <form id="adjustForm" method="post" action="index.php?view=update_stock" onsubmit="return submitAdjustForm(event)">
                    <input type="hidden" name="product_id" id="productId">
                    <input type="hidden" name="operation_type" id="operationType">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Ajustar</button>
                    </div>
                </form>
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
<div class="modal fade" id="deleteSelectedModal" tabindex="-1" aria-labelledby="deleteSelectedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSelectedModalLabel">Eliminar productos seleccionados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar los productos seleccionados? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
			
			// Verificar talla
			if(isset($_GET["size"]) && $_GET["size"] != "") {
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
				} else if(isset($_GET["size"]) && $_GET["size"] != "") {
					echo "Mostrando $total_products productos con la talla seleccionada";
				} else if(isset($_GET["availability"]) && $_GET["availability"] != "") {
					echo "Mostrando $total_products productos con la disponibilidad seleccionada";
				} else if(isset($_GET["date_filter"]) && $_GET["date_filter"] != "") {
					echo "Mostrando $total_products productos del período seleccionado";
				} else {
					echo "Mostrando todos los $total_products productos";
				}
				?>
			</div>

                    <?php if(isset($_COOKIE["category_empty"])): ?>
                        <!-- ===== INICIO DE SECCIÓN PROTEGIDA - NO MODIFICAR ===== -->
                        <!-- Esta sección maneja el mensaje de alerta para categorías vacías -->
                        <div id="categoryEmptyAlert" class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>¡Atención!</strong> No hay productos en la categoría seleccionada.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php setcookie("category_empty","",time()-18600); endif; ?>
                        <!-- ===== FIN DE SECCIÓN PROTEGIDA ===== -->

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

			// Obtener los productos para la página actual
			$start_index = ($page - 1) * $limit;
			$curr_products = array_slice($products, $start_index, $limit);

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
                <div class="pagination-container">
    <?php
    $px = $page-1;
    if($px > 0):
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
    // Mostrar solo un rango de páginas si hay demasiadas
    $maxPagesToShow = 10;
    $startPage = max(1, $page - floor($maxPagesToShow / 2));
    $endPage = min($npaginas, $startPage + $maxPagesToShow - 1);
    
    // Ajustar el inicio si estamos cerca del final
    if ($endPage - $startPage + 1 < $maxPagesToShow) {
        $startPage = max(1, $endPage - $maxPagesToShow + 1);
    }
    
    // Mostrar primera página y "..." si es necesario
    if ($startPage > 1) {
        echo "<a href='index.php?view=inventary&limit=$limit&page=1' class='btn btn-sm btn-default'>1</a>";
        if ($startPage > 2) {
            echo "<span class='btn btn-sm btn-default disabled'>...</span>";
        }
    }
    
    // Mostrar el rango de páginas
    for($i = $startPage; $i <= $endPage; $i++) {
        $url = "index.php?view=inventary&limit=$limit&page=$i";
        if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
            $url .= "&category_id=".$_GET['category_id'];
        }
        if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
            $url .= "&date_filter=".$_GET['date_filter'];
        }
        
        $active_class = ($page == $i) ? 'btn-primary' : 'btn-default';
        echo "<a href='$url' class='btn btn-sm $active_class'>$i</a> ";
    }
    
    // Mostrar "..." y última página si es necesario
    if ($endPage < $npaginas) {
        if ($endPage < $npaginas - 1) {
            echo "<span class='btn btn-sm btn-default disabled'>...</span>";
        }
        echo "<a href='index.php?view=inventary&limit=$limit&page=$npaginas' class='btn btn-sm btn-default'>$npaginas</a>";
    }
    
    // Botón "Siguiente"
    if($page < $npaginas):
        $url = "index.php?view=inventary&limit=$limit&page=".($page + 1);
        if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
            $url .= "&category_id=".$_GET['category_id'];
        }
        if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
            $url .= "&date_filter=".$_GET['date_filter'];
        }
    ?>
    <a class="btn btn-sm btn-default" href="<?php echo $url; ?>">Siguiente <i class="glyphicon glyphicon-chevron-right"></i></a>
    <?php endif; ?>
</div>
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width: 50px;">
							<input type="checkbox" id="selectAll" class="form-check-input">
						</th>
                                <th style="width: 80px;">Codigo</th>
                                <th style="width: 150px;">Nombre</th>
						<th style="width: 80px;">Talla</th>
                                <th style="width: 120px;">Categoría</th>
                                <th style="width: 100px;">Precio de Entrada</th>
                                <th style="width: 100px;">Precio de Salida</th>
                                <th style="width: 80px;">Unidad</th>
                                <th style="width: 100px;">Mínima en Inventario</th>
                                <th style="width: 50px;">Disponible</th>
                                <th style="width: 60px;">Total</th>
                                <th style="width: 180px;">Acciones</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    $current_name = null;
                    $rowspan = 1;
                    $products_count = count($curr_products);
                    $group_id = 0;
                    $total_groups = [];
                    
                    // Primero, agrupar los productos por nombre
                    foreach($curr_products as $index => $product) {
                        if($current_name !== $product->name) {
                            $current_name = $product->name;
                            $group_id++;
                        }
                        $total_groups[$group_id][] = $product;
                    }
                    
                    // Luego, mostrar los productos
                    foreach($curr_products as $index => $product):
                        // Encontrar el grupo actual
                        $current_group = null;
                        foreach($total_groups as $group_id => $group) {
                            if(in_array($product, $group)) {
                                $current_group = $group;
                                break;
                            }
                        }
                        
                        // Calcular el rowspan para el grupo actual
                        $rowspan = count($current_group);
                        
                        // Determinar si es la primera fila del grupo
                        $is_first_in_group = $current_group !== null && $product === reset($current_group);
                    ?>
                    <tr data-product-id="<?php echo $product->id; ?>" data-group-name="<?php echo htmlspecialchars($product->name); ?>" class="product-row">
                                <td>
                                    <input type="checkbox" class="product-checkbox" value="<?php echo $product->id; ?>">
						</td>
						<td><?php echo $product->id; ?></td>
                                <td><?php echo $product->name; ?></td>
                        <td><?php echo $product->size; ?></td>
						<td>
							<?php 
							$categoryColor = '#6c757d'; // Color por defecto
							$categoryName = 'Sin categoría';
							
							if (!empty($product->category_id)) {
                                // Obtener el color de la categoría
								$category = CategoryData::getById($product->category_id);
								if ($category) {
									$categoryName = $category->name;
                                    // Intentar obtener el color de la cookie primero
                                    $categoryColor = isset($_COOKIE['category_color_' . $product->category_id]) 
                                        ? $_COOKIE['category_color_' . $product->category_id] 
                                        : '#28a745'; // Color por defecto si no hay cookie
								}
							}
							?>
							<span class="badge" style="background-color: <?php echo $categoryColor; ?>; color: white; padding: 5px 10px; border-radius: 4px;" data-category-id="<?php echo $product->category_id; ?>">
								<?php echo htmlspecialchars($categoryName); ?>
							</span>
							<?php if ($product->category_id == 1 && $categoryName == 'Jersey'): ?>
								<?php
								$jerseyColor = '#28a745'; // Verde por defecto para adulto
								if ($product->jersey_type == 'niño' || $product->jersey_type == 'nino') {
									$jerseyColor = '#007bff'; // Azul para niño
								} elseif ($product->jersey_type == 'dama') {
									$jerseyColor = '#ff69b4'; // Rosa para dama
								}
								?>
								<?php
								$jerseyTypeDisplay = $product->jersey_type;
								if ($jerseyTypeDisplay == 'nino') {
									$jerseyTypeDisplay = 'niño';
								}
								?>
								<span class="badge" style="background-color: <?php echo $jerseyColor; ?>; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
									<?php echo htmlspecialchars($jerseyTypeDisplay); ?>
								</span>
							<?php endif; ?>
						</td>
						<td><?php echo $product->price_in; ?></td>
						<td><?php echo $product->price_out; ?></td>
						<td><?php echo $product->unit; ?></td>
                        <td><?php echo $product->inventary_min; ?></td>
                                <td class="text-center">
							<?php 
                                    // Obtener todas las operaciones del producto
                                    $operations = OperationData::getAllByProductId($product->id);
                                    $tallas = [];
                            $total = $product->availability; // Usar el valor correcto del stock
                                    
                                    // Agrupar por talla
                                    foreach($operations as $op) {
                                $talla = $op->talla ?? 'unitalla';
                                        if(!isset($tallas[$talla])) {
                                            $tallas[$talla] = 0;
                                        }
                                        if($op->operation_type_id == 1) { // Entrada
                                            $tallas[$talla] += $op->q;
                                        } else { // Salida
                                            $tallas[$talla] -= $op->q;
                                        }
                                    }
                                    
                                    // Determinar el color según el total
                                    $min_q = $product->inventary_min;
                            $total = $product->availability;
                            
                            $color = '#28a745'; // Verde por defecto
                            if($total <= $min_q) {
                                $color = '#dc3545'; // Rojo si está en o por debajo del mínimo
                            } else if($total <= ($min_q + 5)) {
                                $color = '#fd7e14'; // Naranja si está cerca del mínimo (5 unidades por encima)
                            } else if($total <= 20) {
                                $color = '#ffc107'; // Amarillo si está alrededor de 20
                            } else if($total < 100) {
                                $color = '#28a745'; // Verde si está por debajo de 100
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
                        <?php if($is_first_in_group): ?>
                            <td rowspan="<?php echo $rowspan; ?>" id="total-cell-<?php echo $group_id; ?>" style="vertical-align: middle; text-align: center;">
                                    <?php echo $product->total; ?>
						</td>
                        <?php endif; ?>
                        <td class="actions-cell">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success adjust-stock-btn" data-product-id="<?php echo $product->id; ?>" data-operation="add">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger adjust-stock-btn" data-product-id="<?php echo $product->id; ?>" data-operation="subtract">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                                <a href="index.php?view=editproduct&id=<?php echo $product->id; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-product-btn" data-product-id="<?php echo $product->id; ?>" data-product-name="<?php echo addslashes($product->name); ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
					</tr>
                            <?php endforeach; ?>
				</tbody>
			</table>
			</div>
			<div class="pagination-container">
    <?php
    $px = $page-1;
    if($px > 0):
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
    // Mostrar solo un rango de páginas si hay demasiadas
    $maxPagesToShow = 10;
    $startPage = max(1, $page - floor($maxPagesToShow / 2));
    $endPage = min($npaginas, $startPage + $maxPagesToShow - 1);
    
    // Ajustar el inicio si estamos cerca del final
    if ($endPage - $startPage + 1 < $maxPagesToShow) {
        $startPage = max(1, $endPage - $maxPagesToShow + 1);
    }
    
    // Mostrar primera página y "..." si es necesario
    if ($startPage > 1) {
        echo "<a href='index.php?view=inventary&limit=$limit&page=1' class='btn btn-sm btn-default'>1</a>";
        if ($startPage > 2) {
            echo "<span class='btn btn-sm btn-default disabled'>...</span>";
        }
    }
    
    // Mostrar el rango de páginas
    for($i = $startPage; $i <= $endPage; $i++) {
        $url = "index.php?view=inventary&limit=$limit&page=$i";
        if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
            $url .= "&category_id=".$_GET['category_id'];
        }
        if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
            $url .= "&date_filter=".$_GET['date_filter'];
        }
        
        $active_class = ($page == $i) ? 'btn-primary' : 'btn-default';
        echo "<a href='$url' class='btn btn-sm $active_class'>$i</a> ";
    }
    
    // Mostrar "..." y última página si es necesario
    if ($endPage < $npaginas) {
        if ($endPage < $npaginas - 1) {
            echo "<span class='btn btn-sm btn-default disabled'>...</span>";
        }
        echo "<a href='index.php?view=inventary&limit=$limit&page=$npaginas' class='btn btn-sm btn-default'>$npaginas</a>";
    }
    
    // Botón "Siguiente"
    if($page < $npaginas):
        $url = "index.php?view=inventary&limit=$limit&page=".($page + 1);
        if(isset($_GET['category_id']) && $_GET['category_id'] != "") {
            $url .= "&category_id=".$_GET['category_id'];
        }
        if(isset($_GET['date_filter']) && $_GET['date_filter'] != "") {
            $url .= "&date_filter=".$_GET['date_filter'];
        }
    ?>
    <a class="btn btn-sm btn-default" href="<?php echo $url; ?>">Siguiente <i class="glyphicon glyphicon-chevron-right"></i></a>
    <?php endif; ?>
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

<!-- Agregar los scripts de Bootstrap antes del cierre del body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que Bootstrap esté cargado
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado');
    } else {
        console.log('Bootstrap está cargado correctamente');
        
        // Event listener para botones de ajuste de stock
        document.querySelectorAll('.adjust-stock-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const operation = this.dataset.operation;
                showAdjustModal(productId, operation);
            });
        });

        // Event listener para botones de eliminación
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                showDeleteModal(productId, productName);
            });
        });

        // Event listener para el botón de búsqueda
        document.getElementById('searchButton').addEventListener('click', function() {
            applyFilters();
        });

        // Event listener para el campo de búsqueda (Enter)
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // Event listeners para los selectores
        document.getElementById('category_id').addEventListener('change', applyFilters);
        document.getElementById('availability').addEventListener('change', applyFilters);
        document.getElementById('size').addEventListener('change', applyFilters);
        document.getElementById('date_filter').addEventListener('change', applyFilters);
        document.getElementById('jerseyType').addEventListener('change', applyFilters);

        // Event listener para el campo de límite (Enter)
        document.getElementById('limit').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyLimitFilter();
            }
        });

        // Event listener para el checkbox de selección múltiple
        const selectAllCheckbox = document.getElementById('selectAll');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const deleteSelectedBtn = document.getElementById('deleteSelected');
        
        // Función para actualizar el estado del botón de eliminar seleccionados
        function updateDeleteButton() {
            const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
            deleteSelectedBtn.disabled = selectedCount === 0;
        }
        
        // Event listener para el checkbox "Seleccionar todos"
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateDeleteButton();
            });
        }
        
        // Event listener para los checkboxes individuales
        let lastChecked = null;
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function(e) {
                // Si se presionó la tecla Shift
                if (e.shiftKey && lastChecked) {
                    let start = Array.from(productCheckboxes).indexOf(lastChecked);
                    let end = Array.from(productCheckboxes).indexOf(this);
                    
                    // Determinar el rango de checkboxes a seleccionar
                    let startIndex = Math.min(start, end);
                    let endIndex = Math.max(start, end);
                    
                    // Seleccionar todos los checkboxes en el rango
                    for (let i = startIndex; i <= endIndex; i++) {
                        productCheckboxes[i].checked = this.checked;
                    }
                }
                
                // Actualizar el último checkbox seleccionado
                lastChecked = this;
                
                // Verificar si todos los checkboxes están seleccionados
                const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                }
                updateDeleteButton();
            });
        });
        
        // Event listener para el botón de eliminar seleccionados
        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', function() {
                const selectedProducts = Array.from(productCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                
                if (selectedProducts.length > 0) {
                    // Mostrar el modal de confirmación
                    const modal = new bootstrap.Modal(document.getElementById('deleteSelectedModal'));
                    modal.show();
                    
                    // Configurar el botón de confirmación
                    document.getElementById('confirmDeleteSelected').onclick = function() {
                        // Crear un formulario dinámico para enviar los datos por POST
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'index.php?view=deleteproducts';
                        
                        // Crear el campo oculto con los IDs
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'product_ids';
                        input.value = JSON.stringify(selectedProducts);
                        form.appendChild(input);
                        
                        // Agregar los parámetros de filtro actuales
                        const filterParams = [
                            'category_id', 'availability', 'size', 'date_filter',
                            'search', 'limit', 'jerseyType', 'page'
                        ];
                        
                        filterParams.forEach(param => {
                            const value = document.getElementById(param)?.value;
                            if (value) {
                                const filterInput = document.createElement('input');
                                filterInput.type = 'hidden';
                                filterInput.name = param;
                                filterInput.value = value;
                                form.appendChild(filterInput);
                            }
                        });
                        
                        // Agregar el formulario al documento y enviarlo
                        document.body.appendChild(form);
                        
                        // Cerrar el modal antes de enviar el formulario
                        modal.hide();
                        
                        // Enviar el formulario
                        form.submit();
                    };
                }
            });
        }
    }
});

// Función para aplicar los filtros
function applyFilters() {
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const size = document.getElementById('size').value;
    const dateFilter = document.getElementById('date_filter').value;
    const search = document.getElementById('search').value;
    const jerseyType = document.getElementById('jerseyType').value;
    const limit = document.getElementById('limit').value;
    
    let url = 'index.php?view=inventary';
    
    if (categoryId) url += `&category_id=${categoryId}`;
    if (availability) url += `&availability=${availability}`;
    if (size) url += `&size=${size}`;
    if (dateFilter) url += `&date_filter=${dateFilter}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (jerseyType) url += `&jerseyType=${jerseyType}`;
    if (limit) url += `&limit=${limit}`;

    window.location.href = url;
}

// Función para limpiar los filtros
function clearFilters() {
    const limit = document.getElementById('limit').value;
    let url = 'index.php?view=inventary';
    if (limit) url += `&limit=${limit}`;
    window.location.href = url;
}

// Función para aplicar el límite de productos a mostrar
function applyLimitFilter() {
    const limit = document.getElementById('limit').value;
    if (limit && limit > 0) {
        let url = 'index.php?view=inventary';
        
        // Mantener los filtros existentes
        const categoryId = document.getElementById('category_id').value;
        const availability = document.getElementById('availability').value;
        const size = document.getElementById('size').value;
        const dateFilter = document.getElementById('date_filter').value;
        const search = document.getElementById('search').value;
        const jerseyType = document.getElementById('jerseyType').value;
        
        if (categoryId) url += `&category_id=${categoryId}`;
        if (availability) url += `&availability=${availability}`;
        if (size) url += `&size=${size}`;
        if (dateFilter) url += `&date_filter=${dateFilter}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (jerseyType) url += `&jerseyType=${jerseyType}`;
        
        url += `&limit=${limit}`;
        
        window.location.href = url;
    }
}

// Función para mostrar el modal de ajuste
function showAdjustModal(productId, operationType) {
    console.log('Intentando mostrar modal para producto:', productId, 'operación:', operationType);
    
    try {
        // Establecer los valores del modal
        document.getElementById('productId').value = productId;
        document.getElementById('operationType').value = operationType;
        
        // Configurar el título del modal según la operación
        const modalTitle = document.getElementById('adjustModalLabel');
        modalTitle.textContent = operationType === 'add' ? 'Agregar al Inventario' : 'Restar del Inventario';
        
        // Limpiar el campo de cantidad
        document.getElementById('quantity').value = '';
        
        // Mostrar el modal usando Bootstrap 5
        const modalElement = document.getElementById('adjustModal');
        if (!modalElement) {
            throw new Error('No se encontró el elemento del modal');
        }
        
        console.log('Elemento del modal encontrado');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Enfocar el input de cantidad cuando el modal se muestre
        modalElement.addEventListener('shown.bs.modal', function () {
            document.getElementById('quantity').focus();
        });
    } catch (error) {
        console.error('Error al mostrar el modal:', error);
        alert('Hubo un error al mostrar el modal. Por favor, revise la consola para más detalles.');
    }
}

function submitAdjustForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('adjustForm');
    const productId = document.getElementById('productId').value;
    const quantity = document.getElementById('quantity').value;
    const operationType = document.getElementById('operationType').value;
    
    if (!quantity || quantity <= 0) {
        alert('La cantidad debe ser mayor que 0');
        return false;
    }

    // Crear los datos del formulario
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('operation_type', operationType);
    
    // Enviar la solicitud al servidor
    fetch('index.php?view=update_stock', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('adjustModal'));
            if (modal) {
                modal.hide();
            }
            
            // Mostrar mensaje de éxito
            alert(data.message);
            
            // Recargar la página para actualizar todos los datos
            window.location.reload();
        } else {
            alert(data.message || 'Error al actualizar el stock');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Si el stock se actualizó pero hubo un error en la respuesta, recargar la página
        window.location.reload();
    });
    
    return false;
}

// Función para obtener el tipo de jersey y nombre de un producto
function getProductInfo(row) {
    const nameCell = row.querySelector('td:nth-child(3)');
    const categoryCell = row.querySelector('td:nth-child(4)');
    const name = nameCell ? nameCell.textContent.trim() : '';
    let jerseyType = '';
    
    if (categoryCell) {
        const badges = categoryCell.querySelectorAll('.badge');
        if (badges.length > 1) {
            jerseyType = badges[1].textContent.trim().toLowerCase();
        }
    }
    
    return { name, jerseyType };
}

// Función para resaltar el grupo
function highlightGroup(row) {
    const { name, jerseyType } = getProductInfo(row);
    
    // Resaltar todas las filas que coincidan con el nombre y tipo de jersey
    document.querySelectorAll('tr.product-row').forEach(otherRow => {
        const otherInfo = getProductInfo(otherRow);
        
        if (otherInfo.name === name && otherInfo.jerseyType === jerseyType) {
            otherRow.classList.add('highlighted');
            otherRow.querySelectorAll('td').forEach(cell => {
                cell.classList.add('highlighted');
            });
        }
    });
}

// Función para quitar el resaltado
function unhighlightGroup(row) {
    const { name, jerseyType } = getProductInfo(row);
    
    document.querySelectorAll('tr.product-row').forEach(otherRow => {
        const otherInfo = getProductInfo(otherRow);
        
        if (otherInfo.name === name && otherInfo.jerseyType === jerseyType) {
            otherRow.classList.remove('highlighted');
            otherRow.querySelectorAll('td').forEach(cell => {
                cell.classList.remove('highlighted');
            });
        }
    });
}

// Inicializar los eventos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('tr.product-row').forEach(row => {
        row.addEventListener('mouseenter', () => {
            highlightGroup(row);
        });
        
        row.addEventListener('mouseleave', () => {
            unhighlightGroup(row);
        });
    });
});

function showDeleteModal(productId, productName) {
    document.getElementById('productNameToDelete').textContent = productName;
    document.getElementById('confirmDeleteBtn').href = 'index.php?view=delproduct&id=' + productId;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<style>
.highlighted {
    background-color: #e9ecef !important;
    transition: background-color 0.3s ease;
}

.highlighted td {
    background-color: #e9ecef !important;
}

.highlighted .badge {
    opacity: 1 !important;
}

.highlighted .btn {
    opacity: 1 !important;
}
</style>
</body>
</html>



