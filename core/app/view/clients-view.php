<?php
$users = PersonData::getClients();

// Obtener ciudades y estados únicos
$cities = array_unique(array_column($users, 'city'));
$states = array_unique(array_column($users, 'state'));
sort($cities);
sort($states);
?>
<div class="row">
	<div class="col-md-12">

		<h1>Directorio de Clientes 
			<small class="text-muted">
				(<?php echo count($users); ?> clientes registrados)
			</small>
		</h1>
	<div class="">
	<a href="index.php?view=newclient" class="btn btn-secondary"><i class='fa fa-smile-o'></i> Nuevo Cliente</a>
<?php
require_once 'core/app/model/ConfigurationData.php';
$configs = ConfigurationData::getAll();
$word_enabled = false;
$excel_enabled = false;
$pdf_enabled = false;

foreach($configs as $conf) {
    if($conf->short == "active_reports_word" && $conf->val == 1) $word_enabled = true;
    if($conf->short == "active_reports_excel" && $conf->val == 1) $excel_enabled = true;
    if($conf->short == "active_reports_pdf" && $conf->val == 1) $pdf_enabled = true;
}
?>

<div class="btn-group pull-right">
  <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important;">
    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end" style="background-color: #28a745; border: none;">
    <?php if($word_enabled): ?>
    <li><a class="dropdown-item text-white" href="index.php?view=download-clients" style="background-color: transparent !important; transition: color 0.3s ease;">Word 2007 (.docx)</a></li>
    <?php endif; ?>
    <?php if($excel_enabled): ?>
    <li><a class="dropdown-item text-white" href="index.php?view=download-clients-excel" style="background-color: transparent !important; transition: color 0.3s ease;">Excel (.xlsx)</a></li>
    <?php endif; ?>
    <?php if($pdf_enabled): ?>
    <li><a class="dropdown-item text-white" href="index.php?view=download-clients-pdf" style="background-color: transparent !important; transition: color 0.3s ease;">PDF (.pdf)</a></li>
    <?php endif; ?>
  </ul>
