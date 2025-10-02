<?php
include("../connection/connect.php");
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

$sql = "SELECT * FROM res_category ORDER BY c_name ASC";
$result = mysqli_query($db, $sql);
$categories = array();

while($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode($categories);
?>