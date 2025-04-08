<?php
$user = UserData::getById($_SESSION["user_id"]);
?>
<div class="row">
    <div class="col-md-12">
        <h1>Mi Perfil</h1>
        <br>
        <div class="card">
            <div class="card-header">
                INFORMACIÓN DEL PERFIL
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="assets/img/avatars/<?php echo $user->image; ?>" class="img-thumbnail" style="width:200px; height:200px; object-fit:cover;">
                        <br><br>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                            Editar Perfil
                        </button>
                    </div>
                    <div class="col-md-8">
                        <h4>Información Personal</h4>
                        <table class="table">
                            <tr>
                                <th>Nombre:</th>
                                <td><?php echo $user->name; ?></td>
                            </tr>
                            <tr>
                                <th>Apellido:</th>
                                <td><?php echo $user->lastname; ?></td>
                            </tr>
                            <tr>
                                <th>Nombre de usuario:</th>
                                <td><?php echo $user->username; ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo $user->email; ?></td>
                            </tr>
                            <tr>
                                <th>Rol:</th>
                                <td><?php echo $user->is_admin ? 'Administrador' : 'Usuario'; ?></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td><?php echo $user->is_active ? 'Activo' : 'Inactivo'; ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de registro:</th>
                                <td><?php echo $user->created_at; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" action="index.php?view=updateuser" role="form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" value="<?php echo $user->name; ?>" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Apellido</label>
                        <input type="text" name="lastname" value="<?php echo $user->lastname; ?>" class="form-control" id="lastname">
                    </div>
                    <div class="form-group">
                        <label for="username">Nombre de usuario</label>
                        <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" value="<?php echo $user->email; ?>" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" class="form-control" id="password">
                        <small class="form-text text-muted">Deja este campo vacío si no quieres cambiar la contraseña</small>
                    </div>
                    <div class="form-group">
                        <label for="image">Foto de perfil</label>
                        <?php if($user->image!=""):?>
                            <img src="assets/img/avatars/<?php echo $user->image; ?>" class="img-thumbnail" style="width:100px;">
                        <?php endif;?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div> 