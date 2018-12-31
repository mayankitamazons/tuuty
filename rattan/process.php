<?php

$dsn = 'mysql:dbname=rattan;host=127.0.0.1';
$user = 'rattan';
$password = 'Tuutsy@2018#';

$pdoConn = new PDO($dsn, $user, $password);
$pdoConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdoConn->prepare("SELECT id FROM csvtable_import_combine WHERE status='Pending' GROUP BY id");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$id_count = 0;

$main_product_count = 0;

$stmt = $pdoConn->prepare('SELECT max(srno) as counter FROM `export`');
$stmt->execute();
$srno = $stmt->fetch(PDO::FETCH_ASSOC);
$sku_base = 1000000;
if($srno['counter'] > 0){
    $sku_base = $sku_base + $srno['counter'];
}

$insStmt = $pdoConn->prepare("INSERT INTO export (sku, attribute_set, type, simple_skus, configurable_attributes, size, color, categories, name, description, short_description, price, special_price, qty, is_in_stock, manage_stock, use_config_manage_stock, status, visibility, weight, tax_class_id, thumbnail, small_image, image, media_gallery, simples_skus, upc, old_id) VALUES (:sku, :attribute_set, :type, :simple_skus, :configurable_attributes, :size, :color, :categories, :name, :description, :short_description, :price, :special_price, :qty, :is_in_stock, :manage_stock, :use_config_manage_stock, :status, :visibility, :weight, :tax_class_id, :thumbnail, :small_image, :image, :media_gallery, :simples_skus, :upc, :old_id)");

$row_string = "(:sku, :attribute_set, :type, :simple_skus, :configurable_attributes, :size, :color, :categories, :name, :description, :short_description, :price, :special_price, :qty, :is_in_stock, :manage_stock, :use_config_manage_stock, :status, :visibility, :weight, :tax_class_id, :thumbnail, :small_image, :image, :media_gallery, :simples_skus, :upc, :old_id)";
$multi_row_string = array();

