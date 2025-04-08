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
        "adulto" => ["S", "M", "L", "XL", "XXL"],
        "niño" => ["16", "18", "20", "22", "24", "26", "28"]
    ];
} elseif($selected_category_name == "Tenis") {
    $available_sizes = [
        "tenis" => ["6", "7", "8", "9"]
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
        "adulto" => ["S", "M", "L", "XL", "XXL"],
        "niño" => ["16", "18", "20", "22", "24", "26", "28"],
        "tenis" => ["6", "7", "8", "9"]
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
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
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
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="search" class="me-2">Buscar:</label>
                <div class="input-group">
                    <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET["search"]) ? $_GET["search"] : ''; ?>" placeholder="Buscar productos...">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="size" class="me-2">Talla:</label>
                <div class="custom-select-wrapper" id="sizeWrapper">
                    <div class="custom-select" id="customSizeSelect">
                        <div class="custom-select__trigger">
                            <?php
                            $selectedSize = isset($_GET["size"]) ? $_GET["size"] : "";
                            $selectedSizeText = $selectedSize ? $selectedSize : "Todas las tallas";
                            ?>
                            <span><?php echo $selectedSizeText; ?></span>
                            <div class="arrow"></div>
                        </div>
                        <div class="custom-options">
                            <div class="custom-option" data-value="">Todas las tallas</div>
                            <?php if(is_array($available_sizes) && !empty($available_sizes)): ?>
                                <?php foreach($available_sizes as $grupo => $tallas): ?>
                                    <div class="custom-option-group">
                                        <div class="custom-option-header"><?php echo ucfirst($grupo); ?></div>
                                        <?php foreach($tallas as $talla): ?>
                                            <div class="custom-option" data-value="<?php echo $talla; ?>"><?php echo $talla; ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <select id="size" name="size" style="display: none;">
                        <option value="">Todas las tallas</option>
                        <?php if(is_array($available_sizes) && !empty($available_sizes)): ?>
                            <?php foreach($available_sizes as $grupo => $tallas): ?>
                                <?php if(is_array($tallas)): ?>
                                    <?php foreach($tallas as $talla): ?>
                                        <option value="<?php echo $talla; ?>" <?php echo (isset($_GET["size"]) && $_GET["size"] == $talla) ? 'selected' : ''; ?>><?php echo $talla; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
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
            <div class="form-group flex-grow-1 flex-md-grow-0 me-2">
                <label for="limit" class="me-2">Mostrar:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="limit" name="limit" min="1" value="<?php echo isset($_GET['limit']) ? $_GET['limit'] : 100; ?>" style="width: 80px;">
                    <button class="btn btn-primary" type="button" onclick="applyLimitFilter()">
                        <i class="bi bi-filter"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" id="clearFiltersBtn" onclick="clearFilters()">Limpiar filtros</button>
                <div class="ms-auto">
                    <a href="index.php?view=inventary&order=<?php echo $order == 'desc' ? 'asc' : 'desc'; ?><?php 
if(isset($_GET["category_id"])) echo "&category_id=".$_GET["category_id"];
if(isset($_GET["search"])) echo "&search=".$_GET["search"];
if(isset($_GET["availability"])) echo "&availability=".$_GET["availability"];
if(isset($_GET["date_filter"])) echo "&date_filter=".$_GET["date_filter"];
if(isset($_GET["limit"])) echo "&limit=".$_GET["limit"];
if(isset($_GET["size"])) echo "&size=".$_GET["size"];
                    ?>" class="btn btn-secondary">
                        <i class="bi bi-sort-<?php echo $order == 'desc' ? 'down' : 'up'; ?>"></i> 
        <?php echo $order == 'desc' ? 'Ascendente' : 'Descendente'; ?>
                    </a>
                </div>
            </div>
        </div>
        <!-- ===== FIN DE SECCIÓN PROTEGIDA ===== -->

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
                <form id="adjustForm" method="post" action="index.php?view=adjustinventory" onsubmit="return submitAdjustForm(event)">
                    <input type="hidden" name="product_id" id="productId">
                    <input type="hidden" name="operation_type" id="operationType">
                    <!-- Campos ocultos para mantener los filtros -->
                    <input type="hidden" name="category_id" id="category_id">
                    <input type="hidden" name="search" id="search">
                    <input type="hidden" name="availability" id="availability">
                    <input type="hidden" name="date_filter" id="date_filter">
                    <input type="hidden" name="limit" id="limit">
                    <input type="hidden" name="size" id="size">
                    <input type="hidden" name="page" id="page">
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

			// Si el límite es diferente al total y hay filtros, aplicar filtros en el servidor
			if (!$is_full_list && (isset($_GET['category_id']) || isset($_GET['search']) || isset($_GET['size']) || isset($_GET['availability']) || isset($_GET['date_filter']))) {
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
						<th style="width: 80px;">Talla</th>
                                <th style="width: 120px;">Categoría</th>
                                <th style="width: 100px;">Precio de Entrada</th>
                                <th style="width: 100px;">Precio de Salida</th>
                                <th style="width: 80px;">Unidad</th>
						<th style="width: 100px;">Mínima en Inventario</th>
                                <th style="width: 80px;">Disponible</th>
                                <th style="width: 80px;">Total</th>
                                <th style="width: 150px;">Acciones</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    $current_total = null;
                    $rowspan = 1;
                    $products_count = count($curr_products);
                    $group_id = 0;
                    $total_groups = [];
                    
                    // Primero, agrupar los productos por total
                    foreach($curr_products as $index => $product) {
                        if($current_total !== $product->total) {
                            $current_total = $product->total;
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
                        $is_first_in_group = $product === reset($current_group);
                    ?>
                    <tr class="total-group-<?php echo $group_id; ?>">
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
                            </div>
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

<!-- Agregar los scripts de Bootstrap antes del cierre del body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Variable global para almacenar el mensaje de la operación
let lastOperationMessage = '';

// Variables globales para los datos
let allProducts = <?php echo json_encode($products); ?>;
let categoriesData = <?php echo json_encode($categories); ?>;
let filteredProducts = [...allProducts];

function showAdjustModal(productId, operationType) {
    // Establecer los valores del modal
    document.getElementById('productId').value = productId;
    document.getElementById('operationType').value = operationType;
    document.getElementById('adjustModalLabel').textContent = 
        operationType === 'add' ? 'Agregar al Inventario' : 'Restar del Inventario';
    
    // Establecer los valores de los filtros actuales
    const urlParams = new URLSearchParams(window.location.search);
    
    // Copiar todos los parámetros de filtro
    const filterParams = ['category_id', 'search', 'availability', 'date_filter', 'limit', 'size', 'page'];
    filterParams.forEach(param => {
        if (urlParams.has(param)) {
            const value = urlParams.get(param);
            document.getElementById(param).value = value;
        }
    });
    
    // Mostrar el modal usando Bootstrap 5
    const modalElement = document.getElementById('adjustModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Enfocar el input de cantidad cuando el modal se muestre completamente
        modalElement.addEventListener('shown.bs.modal', function () {
            document.getElementById('quantity').focus();
        });
    } else {
        console.error('No se encontró el elemento del modal');
    }
}

function submitAdjustForm(event) {
    event.preventDefault(); // Prevenir el envío normal del formulario
    
    const form = document.getElementById('adjustForm');
    const quantity = document.getElementById('quantity').value;
    const operationType = document.getElementById('operationType').value;
    
    if (quantity <= 0) {
        alert('La cantidad debe ser mayor que 0');
        return false;
    }
    
    // Enviar el formulario usando fetch
    fetch('index.php?view=adjustinventory', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Guardar el mensaje en localStorage
            localStorage.setItem('inventoryAlert', data.message);
            // Redirigir usando la URL proporcionada por el servidor
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Error al procesar la solicitud');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Si hay un error pero los cambios se aplicaron, recargar la página
        window.location.reload();
    });
    
    return false;
}

// Verificar si hay una alerta pendiente al cargar la página
window.addEventListener('load', function() {
    setTimeout(function() {
        const alertMessage = localStorage.getItem('inventoryAlert');
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
            
            // Eliminar el mensaje del localStorage
            localStorage.removeItem('inventoryAlert');
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
    if (!clearFiltersBtn) return;

    // Obtener valores actuales
    const searchTerm = document.getElementById('search').value.trim();
        const categoryId = document.getElementById('category_id').value;
        const availability = document.getElementById('availability').value;
    const size = document.getElementById('size').value;
        const dateFilter = document.getElementById('date_filter').value;
        
    console.log('Valores actuales:', {
        searchTerm,
        categoryId,
        availability,
        size,
        dateFilter
    });
    
    // Verificar si hay algún filtro activo
    const hasActiveFilters = 
        searchTerm !== '' || // Hay texto en la búsqueda
        categoryId !== '' || // No es "Todas las categorías"
        availability !== '' || // No es "Todas las cantidades"
        size !== '' || // No es "Todas las tallas"
        dateFilter !== ''; // No es "Todas las fechas"

    console.log('¿Hay filtros activos?', hasActiveFilters);
    
    // Mostrar u ocultar el botón
    clearFiltersBtn.style.display = hasActiveFilters ? '' : 'none';
    console.log('Estado del botón:', clearFiltersBtn.style.display);
}

// Función para limpiar filtros
function clearFilters() {
    // Obtener el valor actual del filtro "Mostrar"
    const currentLimit = document.getElementById('limit').value;
    
    // Limpiar todos los filtros excepto el de "Mostrar"
    const url = new URL(window.location.href);
    url.searchParams.delete('category_id');
    url.searchParams.delete('size');
    url.searchParams.delete('availability');
    url.searchParams.delete('date_filter');
    url.searchParams.delete('search');
    url.searchParams.delete('page');
    
    // Mantener el filtro "Mostrar"
    if (currentLimit) {
        url.searchParams.set('limit', currentLimit);
    }
    
    window.location.href = url.toString();
}

// Función para filtrar productos
function filterProducts() {
    // Obtener valores actuales de los filtros
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const size = document.getElementById('size').value;
    const dateFilter = document.getElementById('date_filter').value;
    
    // Construir la URL con los filtros
    let url = 'index.php?view=inventary';
    
    if (searchTerm) url += '&search=' + encodeURIComponent(searchTerm);
    if (categoryId) url += '&category_id=' + encodeURIComponent(categoryId);
    if (availability) url += '&availability=' + encodeURIComponent(availability);
    if (size) url += '&size=' + encodeURIComponent(size);
    if (dateFilter) url += '&date_filter=' + encodeURIComponent(dateFilter);
    
    // Redirigir a la URL con los filtros
    window.location.href = url;
}

// Inicializar todos los select personalizados cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...

    // Evento para el botón de búsqueda
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', filterProducts);
    }

    // Evento para el input de búsqueda al presionar Enter
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterProducts();
            }
        });

        // Evento para actualizar el botón de limpiar filtros mientras se escribe
        searchInput.addEventListener('input', function() {
            updateClearFiltersButton();
        });
    }

    // Evento para los select de filtros
    const filterSelects = document.querySelectorAll('select[id="category_id"], select[id="availability"], select[id="size"], select[id="date_filter"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterProducts();
            updateClearFiltersButton();
        });
    });

    // Inicializar el botón de limpiar filtros
    updateClearFiltersButton();

    // ... rest of existing code ...
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

