<?php
$user = UserData::getById($_SESSION["user_id"]);
?>
<div class="modal fade" id="debugInfoModal" tabindex="-1" role="dialog" aria-labelledby="debugInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="debugInfoModalLabel">Información de Depuración</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Datos del Usuario</h6>
            <pre>
ID: <?php echo $user->id; ?>
Nombre: <?php echo $user->name; ?>
Apellido: <?php echo $user->lastname; ?>
Usuario: <?php echo $user->username; ?>
Email: <?php echo $user->email; ?>
Imagen: <?php echo $user->image; ?>
            </pre>
          </div>
          <div class="col-md-6">
            <h6>Datos de la Sesión</h6>
            <pre>
ID de Sesión: <?php echo $_SESSION["user_id"]; ?>
Nombre: <?php echo $_SESSION["user_name"]; ?>
Apellido: <?php echo $_SESSION["user_lastname"]; ?>
Imagen: <?php echo $_SESSION["user_image"]; ?>
            </pre>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-12">
            <h6>Información de la Imagen</h6>
            <pre>
Ruta de la imagen: assets/img/avatars/<?php echo $user->image; ?>
Imagen existe: <?php echo file_exists("assets/img/avatars/" . $user->image) ? 'true' : 'false'; ?>
            </pre>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div> 