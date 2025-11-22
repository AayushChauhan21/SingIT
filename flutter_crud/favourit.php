<?php
header('Content-Type: application/json');
require 'connection.php';

// 1. Get data and make it safe by casting to integer
$user_id = $_POST['user_id'];
$song_id = $_POST['song_id'];

if (empty($user_id) || empty($song_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing user_id or song_id.']);
    exit;
}

// 2. Check if the record already exists
$sql_check = "SELECT id FROM favourite WHERE user_id = $user_id AND song_id = $song_id";
$result = mysqli_query($con, $sql_check);

if (mysqli_num_rows($result) > 0) {
    
    // 3. If it exists, DELETE it
    $sql_delete = "DELETE FROM favourite WHERE user_id = $user_id AND song_id = $song_id";
    
    if (mysqli_query($con, $sql_delete)) {
        echo json_encode(['status' => 'success', 'action' => 'deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete.']);
    }
    
} else {
    
    // 4. If it does NOT exist, INSERT it
    $sql_insert = "INSERT INTO favourite (user_id, song_id) VALUES ($user_id, $song_id)";

    if (mysqli_query($con, $sql_insert)) {
        echo json_encode(['status' => 'success', 'action' => 'inserted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert.']);
    }
}

mysqli_close($con);
?>