// Agregar al final del script
document.addEventListener('DOMContentLoaded', function() {
    // Ocultar automáticamente el mensaje de categoría vacía después de 5 segundos
    const categoryEmptyAlert = document.getElementById('categoryEmptyAlert');
    if (categoryEmptyAlert) {
        setTimeout(() => {
            categoryEmptyAlert.classList.remove('show');
            setTimeout(() => {
                categoryEmptyAlert.remove();
            }, 150); // Tiempo para la animación de fade
        }, 5000); // 5 segundos
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const sizeSelect = document.getElementById('customSizeSelect');
    
    if(categorySelect && sizeSelect) {
        categorySelect.addEventListener('change', function() {
            // Recargar la página para actualizar las tallas disponibles
            const url = new URL(window.location.href);
            url.searchParams.set('category_id', this.value);
            url.searchParams.delete('size'); // Resetear el filtro de tallas
            window.location.href = url.toString();
        });
    }
});

// Función para aplicar la búsqueda
function applySearch() {
    const searchTerm = document.getElementById('search').value;
    const url = new URL(window.location.href);
    
    // Mantener los filtros existentes
    const categoryId = document.getElementById('category_id').value;
    const availability = document.getElementById('availability').value;
    const size = document.getElementById('size').value;
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
    if (size) url.searchParams.set('size', size);
    if (dateFilter) url.searchParams.set('date_filter', dateFilter);
    if (limit) url.searchParams.set('limit', limit);
    
    window.location.href = url.toString();
}

// Inicializar todos los select personalizados cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...

    // Evento para el botón de búsqueda
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', applySearch);
    }

    // Evento para el input de búsqueda al presionar Enter
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applySearch();
            }
        });

        // Evento para actualizar el botón de limpiar filtros mientras se escribe
        searchInput.addEventListener('input', function() {
            updateClearFiltersButton();
        });
    }

    // Evento para los select de filtros
    const filterSelects = document.querySelectorAll('select[id="category_id"], select[id="availability"], select[id="size"], select[id="date_filter"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            updateClearFiltersButton();
        });
    });

    // Inicializar el botón de limpiar filtros
    updateClearFiltersButton();

    // ... rest of existing code ...
});

