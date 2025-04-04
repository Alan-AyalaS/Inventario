<?php


if(count($_POST)>0){
  $product = new ProductData();
  $product->barcode = $_POST["barcode"];
  $product->name = $_POST["name"];
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


  if(isset($_FILES["image"])){
    $image = new Upload($_FILES["image"]);
    if($image->uploaded){
      $image->Process("storage/products/");
      if($image->processed){
        $product->image = $image->file_dst_name;
        $prod = $product->add_with_image();
      }
    }else{

  $prod= $product->add();
    }
  }
  else{
  $prod= $product->add();

  }




if($_POST["q"]!="" && $_POST["q"]!="0"){
 $op = new OperationData();
 $op->product_id = $prod[1];
 $op->operation_type_id = OperationTypeData::getByName("entrada")->id;
 $op->q = floatval($_POST["q"]);
 $op->sell_id = "NULL";
 $op->is_oficial = 1;
 $op->created_at = "NOW()";
 
 // Establecer el ID del último producto agregado en la sesión
 $_SESSION['last_product_id'] = $prod[1];
 
 $result = $op->add();
 
 // Mensajes de depuración solo en consola para el último producto
 echo "<script>";
 echo "console.log('Depuración de registro de producto:');";
 echo "console.log('ID del producto: " . $prod[1] . "');";
 echo "console.log('Cantidad a registrar: " . $_POST["q"] . "');";
 echo "console.log('Tipo de operación: " . $op->operation_type_id . "');";
 echo "console.log('Resultado de la operación: " . json_encode($result) . "');";
 
 // Verificar si la operación se registró
 $operations = OperationData::getAllByProductId($prod[1]);
 echo "console.log('Operaciones encontradas: " . count($operations) . "');";
 foreach($operations as $op){
     echo "console.log('Operación ID: " . $op->id . ", Cantidad: " . $op->q . ", Tipo: " . $op->operation_type_id . "');";
 }
 echo "</script>";
 
 // Redirección automática después de 2 segundos
 echo "<script>
 	setTimeout(function() {
 		window.location.href = 'index.php?view=products';
 	}, 2000);
 </script>";
 exit;
}

print "<script>window.location='index.php?view=products';</script>";


}


?>