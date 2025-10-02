<?php
include("../connection/connect.php");
session_start();

header('Content-Type: application/json');

// Check authentication
if(empty($_SESSION["adm_id"])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Validate input
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Invalid user ID']);
    exit();
}

try {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM users WHERE u_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    
    if(!$stmt) {
        throw new Exception("Query preparation failed");
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if(!mysqli_stmt_execute($stmt)) {
        throw new Exception("Query execution failed");
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if(!$user) {
        echo json_encode(['error' => 'User not found']);
        exit();
    }
    
    echo json_encode(['success' => true, 'data' => $user]);

} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>