<?php

$dsn = 'mysql:dbname=rattan;host=127.0.0.1';
$user = 'rattan';
$password = 'Tuutsy@2018#';

$pdoConn = new PDO($dsn, $user, $password);
$pdoConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdoConn->prepare("UPDATE csvtable_import_combine SET attribute_2_value='' WHERE attribute_2_value='null'")->execute();
$pdoConn->prepare("UPDATE csvtable_import_combine SET attribute_1_value='' WHERE attribute_1_value='null'")->execute();

$stmt = $pdoConn->prepare("SELECT id, srno FROM csvtable_import_combine order by srno ");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$previous_srno = "";

foreach ($result as $row) {
    if (empty($row['id'])) {
        $previous_srno = $row['srno'];
    } else {
        if (!empty($row['id']) && $previous_srno != "") {
            $updateQry = "UPDATE csvtable_import_combine SET id='" . $row['id'] . "' WHERE srno=" . $previous_srno;
            $stmt = $pdoConn->prepare($updateQry);
            $stmt->execute();
            $previous_srno = "";
        }
    }
}

$stmt = $pdoConn->prepare("UPDATE csvtable_import_combine SET sku=id WHERE sku=''");
$stmt->execute();

echo "Process Completed.";
