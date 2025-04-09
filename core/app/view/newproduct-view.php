    <?php 
$categories = CategoryData::getAll();
    ?>
<div class="row">
	<div class="col-md-12">
	<h1>Nuevo Producto</h1>

<div class="card">
  <div class="card-header">
    NUEVO PRODUCTO
  </div>
    <div class="card-body">


		<form class="form-horizontal" method="post" enctype="multipart/form-data" id="addproduct" action="index.php?view=addproduct" role="form">

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Imagen</label>
    <div class="col-md-6">
      <input type="file" name="image" id="image" placeholder="">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Codigo de Barras*</label>
    <div class="col-md-6">
      <input type="text" name="barcode" id="product_code" class="form-control" id="barcode" placeholder="Codigo de Barras del Producto">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" required class="form-control" id="name" placeholder="Nombre del Producto">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Categoria</label>
    <div class="col-md-6">
    <select name="category_id" class="form-control" id="categorySelect">
    <option value="">-- NINGUNA --</option>
    <?php foreach($categories as $category):?>
      <option value="<?php echo $category->id;?>"><?php echo $category->name;?></option>
    <?php endforeach;?>
      </select>    
    </div>
  </div>

  <!-- Campos dinámicos para tallas -->
  <div id="tallasContainer" style="display: none;">
    <div class="form-group">
      <label for="tipoJersey" class="col-lg-2 control-label">Tipo de Jersey</label>
      <div class="col-md-6">
        <select name="tipo_jersey" class="form-control" id="tipoJersey">
          <option value="adulto">Adulto</option>
          <option value="nino">Niño</option>
          <option value="dama">Dama</option>
        </select>
      </div>
    </div>

    <!-- Tallas para Jersey Adulto -->
    <div id="tallasAdulto" class="form-group">
      <label class="col-lg-2 control-label">Tallas Adulto</label>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-2">
            <label>S</label>
            <input type="number" name="talla_s" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>M</label>
            <input type="number" name="talla_m" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>L</label>
            <input type="number" name="talla_l" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>XL</label>
            <input type="number" name="talla_xl" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>XXL</label>
            <input type="number" name="talla_xxl" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>3XL</label>
            <input type="number" name="talla_3xl" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>4XL</label>
            <input type="number" name="talla_4xl" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>6XL</label>
            <input type="number" name="talla_6xl" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>8XL</label>
            <input type="number" name="talla_8xl" class="form-control" min="0" value="0">
          </div>
        </div>
      </div>
    </div>

    <!-- Tallas para Jersey Niño -->
    <div id="tallasNino" class="form-group" style="display: none;">
      <label class="col-lg-2 control-label">Tallas Niño</label>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-2">
            <label>16</label>
            <input type="number" name="talla_16" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>18</label>
            <input type="number" name="talla_18" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>20</label>
            <input type="number" name="talla_20" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>22</label>
            <input type="number" name="talla_22" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>24</label>
            <input type="number" name="talla_24" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>26</label>
            <input type="number" name="talla_26" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>28</label>
            <input type="number" name="talla_28" class="form-control" min="0" value="0">
          </div>
        </div>
      </div>
    </div>

    <!-- Tallas para Tenis -->
    <div id="tallasTenis" class="form-group" style="display: none;">
      <label class="col-lg-2 control-label">Tallas Tenis</label>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-2">
            <label>6</label>
            <input type="number" name="talla_6" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>8</label>
            <input type="number" name="talla_8" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>9</label>
            <input type="number" name="talla_9" class="form-control" min="0" value="0">
          </div>
        </div>
      </div>
    </div>

    <!-- Tallas para Dama -->
    <div id="tallasDama" class="form-group" style="display: none;">
      <label class="col-lg-2 control-label">Tallas Dama</label>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-2">
            <label>S</label>
            <input type="number" name="talla_s_dama" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>M</label>
            <input type="number" name="talla_m_dama" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>L</label>
            <input type="number" name="talla_l_dama" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>XL</label>
            <input type="number" name="talla_xl_dama" class="form-control" min="0" value="0">
          </div>
          <div class="col-md-2">
            <label>XXL</label>
            <input type="number" name="talla_xxl_dama" class="form-control" min="0" value="0">
          </div>
        </div>
      </div>
    </div>

    <!-- Talla única para Gorras y Variado -->
    <div id="tallaUnica" class="form-group" style="display: none;">
      <label class="col-lg-2 control-label">Talla única</label>
      <div class="col-md-6">
        <input type="number" name="talla_unica" class="form-control" min="0" value="1">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Descripcion</label>
    <div class="col-md-6">
      <textarea name="description" class="form-control" id="description" placeholder="Descripcion del Producto"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Precio de Entrada*</label>
    <div class="col-md-6">
      <input type="text" name="price_in" required class="form-control" id="price_in" placeholder="Precio de entrada">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Precio de Salida*</label>
    <div class="col-md-6">
      <input type="text" name="price_out" required class="form-control" id="price_out" placeholder="Precio de salida">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Unidad*</label>
    <div class="col-md-6">
      <input type="text" name="unit" required class="form-control" id="unit" placeholder="Unidad del Producto">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Presentacion</label>
    <div class="col-md-6">
      <input type="text" name="presentation" class="form-control" id="inputEmail1" placeholder="Presentacion del Producto">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Minima en Inventario</label>
    <div class="col-md-6">
      <input type="text" name="inventary_min" class="form-control" id="inventary_min" placeholder="Minima en Inventario" value="10">
    </div>
  </div>

  <!-- Campo de inventario inicial para Gorras, Variado y Balón -->
  <div id="inventarioInicialContainer" class="form-group" style="display: none;">
    <label for="inventario_inicial" class="col-lg-2 control-label">Inventario Inicial*</label>
    <div class="col-md-6">
      <input type="number" name="inventario_inicial" class="form-control" id="inventario_inicial" placeholder="Cantidad inicial de unidades" min="1" value="1" required>
    </div>
  </div>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-primary">Agregar Producto</button>
    </div>
  </div>
