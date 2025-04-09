<?php
$user = UserData::getById($_SESSION["user_id"]);
?>
<div class="row">
    <div class="col-md-12">
        <h1>Mi Perfil</h1>
        <br>
        <!-- Mensaje de confirmación -->
        <?php if(isset($_SESSION["success"])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION["success"];
                    unset($_SESSION["success"]);
                ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">
                INFORMACIÓN DEL PERFIL
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="assets/img/avatars/<?php echo $user->image; ?>" class="img-thumbnail" style="width:200px; height:200px; object-fit:cover;">
                        <br><br>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            Editar Perfil
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            Cambiar Contraseña
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Mensaje de error en el modal -->
                <?php if(isset($_SESSION["password_error"])): ?>
                    <div class="alert alert-danger">
                        <?php 
                            echo $_SESSION["password_error"];
                            unset($_SESSION["password_error"]);
                        ?>
                    </div>
                <?php endif; ?>
                <form class="form-horizontal" method="post" action="index.php?view=updateuser" role="form" enctype="multipart/form-data" id="editProfileForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" name="name" value="<?php echo $user->name; ?>" class="form-control" id="name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="lastname" class="form-label">Apellido</label>
                                <input type="text" name="lastname" value="<?php echo $user->lastname; ?>" class="form-control" id="lastname">
                            </div>
                             <div class="form-group mb-3">
                                <label for="username" class="form-label">Nombre de usuario</label>
                                <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control" id="username" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" value="<?php echo $user->email; ?>" class="form-control" id="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group mb-3">
                                <label for="image" class="form-label">Foto de perfil</label>
                                <div class="mb-2 text-center">
                                    <?php 
                                    $img_src = 'assets/img/avatars/default-avatar-icon.jpg';
                                    if($user->image!="" && file_exists('assets/img/avatars/'.$user->image)){
                                        $img_src = 'assets/img/avatars/'.$user->image;
                                    }
                                    ?>
                                    <img id="imagePreview" src="<?php echo $img_src; ?>" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                                <small class="form-text text-muted">Selecciona una imagen nueva para cambiarla.</small>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                    <hr>
                    <div class="d-flex justify-content-end">
                       <button type="button" class="btn btn-secondary me-2" data-coreui-dismiss="modal">Cancelar</button>
                       <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="passwordErrorMessage" class="alert alert-danger" style="display: none;"></div>
                <form id="changePasswordForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                <input type="password" name="current_password" class="form-control" id="current_password" placeholder="Introduce tu contraseña actual" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Introduce tu nueva contraseña" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Repite tu nueva contraseña" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user->id;?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="savePasswordBtn">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
console.log('[TEST] Script tag en profile-view.php ejecutado.'); // Prueba básica

document.addEventListener('DOMContentLoaded', function () {
  console.log('[TEST] DOMContentLoaded disparado.'); // Verificar si el DOM se carga
  var editModalElement = document.getElementById('editProfileModal');
  var editButton = document.querySelector('button[data-bs-target="#editProfileModal"]');

  if (editModalElement) {
    console.log('[Debug] Modal element #editProfileModal found.');
    editModalElement.addEventListener('show.bs.modal', function (event) {
      console.log('[Debug] Evento: show.bs.modal disparado para #editProfileModal');
    });
    editModalElement.addEventListener('shown.bs.modal', function (event) {
      console.log('[Debug] Evento: shown.bs.modal disparado para #editProfileModal');
    });
     editModalElement.addEventListener('hide.bs.modal', function (event) {
      console.log('[Debug] Evento: hide.bs.modal disparado para #editProfileModal');
    });
  } else {
     console.error('[Debug] Error: Elemento del modal #editProfileModal NO encontrado!');
  }

  if (editButton) {
    console.log('[Debug] Botón "Editar Perfil" encontrado.');
    editButton.addEventListener('click', function(event){
        console.log('[Debug] Botón "Editar Perfil" clickeado.');
        // Intentar forzar la apertura usando coreui.Modal
        
        // Intentar obtener instancia existente con coreui
        var modalInstance = coreui.Modal.getInstance(editModalElement);
        
        if (!modalInstance && editModalElement) { // Asegurarse que editModalElement existe
             console.log('[Debug] Creando nueva instancia de coreui.Modal.');
             // Crear nueva instancia con coreui
             modalInstance = new coreui.Modal(editModalElement);
        }
        
        if(modalInstance) {
             console.log('[Debug] Intentando mostrar modal manualmente con coreui.Modal.');
             modalInstance.show();
        } else {
             console.error('[Debug] No se pudo obtener/crear instancia del modal con coreui.');
        }
        
    });
  } else {
    console.error('[Debug] Error: Botón "Editar Perfil" NO encontrado!');
  }
});

// Script adicional para previsualizar imagen seleccionada
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const defaultImage = 'assets/img/avatars/default-avatar-icon.jpg'; // Ruta a tu imagen por defecto

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                // Si no se selecciona archivo o no es imagen, mostrar la imagen actual o la por defecto
                const currentImageSrc = imagePreview.dataset.originalSrc || defaultImage; 
                imagePreview.src = currentImageSrc;
            }
        });
        // Guardar la imagen original al cargar
        imagePreview.dataset.originalSrc = imagePreview.src; 
    }
});