</div>
</div>	
<br>
<div class="card">
	<div class="card-header">
		CLIENTES
	</div>
		<div class="card-body">
			<div class="row mb-3">
				<div class="col-md-12">
					<form method="get" class="row g-3">
						<input type="hidden" name="view" value="clients">
						
						<div class="col-12 col-sm-6 col-md-3">
							<label for="city" class="form-label">Ciudad/Municipio:</label>
							<select class="form-select" id="city" name="city">
								<option value="">Todas las ciudades</option>
								<?php foreach($cities as $city): ?>
									<option value="<?php echo $city; ?>" <?php echo (isset($_GET['city']) && $_GET['city'] == $city) ? 'selected' : ''; ?>>
										<?php echo $city; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="col-12 col-sm-6 col-md-3">
							<label for="state" class="form-label">Estado:</label>
							<select class="form-select" id="state" name="state">
								<option value="">Todos los estados</option>
								<?php foreach($states as $state): ?>
									<option value="<?php echo $state; ?>" <?php echo (isset($_GET['state']) && $_GET['state'] == $state) ? 'selected' : ''; ?>>
										<?php echo $state; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="col-12 col-sm-6 col-md-3">
							<label for="search" class="form-label">Buscar:</label>
							<input type="text" class="form-control" id="search" name="search" placeholder="Buscar clientes..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
						</div>

						<div class="col-6 col-sm-3 col-md-2">
							<label for="limit" class="form-label">Mostrar:</label>
							<select class="form-select" id="limit" name="limit">
								<option value="10" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 10) ? 'selected' : ''; ?>>10</option>
								<option value="25" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 25) ? 'selected' : ''; ?>>25</option>
								<option value="50" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 50) ? 'selected' : ''; ?>>50</option>
								<option value="100" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 100) ? 'selected' : ''; ?>>100</option>
								<option value="all" <?php echo (isset($_GET['limit']) && $_GET['limit'] == 'all') ? 'selected' : ''; ?>>Todos</option>
							</select>
						</div>

						<div class="col-6 col-sm-3 col-md-1 d-flex align-items-end">
							<div class="d-flex gap-2 w-100">
								<button type="submit" class="btn btn-primary">Filtrar</button>
								<a href="index.php?view=clients" class="btn btn-secondary">Limpiar</a>
							</div>
						</div>
					</form>
				</div>
			</div>

			<?php
			// Aplicar filtros si existen
			$all_users = $users;
			
			if(isset($_GET['city']) && !empty($_GET['city'])) {
				$users = array_filter($users, function($user) {
					return $user->city == $_GET['city'];
				});
			}

			if(isset($_GET['state']) && !empty($_GET['state'])) {
				$users = array_filter($users, function($user) {
					return $user->state == $_GET['state'];
				});
			}

			if(isset($_GET['search']) && !empty($_GET['search'])) {
				$search = strtolower($_GET['search']);
				$users = array_filter($users, function($user) use ($search) {
					return strpos(strtolower($user->name), $search) !== false ||
						   strpos(strtolower($user->lastname), $search) !== false ||
						   strpos(strtolower($user->email1), $search) !== false ||
						   strpos(strtolower($user->phone1), $search) !== false;
				});
			}

			// Configuración de paginación
			$total_users = count($users);
			$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
			$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
			
			if($limit !== 'all') {
				$total_pages = ceil($total_users / $limit);
				$offset = ($page - 1) * $limit;
				$users = array_slice($users, $offset, $limit);
			}

			// Función para mostrar la paginación
			function showPagination($page, $total_pages, $limit) {
				if($limit !== 'all' && $total_pages > 1): ?>
					<nav aria-label="Page navigation" class="my-3">
						<ul class="pagination justify-content-center mb-0">
							<?php if($page > 1): ?>
								<li class="page-item">
									<a class="page-link" href="?view=clients&page=<?php echo $page-1; ?>&city=<?php echo isset($_GET['city']) ? $_GET['city'] : ''; ?>&state=<?php echo isset($_GET['state']) ? $_GET['state'] : ''; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>&limit=<?php echo $limit; ?>">Anterior</a>
								</li>
							<?php endif; ?>

							<?php for($i = 1; $i <= $total_pages; $i++): ?>
								<li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
									<a class="page-link" href="?view=clients&page=<?php echo $i; ?>&city=<?php echo isset($_GET['city']) ? $_GET['city'] : ''; ?>&state=<?php echo isset($_GET['state']) ? $_GET['state'] : ''; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a>
								</li>
							<?php endfor; ?>

							<?php if($page < $total_pages): ?>
								<li class="page-item">
									<a class="page-link" href="?view=clients&page=<?php echo $page+1; ?>&city=<?php echo isset($_GET['city']) ? $_GET['city'] : ''; ?>&state=<?php echo isset($_GET['state']) ? $_GET['state'] : ''; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>&limit=<?php echo $limit; ?>">Siguiente</a>
								</li>
							<?php endif; ?>
						</ul>
					</nav>
				<?php endif;
			}

			// Mostrar paginación arriba
			showPagination($page, $total_pages, $limit);

			if(count($users)>0){
				// si hay usuarios
				?>

				<table class="table table-bordered table-hover">
				<thead>
				<th>Nombre completo</th>
				<th>Dirección</th>
				<th>Ciudad/Municipio</th>
				<th>Estado</th>
				<th>Código Postal</th>
				<th>Email</th>
				<th>Teléfono</th>
				<th></th>
				</thead>
				<?php
				foreach($users as $user){
					?>
					<tr>
					<td><?php echo $user->name." ".$user->lastname; ?></td>
					<td><?php echo $user->address1; ?></td>
					<td><?php echo $user->city; ?></td>
					<td><?php echo $user->state; ?></td>
					<td><?php echo $user->zip_code; ?></td>
					<td><?php echo $user->email1; ?></td>
					<td><?php echo $user->phone1; ?></td>
					<td style="width:130px;">
					<a href="index.php?view=editclient&id=<?php echo $user->id;?>" class="btn btn-warning btn-xs">Editar</a>
					<a href="index.php?view=delclient&id=<?php echo $user->id;?>" class="btn btn-danger btn-xs">Eliminar</a>
					</td>
					</tr>
					<?php

				}
			echo "</table>";


			}else{
				echo "<p class='alert alert-danger'>No hay clientes</p>";
			}

			// Mostrar paginación abajo
			showPagination($page, $total_pages, $limit);
			?>
		</div>
</div>


	</div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.dropdown-item:hover {
  color:rgb(24, 56, 31) !important;
  background-color: white !important;
}
</style>