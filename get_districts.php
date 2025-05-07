<?php
include("connection/connect.php");

if (isset($_GET['q'])) {
    $q = mysqli_real_escape_string($db, $_GET['q']);

    // Truy vấn database
    $sql = "SELECT DISTINCT district FROM restaurant WHERE district LIKE '%$q%' ORDER BY district ASC LIMIT 5";
    $result = mysqli_query($db, $sql);

    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            echo '<a href="#" class="list-group-item list-group-item-action suggestion-item">' . $row['district'] . '</a>';
        }
    } else {
        echo 'Lỗi truy vấn: ' . mysqli_error($db);
    }
}
?>
