<div class="row">
	<div class="col-md-12">
		<h1>Categorías</h1>
		
		<style>
		.color-picker-input {
			border: 1px solid #ccc;
			border-radius: 4px;
			padding: 2px;
			margin-top: 5px;
			background-color: white;
			width: 40px;
			height: 40px;
			cursor: pointer;
		}
		</style>
		
		<?php if(isset($_COOKIE["catdel"]) && $_COOKIE["catdel"]!="") {
			$category = $_COOKIE["catdel"];
			setcookie("catdel", "", time()-3600);
			echo '<div id="deleteAlert" class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>¡Éxito!</strong> La categoría "'.$category.'" ha sido eliminada correctamente.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeAlert(\'deleteAlert\')"></button>
			</div>';
			echo '<script>setTimeout(function(){ closeAlert(\'deleteAlert\'); }, 5000);</script>';
		}

		if(isset($_COOKIE["catadd"]) && $_COOKIE["catadd"]!="") {
			$category = $_COOKIE["catadd"];
			setcookie("catadd", "", time()-3600);
			echo '<div id="addAlert" class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>¡Éxito!</strong> La categoría "'.$category.'" ha sido añadida correctamente.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeAlert(\'addAlert\')"></button>
			</div>';
			echo '<script>setTimeout(function(){ closeAlert(\'addAlert\'); }, 5000);</script>';
		}

		if(isset($_COOKIE["cat_updated"]) && $_COOKIE["cat_updated"]=="true") {
			setcookie("cat_updated", "", time()-3600);
			echo '<div id="updateAlert" class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>¡Éxito!</strong> La categoría ha sido actualizada correctamente.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeAlert(\'updateAlert\')"></button>
			</div>';
			echo '<script>setTimeout(function(){ closeAlert(\'updateAlert\'); }, 5000);</script>';
		}

		if(isset($_COOKIE["color_updated"]) && $_COOKIE["color_updated"]=="true") {
			setcookie("color_updated", "", time()-3600);
			echo '<div id="colorUpdateAlert" class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>¡Éxito!</strong> El color de la categoría ha sido actualizado correctamente.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="closeAlert(\'colorUpdateAlert\')"></button>
			</div>';
			echo '<script>setTimeout(function(){ closeAlert(\'colorUpdateAlert\'); }, 5000);</script>';
		}
		?>
		
<div class="">
	<a href="index.php?view=newcategory" class="btn btn-secondary"><i class='bi bi-plus-circle'></i> Nueva Categoría</a>
</div>
<br>

<div class="card">
	<div class="card-header">
		CATEGORÍAS
	</div>
		<div class="card-body">

		<?php
		$categories = CategoryData::getAll();
		if(count($categories)>0){
			?>
			<table class="table table-bordered table-hover">
			<thead>
			<th>Nombre</th>
			<th>Color</th>
			<th>Acciones</th>
			</thead>
			<?php
			foreach($categories as $category){
				?>
				<tr>
				<td><?php echo $category->name; ?></td>
				<td style="width:100px;">
					<div class="color-display" id="color_display_<?php echo $category->id; ?>" 
						 style="width: 30px; height: 30px; border-radius: 4px; background-color: #28a745; cursor: pointer;" 
						 data-category-id="<?php echo $category->id; ?>"
						 data-category-name="<?php echo $category->name; ?>"
						 onclick="openColorPicker(<?php echo $category->id; ?>, '<?php echo addslashes($category->name); ?>')">
					</div>
					<input type="color" id="color_picker_<?php echo $category->id; ?>" class="color-picker-input" style="position: absolute; visibility: hidden;">
				</td>
				<td style="width:200px;">
					<a href="index.php?view=editcategory&id=<?php echo $category->id;?>" class="btn btn-warning btn-sm">
						<i class="bi bi-pencil"></i> Editar
					</a>
				</td>
				</tr>
				<?php
			}
			echo "</table>";
		} else {
			echo "<p class='alert alert-danger'>No hay Categorías</p>";
		}
		?>
		</div>
</div>

	</div>
</div>

<script>
function closeAlert(alertId) {
    document.getElementById(alertId).classList.remove('show');
    setTimeout(function() {
        document.getElementById(alertId).style.display = 'none';
    }, 150);
}

function openColorPicker(categoryId, categoryName) {
    // Obtener el selector de color y el cuadro de color
    const colorPicker = document.getElementById('color_picker_' + categoryId);
    const colorDisplay = document.getElementById('color_display_' + categoryId);
    
    // Obtener el color actual
    const currentColor = localStorage.getItem('category_color_' + categoryId) || '#28a745';
    colorPicker.value = currentColor;
    
    // Posicionar el selector de color directamente junto al cuadro de color
    const rect = colorDisplay.getBoundingClientRect();
    
    // Mostrar el selector de color y posicionarlo
    colorPicker.style.position = 'fixed';
    colorPicker.style.left = (rect.left + window.scrollX) + 'px';
    colorPicker.style.top = (rect.bottom + window.scrollY + 5) + 'px';
    colorPicker.style.visibility = 'visible';
    colorPicker.style.zIndex = '1000';
    
    // Enfocar y abrir el selector
    colorPicker.click();
    
    // Manejar el evento de cambio
    colorPicker.onchange = function() {
        updateCategoryColor(categoryId, this.value);
        resetColorPicker(colorPicker);
    };
    
    // Cerrar el selector cuando se pierde el foco
    colorPicker.onblur = function() {
        setTimeout(function() {
            resetColorPicker(colorPicker);
        }, 200);
    };
}

function resetColorPicker(colorPicker) {
    colorPicker.style.position = 'absolute';
    colorPicker.style.visibility = 'hidden';
}

function updateCategoryColor(categoryId, color) {
    // Actualizar el color visualmente
    document.getElementById('color_display_' + categoryId).style.backgroundColor = color;
    
    // Guardar en localStorage
    localStorage.setItem('category_color_' + categoryId, color);
    
    // Enviar actualización al servidor mediante AJAX
    updateColorOnServer(categoryId, color);
}

function updateColorOnServer(categoryId, color) {
    // Crear un formulario dinámico para enviar los datos
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?view=updatecategorycolor';
    form.style.display = 'none';
    
    var idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'category_id';
    idInput.value = categoryId;
    form.appendChild(idInput);
    
    var colorInput = document.createElement('input');
    colorInput.type = 'hidden';
    colorInput.name = 'color';
    colorInput.value = color;
    form.appendChild(colorInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Cargar colores de localStorage cuando la página esté cargada
document.addEventListener('DOMContentLoaded', function() {
    var colorBoxes = document.querySelectorAll('.color-display');
    
    colorBoxes.forEach(function(box) {
        var categoryId = box.id.replace('color_display_', '');
        var storedColor = localStorage.getItem('category_color_' + categoryId);
        
        if (storedColor) {
            box.style.backgroundColor = storedColor;
        }
    });
});
</script>