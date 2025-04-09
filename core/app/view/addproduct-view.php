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
  $categoryName = $category ? strtolower(trim($category->name)) : '';

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
        'XXL' => $_POST["talla_xxl"],
        '3XL' => $_POST["talla_3xl"],
        '4XL' => $_POST["talla_4xl"],
        '6XL' => $_POST["talla_6xl"],
        '8XL' => $_POST["talla_8xl"]
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
    $product->jersey_type = $tipoJersey;
  } elseif($categoryName === 'tenis') {
    $tallas = [
      '23.5' => $_POST["talla_23_5"],
      '24' => $_POST["talla_24"],
      '24.5' => $_POST["talla_24_5"],
      '25' => $_POST["talla_25"],
      '25.5' => $_POST["talla_25_5"],
      '26' => $_POST["talla_26"],
      '26.5' => $_POST["talla_26_5"],
      '27' => $_POST["talla_27"]
    ];
  } elseif(in_array($categoryName, ['gorras', 'gorra', 'variado', 'balón', 'balon'])) {
    $tallas = ['unitalla' => $_POST["inventario_inicial"]];
  } else {
    $tallas = ['unitalla' => $_POST["q"]];
  }

  // Crear un producto para cada talla con cantidad mayor a 0
  foreach($tallas as $talla => $cantidad) {
    if($cantidad > 0) {
      $product->name = urldecode($_POST["name"]);
      $product->availability = floatval($cantidad);
      $product->size = $talla;
      $product->total = floatval($total_quantity);
      $total_quantity += floatval($cantidad);

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

  // Actualizar el total en todos los productos creados
  foreach($created_products as $product_id) {
    $product = ProductData::getById($product_id);
    if($product) {
      $product->total = floatval($total_quantity);
      $product->update();
    }
  }

  // Establecer la cookie para la alerta
  setcookie("prdadd", $product->name, time() + 60, "/");
  
  print "<script>window.location='index.php?view=inventary';</script>";
  exit;
}
?>