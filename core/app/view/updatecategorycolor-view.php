<?php
// Actualización de color de categoría

if(count($_POST) > 0 && isset($_POST["category_id"]) && isset($_POST["color"])) {
    $category_id = $_POST["category_id"];
    $color = $_POST["color"];
    
    // Obtenemos la categoría
    $category = CategoryData::getById($category_id);
    
    if($category) {
        // Configuramos las cookies para almacenar el color y mostrar una alerta
        setcookie("category_color_".$category_id, $color, time() + 365 * 24 * 60 * 60);
        setcookie("color_updated", "true", time() + 60);
        
        // Redirigimos de vuelta a la lista de categorías
        echo "<script>
        localStorage.setItem('category_color_".$category_id."', '".$color."');
        window.location='index.php?view=categories';
        </script>";
    } else {
        // En caso de error, redirigir igualmente
        echo "<script>window.location='index.php?view=categories';</script>";
    }
} else {
    // Si no hay datos POST o faltan campos, redirigir a la lista de categorías
    echo "<script>window.location='index.php?view=categories';</script>";
}
?> 