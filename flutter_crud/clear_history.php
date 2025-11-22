<?php
header('Content-Type: application/json');
include 'connection.php';

// 1. Get the user_id from the URL
$user_id = $_GET['user_id'];

// Check if data is missing
if (empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is missing.']);
    exit;
}

// 2. Build the DELETE query
$sql = "DELETE FROM history WHERE user_id = '$user_id'";

// 3. Run the query
if (mysqli_query($con, $sql)) {
    echo json_encode(['status' => 'success', 'message' => 'History cleared.']);
} else {
    // Show the error if the query fails
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . mysqli_error($con)]);
}

// 4. Close the connection
$con->close();

?>