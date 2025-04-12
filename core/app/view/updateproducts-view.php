<?php
// Verificar si el usuario es administrador
if(!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != "1") {
    header("Location: index.php?view=home");
    exit;
}

if(count($_POST) > 0) {
    $product_ids = json_decode($_POST["product_ids"]);
    $updates = 0;
    $errors = 0;

    // Preparar los datos a actualizar
    $update_data = array();
    if(!empty($_POST["price_in"])) $update_data["price_in"] = $_POST["price_in"];
    if(!empty($_POST["price_out"])) $update_data["price_out"] = $_POST["price_out"];
    if(!empty($_POST["unit"])) $update_data["unit"] = $_POST["unit"];
    if(!empty($_POST["inventary_min"])) $update_data["inventary_min"] = $_POST["inventary_min"];
    if(isset($_POST["category_id"]) && $_POST["category_id"] != "") $update_data["category_id"] = $_POST["category_id"];
    if(isset($_POST["tipo_jersey"]) && $_POST["tipo_jersey"] != "") $update_data["jersey_type"] = $_POST["tipo_jersey"];
    if(isset($_POST["is_active"])) $update_data["is_active"] = 1;

    // Procesar cada producto
    foreach($product_ids as $product_id) {
        $product = ProductData::getById($product_id);
        if($product) {
            // Actualizar los campos del producto
            foreach($update_data as $key => $value) {
                $product->$key = $value;
            }

            // Si hay una imagen nueva
            if(isset($_FILES["image"]) && $_FILES["image"]["tmp_name"] != "") {
                $image = new Upload($_FILES["image"]);
                if($image->uploaded) {
                    // Configurar el procesamiento de la imagen
                    $image->image_resize = true;
                    $image->image_x = 300;
                    $image->image_ratio_y = true;
                    
                    // Procesar la imagen
                    $image->Process("storage/products/");
                    if($image->processed) {
                        // Guardar el nombre de la imagen
                        $product->image = $image->file_dst_name;
                        try {
                            $product->update_with_image();
                            $updates++;
                        } catch(Exception $e) {
                            $errors++;
                        }
                    } else {
                        $errors++;
                    }
                } else {
                    $errors++;
                }
            } else {
                // Actualizar sin imagen
                try {
                    $product->update();
                    $updates++;
                } catch(Exception $e) {
                    $errors++;
                }
            }
        }
    }

    // Establecer mensaje de resultado
    if($updates > 0) {
        setcookie("prdupd", "true", time()+3600, "/");
        if($errors > 0) {
            setcookie("prdupd_warning", "Se actualizaron $updates productos, pero hubo $errors errores", time()+3600, "/");
        }
    } else if($errors > 0) {
        setcookie("prdupd_error", "Hubo errores al actualizar los productos", time()+3600, "/");
    }

    // Construir la URL de redirección con los parámetros de filtro
    $redirect_url = "index.php?view=inventary";
    $filter_params = array("category_id", "availability", "size", "date_filter", "search", "limit", "jerseyType", "page");

    foreach($filter_params as $param) {
        if(isset($_POST[$param]) && $_POST[$param] != "") {
            $redirect_url .= "&" . $param . "=" . urlencode($_POST[$param]);
        }
    }

    // Redirigir con mensaje de éxito
    header("Location: " . $redirect_url);
    exit;
}

?> 