</form>
    </div>
</div>

	</div>
</div>

<script>
$(document).ready(function(){
    // Función para manejar el cambio de categoría
    $('#categorySelect').change(function(){
        var categoria = $(this).find('option:selected').text().trim().toLowerCase();
        console.log('Categoría seleccionada:', categoria); // Para depuración
        
        if(categoria === 'jersey') {
            $('#tallasContainer').show();
            $('#tipoJersey').show();
            $('#tallasAdulto').show();
            $('#tallasNino, #tallasTenis, #tallaUnica, #tallasDama').hide();
            $('#inventarioInicialContainer').hide();
        } else if(categoria === 'tenis') {
            $('#tallasContainer').show();
            $('#tipoJersey').hide();
            $('#tallasTenis').show();
            $('#tallasAdulto, #tallasNino, #tallaUnica, #tallasDama').hide();
            $('#inventarioInicialContainer').hide();
        } else if(categoria === 'gorra' || categoria === 'gorras' || categoria === 'variado' || categoria === 'balón' || categoria === 'balon') {
            $('#tallasContainer').hide();
            $('#tipoJersey, #tallasAdulto, #tallasNino, #tallasTenis, #tallaUnica, #tallasDama').hide();
            $('#inventarioInicialContainer').show();
        }
    });

    // Función para manejar el cambio de tipo de jersey
    $('#tipoJersey').change(function(){
        var tipo = $(this).val();
        if(tipo === 'adulto') {
            $('#tallasAdulto').show();
            $('#tallasNino, #tallasDama').hide();
        } else if(tipo === 'dama') {
            $('#tallasDama').show();
            $('#tallasAdulto, #tallasNino').hide();
        } else {
            $('#tallasNino').show();
            $('#tallasAdulto, #tallasDama').hide();
        }
    });

    // Prevenir el uso de Ctrl+J
    $("#product_code").keydown(function(e){
        if(e.which==17 || e.which==74 ){
            e.preventDefault();
        }else{
            console.log(e.which);
        }
    });
});
</script>