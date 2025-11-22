<?php
header('Content-Type: application/json');
include 'connection.php';

// 1. Get the data from the URL
$user_id = $_GET['user_id'];
$song_id = $_GET['song_id'];

// Check if data is missing
if (empty($user_id) || empty($song_id)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID or Song ID is missing.']);
    exit;
}

// 3. Build the DELETE query
$sql = "DELETE FROM history WHERE user_id = '$user_id' AND song_id = '$song_id'";

// 4. Run the query
if (mysqli_query($con, $sql)) {
    if (mysqli_affected_rows($con) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'History item removed.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No history item found to remove.']);
    }
} else {
    // Show the error if the query fails
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . mysqli_error($con)]);
}

// 5. Close the connection
$con->close();

?>