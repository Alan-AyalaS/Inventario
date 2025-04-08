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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Dashboard</h3>
                    <!-- Botón de Debug Admin -->
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#adminDebugModal">
                        <i class="fas fa-bug"></i> Debug Admin
                    </button>
                </div>
                <div class="card-body">
                    <!-- Contenido del dashboard -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Debug Admin -->
<div class="modal fade" id="adminDebugModal" tabindex="-1" role="dialog" aria-labelledby="adminDebugModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminDebugModalLabel">Debug - Menú Administración</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>1. Información del Usuario Actual:</h6>
                        <pre><?php 
                        echo "ID: " . $current_user->id . "\n";
                        echo "Nombre: " . $current_user->name . "\n";
                        echo "is_admin: " . $current_user->is_admin . "\n";
                        echo "Tipo de is_admin: " . gettype($current_user->is_admin) . "\n";
                        ?></pre>
                    </div>
                    <div class="col-md-6">
                        <h6>2. Estado de la Condición:</h6>
                        <pre><?php
                        echo "¿\$current_user está definido? " . ($is_current_user_set ? 'Sí' : 'No') . "\n";
                        echo "Valor de is_admin: " . $is_admin_value . "\n";
                        echo "Tipo de is_admin: " . $is_admin_type . "\n";
                        echo "Comparación is_admin === 1: " . ($is_current_user_set && $current_user->is_admin === 1 ? 'Sí' : 'No') . "\n";
                        echo "Comparación is_admin == 1: " . ($is_current_user_set && $current_user->is_admin == 1 ? 'Sí' : 'No') . "\n";
                        echo "Comparación is_admin === '1': " . ($is_current_user_set && $current_user->is_admin === '1' ? 'Sí' : 'No') . "\n";
                        ?></pre>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>3. Variables de Sesión:</h6>
                        <pre><?php print_r($_SESSION); ?></pre>
                    </div>
                    <div class="col-md-6">
                        <h6>4. Objeto Usuario Completo:</h6>
                        <pre><?php print_r($current_user); ?></pre>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>5. Consulta SQL:</h6>
                        <pre>SELECT * FROM user WHERE id = <?php echo $_SESSION["user_id"]; ?></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div> 