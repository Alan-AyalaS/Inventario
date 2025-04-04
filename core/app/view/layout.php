<?php
$purchases_enabled = false;
foreach($configs as $conf) {
    if($conf->short == "active_purchases" && $conf->val == 1) {
        $purchases_enabled = true;
        break;
    }
}
?>
                    <li class="nav-item"><a class="nav-link" href="./?view=inventary">
                        <svg class="nav-icon">
                          <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-layers"></use>
                        </svg> Inventario</a>
                        <ul class="nav-group-items">
                          <li class="nav-item"><a class="nav-link" href="./?view=inventary"><span class="nav-icon"></span> Inventario</a></li>
                          <?php if($purchases_enabled): ?>
                          <li class="nav-item"><a class="nav-link" href="./?view=purchases"><span class="nav-icon"></span> Compras</a></li>
                          <li class="nav-item"><a class="nav-link" href="./?view=make-purchase"><span class="nav-icon"></span> Hacer Compra</a></li>
                          <?php endif; ?>
                        </ul>
                    </li> 