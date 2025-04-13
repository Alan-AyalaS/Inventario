<?php
$product = ProductData::getById($_GET["id"]);
$categories = CategoryData::getAll();

if($product!=null):
?>
<div class="row">
	<div class="col-md-12">
	<h1><?php echo $product->name ?> <small>Editar Producto</small></h1>
  <?php if(isset($_COOKIE["prdupd"])):?>
    <p class="alert alert-info">La informacion del producto se ha actualizado exitosamente.</p>
  <?php setcookie("prdupd","",time()-18600); endif; ?>

<div class="card">
  <div class="card-header">
    EDITAR PRODUCTO
  </div>
    <div class="card-body">


		<form method="post" action="index.php?view=updateproduct" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-8">
					<input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
					<div class="form-group">
						<label for="name">Nombre*</label>
						<input type="text" name="name" value="<?php echo $product->name; ?>" class="form-control" id="name" placeholder="Nombre del producto">
					</div>
					<div class="form-group">
						<label for="barcode">Código de Barras</label>
						<input type="text" name="barcode" value="<?php echo $product->barcode; ?>" class="form-control" id="barcode" placeholder="Código de barras">
					</div>
					<div class="form-group">
						<label for="description">Descripción</label>
						<textarea name="description" class="form-control" id="description" placeholder="Descripción del producto"><?php echo $product->description; ?></textarea>
					</div>
					<div class="form-group">
						<label for="presentation">Presentación</label>
						<input type="text" name="presentation" value="<?php echo $product->presentation; ?>" class="form-control" id="presentation" placeholder="Presentación">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="image">Imagen</label>
						<input type="file" name="image" id="image" class="form-control">
						<?php if($product->image!=""):?>
							<img src="storage/products/<?php echo $product->image; ?>" class="img-responsive" style="width:100px;">
						<?php endif;?>
					</div>
					<div class="form-group">
						<label for="category_id">Categoría</label>
						<select name="category_id" id="category_id" class="form-control">
							<option value="">-- SELECCIONE --</option>
							<?php foreach($categories as $cat):?>
								<option value="<?php echo $cat->id; ?>" <?php if($product->category_id==$cat->id){ echo "selected"; }?>><?php echo $cat->name; ?></option>
							<?php endforeach;?>
						</select>
					</div>
					<div class="form-group" id="jersey_type_group" style="display:none;">
						<label for="tipo_jersey">Tipo de Jersey</label>
						<select name="tipo_jersey" id="tipo_jersey" class="form-control">
							<option value="">-- SELECCIONE --</option>
							<option value="adulto" <?php if($product->jersey_type=="adulto"){ echo "selected"; }?>>Adulto</option>
							<option value="niño" <?php if($product->jersey_type=="niño"){ echo "selected"; }?>>Niño</option>
							<option value="dama" <?php if($product->jersey_type=="dama"){ echo "selected"; }?>>Dama</option>
						</select>
					</div>
					<div class="form-group">
						<label for="price_in">Precio de Entrada*</label>
						<input type="text" name="price_in" value="<?php echo $product->price_in; ?>" class="form-control" id="price_in" placeholder="Precio de entrada">
					</div>
					<div class="form-group">
						<label for="price_out">Precio de Salida*</label>
						<input type="text" name="price_out" value="<?php echo $product->price_out; ?>" class="form-control" id="price_out" placeholder="Precio de salida">
					</div>
					<div class="form-group">
						<label for="unit">Unidad*</label>
						<input type="text" name="unit" value="<?php echo $product->unit; ?>" class="form-control" id="unit" placeholder="Unidad">
					</div>
					<div class="form-group">
						<label for="inventary_min">Inventario Minimo*</label>
						<input type="text" name="inventary_min" value="<?php echo $product->inventary_min; ?>" class="form-control" id="inventary_min" placeholder="Inventario Minimo">
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="is_active" <?php if($product->is_active){ echo "checked"; }?>> Activo
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn btn-success">Actualizar</button>
				</div>
			</div>
		</form>
    </div>
</div>

<br><br>
	</div>
</div>

<script>
	$(document).ready(function(){
		// Mostrar/ocultar tipo de jersey según la categoría
		$("#category_id").change(function(){
			var category_id = $(this).val();
			if(category_id){
				$.get("index.php?action=getcategory&id="+category_id,function(data){
					if(data.name.toLowerCase() == "jersey"){
						$("#jersey_type_group").show();
					}else{
						$("#jersey_type_group").hide();
					}
				},"json");
			}else{
				$("#jersey_type_group").hide();
			}
		});

		// Verificar categoría al cargar la página
		var category_id = $("#category_id").val();
		if(category_id){
			$.get("index.php?action=getcategory&id="+category_id,function(data){
				if(data.name.toLowerCase() == "jersey"){
					$("#jersey_type_group").show();
				}else{
					$("#jersey_type_group").hide();
				}
			},"json");
		}

		// Modificar el formulario para que envíe todos los campos excepto la imagen si no se modificó
		$("form").on("submit", function(e) {
			e.preventDefault();
			var formData = new FormData();
			
			// Agregar todos los campos del formulario excepto la imagen
			$("input, select, textarea").each(function() {
				var name = $(this).attr("name");
				if (name && !$(this).is(":file")) {
					if ($(this).attr("type") === "checkbox") {
						formData.append(name, $(this).is(":checked") ? "1" : "0");
					} else {
						formData.append(name, $(this).val() || "");
					}
				}
			});

			// Agregar la imagen solo si se seleccionó una nueva
			var imageInput = $("#image")[0];
			if (imageInput.files.length > 0) {
				formData.append("image", imageInput.files[0]);
			}

			// Enviar el formulario
			$.ajax({
				url: "index.php?view=updateproduct",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				success: function(response) {
					window.location.href = "index.php?view=inventary";
				},
				error: function(xhr, status, error) {
					alert("Error al actualizar el producto: " + error);
				}
			});
		});
	});
</script>
<?php endif; ?>