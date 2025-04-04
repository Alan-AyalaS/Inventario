<div class="row">
<div class="col-md-12">
<h1>Configuración</h1>

<?php

$configurations = ConfigurationData::getAll();
$ordered_configs = array();

// Definir el orden deseado
$order = array(
    "title" => 1,
    "use_image_product" => 2,
    "active_sells" => 3,
    "active_sell" => 4,
    "active_box" => 5,
    "active_reports" => 6,
    "active_clients" => 7,
    "active_providers" => 8,
    "active_purchases" => 9,
    "active_categories" => 10,
    "active_reports_word" => 11,
    "active_reports_excel" => 12,
    "active_reports_pdf" => 13
);

// Asegurar que active_purchases sea de tipo bool
foreach($configurations as $conf) {
    if($conf->short == "active_purchases") {
        $conf->kind = 1; // Cambiar a tipo bool
    }
}

// Ordenar las configuraciones
foreach($configurations as $conf) {
    if(isset($order[$conf->short])) {
        $ordered_configs[$order[$conf->short]] = $conf;
    }
}

// Ordenar por clave
ksort($ordered_configs);

// Agrupar las configuraciones por categorías
$system_configs = array();
$section_configs = array();
$report_configs = array();

foreach($ordered_configs as $conf) {
    // Configuraciones del sistema
    if(in_array($conf->short, ["title", "use_image_product"])) {
        $system_configs[] = $conf;
    }
    // Configuraciones de secciones
    elseif(in_array($conf->short, ["active_sells", "active_sell", "active_box", "active_reports", "active_clients", "active_providers", "active_purchases", "active_categories"])) {
        $section_configs[] = $conf;
    }
    // Configuraciones de reportes
    elseif(in_array($conf->short, ["active_reports_word", "active_reports_excel", "active_reports_pdf"])) {
        $report_configs[] = $conf;
    }
    // Otras configuraciones
    else {
        $system_configs[] = $conf;
    }
}

?>

<?php if(count($ordered_configs)>0):?>
<br>
<div class="card mb-4">
	<div class="card-header">
		<h4 class="card-title">Configuración del Sistema</h4>
	</div>
	<div class="card-body">
		<form method="post" action="index.php?view=processsettings">
            <!-- Configuraciones del Sistema -->
            <div class="mb-4">
                <h5 class="border-bottom pb-2">Configuración General</h5>
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Configuración</th>
                        <th>Valor</th>
                    </thead>
                    <?php foreach($system_configs as $conf):?>
                    <tr>
                        <td><?php echo $conf->name; ?></td>
                        <td>
                            <?php if($conf->kind==1):?>
                                <input type="hidden" name="<?php echo $conf->short; ?>" value="0">
                                <input type="checkbox" name="<?php echo $conf->short; ?>" value="1" <?php echo ($conf->val == 1) ? 'checked' : ''; ?>>
                            <?php else:?>
                                <input type="text" name="<?php echo $conf->short; ?>" value="<?php echo $conf->val; ?>" class="form-control">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Configuraciones de Secciones -->
            <div class="mb-4">
                <h5 class="border-bottom pb-2">Secciones del Sistema</h5>
                <p class="text-muted">Activa o desactiva las diferentes secciones del sistema</p>
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Sección</th>
                        <th>Estado</th>
                    </thead>
                    <?php foreach($section_configs as $conf):?>
                    <tr>
                        <td><?php echo $conf->name; ?></td>
                        <td>
                            <?php if($conf->kind==1):?>
                                <input type="hidden" name="<?php echo $conf->short; ?>" value="0">
                                <input type="checkbox" name="<?php echo $conf->short; ?>" value="1" <?php echo ($conf->val == 1) ? 'checked' : ''; ?>>
                            <?php else:?>
                                <input type="text" name="<?php echo $conf->short; ?>" value="<?php echo $conf->val; ?>" class="form-control">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Configuraciones de Reportes -->
            <div class="mb-4">
                <h5 class="border-bottom pb-2">Formato de Reportes</h5>
                <p class="text-muted">Activa o desactiva los formatos disponibles para reportes</p>
                <table class="table table-bordered table-hover">
                    <thead>
                        <th>Formato</th>
                        <th>Estado</th>
                    </thead>
                    <?php foreach($report_configs as $conf):?>
                    <tr>
                        <td><?php echo $conf->name; ?></td>
                        <td>
                            <?php if($conf->kind==1):?>
                                <input type="hidden" name="<?php echo $conf->short; ?>" value="0">
                                <input type="checkbox" name="<?php echo $conf->short; ?>" value="1" <?php echo ($conf->val == 1) ? 'checked' : ''; ?>>
                            <?php else:?>
                                <input type="text" name="<?php echo $conf->short; ?>" value="<?php echo $conf->val; ?>" class="form-control">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary">Guardar Cambios</button>
			</div>
		</form>
	</div>
</div>

<?php endif; ?>

</div>
</div>
