<div class="row">
	<div class="col-md-12">
		<h1>Categorias</h1>
<div class="">
	<a href="index.php?view=newcategory" class="btn btn-secondary"><i class='bi bi-list'></i> Nueva Categoria</a>
</div>
<br>
<div class="card">
	<div class="card-header">
		CATEGORIAS
	</div>
		<div class="card-body">


		<?php

		$categories = CategoryData::getAll();
		if(count($categories)>0){
			// si hay usuarios
			?>

			<table class="table table-bordered table-hover">
			<thead>
			<th>Nombre</th>
			<th>Acciones</th>
			</thead>
			<?php
			foreach($categories as $category){
				?>
				<tr>
				<td><?php echo $category->name; ?></td>
				<td style="width:130px;">
					<a href="index.php?view=editcategory&id=<?php echo $category->id;?>" class="btn btn-warning btn-sm">
						<i class="bi bi-pencil"></i> Editar
					</a> 
					<a href="index.php?view=delcategory&id=<?php echo $category->id;?>" class="btn btn-danger btn-sm">
						<i class="bi bi-trash"></i> Eliminar
					</a>
				</td>
				</tr>
				<?php

			}
			echo "</table>";



		}else{
			echo "<p class='alert alert-danger'>No hay Categorias</p>";
		}


		?>
		</div>
</div>


	</div>
</div>