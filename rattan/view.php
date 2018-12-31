<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$dsn = 'mysql:dbname=rattan;host=127.0.0.1';
$user = 'rattan';
$password = 'Tuutsy@2018#';

$pdoConn = new PDO($dsn, $user, $password);
$pdoConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdoConn->prepare("SELECT id FROM csvtable_filtered WHERE status='Pending' GROUP BY id LIMIT 0, 500");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $id) {
    
    $checkQry = "SELECT * FROM csvtable_filtered WHERE id=".$id['id']." AND attribute_1_value <> '' and attribute_2_value <> '' ";
    $stmt= $pdoConn->prepare($checkQry);
    $checkResult = $stmt->fetchAll();
    
    if(count($checkResult) == 0){
        $reviewQry = "UPDATE csvtable_filtered SET status='Review' WHERE id=".$id['id'];
        $pdoConn->prepare($reviewQry)->execute();
        echo "Skipped<br>";
        continue;
    }
    
    $selectQry = "SELECT srno, name, attribute_1_name, attribute_1_value, attribute_2_value, sku, upc  FROM csvtable_filtered WHERE id = " . $id['id'] . " ORDER BY srno";
    $stmt = $pdoConn->prepare($selectQry);
    $stmt->execute();
    $res_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($res_data) == 1){
        $deleteQry = "UPDATE csvtable_filtered SET status='Checked' WHERE id=".$id['id'];
        $pdoConn->prepare($deleteQry)->execute();
        echo "Skipped<br>";
        continue;
    }
    
    echo '<table width="90%" border="1" cellspacing="0" align="center">';
echo '<tr><th>ID</th><th>Name</th><th>Configure</th><th>Color</th><th>Size</th><th>SKU</th><th>UPC</th><th></th><th></th><th></th></tr>';
$row = array();
    
    foreach ($res_data as $res) {
        $row[] = '<tr id="id-' . $res['srno'] . '" class="id-' . $id['id'] . '"><td>' . $id['id'] . '</td><td>' . $res['name'] . '</td><td>' . $res['attribute_1_name'] . '</td><td>' . $res['attribute_1_value'] . '</td><td>' . $res['attribute_2_value'] . '</td><td>' . $res['sku'] . '</td><td>' . $res['upc'] . '</td>'
                . '<td><button onclick="deleteRow(\'' . $res['srno'] . '\')">Delete</button></td>'
                . '<td><button onclick="deleteIDRow(\'' . $id['id'] . '\')">Delete ID</button></td>'
                . '<td><button onclick="checkRow(\'' . $res['srno'] . '\')">Mark Checked</button></td>'
                . '<td><button onclick="idCheckRow(\'' . $id['id'] . '\')">ID Checked</button></td>'
                . '<td><button onclick="idReview(\'' . $id['id'] . '\')">Review</button></td>'
                . '</tr>';
    }
    echo join("", $row);
echo '</table>';
echo '<br/></br>';
}

?>

<script>
    function deleteRow(srno) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("id-" + srno).style.display = "none";
            }
        };
        var data = new FormData();
        data.append('srno', srno);
        data.append('action', 'delete_duplicate');
        xhttp.open("POST", "ajax.php", true);
        xhttp.send(data);
    }
    function checkRow(srno) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("id-" + srno).style.display = "none";
            }
        };
        var data = new FormData();
        data.append('srno', srno);
        data.append('action', 'check');
        xhttp.open("POST", "ajax.php", true);
        xhttp.send(data);
    }
    function idCheckRow(id) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var divsToHide = document.getElementsByClassName("id-"+id); //divsToHide is an array
                for (var i = 0; i < divsToHide.length; i++) {
                    divsToHide[i].style.display = "none"; // depending on what you're doing
                }
            }
        };
        var data = new FormData();
        data.append('id', id);
        data.append('action', 'id_check');
        xhttp.open("POST", "ajax.php", true);
        xhttp.send(data);
    }
    function idReview(id) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var divsToHide = document.getElementsByClassName("id-"+id); //divsToHide is an array
                for (var i = 0; i < divsToHide.length; i++) {
                    divsToHide[i].style.display = "none"; // depending on what you're doing
                }
            }
        };
        var data = new FormData();
        data.append('id', id);
        data.append('action', 'id_review');
        xhttp.open("POST", "ajax.php", true);
        xhttp.send(data);
    }
    function deleteIDRow(id) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var divsToHide = document.getElementsByClassName("id-"+id); //divsToHide is an array
                for (var i = 0; i < divsToHide.length; i++) {
                    divsToHide[i].style.display = "none"; // depending on what you're doing
                }
            }
        };
        var data = new FormData();
        data.append('id', id);
        data.append('action', 'id_delete');
        xhttp.open("POST", "ajax.php", true);
        xhttp.send(data);
    }
</script>