foreach ($result as $id) {

    $selectQry = "SELECT * FROM csvtable_import_combine WHERE id= :id ORDER BY srno";
    $stmt = $pdoConn->prepare($selectQry);
    $stmt->execute(array("id" => $id['id']));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products['0']['visibility_in_catalog'])) {
        //Configurable Product

        $configue_data = array('sku' => '', 'attribute_set' => '', 'type' => '', 'simple_skus' => '', 'configurable_attributes' => '', 'size' => '', 'color' => '', 'categories' => '', 'name' => '', 'description' => '', 'short_description' => '', 'price' => '', 'special_price' => '', 'qty' => '', 'is_in_stock' => '', 'manage_stock' => '', 'use_config_manage_stock' => '', 'status' => '', 'visibility' => '', 'weight' => '', 'tax_class_id' => '', 'thumbnail' => '', 'small_image' => '', 'image' => '', 'media_gallery' => '', 'simples_skus' => '', 'upc' => '', 'old_id' => '');
        
        $simple_combo = array();
        $srno_combo = array();

        $simples_skus = array();
        $skip = false;
        $config_attr = "";
        $description = "";
        $categories = "";
        $short_description = "";
        $price = array();
        $qty = 100;
        $upc = "";

        if (strtolower(trim($products['0']['attribute_1_name'])) == "size") {
            $config_attr = "size";
        } elseif (strtolower(trim($products['0']['attribute_1_name'])) == "color") {
            $config_attr = "color";
        } else {
            $config_attr = "color,size";
        }
        $configSrno = '';

        foreach ($products as $product) {

            $id_count ++;

            if ($skip == false) {

//                    $configue_data['sku'] = $product['sku']."-$id_count";
                $configSrno = $product['srno'];
                $configue_data['sku'] = $sku_base + $id_count;
                $configue_data['attribute_set'] = 'Default';
                $configue_data['type'] = 'configurable';
                $configue_data['name'] = $product['name'];
                $configue_data['special_price'] = '';
                $configue_data['is_in_stock'] = '1';
                $configue_data['manage_stock'] = '1';
                $configue_data['use_config_manage_stock'] = '1';
                $configue_data['status'] = '1';
                $configue_data['visibility'] = '4';
                $configue_data['weight'] = '1';
                $configue_data['tax_class_id'] = 'Taxable goods';
                $configue_data['old_id'] = $product['id'];

                $configue_data['configurable_attributes'] = $config_attr;
                $temp = explode("\n", $product['images']);
                $image = array();
                foreach ($temp as $i) {
                    if (!empty($i)) {
                        $image[] = $i;
                    }
                }
                if (isset($image[0])) {
                    $configue_data['thumbnail'] = $image[0];
                    $configue_data['small_image'] = $image[0];
                    $configue_data['image'] = $image[0];
                    $configue_data['media_gallery'] = implode(";;", $image);
                }

                $skip = true;
                continue;
            }
            $product_data = array('sku' => '', 'attribute_set' => '', 'type' => '', 'simple_skus' => '', 'configurable_attributes' => '', 'size' => '', 'color' => '', 'categories' => '', 'name' => '', 'description' => '', 'short_description' => '', 'price' => '', 'special_price' => '', 'qty' => '', 'is_in_stock' => '', 'manage_stock' => '', 'use_config_manage_stock' => '', 'status' => '', 'visibility' => '', 'weight' => '', 'tax_class_id' => '', 'thumbnail' => '', 'small_image' => '', 'image' => '', 'media_gallery' => '', 'simples_skus' => '', 'upc' => '', 'old_id' => '');


            $product_data['attribute_set'] = 'Default';
            $product_data['type'] = 'simple';
            $product_data['manage_stock'] = '1';
            $product_data['use_config_manage_stock'] = '1';
            $product_data['tax_class_id'] = 'Taxable goods';
            $product_data['weight'] = '1';
            $product_data['visibility'] = '1';
            $product_data['status'] = '1';
            $product_data['special_price'] = '';
            $product_data['qty'] = '100';
            $product_data['is_in_stock'] = '1';


            $product_data['name'] = $product['name'];
            $product_data['description'] = $product['description'];
            $product_data['short_description'] = $product['short_description'];
            $product_data['price'] = $product['regular_price'];
//                $product_data['sku'] = $product['sku']."-$id_count";;
            $product_data['sku'] = $sku_base + $id_count;
            $product_data['old_id'] = $product['id'];
            $product_data['upc'] = $product['upc'];

            $category = "";
            if (!empty($product['category'])) {
                $category = $product['category'];
                if (!empty($product['tag'])) {
                    $category .= "/" . $product['tag'];
                }
            }
            $temp = explode("\n", $product['images']);
            $img = array();
            foreach ($temp as $i) {
                if (!empty($i)) {
                    $img[] = $i;
                }
            }
            $product_data['categories'] = $category;
            $product_data['configurable_attributes'] = '';
            if ($config_attr == "size") {
                $product_data['size'] = $product['attribute_2_value'];
                $product_data['color'] = '';
            } elseif ($config_attr == "color") {
                $product_data['size'] = '';
                $product_data['color'] = $product['attribute_1_value'];
            } else {
                $product_data['color'] = $product['attribute_1_value'];
                $product_data['size'] = $product['attribute_2_value'];
            }

            if (isset($img[0])) {
                $product_data['thumbnail'] = $img[0];
                $product_data['small_image'] = $img[0];
                $product_data['image'] = $img[0];
                $product_data['media_gallery'] = implode(";;", $img);
            }
            $simples_skus[] = $sku_base + $id_count;
            $product_data['simples_skus'] = '';
            $product_data['simple_skus'] = '';
            $price[] = $product_data['price'];
            if (!empty($product_data['categories'])) {
                $categories = $product_data['categories'];
            }
            if (!empty($product_data['description'])) {
                $description = $product_data['description'];
            }
            if (!empty($product_data['short_description'])) {
                $short_description = $product_data['short_description'];
            }
            if (!empty($product_data['upc'])) {
                $upc = $product_data['upc'];
            }
            
            $simple_combo[] = $product_data;
            $srno_combo[] = $product['srno'];
            $multi_row_string[] = $row_string;
            
//            try {
//                
//                $insStmt->execute($product_data);
//                $statusQry = "UPDATE csvtable_import_combine SET status='Complete' WHERE srno=".$product['srno'];
//                $statusStmt = $pdoConn->prepare($statusQry);
//                $statusStmt->execute();
//                echo "Insert Count : $id_count\n";
//            } catch (PDOException $ex) {
//                echo $ex->getMessage();
//            }
        }
        
        //Simple Products Multi Insert
        try {
            $multiQry = "INSERT INTO export (sku, attribute_set, type, simple_skus, configurable_attributes, size, color, categories, name, description, short_description, price, special_price, qty, is_in_stock, manage_stock, use_config_manage_stock, status, visibility, weight, tax_class_id, thumbnail, small_image, image, media_gallery, simples_skus, upc, old_id) VALUES ".$row_string;
            $multiStmt = $pdoConn->prepare($multiQry);
            foreach ($simple_combo as $single){
                $multiStmt->execute($single);
            }
            $statusQry = "UPDATE csvtable_import_combine SET status='Complete' WHERE srno IN (".  implode(",", $srno_combo).")";
            $statusStmt = $pdoConn->prepare($statusQry);
            $statusStmt->execute();
            echo "Insert Count : $id_count\n";
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }

        $configue_data['simple_skus'] = implode(",", $simples_skus);
        $configue_data['simples_skus'] = implode(",", $simples_skus);
        $configue_data['size'] = '';
        $configue_data['color'] = '';
        $configue_data['upc'] = $upc;
        $configue_data['categories'] = $categories;
        $configue_data['description'] = $description;
        $configue_data['short_description'] = $short_description;
        $configue_data['price'] = min($price);
        $configue_data['qty'] = $qty;
        try {
            $insStmt->execute($configue_data);
            $statusQry = "UPDATE csvtable_import_combine SET status='Complete' WHERE srno=" . $configSrno;
            $statusStmt = $pdoConn->prepare($statusQry);
            $statusStmt->execute();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
//                exit();
        }
    } else {
        $id_count ++;
        //Simplete Product
        $simple_data = array('sku' => '', 'attribute_set' => '', 'type' => '', 'simple_skus' => '', 'configurable_attributes' => '', 'size' => '', 'color' => '', 'categories' => '', 'name' => '', 'description' => '', 'short_description' => '', 'price' => '', 'special_price' => '', 'qty' => '', 'is_in_stock' => '', 'manage_stock' => '', 'use_config_manage_stock' => '', 'status' => '', 'visibility' => '', 'weight' => '', 'tax_class_id' => '', 'thumbnail' => '', 'small_image' => '', 'image' => '', 'media_gallery' => '', 'simples_skus' => '', 'upc' => '', 'old_id' => '');

        $simple_data['sku'] = $sku_base + $id_count;
        $simple_data['attribute_set'] = 'Default';
        $simple_data['type'] = 'simple';
        $simple_data['simple_skus'] = '';
        $simple_data['configurable_attributes'] = '';
        if (!empty($products['0']['attribute_1_value'])) {
            $simple_data['size'] = $products['0']['attribute_2_value'];
        }
        if (!empty($products['0']['attribute_2_value'])) {
            $simple_data['color'] = $products['0']['attribute_1_value'];
        }
        $category = "";
        if (!empty($products['0']['category'])) {
            $category = $products['0']['category'];
            if (!empty($products['0']['tag'])) {
                $category .= "/" . $products['0']['tag'];
            }
        }
        $temp = explode("\n", $products['0']['images']);
        $img = array();
        foreach ($temp as $i) {
            if (!empty($i)) {
                $img[] = $i;
            }
        }
        $simple_data['categories'] = $category;

        $simple_data['name'] = $products['0']['name'];
        $simple_data['description'] = $products['0']['description'];
        $simple_data['short_description'] = $products['0']['short_description'];
        $simple_data['price'] = $products['0']['regular_price'];
        $simple_data['special_price'] = '';
        $simple_data['qty'] = '100';
        $simple_data['is_in_stock'] = '1';
        $simple_data['manage_stock'] = '1';
        $simple_data['use_config_manage_stock'] = '1';
        $simple_data['status'] = '1';
        $simple_data['visibility'] = '4';
        $simple_data['weight'] = '1';
        $simple_data['tax_class_id'] = 'Taxable goods';
        if (isset($img[0])) {
            $simple_data['thumbnail'] = $img[0];
            $simple_data['small_image'] = $img[0];
            $simple_data['image'] = $img[0];
            $simple_data['media_gallery'] = implode(";;", $img);
        }

        $simple_data['simples_skus'] = '';
        $simple_data['old_id'] = $products['0']['id'];
        $simple_data['upc'] = $products['0']['upc'];
        
        try {
            $insStmt->execute($simple_data);
            $statusQry = "UPDATE csvtable_import_combine SET status='Complete' WHERE srno=" . $products['0']['srno'];
            $statusStmt = $pdoConn->prepare($statusQry);
            $statusStmt->execute();
            echo "Insert Count : $id_count\n";
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }
    $main_product_count++;
//    if($main_product_count == 1){
//        break;
//    }
}

echo "Child Product Count: $id_count\n";
echo "Main Product Count: $main_product_count\n";

echo "Process Completed";