// ... existing code ...
function initCustomSelect(select) {
    const trigger = select.querySelector('.custom-select__trigger');
    const options = select.querySelector('.custom-options');
    const originalSelect = select.nextElementSibling;
    
    if (!trigger || !options || !originalSelect) return;
    
    // Función para actualizar el estado visual del select
    function updateSelectState(value, text) {
        // Actualizar el select original
        originalSelect.value = value;
        
        // Actualizar el texto mostrado
        trigger.querySelector('span').textContent = text;
        
        // Actualizar las clases selected
        options.querySelectorAll('.custom-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.dataset.value === value) {
                opt.classList.add('selected');
            }
        });
    }
    
    // Inicializar el estado del select
    const initialValue = originalSelect.value;
    const initialOption = options.querySelector(`.custom-option[data-value="${initialValue}"]`);
    if (initialOption) {
        updateSelectState(initialValue, initialOption.textContent);
    }
    
    // Abrir/cerrar al hacer clic en el trigger
    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Cerrar todos los demás select
        document.querySelectorAll('.custom-select').forEach(s => {
            if (s !== select) {
                s.classList.remove('open');
                s.querySelector('.custom-options').style.display = 'none';
            }
        });
        
        // Abrir/cerrar el select actual
        const isOpen = select.classList.contains('open');
        select.classList.toggle('open');
        
        // Asegurar que las opciones sean visibles
        if (!isOpen) {
            options.style.display = 'block';
            options.style.opacity = '1';
            options.style.visibility = 'visible';
            options.style.zIndex = '1000';
        } else {
            options.style.display = 'none';
            options.style.opacity = '0';
            options.style.visibility = 'hidden';
        }
    });
    
    // Cerrar al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!select.contains(e.target)) {
            select.classList.remove('open');
            options.style.display = 'none';
            options.style.opacity = '0';
            options.style.visibility = 'hidden';
        }
    });
    
    // Manejar la selección de opciones
    const optionItems = options.querySelectorAll('.custom-option');
    optionItems.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const value = option.dataset.value;
            const text = option.textContent;
            
            updateSelectState(value, text);
            select.classList.remove('open');
            options.style.display = 'none';
            options.style.opacity = '0';
            options.style.visibility = 'hidden';
            
            // Actualizar los filtros
            const url = new URL(window.location.href);
            url.searchParams.set(originalSelect.name, value);
            window.location.href = url.toString();
        });
    });
}

