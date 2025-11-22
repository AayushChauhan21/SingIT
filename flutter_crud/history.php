<?php
include 'connection.php';

// 2. Get the data from Flutter (using $_GET as in your code)
$user_id = $_GET['user_id'];
$song_id = $_GET['song_id'];

// 3. Build and run the DELETE query first
// This will remove the old record if it exists.
$sql_delete = "DELETE FROM history WHERE user_id = '$user_id' AND song_id = '$song_id'";
mysqli_query($con, $sql_delete);

// 4. Build the INSERT query string
$sql_insert = "INSERT INTO history (user_id, song_id) VALUES ('$user_id', '$song_id')";

// 5. Run the INSERT query
if (mysqli_query($con, $sql_insert)) {
    // Changed the message to be more accurate
    echo json_encode(['status' => 'success', 'message' => 'History updated.']);
} else {
    // Show the error if the INSERT fails
    echo json_encode(['status' => 'error', 'message' => 'Failed: ' . mysqli_error($con)]);
}

// 6. Close the connection
$con->close();

?>