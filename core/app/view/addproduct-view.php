<?php
if(count($_POST)>0){
  $product = new ProductData();
  $product->barcode = $_POST["barcode"];
  
  // Asegurarse de que el nombre está decodificado desde el formulario
  $product->name = urldecode($_POST["name"]);
  
  $product->price_in = $_POST["price_in"];
  $product->price_out = $_POST["price_out"];
  $product->unit = $_POST["unit"];
  $product->description = $_POST["description"];
  $product->presentation = $_POST["presentation"];
  //$product->inventary_min = $_POST["inventary_min"];
  $category_id="NULL";
  if($_POST["category_id"]!=""){ $category_id=$_POST["category_id"];}
  $inventary_min="\"\"";
  if($_POST["inventary_min"]!=""){ $inventary_min=$_POST["inventary_min"];}

  $product->category_id=$category_id;
  $product->inventary_min=$inventary_min;
  $product->user_id = $_SESSION["user_id"];
  $product->is_active = isset($_POST["is_active"])?1:0;

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
  }
  else{
    $prod = $product->add();
  }

  if($_POST["q"]!="" && $_POST["q"]!="0"){
    $op = new OperationData();
    $op->product_id = $prod[1];
    $op->operation_type_id = OperationTypeData::getByName("entrada")->id;
    $op->q = floatval($_POST["q"]);
    $op->is_oficial = 1;
    
    // Usar SQL directo para asegurar que la operación se realiza correctamente
    $db = Database::getCon();
    $sql = "INSERT INTO operation (product_id, q, operation_type_id, is_oficial, created_at) 
            VALUES ({$op->product_id}, {$op->q}, {$op->operation_type_id}, 1, NOW())";
    
    $db->query($sql);
    
    // Actualizar la disponibilidad del producto
    $product = ProductData::getById($op->product_id);
    
    // Intentar asegurarse de que el nombre está decodificado correctamente
    if($product) {
        // Aplicar doble decodificación para estar seguros
        $productName = urldecode(urldecode($product->name));
    }
    
    $product->updateAvailability($op->q);
    
    // Obtener el nombre de la categoría
    $category = CategoryData::getById($product->category_id);
    $categoryName = $category ? $category->name : '';

    // Establecer las cookies para la alerta con el nombre ya decodificado
    setcookie("productCreated", "true", time() + 3600, "/");
    setcookie("productName", $productName, time() + 3600, "/");
    setcookie("productCategory", $categoryName, time() + 3600, "/");
    
    print "<script>window.location='index.php?view=inventary';</script>";
    exit;
  }

  // Establecer la cookie para mostrar la alerta
  setcookie("productCreated", "true", time() + 3600, "/");
  
  // Aplicar doble decodificación para estar seguros
  $productName = urldecode(urldecode($product->name));
  setcookie("productName", $productName, time() + 3600, "/");

  print "<script>window.location='index.php?view=inventary';</script>";
}
?>