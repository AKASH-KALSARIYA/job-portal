<?php
require_once 'config/db_connection.php';

header('Content-Type: application/json');

if (isset($_GET['state_id']) && !empty($_GET['state_id'])) {
    $state_id = intval($_GET['state_id']);
    
    $query = "SELECT id, name FROM cities WHERE state_id = ? ORDER BY name";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $state_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $cities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cities[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
    
    echo json_encode($cities);
} else {
    echo json_encode([]);
} 