// Inicializar todos los select personalizados cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    
    // Inicializar el botón de limpiar filtros
    updateClearFiltersButton();
    
    // Inicializar los select personalizados
    const customSelects = document.querySelectorAll('.custom-select');
    console.log('Selects encontrados:', customSelects.length);
    
    customSelects.forEach((select, index) => {
        console.log(`Inicializando select ${index + 1}`);
        
        const trigger = select.querySelector('.custom-select__trigger');
        const options = select.querySelector('.custom-options');
        const originalSelect = select.nextElementSibling;
        
        if (!trigger || !options || !originalSelect) {
            console.log(`Select ${index + 1} no tiene todos los elementos necesarios`);
            return;
        }
        
        // Evento para abrir/cerrar el select
        trigger.addEventListener('click', function(e) {
            console.log('Click en trigger');
            e.stopPropagation();
            
            // Cerrar todos los demás select
            customSelects.forEach(s => {
                if (s !== select) {
                    s.classList.remove('open');
                    s.querySelector('.custom-options').style.display = 'none';
                }
            });
            
            // Abrir el select actual
            select.classList.add('open');
            options.style.display = 'block';
            options.style.zIndex = '1000';
        });
        
        // Evento para cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!select.contains(e.target)) {
                console.log('Click fuera del select');
                select.classList.remove('open');
                options.style.display = 'none';
            }
        });
        
        // Eventos para las opciones
        const optionItems = options.querySelectorAll('.custom-option');
        optionItems.forEach(option => {
            option.addEventListener('click', function(e) {
                console.log('Click en opción');
                e.stopPropagation();
                
                const value = option.dataset.value;
                const text = option.textContent;
                
                // Actualizar el select original
                originalSelect.value = value;
                
                // Actualizar el texto mostrado
                trigger.querySelector('span').textContent = text;
                
                // Actualizar las clases selected
                optionItems.forEach(opt => {
                    opt.classList.remove('selected');
                });
                option.classList.add('selected');
                
                // Cerrar el select
                select.classList.remove('open');
                options.style.display = 'none';
                
                // Actualizar los filtros
                const url = new URL(window.location.href);
                url.searchParams.set(originalSelect.name, value);
                
                // Forzar la actualización del botón de limpiar filtros
                setTimeout(() => {
                    updateClearFiltersButton();
                }, 100);
                
                window.location.href = url.toString();
            });
        });
    });
    
    // Manejar el cambio de categoría para el filtro de tallas
    const categorySelect = document.getElementById('category_id');
    const sizeSelect = document.getElementById('customSizeSelect');
    
    if(categorySelect && sizeSelect) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.options[this.selectedIndex].text;
            const categoriesToHide = ["Gorras", "Variado", "Balón"];
            
            if(categoriesToHide.includes(selectedCategory)) {
                sizeSelect.style.display = 'none';
                document.getElementById('size').value = '';
                document.querySelector('#customSizeSelect .custom-select__trigger span').textContent = 'Todas las tallas';
            } else {
                sizeSelect.style.display = 'block';
            }
        });
    }
});
// ... existing code ...

