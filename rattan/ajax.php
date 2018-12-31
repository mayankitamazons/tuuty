<?php
$dsn = 'mysql:dbname=rattan;host=127.0.0.1';
$user = 'rattan';
$password = 'Tuutsy@2018#';

$pdoConn = new PDO($dsn, $user, $password);
$pdoConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_POST['action']) && $_POST['action'] == 'delete_duplicate'){
    $srno = $_POST['srno'];
    $insertQry = "INSERT INTO deleted_data SELECT * FROM csvtable_filtered WHERE srno=$srno";
    $pdoConn->prepare($insertQry)->execute();
    $deleteQry = "DELETE FROM csvtable_filtered WHERE srno=$srno";
    $pdoConn->prepare($deleteQry)->execute();
    echo 'success';
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'check'){
    $srno = $_POST['srno'];
    $deleteQry = "UPDATE csvtable_filtered SET status='Checked' WHERE srno=$srno";
    $pdoConn->prepare($deleteQry)->execute();
    echo 'success';
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'id_check'){
    $id = $_POST['id'];
    $deleteQry = "UPDATE csvtable_filtered SET status='Checked' WHERE id=$id";
    $pdoConn->prepare($deleteQry)->execute();
    echo 'success';
    exit;
}
if(isset($_POST['action']) && $_POST['action'] == 'id_review'){
    $id = $_POST['id'];
    $deleteQry = "UPDATE csvtable_filtered SET status='Review' WHERE id=$id";
    $pdoConn->prepare($deleteQry)->execute();
    echo 'success';
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'id_delete'){
    $id = $_POST['id'];
    $insertQry = "INSERT INTO deleted_data SELECT * FROM csvtable_filtered WHERE id=$id AND status='Pending'";
    $pdoConn->prepare($insertQry)->execute();
    $deleteQry = "DELETE FROM csvtable_filtered WHERE id=$id AND status='Pending'";
    $pdoConn->prepare($deleteQry)->execute();
    echo 'success';
    exit;
}