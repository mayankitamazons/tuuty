<?php

$dsn = 'mysql:dbname=rattan;host=127.0.0.1';
$user = 'rattan';
$password = 'Tuutsy@2018#';

$pdoConn = new PDO($dsn, $user, $password);
$pdoConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdoConn->prepare("TRUNCATE csvtable")->execute();

$insertQry = "INSERT INTO csvtable (name, id, visibility_in_catalog, short_description, description, tax_status, in_stock, allow_customer_reviews, regular_price, category, tag, images, external_url, attribute_1_name, attribute_1_value, attribute_2_name, attribute_2_value, reviews, raiting_count, sku, upc, status) VALUES(:name, :id, :visibility_in_catalog, :short_description, :description, :tax_status, :in_stock, :allow_customer_reviews, :regular_price, :category, :tag, :images, :external_url, :attribute_1_name, :attribute_1_value, :attribute_2_name, :attribute_2_value, :reviews, :raiting_count, :sku, :upc, :status)";
$stmt = $pdoConn->prepare($insertQry);

ini_set('auto_detect_line_endings', TRUE);

$files = array("import_file_1.csv", "import_file_2.csv", "import_file_3.csv", "import_file_4.csv","import_file_5.csv", "import_file_6.csv", "import_file_7.csv", "import_file_8.csv", "import_file_9.csv", "import_file_10.csv", "import_file_11.csv", "import_file_12.csv", "import_file_13.csv", "import_file_14.csv", "import_file_15.csv", "import_file_16.csv");


$total = 0;
foreach ($files as $file) {
    $handle = fopen($file, 'r');
    $i = 0;
    while (($data = fgetcsv($handle) ) !== FALSE) {
        $i++;
        if ($i == 1) {
            continue;
        }

        $values = array();
        $values['name'] = $data['0'];
        $values['id'] = $data['1'];
        $values['visibility_in_catalog'] = $data['2'];
        $values['short_description'] = $data['3'];
        $values['description'] = $data['4'];
        $values['tax_status'] = $data['5'];
        $values['in_stock'] = $data['6'];
        $values['allow_customer_reviews'] = $data['7'];
        $values['regular_price'] = str_replace(array("$", ","), "", $data['8']);
        $values['category'] = $data['9'];
        $values['tag'] = $data['10'];
        $values['images'] = $data['11'];
        $values['external_url'] = $data['12'];
        $values['attribute_1_name'] = $data['13'];
        $values['attribute_1_value'] = $data['14'];
        $values['attribute_2_name'] = $data['15'];
        $values['attribute_2_value'] = $data['16'];
        $values['reviews'] = $data['17'];
        $values['raiting_count'] = $data['18'];
        $values['status'] = 'Pending';
        if (!empty($data['19'])) {
            $values['sku'] = $data['19'];
        } else {
            $values['sku'] = $data['1'];
        }
        if(!isset($data['20'])){
//            echo '<pre>';
//            print_r($data);
//            echo '<pre>';
            echo "Line No : ".$i."\n";
//            exit();
            continue;
        }
        $values['upc'] = $data['20'];

        try {
            $stmt->execute($values);
        } catch (PDOException $ex) {
//            echo '<pre>';
//            print_r($data);
//            print_r($values);
//            echo '</pre>';
//            echo $ex->getMessage();
            echo "Line No : ".$i."\n";
            continue;
//            exit;
        }

    }
    $total += $i;
    echo "++++++ File $file import finished. ++++++\n";
}
ini_set('auto_detect_line_endings', FALSE);
echo 'Process completed. No of Import: '.$total;