// En la sección del JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el botón de limpiar filtros
    updateClearFiltersButton();
    
    // Verificar si hay una talla seleccionada en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectedSize = urlParams.get('size');
    if (selectedSize) {
        const sizeSelect = document.getElementById('size');
        if (sizeSelect) {
            sizeSelect.value = selectedSize;
            // Actualizar el texto mostrado en el select personalizado
            const customSizeSelect = document.querySelector('#customSizeSelect .custom-select__trigger span');
            if (customSizeSelect) {
                customSizeSelect.textContent = selectedSize;
            }
        }
    }
    
    // Resto del código de inicialización...
});

function highlightTotalGroup(groupId) {
    // Resaltar todas las filas del grupo
    const rows = document.querySelectorAll(`tr.total-group-${groupId}`);
    rows.forEach(row => {
        row.classList.add('highlighted');
    });
    
    // Resaltar la celda total
    const totalCell = document.getElementById(`total-cell-${groupId}`);
    if (totalCell) {
        totalCell.classList.add('highlighted');
    }
}

function unhighlightTotalGroup(groupId) {
    // Quitar el resaltado de todas las filas del grupo
    const rows = document.querySelectorAll(`tr.total-group-${groupId}`);
    rows.forEach(row => {
        row.classList.remove('highlighted');
    });
    
    // Quitar el resaltado de la celda total
    const totalCell = document.getElementById(`total-cell-${groupId}`);
    if (totalCell) {
        totalCell.classList.remove('highlighted');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Agrupar las filas por su groupId
    const groups = {};
    document.querySelectorAll('tr[class^="total-group-"]').forEach(row => {
        const groupId = row.className.match(/total-group-(\d+)/)[1];
        if (!groups[groupId]) {
            groups[groupId] = [];
        }
        groups[groupId].push(row);
    });

    // Agregar eventos a todas las filas y celdas totales
    Object.keys(groups).forEach(groupId => {
        // Agregar eventos a las filas del grupo
        groups[groupId].forEach(row => {
            row.addEventListener('mouseenter', function() {
                highlightTotalGroup(groupId);
            });
            
            row.addEventListener('mouseleave', function(e) {
                const relatedTarget = e.relatedTarget;
                if (!relatedTarget || !relatedTarget.closest(`tr.total-group-${groupId}`)) {
                    unhighlightTotalGroup(groupId);
                }
            });
        });

        // Agregar eventos a la celda total
        const totalCell = document.getElementById(`total-cell-${groupId}`);
        if (totalCell) {
            totalCell.addEventListener('mouseenter', function() {
                highlightTotalGroup(groupId);
            });
            
            totalCell.addEventListener('mouseleave', function(e) {
                const relatedTarget = e.relatedTarget;
                if (!relatedTarget || !relatedTarget.closest(`tr.total-group-${groupId}`)) {
                    unhighlightTotalGroup(groupId);
                }
            });
        }
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

<style>
/* Estilos base para la tabla */
tr:hover {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilos para la celda total */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
}

/* Cuando la celda total está resaltada */
[id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Asegurar que el hover de la tabla no interfiera */
tr:hover [id^="total-cell-"] {
    background-color: rgba(0,0,0,.075) !important;
}

/* Mantener el resaltado consistente para todas las filas del grupo */
tr:hover td:not([id^="total-cell-"]) {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilos para los select personalizados */
.custom-select-wrapper {
    position: relative;
    width: 200px;
}

.custom-select {
    position: relative;
    display: flex;
    flex-direction: column;
    border: 1px solid rgb(177 183 193);
    border-radius: 0.375rem;
    background-color: white;
    cursor: pointer;
    z-index: 1000;
}

/* Ocultar el botón de debug */
button[data-bs-toggle="modal"][data-bs-target^="#debugModal"] {
    display: none !important;
}

.custom-select__trigger {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 0.375rem;
    font-size: 14px;
    font-weight: 400;
    color: #000;
    background-color: white;
    cursor: pointer;
}

.custom-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    display: none;
    background-color: white;
    border: 1px solid #6c757d;
    border-radius: 4px;
    margin-top: 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1001;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.custom-select.open .custom-options {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.custom-option {
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.custom-option:hover {
    background-color: #f8f9fa;
}

.custom-option.selected {
    background-color: #e9ecef;
}

.custom-option-group {
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 5px;
}

.custom-option-header {
    padding: 8px 12px;
    font-weight: bold;
    background-color: #f8f9fa;
    color: #6c757d;
}

.custom-option-group .custom-option {
    padding-left: 24px;
}

#customSizeSelect {
    display: block;
}

/* Estilos para resaltar el total */
.total-cell {
    position: relative !important;
    background: none !important;
    border: none !important;
    padding: 0 !important;
}

/* Eliminar todas las líneas de la tabla para la celda total */
table td.total-cell,
tr td.total-cell,
tbody tr td.total-cell,
.table td.total-cell,
.table tbody tr td.total-cell,
.table-bordered td.total-cell,
.table-bordered tbody tr td.total-cell {
    border: none !important;
    border-top: none !important;
    border-bottom: none !important;
    border-left: none !important;
    border-right: none !important;
    background: none !important;
}

/* Contenedor del valor total */
.total-value[data-rowspan] {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    border: 1px solid #dee2e6 !important;
    border-top-width: 1px !important;
    border-bottom-width: 1px !important;
    background: white !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 2 !important;
}

/* Ocultar el valor cuando no es el primero del grupo */
.total-value.hidden {
    display: none !important;
}

/* Estilo para el resaltado */
.total-cell.highlighted .total-value[data-rowspan] {
    background-color: #e9ecef !important;
}

/* Resto de los estilos existentes */
.total-cell {
    background-color: #fff;
    transition: background-color 0.3s ease;
    position: relative;
    z-index: 0;
    padding: 0 !important;
    border: none !important;
}

/* Eliminar todas las líneas de la celda total */
.total-cell,
.total-cell:not(:last-child),
.total-cell:not(:first-child),
tr:not(:last-child) .total-cell,
tr .total-cell {
    border: none !important;
    border-top: none !important;
    border-bottom: none !important;
}

/* Asegurar que solo los bordes externos sean visibles */
.total-cell {
    border-left: 1px solid #dee2e6 !important;
    border-right: 1px solid #dee2e6 !important;
}

/* El primer elemento del grupo tiene borde superior */
tr:first-child .total-cell,
tr.total-group:first-child .total-cell {
    border-top: 1px solid #dee2e6 !important;
}

/* El último elemento del grupo tiene borde inferior */
tr:last-child .total-cell,
tr.total-group:last-child .total-cell {
    border-bottom: 1px solid #dee2e6 !important;
}

.total-value {
    padding: 8px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.total-value[data-rowspan] {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: inherit;
    z-index: 1;
}

.total-value.hidden {
    display: none;
}

.total-cell.highlighted {
    background-color: #e9ecef !important;
}

/* Asegurar que el resaltado tenga prioridad sobre otros estilos */
tr:hover .total-cell.highlighted {
    background-color: #e9ecef !important;
}

/* Estilos para la celda de acciones */
.actions-cell {
    white-space: nowrap;
    width: 1%;
    padding: 8px !important;
    position: relative;
    z-index: 2;
    text-align: center;
    background-color: #fff;
}

.actions-cell .btn-group {
    display: inline-flex;
    gap: 4px;
}

.actions-cell .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    margin: 0;
}

/* Asegurar que la celda de acciones siempre esté visible */
tr:hover .actions-cell {
    background-color: #fff;
}

/* Estilos para simular el rowspan */
.total-group {
    position: relative;
}

.total-group .total-cell {
    border: none !important;
}

.total-group:last-child .total-cell {
    border: none !important;
}

/* Asegurar que las celdas adyacentes mantengan sus bordes */
td:not(.total-cell) {
    border: 1px solid #dee2e6 !important;
}

/* Estilos para la transición suave del resaltado */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
    pointer-events: none;
}

[id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Desactivar el hover de la tabla para la celda total */
tr:hover [id^="total-cell-"] {
    background-color: transparent !important;
}

/* Mantener el resaltado cuando se pasa entre filas del mismo grupo */
tr:hover [id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Desactivar el hover de la tabla para todas las filas */
tr:hover {
    background-color: transparent !important;
}

/* Aplicar el resaltado solo a las celdas que no son total */
tr:hover td:not([id^="total-cell-"]) {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilos para el resaltado de la celda total */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
}

[id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Asegurar que el hover de la tabla no interfiera con el resaltado personalizado */
tr:hover [id^="total-cell-"] {
    background-color: transparent !important;
}

/* Mantener el resaltado cuando se pasa entre filas del mismo grupo */
tr:hover [id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilo para el hover de la fila */
tr:hover {
    background-color: rgba(0,0,0,.075);
}

/* Excluir la celda total del hover de la fila */
tr:hover td:not([id^="total-cell-"]) {
    background-color: rgba(0,0,0,.075);
}

/* Asegurar que la celda total se ilumine con el mismo color que las celdas normales */
tr:hover [id^="total-cell-"] {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilos para el resaltado de la celda total */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
}

[id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Asegurar que el hover de la tabla no interfiera con el resaltado personalizado */
tr:hover [id^="total-cell-"] {
    background-color: transparent !important;
}

/* Mantener el resaltado cuando se pasa entre filas del mismo grupo */
tr:hover [id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilo para el hover de la fila */
tr:hover {
    background-color: #f8f9fa;
}

/* Excluir la celda total del hover de la fila */
tr:hover td:not([id^="total-cell-"]) {
    background-color: #f8f9fa;
}

/* Asegurar que la celda total se ilumine con el mismo color que las celdas normales */
tr:hover [id^="total-cell-"] {
    background-color: #f8f9fa !important;
}

/* Estilos para el resaltado */
tr.highlighted {
    background-color: rgba(0,0,0,.025) !important;
}

tr.highlighted td {
    background-color: rgba(0,0,0,.025) !important;
}

/* La fila activa (sobre la que está el mouse) */
tr:hover {
    background-color: rgba(0,0,0,.075) !important;
}

tr:hover td {
    background-color: rgba(0,0,0,.075) !important;
}

/* Transición suave */
tr, tr td {
    transition: background-color 0.15s ease;
}

/* Eliminar todas las reglas duplicadas */
tr:hover {
    background-color: transparent !important;
}

tr:hover td {
    background-color: transparent !important;
}

[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
}

[id^="total-cell-"].highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilos para el resaltado */
tr.highlighted {
    background-color: rgba(0,0,0,.025) !important;
}

tr.highlighted td {
    background-color: rgba(0,0,0,.025) !important;
}

/* La fila activa (sobre la que está el mouse) */
tr:hover {
    background-color: rgba(0,0,0,.075) !important;
}

tr:hover td {
    background-color: rgba(0,0,0,.075) !important;
}

/* Transición suave */
tr, tr td {
    transition: background-color 0.15s ease;
}

/* Estilos para la celda total */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
}

[id^="total-cell-"]:hover {
    background-color: rgba(0,0,0,.075) !important;
}

/* Resaltar todo el grupo cuando se pasa el mouse sobre la celda total */
[id^="total-cell-"]:hover ~ tr.total-group-* {
    background-color: rgba(0,0,0,.025) !important;
}

[id^="total-cell-"]:hover ~ tr.total-group-* td {
    background-color: rgba(0,0,0,.025) !important;
}

/* ... existing code ... */
<style>
/* Estilos base para el resaltado */
tr.highlighted {
    background-color: rgba(0,0,0,.075) !important;
}

tr.highlighted td {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilo para la fila activa */
tr:hover {
    background-color: rgba(0,0,0,.075) !important;
}

tr:hover td {
    background-color: rgba(0,0,0,.075) !important;
}

/* Estilos para la celda total */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
}

[id^="total-cell-"]:hover {
    background-color: rgba(0,0,0,.075) !important;
}

/* Transición suave para todos los elementos */
tr, tr td, [id^="total-cell-"] {
    transition: background-color 0.15s ease;
}
</style>

<script>
function highlightTotalGroup(groupId) {
    // Resaltar todas las filas del grupo
    const rows = document.querySelectorAll(`tr.total-group-${groupId}`);
    rows.forEach(row => {
        row.classList.add('highlighted');
    });
    
    // Resaltar la celda total
    const totalCell = document.getElementById(`total-cell-${groupId}`);
    if (totalCell) {
        totalCell.classList.add('highlighted');
    }
}

function unhighlightTotalGroup(groupId) {
    // Quitar el resaltado de todas las filas del grupo
    const rows = document.querySelectorAll(`tr.total-group-${groupId}`);
    rows.forEach(row => {
        row.classList.remove('highlighted');
    });
    
    // Quitar el resaltado de la celda total
    const totalCell = document.getElementById(`total-cell-${groupId}`);
    if (totalCell) {
        totalCell.classList.remove('highlighted');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Agrupar las filas por su groupId
    const groups = {};
    document.querySelectorAll('tr[class^="total-group-"]').forEach(row => {
        const groupId = row.className.match(/total-group-(\d+)/)[1];
        if (!groups[groupId]) {
            groups[groupId] = [];
        }
        groups[groupId].push(row);
    });

    // Agregar eventos a todas las filas y celdas totales
    Object.keys(groups).forEach(groupId => {
        // Agregar eventos a las filas del grupo
        groups[groupId].forEach(row => {
            row.addEventListener('mouseenter', function() {
                highlightTotalGroup(groupId);
            });
            
            row.addEventListener('mouseleave', function(e) {
                const relatedTarget = e.relatedTarget;
                if (!relatedTarget || !relatedTarget.closest(`tr.total-group-${groupId}`)) {
                    unhighlightTotalGroup(groupId);
                }
            });
        });

        // Agregar eventos a la celda total
        const totalCell = document.getElementById(`total-cell-${groupId}`);
        if (totalCell) {
            totalCell.addEventListener('mouseenter', function() {
                highlightTotalGroup(groupId);
            });
            
            totalCell.addEventListener('mouseleave', function(e) {
                const relatedTarget = e.relatedTarget;
                if (!relatedTarget || !relatedTarget.closest(`tr.total-group-${groupId}`)) {
                    unhighlightTotalGroup(groupId);
                }
            });
        }
    });
});
</script>

<style>
/* Estilos para la celda total */
[id^="total-cell-"] {
    transition: background-color 0.15s ease;
    background-color: transparent !important;
    pointer-events: auto !important; /* Permitir eventos del mouse */
    position: relative;
    z-index: 1;
}

[id^="total-cell-"]:hover {
    background-color: rgba(0,0,0,.075) !important;
}

/* Resaltar el grupo cuando se pasa el mouse sobre la celda total */
[id^="total-cell-"]:hover ~ tr[class^="total-group-"] {
    background-color: rgba(0,0,0,.025) !important;
}

[id^="total-cell-"]:hover ~ tr[class^="total-group-"] td {
    background-color: rgba(0,0,0,.025) !important;
}
</style>