// Validación adicional para contraseñas
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editProfileForm');
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (editForm && newPasswordInput && confirmPasswordInput) {
        editForm.addEventListener('submit', function(event) {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const currentPassword = currentPasswordInput.value;

            // Si se intenta cambiar la contraseña (nueva no está vacía)
            if (newPassword !== '') {
                // Validar que la actual no esté vacía si se cambia la nueva
                if (currentPassword === '') {
                     alert('Debes introducir tu contraseña actual para establecer una nueva.');
                     event.preventDefault(); // Detener envío
                     currentPasswordInput.focus();
                     return;
                }
                // Validar que las nuevas contraseñas coincidan
                if (newPassword !== confirmPassword) {
                    alert('La nueva contraseña y la confirmación no coinciden.');
                    event.preventDefault(); // Detener envío
                    confirmPasswordInput.focus();
                    return;
                }
                 // Opcional: Añadir validación de longitud/complejidad para newPassword aquí si se desea
                 /*
                 if (newPassword.length < 8) {
                    alert('La nueva contraseña debe tener al menos 8 caracteres.');
                    event.preventDefault();
                    newPasswordInput.focus();
                    return;
                 }
                 */
            }
            // Si la nueva contraseña está vacía, asegurarse que la confirmación y la actual también lo estén
            // (O que el backend ignore estos campos si new_password está vacío)
             else if (newPassword === '' && (confirmPassword !== '' || currentPassword !== '')) {
                if (confirmPassword !== '') {
                     alert('Has introducido una confirmación de contraseña pero no una nueva contraseña.');
                     event.preventDefault();
                     confirmPasswordInput.focus();
                     return;
                 }
                 // Permitir currentPassword si solo se cambia username/email/etc y no la pass?
                 // Considera la lógica del backend aquí. Por ahora, no bloqueamos si solo current está llena y new está vacía.
             }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const editProfileForm = document.getElementById('editProfileForm');
    const editModal = document.getElementById('editProfileModal');
    const newPasswordInput = document.getElementById('new_password');
    const modalErrorMessage = document.getElementById('modalErrorMessage');

    // Verificar si debemos abrir el modal automáticamente
    if (window.location.href.includes('showModal=true')) {
        const modal = coreui.Modal.getInstance(editModal);
        if (modal) {
            modal.show();
        }
    }

    editProfileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar si se está intentando cambiar la contraseña
        const isChangingPassword = newPasswordInput && newPasswordInput.value.trim() !== '';
        
        if (isChangingPassword) {
            // Validar que la contraseña actual no esté vacía
            const currentPassword = document.getElementById('current_password').value;
            if (!currentPassword) {
                modalErrorMessage.style.display = 'block';
                modalErrorMessage.textContent = 'Debes proporcionar tu contraseña actual para cambiarla';
                return;
            }

            // Validar que las nuevas contraseñas coincidan
            const newPassword = newPasswordInput.value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (newPassword !== confirmPassword) {
                modalErrorMessage.style.display = 'block';
                modalErrorMessage.textContent = 'Las nuevas contraseñas no coinciden';
                return;
            }
        }

        // Enviar el formulario
        const formData = new FormData(editProfileForm);
        fetch('index.php?view=updateuser', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('La contraseña actual no es correcta')) {
                // Redirigir a la misma página pero con el parámetro para mostrar el modal
                window.location.href = window.location.pathname + '?showModal=true';
            } else if (data.includes('success')) {
                const modal = coreui.Modal.getInstance(editModal);
                if (modal) {
                    modal.hide();
                }
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalErrorMessage.style.display = 'block';
            modalErrorMessage.textContent = 'Error al guardar los cambios';
        });
    });

    // Limpiar mensaje de error cuando se cierra el modal
    editModal.addEventListener('hidden.coreui.modal', function () {
        modalErrorMessage.style.display = 'none';
        modalErrorMessage.textContent = '';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const changePasswordForm = document.getElementById('changePasswordForm');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const passwordErrorMessage = document.getElementById('passwordErrorMessage');
    const savePasswordBtn = document.getElementById('savePasswordBtn');
    const changePasswordBtn = document.querySelector('button[data-bs-target="#changePasswordModal"]');

    // Asegurarse de que el botón de cambiar contraseña funcione
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            const modal = coreui.Modal.getInstance(changePasswordModal);
            if (!modal) {
                new coreui.Modal(changePasswordModal).show();
            } else {
                modal.show();
            }
        });
    }

    savePasswordBtn.addEventListener('click', function() {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validaciones
        if (!currentPassword) {
            passwordErrorMessage.style.display = 'block';
            passwordErrorMessage.textContent = 'Debes proporcionar tu contraseña actual';
            return;
        }

        if (newPassword !== confirmPassword) {
            passwordErrorMessage.style.display = 'block';
            passwordErrorMessage.textContent = 'Las nuevas contraseñas no coinciden';
            return;
        }

        // Enviar el formulario
        const formData = new FormData(changePasswordForm);
        fetch('index.php?view=updateuser', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('La contraseña actual no es correcta')) {
                passwordErrorMessage.style.display = 'block';
                passwordErrorMessage.textContent = 'La contraseña actual no es correcta';
            } else if (data.includes('success')) {
                const modal = coreui.Modal.getInstance(changePasswordModal);
                if (modal) {
                    modal.hide();
                }
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            passwordErrorMessage.style.display = 'block';
            passwordErrorMessage.textContent = 'Error al guardar los cambios';
        });
    });

    // Limpiar mensaje de error cuando se cierra el modal
    changePasswordModal.addEventListener('hidden.coreui.modal', function () {
        passwordErrorMessage.style.display = 'none';
        passwordErrorMessage.textContent = '';
        changePasswordForm.reset();
    });
});
</script> 