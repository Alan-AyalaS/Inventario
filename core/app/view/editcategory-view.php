<?php $user = CategoryData::getById($_GET["id"]);?>
<div class="row">
	<div class="col-md-12">
	<h1>Editar Categoría</h1>
	<br>
<div class="card">
  <div class="card-header">
    EDITAR CATEGORÍA
  </div>
    <div class="card-body">


		<form class="form-horizontal" method="post" id="addproduct" action="index.php?view=updatecategory" role="form">


  <div class="form-group row mb-3">
    <label for="name" class="col-sm-2 col-form-label">Nombre*</label>
    <div class="col-sm-10">
      <input type="text" name="name" value="<?php echo $user->name;?>" required class="form-control" id="name" placeholder="Nombre de la categoría">
      <small class="form-text text-muted">Este nombre aparecerá en las listas desplegables al categorizar productos.</small>
    </div>
  </div>

  <div class="form-group row mb-3">
    <label for="color" class="col-sm-2 col-form-label">Color</label>
    <div class="col-sm-10">
      <input type="color" name="color" class="form-control form-control-color" id="color" value="#28a745" title="Elija un color para la categoría">
      <small class="form-text text-muted">Este color se usará para identificar visualmente la categoría.</small>
    </div>
  </div>

  <div class="form-group row mb-3">
    <div class="offset-sm-2 col-sm-10">
      <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2"></i> Actualizar Categoría
      </button>
      <a href="index.php?view=categories" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Cancelar
      </a>
    </div>
  </div>
</form>
</div>

    </div>

	</div>
</div>

<script>
// Cargar color desde localStorage si existe
document.addEventListener('DOMContentLoaded', function() {
    var categoryId = '<?php echo $user->id; ?>';
    var colorInput = document.getElementById('color');
    var storedColor = localStorage.getItem('category_color_' + categoryId);
    
    if (storedColor) {
        colorInput.value = storedColor;
    }
});
</script>