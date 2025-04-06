<?php
if(count($_POST)>0){
  $product = new ProductData();
  $product->barcode = $_POST["barcode"];
  $product->name = urldecode($_POST["name"]);
  $product->price_in = $_POST["price_in"];
  $product->price_out = $_POST["price_out"];
  $product->unit = $_POST["unit"];
  $product->description = $_POST["description"];
  $product->presentation = $_POST["presentation"];
  $category_id="NULL";
  if($_POST["category_id"]!=""){ $category_id=$_POST["category_id"];}
  $inventary_min="\"\"";
  if($_POST["inventary_min"]!=""){ $inventary_min=$_POST["inventary_min"];}

  $product->category_id=$category_id;
  $product->inventary_min=$inventary_min;
  $product->user_id = $_SESSION["user_id"];
  $product->is_active = 1;

  // Obtener la categoría seleccionada
  $category = CategoryData::getById($category_id);
  $categoryName = $category ? strtolower($category->name) : '';

  // Manejar las tallas según la categoría
  $total_quantity = 0;
  $created_products = [];

  if($categoryName === 'jersey') {
    $tipoJersey = $_POST["tipo_jersey"];
    if($tipoJersey === 'adulto') {
      $tallas = [
        'S' => $_POST["talla_s"],
        'M' => $_POST["talla_m"],
        'L' => $_POST["talla_l"],
        'XL' => $_POST["talla_xl"],
        'XXL' => $_POST["talla_xxl"]
      ];
    } else {
      $tallas = [
        '16' => $_POST["talla_16"],
        '18' => $_POST["talla_18"],
        '20' => $_POST["talla_20"],
        '22' => $_POST["talla_22"],
        '24' => $_POST["talla_24"],
        '26' => $_POST["talla_26"],
        '28' => $_POST["talla_28"]
      ];
    }
  } elseif($categoryName === 'tenis') {
    $tallas = [
      '6' => $_POST["talla_6"],
      '8' => $_POST["talla_8"],
      '9' => $_POST["talla_9"]
    ];
  } else {
    $tallas = ['1' => $_POST["cantidad_unica"]];
  }

  // Crear un producto para cada talla con cantidad mayor a 0
  foreach($tallas as $talla => $cantidad) {
    if($cantidad > 0) {
      $product->name = urldecode($_POST["name"]) . " - Talla " . $talla;
      $product->availability = $cantidad;
      $total_quantity += $cantidad;

      if(isset($_FILES["image"])){
        $image = new Upload($_FILES["image"]);
        if($image->uploaded){
          $image->Process("storage/products/");
          if($image->processed){
            $product->image = $image->file_dst_name;
            $prod = $product->add_with_image();
          }
        }else{
          $prod = $product->add();
        }
      } else {
        $prod = $product->add();
      }

      if($prod[0]){
        $created_products[] = $prod[1];
        
        // Crear la operación de entrada para esta talla
        $op = new OperationData();
        $op->product_id = $prod[1];
        $op->operation_type_id = OperationTypeData::getByName("entrada")->id;
        $op->q = floatval($cantidad);
        $op->is_oficial = 1;
        $op->talla = $talla;
        $op->add();
      }
    }
  }

  // Actualizar la disponibilidad total en todos los productos creados
  foreach($created_products as $product_id) {
    $product = ProductData::getById($product_id);
    if($product) {
      $product->updateAvailability($total_quantity);
    }
  }

  // Establecer la cookie para la alerta
  setcookie("prdadd", $product->name, time() + 60, "/");
  
  print "<script>window.location='index.php?view=inventary';</script>";
  exit;
}
?>