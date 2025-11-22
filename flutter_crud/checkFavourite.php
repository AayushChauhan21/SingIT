<?php
header('Content-Type: application/json');
require 'connection.php';

// 1. Get data - This API just CHECKS, so we can use GET
$user_id = $_GET['user_id'];
$song_id = $_GET['song_id'];

if (empty($user_id) || empty($song_id)) {
    // Return false instead of error, as a non-logged-in user can't have favourites
    echo json_encode(['is_favourite' => false]);
    exit;
}

// 2. Check if the record exists
$sql_check = "SELECT id FROM favourite WHERE user_id = $user_id AND song_id = $song_id";
$result = mysqli_query($con, $sql_check);

if (mysqli_num_rows($result) > 0) {
    // 3. It exists
    echo json_encode(['is_favourite' => true]);
} else {
    // 4. It does not exist
    echo json_encode(['is_favourite' => false]);
}

mysqli_close($con);
?>