<?php
// Verificación explícita
$is_current_user_set = isset($current_user);
$is_admin_value = $is_current_user_set ? $current_user->is_admin : 'no definido';
$is_admin_type = $is_current_user_set ? gettype($current_user->is_admin) : 'no definido';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard</h3>
                </div>
                <div class="card-body">
                    <!-- Contenido del dashboard -->
                </div>
            </div>
        </div>
    </div>
</div> 