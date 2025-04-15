<?php
require_once 'core/app/model/PersonData.php';

if(isset($_GET['check_phone'])) {
    header('Content-Type: application/json');
    $phone = $_GET['check_phone'];
    $person = PersonData::getByPhone($phone);
    echo json_encode(['exists' => ($person != null)]);
    exit;
}
?>
<div class="row">
	<div class="col-md-12">
	<h1>Nuevo Cliente</h1>
	<br>
<div class="card">
  <div class="card-header">
    NUEVO CLIENTE
  </div>
    <div class="card-body">

		<form class="form-horizontal" method="post" id="addproduct" action="index.php?view=addclient" role="form">

    <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Teléfono*</label>
    <div class="col-md-6">
      <input type="text" name="phone" class="form-control" required id="inputPhone" placeholder="Teléfono">
      <div id="phoneAlert" class="alert alert-danger mt-2" style="display: none;">
        Este teléfono ya está registrado
      </div>
    </div>
  </div>
    
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
    <div class="col-md-6">
      <input type="text" name="name" class="form-control" id="name" placeholder="Nombre">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Apellidos*</label>
    <div class="col-md-6">
      <input type="text" name="lastname" required class="form-control" id="lastname" placeholder="Apellido">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Dirección*</label>
    <div class="col-md-6">
      <input type="text" name="address1" class="form-control" id="address1" placeholder="Dirección">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Ciudad/Municipio*</label>
    <div class="col-md-6">
      <input type="text" name="city" class="form-control" required id="city" placeholder="Ciudad o Municipio">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Estado*</label>
    <div class="col-md-6">
      <input type="text" name="state" class="form-control" required id="state" placeholder="Estado">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Código Postal*</label>
    <div class="col-md-6">
      <input type="text" name="zip_code" class="form-control" id="zip_code" placeholder="Código Postal">
    </div>
  </div>
  

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Email</label>
    <div class="col-md-6">
      <input type="text" name="email1" class="form-control" id="email1" placeholder="Email">
    </div>
  </div>

  <p class="alert alert-info">* Campos obligatorios</p>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-primary" id="submitBtn">Agregar Cliente</button>
    </div>
  </div>
</form>

<script>
$(document).ready(function() {
    $('#inputPhone').on('blur', function() {
        var phone = $(this).val();
        if(phone.length > 0) {
            $.ajax({
                url: 'index.php?view=check_phone',
                method: 'GET',
                data: { phone: phone },
                dataType: 'json',
                success: function(response) {
                    if(response.exists) {
                        $('#phoneAlert').show();
                        $('#submitBtn').prop('disabled', true);
                    } else {
                        $('#phoneAlert').hide();
                        $('#submitBtn').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    });
});
</script>
    </div>
</div>

	</div>
</div>